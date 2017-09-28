<?php

/**
 * Contao Packagist Package.
 *
 * @package    contao-packagist-package
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\PackagistPackage\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Netzmacht\Contao\PackagistPackage\NetzmachtContaoPackagistPackageBundle;

/**
 * Contao Manager Plugin.
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(NetzmachtContaoPackagistPackageBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
        ];
    }
}
