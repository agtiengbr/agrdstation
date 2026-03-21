<?php
namespace AGTI\RdStation\Application\Service;

use AGTI\RdStation\Infrastructure\Service\API\AddContact;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use AGTI\RdStation\Entity\AgrdstationContacts;
use Doctrine\ORM\EntityManagerInterface;
use AGTI\RdStation\Infrastructure\Service\API\GetContact;

class CreateContact 
{
  
    private $token;
    private $addContact;
    private $contact;
    private $em;

    public function __construct(
    GetValidToken $token,
    AddContact $addContact,
    AgrdstationContacts $contact,
    EntityManagerInterface $em,
    GetContact $getContact
     )
    {
        $this->token = $token;
        $this->addContact = $addContact;
        $this->contact = $contact;
        $this->em = $em;
        $this->getContact = $getContact;

    }

    public function exec($newCustomer)
    {

        $rep = $this->em->getRepository(agrdstationContacts::class);
        if(isset($newCustomer->id_customer)){
            $id=$newCustomer->id_customer;
        }else{
            $id=$newCustomer->id;
        }

        $contact = $rep->findOneBy(['customerId' => $id]);

        if(!$contact){
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
            try {

                $request=$this->addContact->exec($bodyRequest,$this->token->exec());

                $response = json_decode($request->getResponse());

                if($request->getHttpCode() != 200){

                    if($response->errors->error_type == "EMAIL_ALREADY_IN_USE"){
                        //user already exist

                        $this->getContact->setApiEndpoint('email',$newCustomer->email);
                        $contact=json_decode($this->getContact->exec($this->token->exec())->getResponse());

                        $this->contact->setUuid($contact->uuid);

                        if(isset($newCustomer->id_customer)){
                            $this->contact->setCustomerId($newCustomer->id_customer);
                        }else{
                            $this->contact->setCustomerId($newCustomer->id);
                        }
    
                        $this->contact->setDateAdd();
                        $this->contact->setDateUpdate();

                        $this->em->persist($this->contact);
                        $this->em->flush();

                        return $this->contact;
                    }

                    throw new \Exception("Request Error-CreateContact");

                }else{
                    $this->contact->setUuid($response->uuid);

                    if(isset($newCustomer->id_customer)){
                        $this->contact->setCustomerId($newCustomer->id_customer);
                    }else{
                        $this->contact->setCustomerId($newCustomer->id);
                    }

                    $this->contact->setDateAdd();
                    $this->contact->setDateUpdate();
                }
            

                $this->em->persist($this->contact);
                $this->em->flush();
                return $this->contact;

            } catch (\Exception $e) {
                \Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
            }
        }
    }
}