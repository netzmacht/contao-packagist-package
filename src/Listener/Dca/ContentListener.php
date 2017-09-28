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

namespace Netzmacht\Contao\PackagistPackage\Listener\Dca;

/**
 * Class ContentListener.
 *
 * @package Netzmacht\Contao\PackagistPackage\Listener\Dca
 */
class ContentListener
{
    /**
     * Field options.
     *
     * @var array
     */
    private $fieldOptions;

    /**
     * ContentListener constructor.
     *
     * @param array $fieldOptions Field options.
     */
    public function __construct(array $fieldOptions)
    {
        $this->fieldOptions = $fieldOptions;
    }

    /**
     * Get the field options.
     *
     * @return array
     */
    public function getFieldOptions(): array
    {
        return $this->fieldOptions;
    }
}
