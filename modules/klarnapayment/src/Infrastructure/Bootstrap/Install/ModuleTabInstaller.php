<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Install;

use KlarnaPayment\Module\Infrastructure\Adapter\Language;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Adapter\Tab;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Exception\CouldNotInstallModule;
use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;
use KlarnaPayment\Module\Infrastructure\EntityManager\EntityManagerInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\ObjectModelUnitOfWork;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModuleTabInstaller implements InstallerInterface
{
    private $tab;
    private $language;
    private $moduleTabs;
    private $entityManager;
    private $module;

    public function __construct(
        ModuleFactory $moduleFactory,
        Tab $tab,
        Language $language,
        ModuleTabs $moduleTabs,
        EntityManagerInterface $entityManager
    ) {
        $this->tab = $tab;
        $this->language = $language;
        $this->moduleTabs = $moduleTabs;
        $this->entityManager = $entityManager;
        $this->module = $moduleFactory->getModule();
    }

    /**
     * @throws CouldNotInstallModule
     */
    public function init(): void
    {
        $tabs = $this->moduleTabs->getTabs();

        foreach ($tabs as $tab) {
            if ($this->tab->getIdFromClassName($tab['class_name'])) {
                continue;
            }

            $this->installTab(
                $tab['class_name'],
                $tab['parent_class_name'],
                $tab['name'],
                $tab['visible']
            );
        }
    }

    /**
     * @throws CouldNotInstallModule
     */
    private function installTab(string $className, string $parent, array $name, bool $visible): void
    {
        $idParent = $this->tab->getIdFromClassName($parent . '_MTR');

        if (!$idParent) {
            $idParent = $this->tab->getIdFromClassName($parent);
        }

        $moduleTab = $this->tab->initTab();
        $moduleTab->class_name = $className;
        $moduleTab->id_parent = $idParent;
        $moduleTab->module = $this->module->name;
        $moduleTab->active = $visible;

        $languages = $this->language->getAllLanguages();
        foreach ($languages as $language) {
            $moduleTab->name[$language['id_lang']] = isset($name[$language['iso_code']]) ? pSQL($name[$language['iso_code']]) : pSQL($name['en']);
        }

        try {
            $this->entityManager->persist($moduleTab, ObjectModelUnitOfWork::UNIT_OF_WORK_SAVE);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw CouldNotInstallModule::failedToInstallModuleTab($exception, $className);
        }
    }
}
