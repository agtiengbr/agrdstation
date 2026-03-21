<?php

namespace AGTI\RdStation\Infrastructure\Service\API;

use AGTI\RdStation\Entity\AgrdstationApiRequest;

abstract class BaseService
{
    const API_BASE = "https://api.rd.services/";


    abstract function getApiEndpoint();

    /**
     * @return AgrdstationApiRequest
     */
    protected function send($method, $querystring=[], $bodyData=null, $extraHeaders = [])
    {
        $url = $this->getBaseUrl() . $this->getApiEndpoint();
        
        if (count($querystring)) {
            $url .= "?" . implode('&', $querystring);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);


        $headers = [
            'Accept: application/json'
        ];


        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $bodyData);
        } elseif ($method === 'PATCH') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $bodyData);
        } elseif ($method === 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($bodyData));
        }

        $headers = array_merge($headers, $extraHeaders);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        
        $body = curl_exec($curl);
        
        $http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $request = new AgrdstationApiRequest;

        $request->setEndpoint($url);
        $request->setHeaders($headers);
        $request->setMethod($method);
        $request->setBody($bodyData);
        $request->setHttpCode($http);
        $request->setResponse($body);
        $request->setDateAdd(new \DateTime);
        $request->setTimeSpent(curl_getinfo($curl, CURLINFO_TOTAL_TIME));

       

        return $request;
    }

    /**
     * Get the value of token
     */ 
    private function getToken()
    {
        return $this->token;
    }

    private function getBaseUrl()
    {
        return self::API_BASE;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */ 
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}