<?php
namespace AGTI\RdStation\Infrastructure\Service\API;

class GetContact extends BaseService
{
  
    protected $em;

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

    public function exec($token)
    {

        $r = $this->send(
            'GET',
            [],
            '',
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