<div class="wrap">
    <h1>Horoscope Plugin Settings</h1>
    <form method="post" action="options.php">
        <?php
            settings_fields('horoscope_options_group');
            do_settings_sections('horoscope-plugin');
        ?>
        <label for="horoscope_override_today">Override Today's Horoscope</label>
        <input type="text" id="horoscope_override_today" name="horoscope_overrides[today]" value="<?php echo esc_attr(get_option('horoscope_overrides')['today'] ?? ''); ?>">
        <?php submit_button(); ?>
    </form>
</div>
