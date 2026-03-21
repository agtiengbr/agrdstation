<?php
namespace AGTI\RdStation\Application\Service;


use AGTI\RdStation\Infrastructure\Service\API\Event;
use AGTI\RdStation\Infrastructure\Service\Utils\GetValidToken;
use Doctrine\ORM\EntityManagerInterface;

class EventCartAddItens 
{
  
 
    public function exec($order,$bash)
    {

        $customer=$order->getCustomer();

        foreach ($order->getCartProducts() as $product) {
            $bodyRequest=[
                'event_type'=>'ORDER_PLACED_ITEM',
                'event_family'=>'CDP',
                'payload'=>[
                    'name'=>$customer->firstname .' '.$customer->lastname,
                    'email'=>$customer->email,
                    'cf_order_id'=>(String)$order->id,
                    'cf_order_product_id'=>$product['id_product'],
                    'cf_order_product_sku'=>$product['ean13'],
                    "legal_bases"=> [
                        [
                          "category"=>"communications",
                          "type"=>"consent",
                          "status"=>$customer->newsletter ? 'granted':'declined'
                        ]
                    ]
                ],
            ];
            $bash[]=$bodyRequest;
        }
        return $bash;
    }
}