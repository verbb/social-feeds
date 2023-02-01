<?php
namespace verbb\socialfeeds\assetbundles;

use craft\web\AssetBundle;

class FeedMasonryAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialfeeds/resources/dist";

        $this->css = [
            'css/social-feeds-masonry.css',
        ];

        $this->js = [
            'js/social-feeds-masonry.js',
        ];

        parent::init();
    }
}
