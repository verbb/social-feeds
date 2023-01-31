<?php
namespace verbb\socialfeed\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use verbb\base\assetbundles\CpAsset as VerbbCpAsset;

class SocialFeedAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialfeed/resources/dist";

        $this->depends = [
            VerbbCpAsset::class,
            CpAsset::class,
        ];

        $this->css = [
            'css/social-feed-cp.css',
        ];

        $this->js = [
            'js/social-feed-cp.js',
        ];

        parent::init();
    }
}
