<?php

namespace fwai\Classes;
use fwai\Classes\Readfile;

class FWAI_TAXONOMY {

    static public function init()
    {
        // Create a new variation
        $newVariation = array(
            'post_title' => $productName . ' ' . $variation['color_code'],
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'product_variation',
            'post_parent' => $productId
        );

        // Insert the variation into the database
        $variationId = wp_insert_post($newVariation);

    }
    
    static function create_product_brands_taxonomy($taxonomy_name) {


    if (!taxonomy_exists($taxonomy_name)) {
        $labels = array(
            'name' => _x( 'Marques', 'Taxonomy General Name', 'textdomain' ),
            'singular_name' => _x( 'Marque', 'Taxonomy Singular Name', 'textdomain' ),
            'menu_name' => __( 'Marques', 'textdomain' ),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'marque' ),
        );

        register_taxonomy( $taxonomy_name, 'product', $args );
    }
}
}