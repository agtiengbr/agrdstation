<?php

namespace AGTI\RdStation\Infrastructure\Serializer;

use AGTI\RdStation\Entity\Tab as EntityTab;

class Tab
{
    /** @return EntityTab */
    public static function createEntityFromPsTab(\Tab $tab)
    {
        $ret = new EntityTab;

        $ret
            ->setId($tab->id)
            ->setName(array_values($tab->name)[0])
            ->setIdParent($tab->id_parent)
            ->setActive($tab->active)
            ->setClassName($tab->class_name)
            ->setModuleName($tab->module);

        return $ret;
    }
}
