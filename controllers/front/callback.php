<?php
use AGTI\RdStation\Entity\AgrdstationToken;

class AgRdStationCallbackModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();
        $em = $this->get('doctrine.orm.entity_manager');
       
        \Configuration::updateValue('AGRDSTATION_CODE', $_GET['code']);
        $service = $this->get('agti.rdstation.infrastructure.api.oauth');
        $request = $service->exec();

        $response=json_decode($request->getResponse());


        $date = new DateTime();
        $date->add(new DateInterval('PT'.$response->expires_in.'S')); 

        $token = new AgrdstationToken();
        

        $token->setAccessToken($response->access_token);
        $token->setExpiresIn($response->expires_in);
        $token->setRefreshToken($response->refresh_token);
        $token->setDateExpire($date);
        $token->setDateAdd();
      
        try {
            // $em->persist($request);
            $em->persist($token);
            $em->flush();
            echo "<h2>Permissão concedida, por favor volte a página de configurações</h2>";
        } catch (Exception $e) {
            echo 'Exceção capturada: ',  $e->getMessage(), "\n";
        }
        die();
    }

    
}
