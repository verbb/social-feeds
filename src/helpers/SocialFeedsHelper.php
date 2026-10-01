<?php
namespace verbb\socialfeeds\helpers;

use craft\helpers\StringHelper;

class SocialFeedsHelper
{
    // Constants
    // =========================================================================

    private const SENSITIVE_PROVIDER_KEYS = [
        'access_token',
        'appsecret_proof',
    ];


    // Static Methods
    // =========================================================================

    public static function scrubProviderSecrets(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_string($key) && self::_isSensitiveProviderKey($key)) {
                    unset($value[$key]);

                    continue;
                }

                $value[$key] = self::scrubProviderSecrets($item);
            }

            return $value;
        }

        if (is_string($value)) {
            return self::_scrubProviderUrl($value);
        }

        return $value;
    }

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


    // Private Methods
    // =========================================================================

    private static function _isSensitiveProviderKey(string $key): bool
    {
        return in_array(strtolower(rawurldecode($key)), self::SENSITIVE_PROVIDER_KEYS, true);
    }

    private static function _scrubProviderUrl(string $value): string
    {
        if (!preg_match('/^https?:\/\//i', $value) || filter_var($value, FILTER_VALIDATE_URL) === false) {
            return $value;
        }

        $queryPosition = strpos($value, '?');

        if ($queryPosition === false) {
            return $value;
        }

        $fragmentPosition = strpos($value, '#', $queryPosition);
        $queryLength = $fragmentPosition === false ? null : $fragmentPosition - $queryPosition - 1;
        $query = substr($value, $queryPosition + 1, $queryLength);
        $queryParts = explode('&', $query);
        $filteredQueryParts = [];

        foreach ($queryParts as $queryPart) {
            $key = explode('=', $queryPart, 2)[0];

            if (!self::_isSensitiveProviderKey($key)) {
                $filteredQueryParts[] = $queryPart;
            }
        }

        if ($queryParts === $filteredQueryParts) {
            return $value;
        }

        $url = substr($value, 0, $queryPosition);

        if ($filteredQueryParts !== []) {
            $url .= '?' . implode('&', $filteredQueryParts);
        }

        if ($fragmentPosition !== false) {
            $url .= substr($value, $fragmentPosition);
        }

        return $url;
    }
}
