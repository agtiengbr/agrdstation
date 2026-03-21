<?php
namespace AGTI\RdStation\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="AGTI\RdStation\Repository\AgrdstationToken")
 * 
 */
class AgrdstationToken
{
   /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idAgrdstationToken;

    /**
     * @ORM\Column(type="string")
     */
    private $accessToken;

    /**
     * @ORM\Column(type="integer")
     */
    private $expiresIn;

    /**
     * @ORM\Column(type="string")
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

     /**
     * @ORM\Column(type="datetime")
     */
    private $dateExpire;

    /************* GETTERS AND SETTERS *********************/
    /**
     * Get the value of idAgrdstationToken
     */ 
    public function getIdAgrdstationToken()
    {
        return $this->idAgrdstationToken;
    }


    /**
     * Get the value of accessToken
     */ 
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the value of accessToken
     *
     * @return  self
     */ 
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get the value of expiresIn
     */ 
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Set the value of expiresIn
     *
     * @return  self
     */ 
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Get the value of refreshToken
     */ 
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set the value of refreshToken
     *
     * @return  self
     */ 
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }


    /**
     * Get the value of dateAdd
     */ 
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set the value of dateAdd
     *
     * @return  self
     */ 
    public function setDateAdd()
    {
        $date=new DateTime();
        $this->dateAdd = $date;

        return $this;
    }


    /**
     * Get the value of dateExpire
     */ 
    public function getDateExpire()
    {
        return $this->dateExpire;
    }

    /**
     * Set the value of dateExpire
     *
     * @return  self
     */ 
    public function setDateExpire($dateExpire)
    {
        $this->dateExpire = $dateExpire;

        return $this;
    }
}