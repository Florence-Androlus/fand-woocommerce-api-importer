<?php
namespace fwai\Classes;

use fwai\Classes\Database\Database;

class Router {
    
    static public function init()
    {
        Api::init();
        // objectif :

        // @TODO: déplacer la déclaration de la rewrite rule dans l'activation du plugin
        // si l'URL courante est ajout, afficher le template readfile.php du thème
        // 2e argument : URL réelle correspondant à la "fausse URL" de l'argument 1 
        // 1. ajout de la réécriture = on permet à WP de reconnaître notre URL custom :
        add_rewrite_rule('ppom', 'index.php?fwai-page=ppom', 'top');    
        add_rewrite_rule('product', 'index.php?fwai-page=product', 'top');  
        add_rewrite_rule('variations', 'index.php?fwai-page=variations', 'top');  
        add_rewrite_rule('category', 'index.php?fwai-page=category', 'top'); 
        add_rewrite_rule('images', 'index.php?fwai-page=images', 'top');   
        add_rewrite_rule('printingtechniques', 'index.php?fwai-page=printingtechniques', 'top');  
        
        add_rewrite_rule('suppression', 'index.php?fwai-page=suppression', 'top');

        // 2. on rafraîchit les réécritures au sein de WP
        flush_rewrite_rules();

        // 3. Autoriser notre query var (paramètre d'URL) custom dans WP
        add_filter('query_vars', function($query_vars) {
            $query_vars[] = 'fwai-page'; // on rajoute notre propre query var en tant que query var autorisée

            // on return le tableau $query_vars
            return $query_vars;
        });

        // 4. Surcharger (ou pas !) le choix de template fait par WP
        // $template contient le chemin vers le fichier de template que WP comptait charger si on ne l'avait pas interrompu
        add_action( 'template_include', function( $template ) {
            // on vérifie si notre query var custom est présente et a une valeur qu'on connaît
            // pour lire une query var, on utilise get_query_var()
            if (get_query_var('fwai-page') == 'product') {
                $compteur = 0;
                $products = Api::getApiData();

                if (is_array($products)) {
                    foreach ($products as $product) {
                        // Accéder aux données du produit
                        $product_id=self::product_exist($product);
                        //var_dump($product);
                        //die;
                        if ($product_id) {
                          //  Products::delete_product_by_name($product_id);
                            Products::update_product($product, $product_id);
                        } else {
                            Products::add_product($product);
                        }
                            $compteur++;
                    }
                }
            
                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "product"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

            } 
            else if (get_query_var('fwai-page') == 'variations') {
                // var_dump('ajout variations');
                $compteur = 0;
                //$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
                $offset = 0;
                $lot_taille = 50; // Nombre de produits à traiter par lot
 
                 // Obtenir les produits à importer avec l'offset
                 //$products = Api::getApiData($lot_taille,$offset); // Assure-toi que la méthode prend en compte l'offset
                      // On traite tous les produits par lot, jusqu'à épuisement des produits
                 while (true) {
                     // Obtenir les produits via l'API avec l'offset
                     $products = Api::getApiData($lot_taille, $offset);    
                     if (empty($products)) {
                         break; // Sortir de la boucle si aucun produit n'est récupéré
                     }     
 
                     foreach ($products as $product) {
                         $product_id=self::product_exist($product);
                         if(isset($product_id)){
                 //           var_dump($product_id);
                             $variants=$product['variants'];
                 //           var_dump($variants);
                             if (count($variants) > 0 && $product_id) {
                                 foreach ($variants as $variant){
                                 //   var_dump($variant);
                                 // set_time_limit(3000); // définir la limite de temps d'exécution à 30 secondes
 
                                     Variations::add_variations($product_id,$variant);
                                     $compteur++;
                                 }
                             }
                             else 
                             {
                                //var_dump("Aucune variations trouvées dans le tableau.");
                                error_log("Aucune variations trouvées dans le tableau.");
                             }
                         }
                     }
 
                     // Mise à jour de l'offset pour le prochain lot
                     $offset += $lot_taille;
                 }
                 // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                 $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                 wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "variations"], $url));
                 exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.
 
             }
/*            else if (get_query_var('fwai-page') == 'variations') {
               // var_dump('ajout variations');
               $compteur = 0;
               $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
               $lot_taille = 50; // Nombre de produits à traiter par lot

                // Obtenir les produits à importer avec l'offset
                $products = Api::getApiData($lot_taille,$offset); // Assure-toi que la méthode prend en compte l'offset
                                   
                if (is_array($products)) {
                    foreach ($products as $product) {
                        $product_id=self::product_exist($product);
                        if(isset($product_id)){
                 //           var_dump($product_id);
                            $variants=$product['variants'];
                 //           var_dump($variants);
                            if (count($variants) > 0 && $product_id) {
                                foreach ($variants as $variant){
                                 //   var_dump($variant);
                                // set_time_limit(3000); // définir la limite de temps d'exécution à 30 secondes

                                    Variations::add_variations($product_id,$variant);
                                    $compteur++;
                                }
                            }
                            else 
                            {
                                var_dump("Aucune variations trouvées dans le tableau.");
                            }
                        }
                    }
                }

                // Mise à jour de l'offset
                $new_offset = $offset + $lot_taille;
                $total_produits = count($products);
                if ($new_offset >= $total_produits) {
                    // var_dump('Importation des images continues.');
                     // Si tous les produits n'ont pas été traités, redirection pour le prochain lot
                     wp_redirect(add_query_arg(['compteur' => $compteur, 'action' => 'variations', 'offset' => $new_offset], home_url('variations')));
                 } else {
                 // Si aucun produit n'a été récupéré, cela signifie que tous les lots ont été traités
                 error_log('Importation des images terminée.');
                 //var_dump('Importation des images terminée.');
                 //die;
                 // Redirection vers la page une fois terminé
                 $url = home_url('wp-admin/admin.php?page=fwai-settings');
                 wp_redirect(add_query_arg(['compteur' => $compteur, 'action' => 'variations', 'finished' => '1'], $url));
                 exit();
                 }
                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
/*                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "variations"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

            }*/
            else if (get_query_var('fwai-page') == 'category') {
                $compteur=0;
                //$data=Api::json_api_test_product();
                $products = Api::getApiData();

                if (is_array($products)) {
                    foreach ($products as $product) {
                        // Access the product data
                        $product_id=self::product_exist($product);
        
                        if ($product_id) {
                            $variant = $product['variants'][0];
                            // Trouver les clés contenant le mot "category"
                            $category_keys = array_filter(array_keys($variant), function($key) {
                                return strpos($key, 'category') === 0;
                            });

                            // Vérifier si des catégories ont été trouvées
                            if (count($category_keys) > 0) {

                                Categorys::add_categorys($product_id,$variant,$category_keys);
                            } 
                        }
                        $compteur++;
                    }
                }

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "category"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.
            } 
            /*else if (get_query_var('fwai-page') == 'images') {

                $compteur=0;
                $products = Api::getApiData();

                if (is_array($products)) {
                    foreach ($products as $product) {
                       // Images::delete_all_media();
                        // Access the product data
                        $product_id=self::product_exist($product);

                        if ($product_id) {
                            // gestion de la galerie d'image du produit
                            $variant = $product['variants'][0];

                                // Trouver les clés contenant le mot "digital_assets"
                                $images_keys = array_filter(array_keys($variant), function($key) {
                                return strpos($key, 'digital_assets') === 0;
                            });

                            // Vérifier si des digital_assets ont été trouvées
                            if (count($images_keys) > 0) {
                                $digital_assets = $product['variants'][0]['digital_assets'];
                                Images::add_update_images($product_id,$digital_assets);
                            } 
                            continue; // Skip to the next product
                        }
                        $compteur++;
                    }
                }

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "images"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.
                
            } */
            else if (get_query_var('fwai-page') == 'images') {
                $compteur = 0;
                //var_dump(isset($_GET['offset']));
                $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
                $lot_taille = 50; // Nombre de produits à traiter par lot
                //var_dump($offset );
                // Obtenir les produits à importer avec l'offset
                $products = Api::getApiData($lot_taille,$offset); // Assure-toi que la méthode prend en compte l'offset
                //var_dump($produits );
                if (is_array($products)) {
                    // Processus d'importation des images
                    foreach ($products as $product) {
                        // Gestion de la galerie d'image du produit
                        $product_id = self::product_exist($product);
                        //var_dump($product_id );
                        if ($product_id) {
                            $variant = $product['variants'][0];
                            $images_keys = array_filter(array_keys($variant), function($key) {
                                return strpos($key, 'digital_assets') === 0;
                            });
            
                            if (count($images_keys) > 0) {
                                $digital_assets = $product['variants'][0]['digital_assets'];
                                Images::add_update_images($product_id, $digital_assets);
                            }
                        }
                        $compteur++;
                    }
            
                    // Mise à jour de l'offset
                    $new_offset = $offset + $lot_taille;
                    $total_produits = count($products);
                   // var_dump($new_offset);
                    ///var_dump($total_produits);

                    if ($new_offset >= $total_produits) {
                       // var_dump('Importation des images continues.');
                        // Si tous les produits n'ont pas été traités, redirection pour le prochain lot
                        wp_redirect(add_query_arg(['compteur' => $compteur, 'action' => 'images', 'offset' => $new_offset], home_url('images')));
                    } else {
                    // Si aucun produit n'a été récupéré, cela signifie que tous les lots ont été traités
                    error_log('Importation des images terminée.');
                    //var_dump('Importation des images terminée.');
                    //die;
                    // Redirection vers la page une fois terminé
                    $url = home_url('wp-admin/admin.php?page=fwai-settings');
                    wp_redirect(add_query_arg(['compteur' => $compteur, 'action' => 'images', 'finished' => '1'], $url));
                    exit();
                    }
                }
            }
            else if (get_query_var('fwai-page') == 'ppom') {

                $compteur = 0;
                $products = Api::getApiData();

                if (is_array($products)) {
                    foreach ($products as $product) {
                        $master_code=$product['master_code'];
                        $color_code=$product['variants'][0]['color_code'];

                        $apiPrintData = Api::getApiPrintData();
                        $zonemarquage=PrintData::getPrintData($master_code,$color_code,$apiPrintData);

                        // Access the product data
                        $ppom_id=FWAI_ppom::ppom_exist($master_code);

                        FWAI_ppom::update_ppom_field($ppom_id,$zonemarquage);
                        //insert champ ppom
                        // L'ID du produit auquel tu veux associer le PPOM
                        $product_id=self::product_exist($product);

                        // La clé pour associer un PPOM à un produit dans WooCommerce
                        $meta_key = '_product_meta_id';
                        // Mettre à jour la métadonnée du produit avec l'ID du PPOM
                        update_post_meta($product_id, $meta_key, $ppom_id);
           
                        $compteur++;

                    }
                }          

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "ppom"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.
                
            } 
            else if (get_query_var('fwai-page') == 'printingtechniques') {
                // var_dump('ajout printingtechniques');
                $compteur = 0;
                $data = Api::getApiPrintData();
                
                global $wpdb;
                $table_name = $wpdb->prefix . 'printing_technique_descriptions';

                $compteur=Database::insert_printing_techniques($data,$compteur);

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "printingtechniques"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

            }
            else {
                // sinon, on laisse WP faire
                return $template;
            }
        } );
    }

    static function product_exist($product){
        // Accéder aux données du produit
        if (array_key_exists('master_code', $product)) {

            $productCode = $product['master_code'];
            //var_dump($productCode);
        } else {
            // Gérer l'erreur ici, par exemple, attribuer une valeur par défaut à $productCode
            return;
        }

        // Vérifier si le produit existe déjà
        global $wpdb;

        // Requête SQL pour rechercher le post avec le SKU spécifié
        // Le produit a été trouvé, retourner l'id du produit correspondant
        $product_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value=%s",
            $productCode
        ));
       // var_dump($productCode);
       // var_dump($product_id);
       $product_id=intval($product_id);
       return $product_id;
    }
}
