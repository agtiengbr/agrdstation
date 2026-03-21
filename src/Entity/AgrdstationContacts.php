<?php
namespace AGTI\RdStation\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
// 
/**
 * @ORM\Entity()
 * @ORM\Table()
 * 
 */
class AgrdstationContacts
{
   /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idAgrdstationContacts;

    /**
     * @ORM\Column(type="string")
     */
    private $uuid;

    /**
     * @ORM\Column(type="integer")
     */
    private $customerId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastOpportunity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

     /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpdate;

    /************* GETTERS AND SETTERS *********************/


    /**
     * Get the value of idAgrdstationContact
     */ 
    public function getIdAgrdstationContacts()
    {
        return $this->idAgrdstationContacts;
    }

    /**
     * Set the value of idAgrdstationContacts
     *
     * @return  self
     */ 
    public function setIdAgrdstationContacts($idAgrdstationContacts)
    {
        $this->idAgrdstationContacts = $idAgrdstationContacts;

        return $this;
    }

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @return  self
     */ 
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get the value of customerId
     */ 
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set the value of customerId
     *
     * @return  self
     */ 
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

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
        $this->dateAdd = new DateTime();

        return $this;
    }

    /**
     * Get the value of dateUpdate
     */ 
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set the value of dateUpdate
     *
     * @return  self
     */ 
    public function setDateUpdate()
    {
        $this->dateUpdate = new DateTime();
        return $this;
    }

    /**
     * Get the value of lastOpportunity
     */ 
    public function getLastOpportunity()
    {
        return $this->lastOpportunity;
    }

    /**
     * Set the value of lastOpportunity
     *
     * @return  self
     */ 
    public function setLastOpportunity()
    {
        $this->lastOpportunity = new DateTime();
        return $this;
    }

       /**
     * Set the value of lastOpportunity
     *
     * @return  self
     */ 
    public function resetLastOpportunity()
    {
        $datetime = new DateTime();
        $datetime->modify('-1 day');
        $this->lastOpportunity = $datetime;

        return $this;
    }
}