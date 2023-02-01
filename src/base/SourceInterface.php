<?php
namespace verbb\socialfeeds\base;

use craft\base\SavableComponentInterface;

interface SourceInterface extends SavableComponentInterface
{
    // Public Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string;
    public function fetchPosts(): ?array;

}
