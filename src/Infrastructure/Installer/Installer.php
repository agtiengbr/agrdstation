<?php

namespace AGTI\RdStation\Infrastructure\Installer;


use AGTI\RdStation\Entity\Tab as EntityTab;
use AGTI\RdStation\Repository\Tab as RepositoryTab;
class Installer
{
    public static function install()
    {
        $parentTab = \Tab::getInstanceFromClassName('AdminParentModulesSf');

        // //aba principal
        $mainTab = new EntityTab;
        $mainTab
            ->setName('Rd Station')
            ->setIdParent($parentTab->id)
            ->setActive(1)
            ->setClassName('AdminRdStation')
            ->setModuleName('agrdstation');

        if (!RepositoryTab::exist($mainTab)) {
            $mainTab = RepositoryTab::add($mainTab);
        }

        $reqTab = new EntityTab;
        $reqTab
            ->setName('Requisições API')
            ->setIdParent($mainTab->getId())
            ->setActive(1)
            // AmeApiRequest
            ->setClassName('AdminRdstationRequest')
            ->setModuleName('agrdstation');

        if (!RepositoryTab::exist($reqTab)) {
            RepositoryTab::add($reqTab);
        }
    }
}
