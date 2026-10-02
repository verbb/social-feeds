<?php
namespace verbb\socialfeeds\web\assets\frontend;

use craft\web\AssetBundle;

class FeedMasonryAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/socialfeeds/web/assets/frontend/dist';

        $this->css = [
            'social-feeds-masonry.css',
        ];

        $this->js = [
            'social-feeds-masonry.js',
        ];

        parent::init();
    }
}
