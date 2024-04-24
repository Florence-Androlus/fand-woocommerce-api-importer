<?php
namespace fwai\Classes;

class Router {
    // Définir les propriétées statiques pour stocker les données de l'API
    static private $apiData = null;
    static private $apiStock = null;

    // Méthode pour obtenir les données Produits de l'API
    static public function getApiData() {
        // Retourner les données stockées
        return self::$apiData;
    }
    // Méthode pour obtenir les données du stock de l'API
    static public function getApiStock() {
        // Retourner les données stockées
        return self::$apiStock;
    }

    static public function init()
    {
        // Vérifier si les données de l'API ont déjà été récupérées
        if (self::$apiData === null) {
            // Si non, récupérer les données de l'API et les stocker dans la propriété statique
            $file = "produituniqueP.json";//"produits.json";//
            self::$apiData = Api::json_product($file);
            //self::$apiData = Api::json_api_test_product();
            //self::$apiData = Api::json_api_product();
        }
        // Vérifier si les données de l'API ont déjà été récupérées
        if (self::$apiStock === null) {
            // Si non, récupérer les données de l'API et les stocker dans la propriété statique
            // Chemin vers votre fichier JSON
            $file = "stock.json";//"stockunique.json";
            self::$apiStock = Api::json_stock($file);
        }
        // objectif :

        // @TODO: déplacer la déclaration de la rewrite rule dans l'activation du plugin
        // si l'URL courante est ajout, afficher le template readfile.php du thème
        // 2e argument : URL réelle correspondant à la "fausse URL" de l'argument 1 
        // 1. ajout de la réécriture = on permet à WP de reconnaître notre URL custom :  
        add_rewrite_rule('product', 'index.php?fwai-page=product', 'top');  
        add_rewrite_rule('variations', 'index.php?fwai-page=variations', 'top');  
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
                        $product_id=self::product_exist($product);

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
                $data = self::getApiData();
                
                if (is_array($data)) {
                    foreach ($data as $product) {
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
                // on redirige vers la page wp-admin/admin.php?page=fwai-settings (mais en GET) => si l'utilisateur rafraîchit, on ne resoumettra pas le formulaire (on rafraîchira la requête GET et non POST)
                $url=home_url( 'wp-admin/admin.php?page=fwai-settings');
                wp_redirect(add_query_arg(['compteur'=> $compteur,'action'=> "variations"], $url));
                exit(); // on empêche le reste du code de s'exécuter, on laisse la redirection se faire tout de suite.

            }
            else if (get_query_var('fwai-page') == 'category') {
                $compteur=0;
                //$data=Api::json_api_test_product();
                $data = self::getApiData();

                if (is_array($data)) {
                    foreach ($data as $product) {
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
            else if (get_query_var('fwai-page') == 'images') {

                $compteur=0;
                $data = self::getApiData();

                if (is_array($data)) {
                    foreach ($data as $product) {
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
        return $product_id;
    }
}
