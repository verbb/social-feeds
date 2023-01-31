<?php
namespace verbb\socialfeed\assetbundles;

use craft\web\AssetBundle;

class FeedAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialfeed/resources/dist";

        $this->css = [
            'css/social-feed.css',
        ];

        parent::init();
    }
}
