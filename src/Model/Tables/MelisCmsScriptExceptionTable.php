<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageScriptEditor\Model\Tables;

use MelisCore\Model\Tables\MelisGenericTable;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\TableGateway;

class MelisCmsScriptExceptionTable extends MelisGenericTable
{
    /**
     * Model table
     */
    const TABLE = 'melis_cms_scripts_exceptions';

    /**
     * Table primary key
     */
    const PRIMARY_KEY = 'mcse_id';

    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }


     /**
     * Retrieves the exceptions
     * @param array $siteId  
     * @param string|null $orderColumn  
     * @param string|null $order     
     * @return mixed   
     */
    public function getScriptExceptions($siteId = array(), $orderColumn = null, $order = null) 
    {        
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(                
            'mcse_id',          
            'mcse_page_id',
            'page_name' => new \Laminas\Db\Sql\Expression('COALESCE(page_published.page_name, page_saved.page_name)')
        ));
       
        $select->join(array('page_saved' => 'melis_cms_page_saved'), 'page_saved.page_id = melis_cms_scripts_exceptions.mcse_page_id', array(), $select::JOIN_LEFT);

         $select->join(array('page_published' => 'melis_cms_page_published'), 'page_published.page_id = melis_cms_scripts_exceptions.mcse_page_id', array(), $select::JOIN_LEFT);  

        if ($siteId) {   
            $select->where(array('mcse_site_id' => $siteId));  
        } 
    
        if (!is_null($orderColumn)) {            
            $select->order($orderColumn.' '.$order);   
        } else {
            $select->order('mcse_id DESC');
        }

        $select->group('mcse_id');
                   
        $resultSet = $this->tableGateway->selectWith($select);        
        return $resultSet;
    }
}
