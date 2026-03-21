<?php
namespace AGTI\RdStation\Application\Service;


use AGTI\RdStation\Infrastructure\Service\API\EventBatch;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use Doctrine\ORM\EntityManagerInterface;

class EventBash 
{
  
 
    private $token;
    private $em;
    private $event;

    public function __construct(
        GetValidToken $token,
        EntityManagerInterface $em,
        EventBatch $event
         )
        {
            $this->token = $token;
            $this->em = $em;
            $this->event = $event;
        }

    public function exec($itens)
    {
        try {
            $request = $this->event->exec($itens,$this->token->exec());    

            if($request->getHttpCode() != 200){
                throw new \Exception("Request Error-EventBash");
            }

            } catch (\Exception $e) {
                \Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
            }
    }
}