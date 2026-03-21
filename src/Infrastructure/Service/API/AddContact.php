<?php
namespace AGTI\RdStation\Infrastructure\Service\API;

// use AgClienteConfig;

class AddContact extends BaseService
{
  
    protected $em;

    public function __construct($em)
    {
        $this->em=$em;
    }

    public function getApiEndpoint()
    {
        return "platform/contacts";
    }

    public function exec($bodyRequest,$token)
    {

        $r = $this->send(
            'POST',
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