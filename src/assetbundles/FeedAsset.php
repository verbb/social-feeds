<?php
namespace verbb\socialfeeds\assetbundles;

use craft\web\AssetBundle;

class FeedAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialfeeds/resources/dist";

        $this->css = [
            'css/social-feeds.css',
        ];

        parent::init();
    }
}
