<?php

namespace HoroscopePlugin;

use InvalidArgumentException;

/**
 * Class Cache
 *
 * Manages transient caching for storing temporary data using WordPress's
 * `set_transient`, `get_transient`, and `delete_transient` functions.
 */
class Cache
{
    private const DEFAULT_EXPIRATION = 86400; // Default expiration time in seconds (1 day).

    /**
     * Generates a unique cache key based on sign and date.
     *
     * @param string $sign The zodiac sign.
     * @param string $date The date.
     * @return string A unique cache key.
     */
    public function generateCacheKey(string $sign, string $date): string
    {
        return 'horoscope_' . strtolower($sign) . '_' . $date;
    }

    /**
     * Sets a cache entry with a specified key and expiration time.
     *
     * @param string $key Unique identifier for the cache.
     * @param mixed $data Data to be cached.
     * @param int $expiration Expiration time in seconds (defaults to 1 day).
     *
     * @throws InvalidArgumentException If $key is empty or $expiration is less than 0.
     */
    public function setCache(string $key, mixed $data, int $expiration = self::DEFAULT_EXPIRATION): void
    {
        $this->validateCacheKey($key);
        $this->validateExpiration($expiration);

        if (set_transient($key, $data, $expiration)) {
            error_log("Cache set successfully for key: $key with expiration: $expiration seconds. Data: " . print_r($data, true));
        } else {
            error_log("Failed to set cache for key: $key");
        }
    }

    /**
     * Retrieves cached data for a specified key.
     *
     * @param string $key Unique identifier for the cache.
     * @return mixed Cached data or false if not found.
     *
     * @throws InvalidArgumentException If $key is empty.
     */
    public function getCache(string $key): mixed
    {
        $this->validateCacheKey($key);
        $data = get_transient($key);

        if ($data === false) {
            error_log("Cache miss for key: $key");
        } else {
            error_log("Cache hit for key: $key. Data: " . print_r($data, true));
        }

        return $data;
    }

    /**
     * Deletes cached data for a specified key.
     *
     * @param string $key Unique identifier for the cache.
     *
     * @throws InvalidArgumentException If $key is empty.
     */
    public function deleteCache(string $key): void
    {
        $this->validateCacheKey($key);

        if (delete_transient($key)) {
            error_log("Cache deleted for key: $key");
        } else {
            error_log("Failed to delete cache for key: $key, it may not exist.");
        }
    }

    /**
     * Validates that a cache key is not empty.
     *
     * @param string $key The cache key to validate.
     *
     * @throws InvalidArgumentException If $key is empty.
     */
    private function validateCacheKey(string $key): void
    {
        if (empty($key)) {
            error_log('Cache key validation failed: Key is empty.');
            throw new InvalidArgumentException('Cache key cannot be empty.');
        }
    }

    /**
     * Validates that the expiration time is not negative.
     *
     * @param int $expiration Expiration time in seconds.
     *
     * @throws InvalidArgumentException If $expiration is less than 0.
     */
    private function validateExpiration(int $expiration): void
    {
        if ($expiration < 0) {
            error_log('Invalid expiration time: ' . $expiration);
            throw new InvalidArgumentException('Expiration time must be 0 or greater.');
        }
    }
}
