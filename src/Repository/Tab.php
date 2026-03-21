<?php

namespace AGTI\RdStation\Repository;

use AGTI\RdStation\Entity\Tab as EntityTab;
use AGTI\RdStation\Infrastructure\Serializer\Tab as SerializerTab;

class Tab
{
    public static function exist(EntityTab $tab)
    {
        $tabId = \Tab::getIdFromClassName($tab->getClassName());
        if ($tabId) {
            return true;
        } else {
            return false;
        }
    }
    public static function remove(EntityTab $tab)
    {
        foreach (\Language::getLanguages(true) as $lang) {
            $language = $lang;
        }
        $tabId = \Tab::getIdFromClassName($tab->getClassName());
        $tabModel = \Tab::getTab($language, $tabId);

        $tabModel->delete();

        return SerializerTab::createEntityFromPsTab($tabModel);
    }
    public static function add(EntityTab $tab)
    {
        $tabModel             = new \Tab();
        $tabModel->module     = $tab->getModuleName();
        $tabModel->active     = $tab->getActive();
        $tabModel->class_name = $tab->getClassName();
        $tabModel->id_parent  = $tab->getIdParent();

        foreach (\Language::getLanguages(true) as $lang) {
            $tabModel->name[$lang['id_lang']] = $tab->getName();
        }

        $tabModel->add();

        return SerializerTab::createEntityFromPsTab($tabModel);
    }
}
