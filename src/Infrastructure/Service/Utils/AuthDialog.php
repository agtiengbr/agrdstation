<?php

namespace AGTI\RdStation\Infrastructure\Service\Utils;
// use AgClienteConfig;

class AuthDialog 
{
    private $endpoint;

    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function exec($client_id)
    {
        $context = \Context::getContext();
        $redirect_uri = $context->link->getModuleLink('agrdstation', 'callback', array(), true);;

        return  $this->endpoint."auth/dialog?client_id={$client_id}&redirect_uri={$redirect_uri}";
    }

}