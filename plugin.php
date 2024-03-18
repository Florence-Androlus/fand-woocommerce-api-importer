<?php

namespace fwai;

use fwai\Classes\fand_ppom;
use fwai\Classes\Api;
use fwai\Classes\Colors;
use fwai\Classes\Products;

class FWAISettingsPage {

    public function __construct() {

        add_action( 'init', [$this,'woocommerce_api_import_products'] );
		// Register the settings page.
	//	add_action( 'admin_menu', array( $this, 'register_settings' ) );
		// on ajoute nos URL custom
	//	add_action('init', [$this, 'registerCustomRewrites']);
    }

    public function woocommerce_api_import_products() {

      //  Api::json_api_test();
        fand_ppom::ppom_exist();
      //  die;
        // JSON data containing product information
        $file_path = ABSPATH . 'wp-content/plugins/fand-woocommerce-api-importer/produitunique.json';
        $json = file_get_contents($file_path);
        
        $data = json_decode($json, true);

        if (is_array($data)) {
            foreach ($data as $product) {
                // Access the product data
                $productName = $product['product_name'];

                // Check if the product already exists
                $existing_product = get_page_by_title($productName, OBJECT, 'product');

                if ($existing_product) {
                    $product_id=$existing_product->ID;
                    Products::update_product($product,$product_id);
                    continue; // Skip to the next product
                }

                else
                {
                    Products::add_product( $product);
                }
               // die;
            }
        }
    }

}


