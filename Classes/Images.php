<?php

namespace fwai\Classes;

class Images {

    static function add_update_images($product_id, $digital_assets) {
        // Inclure le fichier nécessaire
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $i = 0;

        foreach ($digital_assets as $image) {
            // Réinitialiser $galleryImages à chaque itération
            $galleryImages = [];
            // url de l'image Midocean
            $url = $image['url']; 
            // on recupere le nom du fichier
            $filename =basename($url);
            // on crée le chemin dans media
            $upload_dir = wp_upload_dir();
            //$thumbnail_dir = $upload_dir['url'] . '/' .$filename;
            //var_dump($thumbnail_dir);
            //var_dump($filename);

            // Obtenir tous les attachements de la bibliothèque de médias
            $attachments = get_posts(array(
                'post_type'      => 'attachment',
                'posts_per_page' => -1,
                'post_status'    => 'any',
            ));

            // Initialise une variable pour stocker l'ID de l'attachement correspondant
            $matching_attachment_id = false;

            // Parcours tous les attachements pour rechercher une correspondance
            foreach ($attachments as $attachment) {
                // Obtient le nom du fichier attaché
                $attached_file = basename(get_attached_file($attachment->ID));
                
                // Vérifie si le nom du fichier attaché correspond à ce que vous recherchez
                if ($attached_file === $filename) {
                    // Correspondance trouvée, enregistre l'ID de l'attachement
                    $matching_attachment_id = $attachment->ID;
                    break; // Sort de la boucle car nous avons trouvé une correspondance
                }
            }

            // Vérifie si une correspondance a été trouvée
            if (!$matching_attachment_id) {
                // Aucune correspondance trouvée dans la bibliothèque
               // echo "L'image n'existe pas dans la bibliothèque.";

                $attachment_id = self::add_image($url,$product_id);
                if ($i === 0) {
                    // Definie l'image comme l'image du produit
                    set_post_thumbnail($product_id, $attachment_id);
                    $i = 1;
                } else {
                    // Ajoute les autres image à la galerie du produit
                    $galleryImages[] = $attachment_id;
                }
            } 
            else {
                // L'image existe déjà dans la bibliothèque avec cet ID
                //echo "L'image existe déjà dans la bibliothèque avec l'ID : $matching_attachment_id";
               //var_dump('l\'image existe');
               self::update_image($matching_attachment_id,$product_id);
               if ($i === 0) {
                    // Set the downloaded image as the featured image of the product
                    set_post_thumbnail($product_id, $matching_attachment_id);
                    $i = 1;
                } else {
                    // Add the existing attachment to the product gallery
                    $galleryImages[] = $matching_attachment_id;
                }
            }   
        }
        // mets a jour la galerie d'images du produit
        update_post_meta($product_id, '_product_image_gallery', implode(',', $galleryImages));
       // die;
    }

    static function add_update_images_variation($variation_id, $digital_assets) {
        // Tableau des images à ajouter
        $woo_variation_gallery_images = [82719,82720,82721];
           // Inclure le fichier nécessaire
           require_once ABSPATH . 'wp-admin/includes/media.php';
           require_once ABSPATH . 'wp-admin/includes/file.php';
           require_once ABSPATH . 'wp-admin/includes/image.php';
           $i = 0;
            // Réinitialiser $galleryImages à chaque itération
            $galleryImages = [];
           foreach ($digital_assets as $image) {

               // url de l'image Midocean
               $url = $image['url']; 
               // on recupere le nom du fichier
               $filename =basename($url);
               // on crée le chemin dans media
               $upload_dir = wp_upload_dir();
               //$thumbnail_dir = $upload_dir['url'] . '/' .$filename;
               //var_dump($thumbnail_dir);
               //var_dump($filename);
   
               // Obtenir tous les attachements de la bibliothèque de médias
               $attachments = get_posts(array(
                   'post_type'      => 'attachment',
                   'posts_per_page' => -1,
                   'post_status'    => 'any',
               ));
   
               // Initialise une variable pour stocker l'ID de l'attachement correspondant
               $matching_attachment_id = false;
   
               // Parcours tous les attachements pour rechercher une correspondance
               foreach ($attachments as $attachment) {
                   // Obtient le nom du fichier attaché
                   $attached_file = basename(get_attached_file($attachment->ID));
                   
                   // Vérifie si le nom du fichier attaché correspond à ce que vous recherchez
                   if ($attached_file === $filename) {
                       // Correspondance trouvée, enregistre l'ID de l'attachement
                       $matching_attachment_id = $attachment->ID;
                       break; // Sort de la boucle car nous avons trouvé une correspondance
                   }
               }
   
               // Vérifie si une correspondance a été trouvée
               if (!$matching_attachment_id) {
                    // Aucune correspondance trouvée dans la bibliothèque
                    // echo "L'image n'existe pas dans la bibliothèque.";
                    $attachment_id = self::add_image($url,$variation_id);

                    // Ajoute les autres image à la galerie du produit
                    $galleryImages[] = $attachment_id;
               } 
               else {
                    // L'image existe déjà dans la bibliothèque avec cet ID
                    //echo "L'image existe déjà dans la bibliothèque avec l'ID : $matching_attachment_id";
                    //var_dump('l\'image existe');
                    self::update_image($matching_attachment_id,$variation_id);

                    // Add the existing attachment to the product gallery
                    $galleryImages[] = $matching_attachment_id;
               }   
           }
         //  var_dump($galleryImages);
           
        update_post_meta($variation_id, 'woo_variation_gallery_images', $galleryImages);
          // die;
        // Ajouter les métadonnées pour chaque image dans le tableau
        foreach ($woo_variation_gallery_images as $image_id) {
            
        }
    }

    static function add_image($url,$product_id) {
        // on recupere le nom du fichier
        $filename =basename($url);
        // on crée le chemin dans media
        $upload_dir = wp_upload_dir();
        $thumbnail_dir = $upload_dir['url'] . '/' .$filename;
        // Récupérer les informations du produit
        $product_info = get_post($product_id);

        // Vérifier si les informations du produit ont été récupérées avec succès
        if ($product_info) {
            $product_title = $product_info->post_title; // Titre du produit
        }
        // Telecharge l'image et la met dans fichier temporaire
        $tempFile = tempnam(sys_get_temp_dir(), 'image');
        $response = wp_remote_get($url, array('timeout' => 30)); // ajuster le temps si besoin 
        if (!is_wp_error($response) && $response['response']['code'] == 200) {

            $imageData = wp_remote_retrieve_body($response);
            file_put_contents($tempFile, $imageData);

            // Recuperer le type de l'image
            $imageType = exif_imagetype($tempFile);   
            if ($imageType) {
                // Upload l'image dans la librairie media
                $upload = wp_upload_bits($filename, null, $imageData);
                // verifie si l'upload a reussi 
                if (!$upload['error']) {
                    $filename = pathinfo($filename, PATHINFO_FILENAME);
                    $alt_text=$product_title.' '.$filename;
                    // Crée les elements a attacher au produit
                    $attachment = [
                        'post_mime_type' => $upload['type'],
                        'post_title' => $alt_text,
                        'post_content' => $alt_text,
                        'post_status' => 'inherit',
                        'width' => 700, // Largeur souhaitée de l'image
                        'height' => 700, // Hauteur souhaitée de l'image
                    ];


                    $attachment_id = wp_insert_attachment($attachment, $upload['file'], $product_id);
                    wp_get_attachment_image_src($attachment_id, 'full');
                    
                    // on génére également les miniatures associées
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                    $attachment_data = wp_generate_attachment_metadata($attachment_id, $thumbnail_dir);
                    wp_update_attachment_metadata($attachment_id, $attachment_data);
                
                    return $attachment_id;
                }
            }

        }
    }

    static function update_image($matching_attachment_id, $product_id) {
        // Vérifier que l'identifiant de l'attachement est valide
        if (!$matching_attachment_id || !get_post($matching_attachment_id)) {
            return;
        }
        // Vérifier si l'image a été téléversée vers un produit
        $attached_to = get_post_meta($matching_attachment_id, '_wp_attached_file', true);
        // Extraire le nom de fichier sans extension
        $filename = pathinfo($attached_to, PATHINFO_FILENAME);

       // Vérifier si l'ID du produit est valide
       if ($product_id) {
            // Récupérer les informations du produit
            $product_info = get_post($product_id);

            // Vérifier si les informations du produit ont été récupérées avec succès
            if ($product_info) {
                $product_title = $product_info->post_title; // Titre du produit
            }
        }
    
        $alt_text = $product_title.' '.$filename;
        //var_dump($alt_text);
        // Mettre à jour les informations de l'image
        wp_update_post(array(
            'ID' => $matching_attachment_id,
            'post_title' => $alt_text,
            'post_content' => $alt_text,
            'width' => 700, // Largeur souhaitée de l'image
            'height' => 700, // Hauteur souhaitée de l'image
        ));
        
        // Insérer la métadonnée _wp_attachment_image_alt
        update_post_meta($matching_attachment_id, '_wp_attachment_image_alt', $product_title);
    
        // Insérer la métadonnée _wp_attached_file
        $upload_dir = wp_upload_dir();
        $attached_file = str_replace($upload_dir['basedir'] . '/', '', get_attached_file($matching_attachment_id));
        update_post_meta($matching_attachment_id, '_wp_attached_file', $attached_file);
    }
    
  /*  static function update_image($matching_attachment_id,$product_id) {
        //var_dump('l\'image existe');
        // Vérifier si l'image a été téléversée vers un produit
        $attached_to = get_post_meta($matching_attachment_id, '_wp_attached_file', true);
        // Extraire le nom de fichier sans extension
        $filename = pathinfo($attached_to, PATHINFO_FILENAME);

        // Vérifier si l'ID du produit est valide
        if ($product_id) {
            // Récupérer les informations du produit
            $product_info = get_post($product_id);

            // Vérifier si les informations du produit ont été récupérées avec succès
            if ($product_info) {
                $product_title = $product_info->post_title; // Titre du produit
            }
        }
        $alt_text = $product_title.' '.$filename;
        //var_dump($alt_text);
        // Mettre à jour les informations de l'image
        wp_update_post(array(
            'ID' => $matching_attachment_id,
            'post_title' => $alt_text,
            'post_content' => $alt_text,
            'width' => 700, // Largeur souhaitée de l'image
            'height' => 700, // Hauteur souhaitée de l'image
        ));
        // Insert the _wp_attachment_image_alt meta field
        update_post_meta($matching_attachment_id, '_wp_attachment_image_alt', $alt_text);

    }*/

 /*   static function add_images_mitigié($product_id, $variant) {
        $productImages = $variant;
      //  var_dump($productImages);
     // Add images to the product gallery
        if (!empty($productImages)) {
            $galleryImages = array();
            $i = 0;
            foreach ($productImages as $image) {
    
                // Generate a unique file name
                $productName = $image['subtype']; 
                $url = $image['url']; 
                $fileName =basename($url);
                //var_dump($fileName);
            
                $imageType = $image['subtype'];
                $upload_dir = wp_upload_dir();
                $thumbnail_dir = $upload_dir['url'] . '/' .$fileName;
                //var_dump($thumbnail_dir);
    
                // Check if the image already exists in the media library
                $matching_attachment_id = attachment_url_to_postid($thumbnail_dir);
                //var_dump($thumbnail_dir);
                
                if (!$matching_attachment_id) {
                    //var_dump('image existe pas');

                    // Download the image and save it as a temporary file
                  /*  $tempFile = tempnam(sys_get_temp_dir(), 'image');
                    $response = wp_remote_get($url, array('timeout' => 30)); // Adjust timeout as needed
    
                    if (!is_wp_error($response) && $response['response']['code'] == 200) {
                        $imageData = wp_remote_retrieve_body($response);

                        file_put_contents($tempFile, $imageData);

                        // Get the image type
                        $imageType = exif_imagetype($tempFile);
                        if ($imageType) {
                            // Upload the image to the media library
                            $upload = wp_upload_bits($fileName, null, $imageData);
                            //   var_dump($imageData);
                            //   var_dump($upload);
                            //  die;
                            // Check if the upload was successful
                            if (!$upload['error']) {
                                // Create attachment post
                                $attachment = array(
                                    'post_mime_type' => $upload['type'],
                                    'post_title' => sanitize_file_name($fileName),
                                    'post_content' => sanitize_file_name($fileName),
                                    'post_status' => 'inherit',
                                    'width' => 700, // Largeur souhaitée de l'image
                                    'height' => 700, // Hauteur souhaitée de l'image
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
                    }*/
               /* } 
                else {
                    //var_dump('l\'image existe');
                    // Récupérer les informations de l'image
                /*    $image_info = get_post($matching_attachment_id);
                    // Vérifier si l'image a été téléversée vers un produit (custom post type)
                    $attached_to = get_post_meta($matching_attachment_id, '_wp_attached_file', true);
                    // Extraire le nom de fichier sans extension
                    $filename = pathinfo($attached_to, PATHINFO_FILENAME);
                    // Vérifier si l'ID du produit est valide
                    if ($product_id) {
                        // Récupérer les informations du produit
                        $product_info = get_post($product_id);

                        // Vérifier si les informations du produit ont été récupérées avec succès
                        if ($product_info) {
                            $product_title = $product_info->post_title; // Titre du produit
                        }
                    }

                    update_post_meta($matching_attachment_id, '_wp_attachment_image_alt', $product_title.' '.$filename);

                    // Mettre à jour les informations de l'image
                    wp_update_post(array(
                        'ID' => $matching_attachment_id,
                        '_wp_attachment_image_alt'=> $product_title.' '.$filename,
                        'post_title' => $product_title.' '.$filename,
                        'post_content' => $product_title.' '.$filename,
                        'width' => 700, // Largeur souhaitée de l'image
                        'height' => 700, // Hauteur souhaitée de l'image
                    ));

                  //  var_dump('image existe');
                    if ($i === 0) {
                        // Set the downloaded image as the featured image of the product
                        set_post_thumbnail($product_id, $matching_attachment_id);
                        $i = 1;
                    } else {
                        // Add the existing attachment to the product gallery
                        $galleryImages[] = $matching_attachment_id;
                    }*/
                    
         /*       }
            }

            // Set the product gallery images
            update_post_meta($product_id, '_product_image_gallery', implode(',', $galleryImages));
        }
    }*/


    static function delete_all_media(){
        // Récupérer tous les médias de WordPress
        $args = array(
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
        );

        $attachments = get_posts($args);

        // Supprimer chaque média trouvé
        foreach ($attachments as $attachment) {
            wp_delete_attachment($attachment->ID, true); // true pour forcer la suppression définitive
        }

       // echo "Tous les médias ont été supprimés avec succès !";
    }

}
