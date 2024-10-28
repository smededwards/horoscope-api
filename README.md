# Horoscope API

This plugin integrates with the Horoscopes API to provide daily horoscope readings for one star sign (Gemini). Users can fetch horoscopes for yesterday, today, and tomorrow, with options for overriding the data in the admin area.

## Table of Contents

- [Horoscope API](#horoscope-api)
  - [Table of Contents](#table-of-contents)
  - [Features](#features)
  - [Installation](#installation)
  - [Usage](#usage)
  - [Admin Settings](#admin-settings)

## Features

- Fetch daily horoscopes for a specified star sign (default: Gemini).
- View horoscopes for yesterday, today, and tomorrow.
- Override horoscope data for each day via the admin settings.
- Cached responses using WordPress transients for improved performance.

## Installation

1. Download the plugin files.
2. Upload the `horoscope-api` folder to the `/wp-content/plugins/` directory of your WordPress installation.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Configure the settings as desired in the WordPress admin area.

## Usage

To display the horoscope in your posts or pages, use the shortcode:

```plaintext
[horoscope]
```

You can override the default star sign and date by specifying the `sign` and `date` attributes:

```plaintext
[horoscope sign="Aries" date="YYYY-MM-DD"]
```

## Admin Settings

After activating the plugin, navigate to Horoscope link in the WordPress admin area. Here, you can override the horoscope data for yesterday, today, and tomorrow for the selected star sign, by selecting the desired date and entering your own text in the provided text areas.
