<?php
namespace AGTI\RdStation\Infrastructure\Service\API;


class Event extends BaseService
{
  
    protected $em;

    public function __construct($em)
    {
        $this->em=$em;
    }

    public function getApiEndpoint()
    {
        return "platform/events";
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