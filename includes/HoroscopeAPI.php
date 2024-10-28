<?php
namespace HoroscopePlugin;

/**
 * Class HoroscopeAPI
 *
 * Handles fetching daily horoscope data from a specified API for a given zodiac sign and date.
 */
class HoroscopeAPI
{
    /**
     * Base URL for the horoscope API
     *
     * @var string
     */
    private const BASE_URL = 'https://horoscope-app-api.vercel.app/api/v1/get-horoscope/daily?';

    /**
     * Constructs the API URL with query parameters.
     *
     * @param string $sign The zodiac sign for the horoscope, defaults to 'Gemini'.
     * @param string $day The specified day in 'YYYY-MM-DD' format.
     * @return string The constructed API URL.
     */
    private function buildApiUrl(string $sign, string $day): string
    {
        return self::BASE_URL . 'sign=' . urlencode($sign) . '&day=' . urlencode($day);
    }

    /**
     * Fetches horoscope data for a specified sign and day.
     *
     * @param string $sign The zodiac sign for the horoscope.
     * @param string $day The specified day in 'YYYY-MM-DD' format.
     * @return array An associative array of the horoscope data or an error message.
     */
    public function getHoroscope(string $sign, string $day): array
    {
        // Build the API URL with parameters
        $url = $this->buildApiUrl($sign, $day);

        // Make the API request and handle any WP errors
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            error_log('Horoscope API request failed: ' . $response->get_error_message());
            return ['error' => 'API request failed.'];
        }

        // Retrieve and decode the API response
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Failed to decode JSON response: ' . json_last_error_msg());
            return ['error' => 'Failed to decode response.'];
        }

        // Check for the status and success fields in the response
        if (isset($data['status']) && $data['status'] === 200 && isset($data['success']) && $data['success'] === true) {
            return $data; // Return the full data if the checks pass
        } else {
            error_log('Invalid API response: ' . print_r($data, true));
            return ['error' => 'Invalid API response.'];
        }
    }
}
