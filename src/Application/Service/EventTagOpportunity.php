<?php
namespace AGTI\RdStation\Application\Service;

use AGTI\RdStation\Infrastructure\Service\API\Event;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use AGTI\RdStation\Entity\AgrdstationContacts;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use AGTI\RdStation\Application\Service\CreateContact;

class EventTagOpportunity 
{
  
    private $token;
    private $em;
    private $event;
    private $createContact;
    
    public function __construct(
    GetValidToken $token,
    EntityManagerInterface $em,
    Event $event,
    CreateContact $createContact
     )
    {
        $this->token = $token;
        $this->em = $em;
        $this->event = $event;
        $this->createContact = $createContact;
    }

    public function exec($customer)
    {
      
        $rep = $this->em->getRepository(AgrdstationContacts::class);
        if(isset($customer->id_customer)){
            $contact = $rep->findOneBy(['customerId' => $customer->id_customer]);
        }elseif(isset($customer->id)){
            $contact = $rep->findOneBy(['customerId' => $customer->id]);
        }else{
            return;
        }

        if(!$contact){
            $contact =$this->createContact->exec($customer);
        }
        $now=new DateTime();
        if($contact->getLastOpportunity() == NULL){
            $hoursInterval=24;

        }else{
            $interval=$contact->getLastOpportunity()->diff($now);
            $hours = (int)$interval->h;
            $hoursInterval = $hours + ($interval->days*24);
        }

        if((int)$hoursInterval >= 24){
            $bodyRequest=[
                'event_type'=>'OPPORTUNITY',
                'event_family'=>'CDP',
                'payload'=>[
                    'email'=>$customer->email,
                    'funnel_name'=>\Configuration::get('AGRDSTATION_FUNNEL_NAME'),
                ],
            ];
            try {
                $request = $this->event->exec($bodyRequest,$this->token->exec());
                if($request->getHttpCode() != 200){
                    throw new \Exception("Request Error-EventTagOportunity");
                }
                $contact->setLastOpportunity();
                $this->em->persist($contact);
                $this->em->flush();
            } catch (\Exception $e) {
                \Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
            }
        }
    }
}