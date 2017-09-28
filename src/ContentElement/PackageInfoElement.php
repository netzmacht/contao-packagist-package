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

use Contao\Config;
use Contao\ContentElement;
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
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function compile()
    {
        $this->Template->package = $this->getPackageInfo();
        $this->Template->fields  = StringUtil::deserialize($this->packagist_fields, true);
        $this->Template->labels  = $GLOBALS['TL_LANG']['packagist'];
    }

    /**
     * Get the packagist information.
     *
     * @return array
     */
    private function getPackageInfo():? array
    {
        if (!$this->packagist_package) {
            return null;
        }

        $cache = $this->getCache();

        if ($cache->contains($this->packagist_package)) {
            return $cache->fetch($this->packagist_package);
        }

        $container = $this->getContainer();
        $url       = $container->getParameter('netzmacht.contao_packagist_package.package_json_url');
        $json      = file_get_contents(sprintf($url, $this->packagist_package));
        $info      = json_decode($json, true);
        $info      = array_merge(
            $info['package'],
            $this->getLatestVersion($info['package']['versions'])
        );

        $info['downloads_total'] = $info['downloads']['total'] ?? 0;
        $info['date']            = $this->parseTime($info['time']);

        $lifeTime = $container->getParameter('netzmacht.contao_packagist_package.cache.lifetime');
        $cache->save($this->packagist_package, $info, $lifeTime);

        return $info;
    }

    /**
     * Get the cache.
     *
     * @return Cache
     */
    private function getCache()
    {
        return $this->getContainer()->get('netzmacht.contao_packagist_package.cache');
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

        return Date::parse(Config::get('dateFormat'), $time->getTimestamp());
    }
}
