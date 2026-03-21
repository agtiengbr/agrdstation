<?php
namespace AGTI\RdStation\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;


class AgrdstationToken extends EntityRepository
{
    public function getValidToken()
    {
        $date = new DateTime();

        $dateLast = new DateTime();
        $tosub = new \DateInterval('PT24H');
        $dateLast->sub($tosub);

        $data = $this->createQueryBuilder('t')
            ->where('t.dateExpire > :dateCurrent')
            ->setParameter('dateCurrent', $date->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();
        
        return $data;
    }

    public function getLastToken()
    {
        $date = new DateTime();

        $dateLast = new DateTime();
        $tosub = new \DateInterval('PT24H');
        $dateLast->sub($tosub);

        $data = $this->createQueryBuilder('t')
            ->where('t.dateExpire > :dateCurrent')
            ->setParameter('dateCurrent', $date->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();
        
        return $data;
    }
}