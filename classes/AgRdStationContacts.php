<?php

class AgRdStationContacts extends AgObjectModel
{
    public static $definition = [
        'table'   => 'agrdstation_contacts',
        'primary' => 'id_agrdstation_contacts',
        'multilang' => false,
        'fields'  => [
            'id_agrdstation_contacts' => ['type' => self::TYPE_INT,'validate' => 'isInt'],
            'uuid' => ['type' => self::TYPE_STRING, 'db_type' => 'varchar(45)','required' => true],
            'customer_id' => ['type' => self::TYPE_INT, 'db_type' => 'int','required' => true],
            'last_opportunity' => ['type'=> self::TYPE_DATE, 'validate' => 'isDate', 'db_type'  => 'datetime'],
            'date_add' => ['type'=> self::TYPE_DATE, 'validate' => 'isDate', 'db_type'  => 'datetime'],
            'date_update' => ['type'=> self::TYPE_DATE, 'validate' => 'isDate', 'db_type'  => 'datetime'],
        ]
    ];


    public $id_agrdstation_contacts;
    public $uuid;
    public $customer_id;
    public $date_add;
    public $date_update;
    
}