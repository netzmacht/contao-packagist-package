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

namespace Netzmacht\Contao\PackagistPackage\ContentElement;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\Date;
use Contao\StringUtil;
use Doctrine\Common\Cache\Cache;

/**
 * Package info element.
 *
 * @property string packagist_package Packagist package.
 * @property mixed  packagist_fields
 */
class PackageInfoElement extends ContentElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'ce_packagist_package';

    /**
     * Packagist url pattern.
     *
     * @var string
     */
    private $packagistUrl;

    /**
     * Cache life time.
     *
     * @var Cache life time.
     */
    private $cacheLifeTime;

    /**
     * Cache.
     *
     * @var \Doctrine\Common\Cache\FilesystemCache|object
     */
    private $cache;

    /**
     * PackageInfoElement constructor.
     *
     * @param ContentModel $objElement Content element.
     * @param string       $strColumn  Column name.
     */
    public function __construct(ContentModel $objElement, $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);

        $container = static::getContainer();

        $this->packagistUrl  = $container->getParameter('netzmacht.contao_packagist_package.package_json_url');
        $this->cacheLifeTime = $container->getParameter('netzmacht.contao_packagist_package.cache.lifetime');
        $this->cache         = $container->get('netzmacht.contao_packagist_package.cache');
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function compile()
    {
        $this->Template->packages = $this->compilePackages();
        $this->Template->fields   = StringUtil::deserialize($this->packagist_fields, true);
        $this->Template->labels   = $GLOBALS['TL_LANG']['packagist'];
    }

    /**
     * Compile packages.
     *
     * @return array
     */
    private function compilePackages(): array
    {
        $packages = StringUtil::trimsplit("\n", $this->packagist_package);
        $compiled = [];

        foreach ($packages as $package) {
            if ($package) {
                $compiled[$package] = $this->getPackageInfo($package);
            }
        }

        return $compiled;
    }

    /**
     * Get the packagist information.
     *
     * @param string $package Package name.
     *
     * @return array
     */
    private function getPackageInfo(string $package): array
    {
        if ($this->cache->contains($package)) {
            return $this->cache->fetch($package);
        }

        $json      = file_get_contents(sprintf($this->packagistUrl, $package));
        $info      = json_decode($json, true);
        $info      = array_merge(
            $info['package'],
            $this->getLatestVersion($info['package']['versions'])
        );

        $info['downloads_total'] = $info['downloads']['total'] ?? 0;
        $info['date']            = $this->parseTime($info['time']);

        $this->cache->save($package, $info, $this->cacheLifeTime);

        return $info;
    }

    /**
     * Get the latest version.
     *
     * @param array $versions Versions.
     *
     * @return array
     */
    private function getLatestVersion(array $versions): array
    {
        $latest = [];

        foreach ($versions as $version => $data) {
            if (strncmp($version, 'dev-', 4) === 0) {
                if (empty($latest)) {
                    $latest = $data;
                }
            } elseif (!empty($latest) || strncmp($latest['version'], 'dev-', 4) === 0) {
                $latest = $data;

                break;
            }
        }

        return $latest;
    }

    /**
     * Parse the time.
     *
     * @param string $time Given time in atom format.
     *
     * @return string
     */
    private function parseTime(string $time): string
    {
        $time = new \DateTime($time);

        return Date::parse(Date::getNumericDateFormat(), $time->getTimestamp());
    }
}
