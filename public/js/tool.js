$(function(){
    // Tool scripts
    $body = $("body");
   
    /**
     * Exclude Site Script checkbox
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

    //when 'delete exception' button is clicked, remove the page exception entry from the table
    $body.on('click', '.btnDeletePageException', function (event) {   
        event.preventDefault();   
        var scriptDataTable = getDataTable();
   
        scriptDataTable.row($(this).parents('tr')).remove();    
        saveSiteScriptException(scriptDataTable, 'delete');                
    }); 

    //add page to exception list
    $body.on('click', '#add_tool_site_script_exception_btn', function (event) {   
        event.preventDefault();
        var scriptDataTable = getDataTable();

        var pageId = $("#tool_site_exception_page_id").val();
        var isExisting = 0;

        //check here if the page is already in the exception list
        var currentExceptionList = scriptDataTable.data().toArray();
        $.each(currentExceptionList , function(index, val) {              
            if (pageId == val['mcse_page_id']) {
                isExisting = 1;
                return false
            }
        });

        if (isExisting == 1) {        
            melisHelper.melisKoNotification(translations.tr_meliscmspagescripteditor_tool_site_exception_title, translations.tr_meliscmspagescripteditor_add_exception_duplicate_error);  
        } else {
           
            //check if the page id entered belongs to the current site
            var siteId = activeTabId.split("_")[0];
            $.ajax({
                type: 'POST',
                url: '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorToolSiteEdition/checkPage',
                data: {siteId:siteId, pageId:pageId},          
                dataType: "json",
                encode: true,
            }).done(function (data) {

                if (data.pageOk) {  

                    //update data table
                    scriptDataTable.row.add( {
                        "mcse_id": 0,
                        "mcse_page_id": pageId,
                        "page_name": "",            
                    } );

                    //calls function to update exception list in DB
                    saveSiteScriptException(scriptDataTable, 'add'); 
                } else {
                    melisHelper.melisKoNotification(data.textTitle, data.textMessage);                
                }

            }).fail(function () {                    
                alert(translations.tr_meliscore_error_message);
            });         
        }            
    });

    /*this will add or delete the pages that exclude the site script*/
    function saveSiteScriptException(scriptDataTable, operation) {       
        var tableRowDataArr = scriptDataTable.data().toArray();
        var pages = [];

        //add to array the list of page exceptions
        $.each(tableRowDataArr , function(index, val) {   
            pages.push(val['mcse_page_id']);
        });

        //set page exception to hidden field
        var implodedPage = pages.join(',');
        var siteId = activeTabId.split("_")[0];
        $("#"+siteId+"_id_meliscms_tool_sites_script_content").find('input[name="tool_site_mcse_page_id"]').val(implodedPage);  
      
        //get page_name first of the selected page id from the site map
        $.ajax({
            type: 'POST',
            url: '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorToolSiteEdition/saveSiteScriptException',
            data: {siteId:siteId, operation:operation, tool_site_mcse_page_id:implodedPage},          
            dataType: "json",
            encode: true,
        }).done(function (data) {

            if (data.success) {
                melisHelper.melisOkNotification( data.textTitle, data.textMessage );
                melisHelper.zoneReload("id_MelisCmsSlider_list_content_table", "MelisCmsSlider_list_content_table", {});

                //reload table
                scriptDataTable.ajax.url('/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorToolSiteEdition/getScriptExceptions').load();

                //empty input field
                $("#"+activeTabId.split("_")[0]+"_id_meliscms_tool_sites_script_content").find("#tool_site_exception_page_id").val("");                 

            } else {
                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);                
            }

        }).fail(function () {                    
            alert(translations.tr_meliscore_error_message);
        });  
    }

    /*this will retrieve the data table*/
    function getDataTable() {        
        var siteId = activeTabId.split("_")[0];
        var MelisCmsPageScriptExceptionTable = $("#"+siteId+"MelisCmsPageScriptEditorScriptExceptionsTable").DataTable(); 
        return MelisCmsPageScriptExceptionTable;
    }

    //used in tool site edition scrip tab to set the site id used in getting the script exceptions of the site
    window.initSiteId = function (data) {   
        var siteId = activeTabId.split("_")[0];

        if (!isNaN(siteId)) {   
            data.siteId = siteId;    
        }       
    };
});