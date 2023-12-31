<?php

/**
 * Plugin Name: Toolkit for WordPress
 * Description: Chances are this plugin should be a Composer package. It doesn't do anything on it's own. See README.md file for details.
 * Version:     1.0.0
 * Author:      Frederic Barry
 * Author URI:  https://fredericbarry.com/
 */

namespace FredericBarry\WordPress\Toolkit;

if (!defined("ABSPATH")) {
    exit();
}

require_once "includes/admin/class-dashboard-widget.php";
require_once "includes/common/class-register-post-type.php";
require_once "includes/common/class-register-taxonomy.php";
require_once "includes/theme/class-term-title.php";
