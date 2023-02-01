<?php
namespace verbb\socialfeeds\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use verbb\base\assetbundles\CpAsset as VerbbCpAsset;

class SocialFeedsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialfeeds/resources/dist";

        $this->depends = [
            VerbbCpAsset::class,
            CpAsset::class,
        ];

        $this->css = [
            'css/social-feeds-cp.css',
        ];

        $this->js = [
            'js/social-feeds-cp.js',
        ];

        parent::init();
    }
}
