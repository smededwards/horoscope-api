<?php

namespace HoroscopePlugin;

/**
 * Class Shortcode
 *
 * This class handles fetching, caching, and displaying horoscope data via a shortcode.
 * It also provides a form for overriding the default horoscope data.
 */
class Shortcode
{
    public const DAYS = ['yesterday', 'today', 'tomorrow']; // Define days of interest as a constant to avoid repetition.
    public const DEFAULT_SIGN = 'Gemini'; // Default sign to display if not specified in the shortcode.
    public const SIGNS = [
        'aries' => '♈',
        'taurus' => '♉',
        'gemini' => '♊',
        'cancer' => '♋',
        'leo' => '♌',
        'virgo' => '♍',
        'libra' => '♎',
        'scorpio' => '♏',
        'sagittarius' => '♐',
        'capricorn' => '♑',
        'aquarius' => '♒',
        'pisces' => '♓'
    ]; // Define all zodiac signs as a constant array

    private HoroscopeAPI $api;
    private Cache $cache;

    /**
     * Constructor to initialize the API and Cache objects, and register the shortcode.
     */
    public function __construct()
    {
        $this->api = new HoroscopeAPI();
        $this->cache = new Cache();

        add_shortcode('horoscope', [$this, 'renderShortcode']);
    }

    /**
     * Render the horoscope shortcode.
     *
     * @param array $atts Shortcode attributes. 'sign' for zodiac sign (default is 'Gemini'),
     *                    'date' is an optional date (default is today) in 'YYYY-MM-DD' format.
     * @return string HTML content for the horoscope.
     */
    public function renderShortcode(array $atts = []): string
    {
        // Set default attributes for the shortcode.
        $atts = shortcode_atts(
            [
                'sign' => '', // Default to an empty string if no sign is provided.
                'date' => ''  // Default to an empty string if no date is provided.
            ],
            $atts
        );

        $sign = !empty($atts['sign']) ? $atts['sign'] : get_option('horoscope_data')['selected_sign'] ?? self::DEFAULT_SIGN;         // Use the provided sign or the selected sign from options.
        $main_date = !empty($atts['date']) ? $atts['date'] : get_option('horoscope_data')['selected_date'] ?? date('Y-m-d'); // Use provided date or the selected date from options.

        // Validate the provided date format (YYYY-MM-DD) and use today if invalid.
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $main_date) || strtotime($main_date) === false) {
            $main_date = date('Y-m-d');
        }

        // Calculate yesterday, today, and tomorrow based on the main date provided by the user or defaulting to today.
        $dates = [
            'yesterday' => date('Y-m-d', strtotime($main_date . ' -1 day')),
            'today'     => $main_date,
            'tomorrow'  => date('Y-m-d', strtotime($main_date . ' +1 day'))
        ];

        // Generate a cache key based on the sign and main date for storing or retrieving cached horoscope data.
        $cache_key = 'horoscope_' . $sign . '_' . $main_date;
        $horoscope_data = $this->cache->getCache($cache_key);

        // If no data in cache, fetch it from the API.
        if (!$horoscope_data || empty($horoscope_data)) {
            $horoscope_data_response = $this->fetchHoroscopes($sign, $dates);
            $horoscope_data = $horoscope_data_response['data'];
            // Cache the fetched horoscope data using the main date.
            $this->cache->setCache($cache_key, $horoscope_data, DAY_IN_SECONDS);
        } else {
            error_log("Using cached horoscope data for sign: $sign on date: $main_date");
        }

        // Get overridden data from options in the admin panel.
        $overridden_data = get_option('horoscope_data', [
            'yesterday' => '',
            'today' => '',
            'tomorrow' => ''
        ]);

        // Override with custom data from admin if available.
        foreach (self::DAYS as $day) {
            $horoscope_data[$day] = !empty($overridden_data[$day]) ? $overridden_data[$day] : $horoscope_data[$day];
        }

        return $this->formatHoroscope($horoscope_data, $dates, $atts);
    }

    /**
     * Fetch horoscope data from the API for the specified dates.
     *
     * @param string $sign The zodiac sign for which to fetch horoscope data.
     * @param array $dates An array of dates corresponding to 'yesterday', 'today', and 'tomorrow' based on main_date.
     * @return array An array containing horoscope data and associated dates.
     */
    private function fetchHoroscopes(string $sign, array $dates): array
    {
        $data = [];
        // Retrieve data for each date calculated based on main_date.
        foreach ($dates as $day => $date) {
            $response = $this->api->getHoroscope($sign, $date);
            if (!isset($response['data']) || empty($response['data']['horoscope_data'])) {
                error_log("API response missing data for $sign on $date");
                $data[$day] = 'No data available';
            } else {
                $data[$day] = $response['data']['horoscope_data'];
            }
        }

        return ['data' => $data, 'dates' => $dates];
    }

    /**
     * Format horoscope data into HTML structure.
     *
     * @param array $horoscope_data Array of horoscope data for each day.
     * @param array $dates Array of dates corresponding to each day based on main_date.
     * @return string HTML formatted output for the horoscope.
     */
    private function formatHoroscope(array $horoscope_data, array $dates, array $atts = []): string
    {
        // Initialize the output with the default sign.
        $output = '<div class="horoscope">';
        $selected_sign = !empty($atts['sign']) ? $atts['sign'] : get_option('horoscope_data')['selected_sign'] ?? self::DEFAULT_SIGN;
        $output .= sprintf('<h2 class="horoscope__sign">%s %s</h2>', self::SIGNS[strtolower($selected_sign)], ucfirst($selected_sign));
        $output .= '<div class="horoscope__content">';
        $output .= $this->renderNavigation();

        // Render each day's horoscope content.
        foreach (self::DAYS as $day) {
            $output .= $this->renderDay($day, $horoscope_data[$day], $dates[$day]);
        }

        $output .= '</div></div>';

        return $output;
    }

    /**
     * Render the navigation buttons for selecting each day.
     *
     * @return string HTML content for the navigation buttons.
     */
    private function renderNavigation(): string
    {
        // Use a foreach loop to generate the navigation buttons for DAYS constant.
        $nav = '';
        foreach (self::DAYS as $day) {
            $activeClass = $day === 'today' ? ' horoscope__nav-link--active' : '';
            $nav .= sprintf(
                '<button class="horoscope__nav-link %s" data-day="%s">%s</button>',
                esc_attr($activeClass),
                esc_attr($day),
                ucfirst($day)
            );
        }

        return sprintf('<div class="horoscope__nav">%s</div>', $nav);
    }

    /**
     * Render the content for a specific day.
     *
     * @param string $day The day (e.g., 'yesterday', 'today', 'tomorrow').
     * @param string $content The horoscope content for the specified day.
     * @param string $date The date corresponding to the day.
     * @return string HTML content for a specific day's horoscope.
     */
    private function renderDay(string $day, string $content, string $date): string
    {
        // Determine if this day should have the active class.
        $activeClass = $day === 'today' ? ' horoscope__days--active' : '';

        return sprintf(
            '<div class="horoscope__days horoscope__%s%s" data-day="%s">
                <h3 class="horoscope__title">%s</h3>
                <h4 class="horoscope__date">(%s)</h4>
                <p class="horoscope__content">%s</p>
            </div>',
            esc_attr($day),
            $activeClass,
            esc_attr($day),
            ucfirst($day),
            date('F j, Y', strtotime($date)),
            esc_html($content)
        );
    }
}
