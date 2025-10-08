<?php
/**
 * Plugin Name: WP Upload Cleaner
 * Description: Scan the uploads directory for orphaned files not referenced by any attachment. Preview results, delete safely, or run via WP-CLI.
 * Version: 1.0.0
 * Author: Best Website
 * Author URI: https://bestwebsite.com/
 * License: GPL-2.0+
 * Text Domain: wp-upload-cleaner
 */
if (!defined('ABSPATH')) exit;
define('WPUC_VERSION', '1.0.0');
define('WPUC_PATH', plugin_dir_path(__FILE__));
require_once WPUC_PATH . 'includes/class-wpuc-core.php';
require_once WPUC_PATH . 'includes/class-wpuc-admin.php';
if (defined('WP_CLI') && WP_CLI) require_once WPUC_PATH . 'includes/class-wpuc-cli.php';
