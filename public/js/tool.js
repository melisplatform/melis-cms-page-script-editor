$(function(){
    // Tool scripts
    $body = $("body");
   
    /**
     * Exclude site script checkbox
     */
    $body.on(
        "click",
        ".melis-cms-page-script-checkbox input[type=checkbox]",
        function() {
            $(this)
                .parent()
                .find(".cbmask-inner")
                .toggleClass("cb-active");
        }
    );   

    //when 'delete exception' button is clicked, remove the page exception entry from DB 
    $body.on('click', '.btnDeletePageException', function (event) {    
        var pageID = null;
        var scriptDataTable = getDataTable();      
        var selectedTrClass = $(this).parents('tr').attr('class');      

        //get the page id
        if (selectedTrClass == 'child') {    
            pageID = scriptDataTable.row($(this).closest("tr").prev()[0]).data().mcse_page_id; 
        } else {
            pageID = scriptDataTable.row($(this).closest("tr")).data().mcse_page_id; 
        }

        //if deletion is confirmed, proceed to delete
        melisCoreTool.confirm(
            translations.tr_meliscmspagescripteditor_common_label_yes,
            translations.tr_meliscmspagescripteditor_common_label_no,
            translations.tr_meliscmspagescripteditor_delete_exception_btn_tooltip,
            translations.tr_meliscmspagescripteditor_delete_exception_btn_confirm,              
            function() {  
                saveSiteScriptException('delete', pageID);
        });
    }); 

    //add page to exception list
    $body.on('click', '#add_tool_site_script_exception_btn', function (event) {         
        var pageId = $("#tool_site_exception_page_id").val();
        
        //calls function to add exception in DB
        saveSiteScriptException('add', pageId);    
    });

    /*this will add or delete the pages that exclude the site scripts*/
    function saveSiteScriptException(operation, pageID) {           
        var siteId = activeTabId.split("_")[0];
           
        $.ajax({
            type: 'POST',
            url: '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorToolSiteEdition/saveSiteScriptException',
            data: {siteId:siteId, operation:operation, tool_site_mcse_page_id:pageID},          
            dataType: "json",
            encode: true,
        }).done(function (data) {
            if (data.success) {
                melisHelper.melisOkNotification( data.textTitle, data.textMessage);
                
                //remove highlight
                $("#"+siteId+"_id_meliscms_tool_sites_script_content").find("#tool_site_exception_page_id").removeClass('tool_site_page_exception_error');
               
                // refresh the main list table 
                melisHelper.zoneReload(siteId+"_id_meliscms_tool_sites_script_exceptions", "meliscms_tool_sites_script_exceptions", {siteId:siteId});   

                //empty input field
                $("#"+siteId+"_id_meliscms_tool_sites_script_content").find("#tool_site_exception_page_id").val("");                 

            } else {
                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);  
                
                //highlight input field           
                $("#"+siteId+"_id_meliscms_tool_sites_script_content").find("#tool_site_exception_page_id").addClass('tool_site_page_exception_error');               
            }

        }).fail(function () {                    
            alert(translations.tr_meliscore_error_message);
        });  
    }

    /*this will retrieve the data table*/
    function getDataTable() {        
        var siteId = activeTabId.split("_")[0];
        var MelisCmsPageScriptExceptionTable = null;

        if ($("#"+siteId+"MelisCmsPageScriptEditorScriptExceptionsTable").length > 0) {      
            MelisCmsPageScriptExceptionTable = $("#"+siteId+"MelisCmsPageScriptEditorScriptExceptionsTable").DataTable();             
        }

        return MelisCmsPageScriptExceptionTable;        
    }

    //used in tool site edition script tab to set the site id used in getting the script exceptions of the site
    window.initSiteId = function (data) {   
        var siteId = activeTabId.split("_")[0];

        if (!isNaN(siteId)) {   
            data.siteId = siteId;    
        }       
    };
});