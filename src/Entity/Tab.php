<?php

namespace AGTI\RdStation\Entity;

class Tab
{
    private $id;
    private $name;
    private $idParent;
    private $active;
    private $className;
    private $moduleName;
    private $position;


    /**
     * Get the value of position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @return  self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of moduleName
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set the value of moduleName
     *
     * @return  self
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Get the value of className
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set the value of className
     *
     * @return  self
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get the value of active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of idParent
     */
    public function getIdParent()
    {
        return $this->idParent;
    }

    /**
     * Set the value of idParent
     *
     * @return  self
     */
    public function setIdParent($idParent)
    {
        $this->idParent = $idParent;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
