<?php
namespace verbb\socialfeeds\web\assets\frontend;

use craft\web\AssetBundle;

class FeedAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/socialfeeds/web/assets/frontend/dist';

        $this->css = [
            'social-feeds.css',
        ];

        parent::init();
    }
}
