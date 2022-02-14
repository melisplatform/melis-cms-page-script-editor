# Melis CMS Page Script Editor

This module allows users to add custom scripts and styles at the site and page levels.


## Getting started

These instructions will get you a copy of the project up and running on your machine.

### Prerequisites

The following modules need to be installed to run the Melis CMS Page Script Editor module:

- Melis core
- Melis engine
- Melis front
- Melis CMS

### Installing

Run the composer command:

```
composer require melisplatform/melis-cms-page-script-editor
```

### Database

Database model is accessible via the MySQL Workbench file:

```
/melis-cms-page-script-editor/install/sql/Model
```

Database will be installed through composer and its hooks.  
In case of problems, SQL files are located here:

```
/melis-cms-page-script-editor/install/sql
```

## Tools and elements provided

- Scripts tab in Melis CMS' Page System
- Scripts tab in Melis CMS' Tool Site
- Page Script Editor Service
- Listeners


### Scripts Tab in Melis CMS' Page System

- A 'Scripts' tab is added inside Melis CMS' page system where the user can add scripts to be inserted after the opening head tag, before the closing head tag, or before the closing body tag of the page when rendered
- The user may opt to exclude the site's scripts in which during the rendering process, only the page's defined scripts are included

### Scripts Tab in Melis CMS' Tool Site

- A 'Scripts' tab is added inside Melis CMS' Tool Site in which the scripts that are set here will be applied to all pages belonging to the site, except for the pages that exclude the site's scripts
- List of pages that exclude the site's scripts are displayed and the user has the option to remove the page from the exception list
- The user also has the ability to add a page to the exception list 

### Page Script Editor Service

```
Files: 
      - /melis-cms-page-script-editor/src/Service/MelisCmsPageScriptEditorService.php   
```

- MelisCmsPageScriptEditorService
    - This service's functions include the retrieval and adding of page or site's scripts.  
     
    ```
    //Get the service
    $pageScriptEditorService = $this->getServiceManager()->get("MelisCmsPageScriptEditorService");

    //Retrieve final scripts of the page
    $resultList = $pageScriptEditorService->getMixedScriptsPerPage($pageId);   
    ```
    - Common methods this service is used for are as follows:
        - Retrieving page scripts: getScriptsPerPage(...)
        - Retrieving site scripts : getScriptsPerSite(...)
        - Retrieving site exceptions: getScriptExceptions(...)
        - Saving page or site scripts : addScript(...)
        - Saving exception : addScriptException(...)
 

* For a more detailed information on the methods, please visit the file.

### Listeners

There are three listeners inside the module:
- MelisCmsPageScriptEditorSavePageListener 
- MelisCmsPageScriptEditorSaveSiteScriptListener
- MelisCmsPageScriptEditorScriptTagListener

```
Files: 
      - /melis-cms-page-script-editor/src/Listener/MelisCmsPageScriptEditorSavePageListener.php
      - /melis-cms-page-script-editor/src/Listener/MelisCmsPageScriptEditorSaveSiteScriptListener.php
      - /melis-cms-page-script-editor/src/Listener/MelisCmsPageScriptEditorScriptTagListener.php
```

- MelisCmsPageScriptEditorSavePageListener
    - This is triggered when the page in Melis Cms' Page Edition is saved or published and will automatically save the script data and the exception configuration defined for the given page

- MelisCmsPageScriptEditorSaveSiteScriptListener
    - This is triggered when the site is saved and will automatically save the script data and the exception configuration defined for the given site

- MelisCmsPageScriptEditorScriptTagListener
    - This is triggered when the page is rendered     
    - The scripts for the page will be inserted to its defined destination(after the opening head tag, before the closing head tag or before the closing body tag)
    - If the page exluded the site's scripts, only the page's scripts will be inserted, else, the scripts will be the combination of the site and page's scripts where the site's scripts will always come first during the rendering process
       
* For a more detailed information on the listeners, please visit the files.


## Authors

- **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-cms-page-script-editor/contributors) who participated in this project.

## License

This project is licensed under the Melis Technology premium versions end user license agreement (EULA) - see the [LICENSE.md](LICENSE.md) file for details

