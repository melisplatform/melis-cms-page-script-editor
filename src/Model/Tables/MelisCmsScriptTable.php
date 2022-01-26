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

class MelisCmsScriptTable extends MelisGenericTable
{
    /**
     * Model table
     */
    const TABLE = 'melis_cms_scripts';

    /**
     * Table primary key
     */
    const PRIMARY_KEY = 'mcs_id';

    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }

   

}
