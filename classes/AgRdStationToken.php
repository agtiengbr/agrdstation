<?php

class AgRdStationToken extends AgObjectModel
{
    public static $definition = [
        'table'   => 'agrdstation_token',
        'primary' => 'id_agrdstation_token',
        'multilang' => false,
        'fields'  => [
            'id_agrdstation_token' => ['type' => self::TYPE_INT,'validate' => 'isInt'],
            'access_token' => ['type' => self::TYPE_STRING, 'db_type' => 'varchar(700)','required' => true],
            'expires_in' => ['type' => self::TYPE_INT, 'db_type' => 'int','required' => true],
            'refresh_token' => ['type' => self::TYPE_STRING, 'db_type' => 'varchar(50)','required' => true],
            'date_add' => ['type'=> self::TYPE_DATE, 'validate' => 'isDate', 'db_type'  => 'datetime'],
            'date_expire' => ['type'=> self::TYPE_DATE, 'validate' => 'isDate', 'db_type'  => 'datetime'],
        ]
    ];


    public $id_agrdstation_token;
    public $access_token;
    public $expires_in;
    public $refresh_token;
    public $date_add;
    public $date_expire;
}