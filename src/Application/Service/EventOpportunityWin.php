<?php
namespace AGTI\RdStation\Application\Service;

use AGTI\RdStation\Infrastructure\Service\API\Event;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use AGTI\RdStation\Entity\AgrdstationContacts;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use AGTI\RdStation\Application\Service\CreateContact;

class EventOpportunityWin 
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

    public function exec($order,$customer)
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

        $bodyRequest=[
            'event_type'=>'SALE',
            'event_family'=>'CDP',
            'payload'=>[
                'email'=>$customer->email,
                'funnel_name'=>\Configuration::get('AGRDSTATION_FUNNEL_NAME'),
                'value'=>(Float)$order->getTotalProductsWithTaxes()/1
            ],
        ];
        // return $bodyRequest;
        try {
            $request = $this->event->exec($bodyRequest,$this->token->exec());
            if($request->getHttpCode() != 200){
                throw new \Exception("Request Error-EventTagOportunity");
            }
            $contact->resetLastOpportunity();
            $this->em->persist($contact);
            $this->em->flush();
        } catch (\Exception $e) {
            \Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
        }
    }
}