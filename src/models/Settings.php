<?php
namespace verbb\socialfeeds\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Social Feeds';
    public bool $hasCpSection = true;
    public bool $enableCache = true;
    public mixed $cacheDuration = 'PT6H';
    public int $postsLimit = 50;
    public ?string $redirectUri = null;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['pluginName'], 'trim'];
        $rules[] = [['pluginName'], 'required'];
        $rules[] = [['pluginName'], 'string', 'max' => 52];

        return $rules;
    }

}
