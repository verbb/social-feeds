<?php
namespace verbb\socialfeed\assetbundles;

use craft\web\AssetBundle;

class FeedMasonryAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialfeed/resources/dist";

        $this->css = [
            'css/social-feed-masonry.css',
        ];

        $this->js = [
            'js/social-feed-masonry.js',
        ];

        parent::init();
    }
}
