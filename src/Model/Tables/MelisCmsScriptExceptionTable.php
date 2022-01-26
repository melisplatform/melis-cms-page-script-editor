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
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Db\Sql\Predicate\PredicateSet;
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

   

}
