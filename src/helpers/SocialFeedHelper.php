<?php
namespace verbb\socialfeeds\helpers;

use craft\helpers\StringHelper;

class SocialFeedsHelper
{
    // Static Methods
    // =========================================================================

    public static function splitString(?string $value): array
    {
        if (!$value) {
            return [];
        }

        // Stripe out any special extra chatacters
        $value = str_replace(['@', '#', ':'], '', $value);

        // Treat `;` like a comma
        $value = str_replace(':', ',', $value);

        // Split by comma
        $values = preg_split('/[\s,]+/', $value);

        $parsedValues = [];

        foreach (array_filter($values) as $v) {
            // Prevent non-utf characters sneaking in.
            $v = StringHelper::convertToUtf8($v);

            // Also check for control characters, which aren't included above
            $v = preg_replace('/[^\PC\s]/u', '', $v);

            $parsedValues[] = trim($v);
        }

        return array_filter($parsedValues);
    }
}
