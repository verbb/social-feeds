<?php
namespace verbb\socialfeeds\web\assets\cp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SocialFeedsPluginKitAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/socialfeeds/web/assets/cp/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'social-feeds-plugin-kit.js',
        ];

        parent::init();
    }
}
