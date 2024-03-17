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
        // Access the product data
        $productName = $product['product_name'];
        $productDescription = $product['long_description'];

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
        $productLength = $product['length'];
        $productWidth = $product['width'];
        $productHeight = $product['height'];
        $productGrossWeight = $product['gross_weight'];
        $regularPrice= '8,34';

        update_post_meta($product_id, '_short_description', $productShortDescription);
        update_post_meta($product_id, '_sku', $productCode);
        update_post_meta($product_id, '_length', $productLength);
        update_post_meta($product_id, '_width', $productWidth);
        update_post_meta($product_id, '_height', $productHeight);
        update_post_meta($product_id, '_gross_weight', $productGrossWeight);
        update_post_meta($product_id, '_regular_price', $regularPrice);


        // gestion de la galerie d'image du produit
        $variant = $product['variants'][0]['digital_assets'];
        galery_image($variant,$product_id);
    }


    static public function update_product($product,$product_id)
    {

        // Access the product data
        $productCode = $product['master_code'];
        $productName = $product['product_name'];
        $productDescription = $product['long_description'];

        //insert les informations produits
        $productCode = $product['master_code'];
        $productShortDescription = $product['short_description'];
        $productLength = $product['length'];
        $productWidth = $product['width'];
        $productHeight = $product['height'];
        $productGrossWeight = $product['gross_weight'];
        $Price= '8,34';

        // Définir le type de produit sur "variable"
        wp_set_object_terms($product_id, 'variable', 'product_type');
        update_post_meta($product_id, '_short_description', $productShortDescription);
        update_post_meta($product_id, '_sku', $productCode);
        update_post_meta($product_id, '_length', $productLength);
        update_post_meta($product_id, '_width', $productWidth);
        update_post_meta($product_id, '_height', $productHeight);
        update_post_meta($product_id, '_gross_weight', $productGrossWeight);
        update_post_meta($product_id, '_regular_price', $Price);
        update_post_meta($product_id, '_price', $Price);

        // Access the variations data
        $variant = $product['variants'][0];
        categorys($product_id,$variant);
        colors($product_id,$product['variants']);
    }
}

function categorys($product_id,$variant){
    // Trouver les clés contenant le mot "category"
    $category_keys = array_filter(array_keys($variant), function($key) {
        return strpos($key, 'category') === 0;
    });

    // Vérifier si des catégories ont été trouvées
    if (count($category_keys) > 0) {

        Categorys::add_categorys($product_id,$variant,$category_keys);
    } 
    else 
    {
        //var_dump("Aucune catégorie trouvée dans le tableau.");
    }
}

function colors($product_id,$variant){
   
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


function galery_image($variants, $product_id) {
    
    $productImages = $variants;
    var_dump($productImages[0]['subtype']);


    // Add images to the product gallery
    if (!empty($productImages)) {
        $galleryImages = array();
        $i = 0;
        foreach ($productImages as $image) {

            // Generate a unique file name
            $productName = $image['subtype']; 
            $fileName = $productName . '.jpg';
    
            $imageType = $image['subtype'];
            $upload_dir = wp_upload_dir();
            $thumbnail_dir = $upload_dir['url'] . '/' .$fileName;

            // Check if the image already exists in the media library
            $existing_attachment_id = attachment_url_to_postid($thumbnail_dir);

            if (!$existing_attachment_id) {
                $imageURL = $image['url'];
                // Download the image and add it to the media library
                $response = wp_remote_get($imageURL);

                if (!is_wp_error($response) && $response['response']['code'] == 200) {
                    $imageData = wp_remote_retrieve_body($response);

                    // Upload the image to the media library
                    $upload = wp_upload_bits($fileName, null, $imageData);

                    // Check if the upload was successful
                    if (!$upload['error']) {
                        // Create attachment post
                        $attachment = array(
                            'post_mime_type' => $upload['type'],
                            'post_title' => sanitize_file_name($fileName),
                            'post_status' => 'inherit',
                            'width' => 100, // Largeur souhaitée de l'image
                            'height' => 100, // Hauteur souhaitée de l'image
                        );

                        $attachment_id = wp_insert_attachment($attachment, $upload['file'], $product_id);
                        wp_get_attachment_image_src($attachment_id, 'full');
                        // Vous devrez également générer les miniatures associées
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $thumbnail_dir);
                        wp_update_attachment_metadata($attachment_id, $attachment_data);
                        if ($i === 0) {
                            // Set the downloaded image as the featured image of the product
                            set_post_thumbnail($product_id, $attachment_id);
                            $i = 1;
                        } else {
                            // Add the attachment to the product gallery
                            $galleryImages[] = $attachment_id;
                        }
                    }
                }
            } else {
                if ($i === 0) {
                    // Set the downloaded image as the featured image of the product
                    set_post_thumbnail($product_id, $existing_attachment_id);
                    $i = 1;
                } else {
                    // Add the existing attachment to the product gallery
                    $galleryImages[] = $existing_attachment_id;
                }

            }
        }

        // Set the product gallery images
        update_post_meta($product_id, '_product_image_gallery', implode(',', $galleryImages));
    }
}





