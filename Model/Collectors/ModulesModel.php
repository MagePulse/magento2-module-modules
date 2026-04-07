<?php
/*
 * MagePulse
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagePulse Proprietary EULA
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * https://magepulse.com/legal/magento-license/
 *
 * @category    MagePulse
 * @package     MagePulse_Modules
 * @copyright   Copyright (c) MagePulse (https://magepulse.com)
 * @license     https://magepulse.com/legal/magento-license/  MagePulse Proprietary EULA
 *
 */

declare(strict_types=1);

namespace MagePulse\Modules\Model\Collectors;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Module\Manager;
use Magento\Framework\Module\ModuleListInterface;
use MagePulse\Collector\Model\Collectors\CollectorInterface;
use MagePulse\Modules\Model\ModuleMetaInfo;

class ModulesModel implements CollectorInterface
{
    private FullModuleList $fullModuleList;
    private ModuleListInterface $moduleList;
    private Manager $moduleManager;
    private ModuleMetaInfo $moduleMetaInfo;

    public function __construct(
        FullModuleList      $fullModuleList,
        ModuleListInterface $moduleList,
        Manager             $moduleManager,
        ModuleMetaInfo      $moduleMetaInfo
    ) {
        $this->fullModuleList = $fullModuleList;
        $this->moduleList = $moduleList;
        $this->moduleManager = $moduleManager;
        $this->moduleMetaInfo = $moduleMetaInfo;
    }

    public function getData(): array
    {
        return $this->getModules();
    }

    private function getModules(): array
    {
        $modules = [];
        foreach ($this->fullModuleList->getNames() as $moduleName) {
            $moduleMeta = $this->moduleMetaInfo->getModuleMeta($moduleName);
            $moduleMeta = is_array($moduleMeta) ? $moduleMeta : [];
            $modules[] = [
                'name'             => $moduleName,
                'module_version'   => $this->getVersion($moduleName),
                'composer_version' => $moduleMeta['version'] ?? 'N/A',
                'status'           => $this->getStatus($moduleName),
                'composer_name'    => $moduleMeta['name'] ?? 'N/A',
            ];
        }
        return $modules;
    }

    private function getVersion(string $moduleName): string
    {
        $module = $this->moduleList->getOne($moduleName);
        return $module['setup_version'] ?? 'N/A';
    }

    private function getStatus(string $moduleName): string
    {
        return $this->moduleManager->isEnabled($moduleName) ? 'enabled' : 'disabled';
    }
}
