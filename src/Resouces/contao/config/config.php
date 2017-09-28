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

use Netzmacht\Contao\PackagistPackage\ContentElement\PackageInfoElement;

$GLOBALS['TL_CTE']['packagist']['packagist_package'] = PackageInfoElement::class;
