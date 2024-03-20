<?php

namespace fwai\Classes;

class Images {

    static function add_images($product_id, $variant, $images_keys) {
    
        $productImages = $variant;
    
        // Add images to the product gallery
        if (!empty($productImages)) {
            $galleryImages = array();
            $i = 0;
            foreach ($productImages as $image) {
    
                // Generate a unique file name
                $productName = $image['subtype']; 
                $url = $image['url']; 
                $fileName = basename($url);
    
                // Download the image and save it as a temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'image');
                $response = wp_remote_get($url, array('timeout' => 30)); // Adjust timeout as needed
    
                if (!is_wp_error($response) && $response['response']['code'] == 200) {
                    $imageData = wp_remote_retrieve_body($response);
                    file_put_contents($tempFile, $imageData);
    
                    // Get the image type
                    $imageType = exif_imagetype($tempFile);
                    if ($imageType) {
                        // Upload the image to the media library
                        $upload = wp_upload_bits($fileName, null, $imageData);
    
                        if (!$upload['error']) {
                            $attachment = array(
                                'post_mime_type' => $upload['type'],
                                'post_title' => sanitize_file_name($fileName),
                                'post_status' => 'inherit',
                                'width' => 100, // Desired image width
                                'height' => 100, // Desired image height
                            );
    
                            $attachment_id = wp_insert_attachment($attachment, $upload['file'], $product_id);
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
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
                    } else {
                        // Handle unsupported image type
                        // Optionally, you can log or handle this error
                    }
                } else {
                    // Handle download failure
                    // Optionally, you can log or handle this error
                }
    
                // Delete the temporary file
                unlink($tempFile);
            }
    
            // Set the product gallery images
            update_post_meta($product_id, '_product_image_gallery', implode(',', $galleryImages));
        }
    }
    
}



