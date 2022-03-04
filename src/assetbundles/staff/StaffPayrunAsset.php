<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\assetbundles\staff;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\assets\vue\VueAsset;

/**
 * StaffAsset AssetBundle
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0
 */
class StaffPayrunAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@percipiolondon/staff/web/assets/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
            VueAsset::class,
            StaffAsset::class,
        ];

        parent::init();
    }
}
