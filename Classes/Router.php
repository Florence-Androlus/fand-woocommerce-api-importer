<?php

namespace fwai\Classes;
use fwai\Classes\Api;

class Router {
    // Définir une propriété statique pour stocker les données de l'API
    static private $apiData = null;

    // Méthode pour obtenir les données de l'API
    static public function getApiData() {
        // Retourner les données stockées
        return self::$apiData;
    }

    static public function init()
    {
        // Vérifier si les données de l'API ont déjà été récupérées
        if (self::$apiData === null) {
            // Si non, récupérer les données de l'API et les stocker dans la propriété statique
            self::$apiData = Api::json_api_test_product();
        }
        // objectif :

        // @TODO: déplacer la déclaration de la rewrite rule dans l'activation du plugin
        // si l'URL courante est ajout, afficher le template readfile.php du thème
        // 2e argument : URL réelle correspondant à la "fausse URL" de l'argument 1 
        // 1. ajout de la réécriture = on permet à WP de reconnaître notre URL custom :  
        add_rewrite_rule('product', 'index.php?fwai-page=product', 'top');  
        add_rewrite_rule('category', 'index.php?fwai-page=category', 'top'); 
        add_rewrite_rule('images', 'index.php?fwai-page=images', 'top');   
        
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
                $data = self::getApiData();
            
                if (is_array($data)) {
                    foreach ($data as $product) {
                        // Accéder aux données du produit
                        if (array_key_exists('master_code', $product)) {

                            $productCode = $product['master_code'];
                            //var_dump($productCode);
                        } else {
                            // Gérer l'erreur ici, par exemple, attribuer une valeur par défaut à $productCode
                            continue;
                        }
                        // Initialiser une variable pour suivre si le produit a été traité avec succès ou non
                        $productProcessed = false;
            
                        // Vérifier si le produit existe déjà
                        global $wpdb;

                        // Requête SQL pour rechercher le post avec le SKU spécifié
                        $product_id = $wpdb->get_var($wpdb->prepare(
                            "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value=%s",
                            $productCode
                        ));

                            // Le produit a été trouvé, retourner l'objet post correspondant
                       // $existing_product = get_page_by_title($productCode, OBJECT, 'product');

                        if ($product_id) {
                           // $product_id = $existing_product->ID;
                          //  Products::delete_product_by_name($product_id);
                            Products::update_product($product, $product_id);
                            $productProcessed = true; // Marquer le produit comme traité avec succès
                        } else {
                            Products::add_product($product);
                            $productProcessed = true; // Marquer le produit comme traité avec succès
                        }
            
                        // Si le produit a été traité avec succès, incrémenter le compteur
                        if ($productProcessed) {
                            $compteur++;
                        }
                    }
                }
            

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "product"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

                // si c'est le cas, on réagit en conséquence
                wp_redirect(home_url('wp-admin/admin.php?page=fwai-settings'));
            } 
            else if (get_query_var('fwai-page') == 'category') {
                $compteur=0;
                //$data=Api::json_api_test_product();
                $data = self::getApiData();

                if (is_array($data)) {
                    foreach ($data as $product) {
                        // Access the product data
                        if (array_key_exists('master_code', $product)) {
                            $productCode = $product['master_code'];
                        } else {
                            // Gérer l'erreur ici, par exemple, attribuer une valeur par défaut à $productName
                            continue;
                        }
                        // Check if the product already exists
                        $existing_product = get_page_by_title($productCode, OBJECT, 'product');
        
                        if ($existing_product) {
                            $product_id=$existing_product->ID;
                            $variant = $product['variants'][0];
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
                            continue; // Skip to the next product
                        }
        
                        else
                        {
                         //   Products::add_product( $product);
                        }
                        $compteur++;
                       // die;
                    }
                }

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "category"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

                // si c'est le cas, on réagit en conséquence
                wp_redirect(home_url('wp-admin/admin.php?page=fwai-settings'));
            } 
            else if (get_query_var('fwai-page') == 'images') {

                $compteur=0;
                //$data=Api::json_api_test_product();
                $data = self::getApiData();

                if (is_array($data)) {
                    foreach ($data as $product) {
                        // Access the product data
                        if (array_key_exists('master_code', $product)) {
                            $productCode = $product['master_code'];
                        } else {
                            // Gérer l'erreur ici, par exemple, attribuer une valeur par défaut à $productName
                            continue;
                        }
                        // Check if the product already exists
                        $existing_product = get_page_by_title($productCode, OBJECT, 'product');

                        if ($existing_product) {
                            $product_id=$existing_product->ID;
                            // gestion de la galerie d'image du produit
                            $variant = $product['variants'][0];

                                // Trouver les clés contenant le mot "category"
                            $images_keys = array_filter(array_keys($variant), function($key) {
                                return strpos($key, 'digital_assets') === 0;
                            });

                            // Vérifier si des catégories ont été trouvées
                            if (count($images_keys) > 0) {
                                $variant = $product['variants'][0]['digital_assets'];
                                Images::add_images($product_id,$variant,$images_keys);
                            } 
                            else 
                            {
                                //var_dump("Aucune catégorie trouvée dans le tableau.");
                            }
                            continue; // Skip to the next product
                        }
        
                        else
                        {
                         //   Products::add_product( $product);
                        }
                        $compteur++;
                       // die;
                    }
                }

                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "images"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

                // si c'est le cas, on réagit en conséquence
                wp_redirect(home_url('wp-admin/admin.php?page=fwai-settings'));
            } 
            else {
                // sinon, on laisse WP faire
                return $template;
            }
        } );
    }
}
