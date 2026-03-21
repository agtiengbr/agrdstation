<?php
use AGTI\RdStation\Entity\AgrdstationToken;

class AgRdStationCronModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $updateContact=$this->get('agti.rdstation.application.update_contact');
        $createContact=$this->get('agti.rdstation.application.create_contact');

        try {
            $sql = new DbQuery;
            $sql->from('agrdstation_contacts', 'rdc')
                ->rightJoin("customer", "c", "c.id_customer=rdc.customer_id")
                ->where("((c.date_upd-rdc.date_update > 1000000) AND (rdc.date_update < c.date_upd)) OR (rdc.date_add IS NULL)");

        $contacts = \Db::getInstance()->executeS($sql);

        foreach ($contacts as $contact) {

            if($contact['id_agrdstation_contacts']){
                $updateContact->exec((object)$contact);

            }else{
                $createContact->exec((object)$contact);
            }
           
        }
        } catch (Exception $e) {
            echo 'Exceção capturada: ',  $e->getMessage(), "\n";
        }
       
        
    }

    
}
