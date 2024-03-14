<?php
/*
Plugin Name: Fand WooCommerce API Importer
Description: Import products from JSON data using WooCommerce API.
Version: 1.0
Author: Fan-develop
*/
namespace fwai;
defined( 'ABSPATH' ) || exit;

// Include the WooCommerce functions
include_once(ABSPATH.'wp-admin/includes/plugin.php');
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    // WooCommerce plugin is not active
    exit('WooCommerce plugin is not active.');
}

// on utilise l'autoload PSR4 de composer
require __DIR__ . '/vendor/autoload.php';

/* Chemin vers ce fichier dans une constante
* => sera utile pour les hook d'activation et désactivation
*/
define('FWAI_MAIN_FILE', __FILE__);
define( 'FWAI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FWAI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/* If this file is called directly, abort.*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

//require_once FWAI_PLUGIN_DIR . 'inc/scripts.php';
require_once FWAI_PLUGIN_DIR . 'plugin.php';

new FWAISettingsPage;