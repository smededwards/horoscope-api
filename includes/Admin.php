<?php

namespace HoroscopePlugin;

/**
 * Class Admin
 *
 * This class is responsible for the admin settings page.
 */
class Admin
{
    private const OPTION_GROUP = 'horoscope_settings_group';
    private const OPTION_NAME = 'horoscope_data';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * Add admin menu
     *
     * @return void
     */
    public function addAdminMenu(): void
    {
        add_menu_page(
            'Horoscope Settings',           // Page title
            'Horoscope',                    // Menu title
            'manage_options',               // Capability
            'horoscope-settings',           // Slug
            [$this, 'settingsPage'],        // Callback function
            'dashicons-welcome-write-blog', // Icon
            20                              // Position in the menu
        );
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function registerSettings(): void
    {
        register_setting(self::OPTION_GROUP, self::OPTION_NAME, [$this, 'validateSettings']);
    }

    /**
     * Settings page
     *
     * @return void
     */
    public function settingsPage(): void
    {
        // Get the saved horoscope data, defaulting 'selected_date' to today if not set
        $horoscope_data = get_option(self::OPTION_NAME, [
            'yesterday' => '',
            'today' => '',
            'tomorrow' => '',
            'selected_date' => date('Y-m-d') // Default to today's date
        ]);
        
        ?>
<div class="wrap">
    <h1>Horoscope Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields(self::OPTION_GROUP); ?>
        <h2>Edit Horoscope Data</h2>
        <table class="form-table">
            <tr>
                <th scope="row">Date</th>
                <td>
                    <input type="date"
                        name="<?php echo esc_attr(self::OPTION_NAME . '[selected_date]'); ?>"
                        value="<?php echo esc_attr($horoscope_data['selected_date']); ?>">
                </td>
            </tr>
            <?php foreach (['yesterday', 'today', 'tomorrow'] as $day): ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html(ucfirst($day)); ?></th>
                <td>
                    <textarea class="large-text"
                        name="<?php echo esc_attr(self::OPTION_NAME . "[$day]"); ?>"
                        rows="5"><?php echo esc_textarea($horoscope_data[$day]); ?></textarea>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php
    }

    /**
     * Validate settings before saving
     *
     * @param array $input
     * @return array
     */
    public function validateSettings(array $input): array
    {
        // Simple validation: sanitize each entry
        foreach (['yesterday', 'today', 'tomorrow'] as $day) {
            if (isset($input[$day])) {
                $input[$day] = sanitize_textarea_field($input[$day]);
            }
        }

        // Sanitize the selected date
        if (isset($input['selected_date'])) {
            $input['selected_date'] = sanitize_text_field($input['selected_date']);
        }

        return $input;
    }
}
?>