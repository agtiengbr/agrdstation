<?php
namespace AGTI\RdStation\Application\Service;

use AGTI\RdStation\Infrastructure\Service\API\UpdateContact AS UpdateContactAPI;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use AGTI\RdStation\Entity\AgrdstationContacts;
use Doctrine\ORM\EntityManagerInterface;

class UpdateContact 
{
  
    private $token;
    private $updateContact;
    private $em;

    public function __construct(
    GetValidToken $token,
    UpdateContactAPI $updateContact,
    EntityManagerInterface $em
     )
    {
        $this->token = $token;
        $this->updateContact = $updateContact;
        $this->em = $em;
    }

    public function exec($newCustomer)
    {
        $bodyRequest=[
            "name"=>$newCustomer->firstname .' '.$newCustomer->lastname,
            "email"=>$newCustomer->email,
            "legal_bases"=> [
                [
                    "category"=>"communications",
                    "type"=>"consent",
                    "status"=>$newCustomer->newsletter ? 'granted':'declined'
                ]
           ],
        ];
        $rep = $this->em->getRepository(AgrdstationContacts::class);


        if(isset($newCustomer->id_customer)){
            $contact = $rep->findOneBy(['customerId' => $newCustomer->id_customer]);
        }else{
            $contact = $rep->findOneBy(['customerId' => $newCustomer->id]);
        }

        $request=$this->updateContact->setApiEndpoint('uuid',$contact->getUuid());
        
        $request=$this->updateContact->exec($bodyRequest,$this->token->exec());

        try {
            if($request->getHttpCode() != 200){
                throw new \Exception("Request Error-UpdateContact");
    
            }else{
                $contact->setDateUpdate();
            }
            $this->em->persist($contact);
            $this->em->flush();
        } catch (Exception $e) {
            \Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
        }
    }
        
}