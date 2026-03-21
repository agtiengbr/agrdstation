<?php

namespace AGTI\RdStation\Infrastructure\Service\API;
// use AgClienteConfig;
use AGTI\RdStation\Infrastructure\Service\API\BaseService;

class Oauth extends BaseService
{
    protected $em;

    public function __construct($em)
    {
        $this->em=$em;
    }
    public function getApiEndpoint()
    {
        return "auth/token";
    }

    public function exec()
    {
        $r = $this->send(
            'POST',
            [],
            [
                "client_id" => \Configuration::get('AGRDSTATION_CLIENT_ID'),
                "client_secret" => \Configuration::get('AGRDSTATION_CLIENT_SECRET'),
                "code" => \Configuration::get('AGRDSTATION_CODE')
            ]
        );

        $this->em->persist($r);
        $this->em->flush();

        return $r;
    }
}