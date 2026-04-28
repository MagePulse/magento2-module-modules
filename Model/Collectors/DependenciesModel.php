<?php
/**
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

use MagePulse\Collector\Model\Collectors\CollectorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;

class DependenciesModel implements CollectorInterface
{
    /** Package types already reported with full metadata by ModulesModel */
    private const EXCLUDED_TYPES = ['magento2-module', 'magento2-theme'];

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Json $serializer
    ) {}

    public function getData(): array
    {
        return $this->getDependencies();
    }

    private function getDependencies(): array
    {
        try {
            $rootDir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
            $content = $rootDir->readFile('composer.lock');
            $lock = $this->serializer->unserialize($content);
        } catch (\Exception $e) {
            return [];
        }

        $dependencies = [];
        foreach ($lock['packages'] ?? [] as $package) {
            if (in_array($package['type'] ?? '', self::EXCLUDED_TYPES, true)) {
                continue;
            }
            $dependencies[] = [
                'name'    => $package['name'],
                'version' => $package['version'],
                'type'    => $package['type'] ?? 'library',
            ];
        }

        return $dependencies;
    }
}
