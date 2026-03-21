<?php

namespace AGTI\RdStation\Infrastructure\Service\Utils;
// use AgClienteConfig;
use AGTI\RdStation\Entity\AgrdstationToken;
use AGTI\RdStation\Infrastructure\Service\API\ReauthToken;

use Doctrine\ORM\EntityManagerInterface;

class GetValidToken 
{

    private $doctrine;
    private $AgrdstationToken;
    private $reauthToken;

    public function __construct(
        EntityManagerInterface $doctrine,
        AgrdstationToken $AgrdstationToken,
        ReauthToken $reauthToken
    )
    {
        $this->doctrine = $doctrine;
        $this->AgrdstationToken = $AgrdstationToken;
        $this->reauthToken = $reauthToken;
    }

    public function exec()
    {
        $em = $this->doctrine;

        try {
            $repository = $em->getRepository(AgrdstationToken::class);
            $token = $repository->getValidToken();

            if(count($token) == 0){
                $lastToken=$repository->findOneBy([], ['dateAdd' => 'desc']);

                if(!isset($lastToken)){
                    return false;
                }
                
                $reauthToken = $this->reauthToken;
                $returnRefresh = $reauthToken->exec($lastToken->getRefreshToken());
                $response=json_decode($returnRefresh->getResponse());

                $AgrdstationToken = new $this->AgrdstationToken;

                $dateRefresh = new \DateTime();
                $dateRefresh->add(new \DateInterval('PT'.$response->expires_in.'S')); 

                $AgrdstationToken->setAccessToken($response->access_token);
                $AgrdstationToken->setExpiresIn($response->expires_in);
                $AgrdstationToken->setRefreshToken($response->refresh_token);
                $AgrdstationToken->setDateExpire($dateRefresh);
                $AgrdstationToken->setDateAdd();

                $em->persist($AgrdstationToken);
                $em->flush();

                return $AgrdstationToken->getAccessToken();
            }else{
                return $token[0]->getAccessToken();
            }

        } catch (Exception $e) {
            echo 'Exceção capturada: ',  $e->getMessage(), "\n";
        }
    }

}