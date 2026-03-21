<?php
namespace AGTI\RdStation\Infrastructure\Service\API;

// use AgClienteConfig;

class UpdateContact extends BaseService
{
  
    protected $em;
    protected $endpoint;

    public function __construct($em)
    {
        $this->em=$em;
    }

    public function setApiEndpoint($identifier,$value)
    {
        $this->endpoint="platform/contacts/".$identifier.':'.$value;
    }

    public function getApiEndpoint()
    {
        return $this->endpoint;
    }

    public function exec($bodyRequest,$token)
    {

        $r = $this->send(
            'PATCH',
            [],
            json_encode($bodyRequest),
            [
                'Content-Type:application/json',
                'Authorization:Bearer '.$token
            ]
        );

        $this->em->persist($r);
        $this->em->flush();

        return $r;
    }
}