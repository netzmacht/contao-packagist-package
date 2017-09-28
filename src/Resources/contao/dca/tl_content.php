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

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['packagist_package'] = [
    'type'      => ['type', 'headline'],
    'config'    => ['packagist_package', 'packagist_fields'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
    'invisible' => ['invisible', 'start', 'stop'],
];

/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['packagist_package'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['packagist_package'],
    'exclude'   => true,
    'inputType' => 'text',
    'reference' => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "varchar(128) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['packagist_fields'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['bs_grid_name'],
    'exclude'   => true,
    'inputType' => 'checkboxWizard',
    'options_callback' => [
        'netzmacht.contao_packagist_package.listeners.dca.content',
        'getFieldOptions'
    ],
    'reference' => &$GLOBALS['TL_LANG']['packagist'],
    'eval'      => [
        'tl_class' => 'clr long',
    ],
    'sql'       => 'tinyblob NULL',
];
