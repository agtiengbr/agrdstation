<?php
namespace AGTI\RdStation\Application\Service;

use AGTI\RdStation\Infrastructure\Service\API\GetContact;
use AGTI\RdStation\Infrastructure\Service\API\UpdateContact;

use AGTI\RdStation\Entity\AgrdstationContacts;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use Doctrine\ORM\EntityManagerInterface;
use AGTI\RdStation\Application\Service\CreateContact;

class UpdateTags
{
  
    private $token;
    private $em;
    private $updateContact;
    private $createContact;
    
    public function __construct(
    GetValidToken $token,
    EntityManagerInterface $em,
    UpdateContact $updateContact,
    GetContact $getContact,
    CreateContact $createContact
     )
    {
        $this->token = $token;
        $this->em = $em;
        $this->updateContact = $updateContact;
        $this->getContact = $getContact;
        $this->createContact = $createContact;
    }

    public function exec($product,$customer)
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
            $contact = $this->createContact->exec($customer);
        }
        $this->getContact->setApiEndpoint('uuid',$contact->getUuid());
        $request=$this->getContact->exec($this->token->exec());
        try {

            if($request->getHttpCode() != 200){
                throw new \Exception("Request Error-UpdateTags");
            }

            $response=json_decode($request->getResponse());

            $lang=\Configuration::get("PS_LANG_DEFAULT");
            $productName=$product->name[$lang];
            $tags=$response->tags;
            $exist=in_array($productName,$tags);

            if(!$exist){
                $tags[]=strtolower($productName);
                $request=$this->updateContact->setApiEndpoint('uuid',$contact->getUuid());
                $request=$this->updateContact->exec(['tags'=>$tags],$this->token->exec());
            }

        } catch (\Exception $e) {
            \Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
        }
    }
}