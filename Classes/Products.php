<?php

namespace fwai\Classes;
use fwai\Classes\Categorys;
use fwai\Classes\Colors;
use \WC_Product_Variable;
use \WC_Product_Variation;

class Products {
// Check if WooCommerce is active
    static function add_product($product)
    {

        // Vérifier si le produit a un nom
        if (!array_key_exists('product_name', $product)) {
            // Gérer l'erreur ici, par exemple, enregistrer un message d'erreur ou lever une exception
            return; // Quitter la fonction sans ajouter le produit
        }

        // Access the product data
        $productName = $product['product_name'];
        $productDescription = isset($product['long_description']) ? $product['long_description'] : '';


        // insert product
        $product_id = wp_insert_post( array(
            'post_type'   => 'product',
            'post_title'  => $productName,
            'post_content' => $productDescription,
            'post_status' => 'publish'
        ) );

        //insert les informations produits
        $productCode = $product['master_code'];
        $productShortDescription = $product['short_description'];
        if (array_key_exists('length', $product)) {
            $productLength = $product['length'];
            update_post_meta($product_id, '_length', $productLength);
        }
        if (array_key_exists('width', $product)) {
            $productWidth = $product['width'];
            update_post_meta($product_id, '_width', $productWidth);
        }
        if (array_key_exists('height', $product)) {
            $productHeight = $product['height'];
            update_post_meta($product_id, '_height', $productHeight);
        }
        if (array_key_exists('height', $product)) {
            $productGrossWeight = $product['gross_weight'];
            update_post_meta($product_id, '_gross_weight', $productGrossWeight);
        }
        $regularPrice= '8,34';

        update_post_meta($product_id, '_short_description', $productShortDescription);
        update_post_meta($product_id, '_sku', $productCode);
        update_post_meta($product_id, '_regular_price', $regularPrice);

    }


    static public function update_product($product,$product_id)
    {

        // Access the product data
        $productName = $product['product_name'];
        if (array_key_exists('long_description', $product)) {
            $productDescription = $product['long_description'];
        } else {
            // Gérer l'erreur ici, par exemple, attribuer une valeur par défaut à $productName
            $productDescription = '';
        }


        //insert les informations produits
        $productShortDescription = $product['short_description'];
        if (array_key_exists('length', $product)) {
            $productLength = $product['length'];
            update_post_meta($product_id, '_length', $productLength);
        }
        if (array_key_exists('width', $product)) {
            $productWidth = $product['width'];
            update_post_meta($product_id, '_width', $productWidth);
        }
        if (array_key_exists('height', $product)) {
            $productHeight = $product['height'];
            update_post_meta($product_id, '_height', $productHeight);
        }
        if (array_key_exists('height', $product)) {
            $productGrossWeight = $product['gross_weight'];
            update_post_meta($product_id, '_gross_weight', $productGrossWeight);
        }
        $Price= '8,34';

        // Définir le type de produit sur "variable"
        wp_set_object_terms($product_id, 'variable', 'product_type');
        update_post_meta($product_id, '_short_description', $productShortDescription);
        update_post_meta($product_id, '_regular_price', $Price);
        update_post_meta($product_id, '_price', $Price);

        // Access the variations data
        $variant = $product['variants'][0];
        // colors($product_id,$product['variants']);
    }
    static function delete_product_by_name($product_id)
    {
   
            // Supprimer le produit en utilisant son ID
            wp_delete_post($product_id, true); // true pour forcer la suppression définitive
            
    }

    static function colors($product_id,$variant){
    
        // Trouver les clés contenant le mot "couleur"
        $color_keys = array_filter(array_keys($variant[0]), function($key) {
            return strpos($key, 'color_group') === 0;
        });
        //var_dump($color_keys);

        // Vérifier si des couleurs ont été trouvées
        if (count($color_keys) > 0) {
            Colors::add_colors($product_id,$variant);
        } 
        else 
        {
            var_dump("Aucune couleurs trouvée dans le tableau.");
        }
    }
}



