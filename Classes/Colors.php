<?php
namespace fwai\Classes;
use \WC_Product_Variation;
use \WC_Product_Attribute;

class Colors {
    // Fonction pour ajouter un attribut de couleur à une variation de produit WooCommerce
    public static function add_colors($product_id, $variant) {

        // Trouver les clés contenant le mot "couleur"
        $groupe_keys = array_filter(array_keys($variant[0]), function($key) {
            return strpos($key, 'color_group') === 0;
        });

        // Récupérer le groupe de l'attribut
        $groupe = current($groupe_keys);
        $groupe_key = explode('_', $groupe);
        // Récupérer le nom de l'attribut
        $nom_attribut = $groupe_key[0];
        // Defini son slug
        $attribut_slug='pa_'.sanitize_title($nom_attribut);

        // Ajouter l'attribut s'il n'existe pas encore
        self::ajouter_nouvel_attribut($nom_attribut,$attribut_slug);

        // Récupérer le terme
        $term = $variant[0][$groupe];

        // Ajouter le terme à l'attribut s'il n'existe pas encore
        self::ajouter_termes_a_attribut($attribut_slug, $term);

        // Associer l'attribut au produit parent s'il n'est pas déjà associé
        self::associer_attribut_produit($product_id, $attribut_slug,$term);
        $result = has_term($term, $attribut_slug, $product_id);
        var_dump($result);

        // Vérifier si le produit contient déjà l'attribut avec le terme
        if (has_term($term, $attribut_slug, $product_id)) {
            var_dump('contient déjà l\'attribut');
            $sku=$variant[0]['sku'];
            // Obtenez l'ID de la variation existante avec les mêmes attributs
            $variations = self::get_variation_id_with_attributes($product_id, $attribut_slug, $term);
            var_dump($variations);
            var_dump($product_id);
            var_dump($attribut_slug);
            var_dump($term);
            if ($variations) {
                var_dump('existe');
                foreach ($variations as $variation_id) {
                    // Instancier la variation en utilisant son ID
                    $variation = new WC_Product_Variation($variation_id);
                    $attributes = $variation->get_attributes();
                    $sku = $variation->get_sku();
                    var_dump($sku);
                }

                die;
                // Mettez à jour la variation existante
                $variation_data = new WC_Product_Variation($variation_id);
                $variation_data->set_regular_price('8,34'); // Mettez à jour les autres attributs si nécessaire
                $variation_data->save();
            } 
            else {
                var_dump('existe pas');
                var_dump($sku);
                $term_slug = sanitize_title($term);
                // Récupérer l'ID de l'attribut de couleur (s'il existe)
                $attribute_id = wc_attribute_taxonomy_id_by_name($nom_attribut);
                // La variation n'existe pas, créer une nouvelle variation
                $variation_data = array(
                    'attributes' => array(
                        $attribut_slug => $term_slug,
                    ),
                    'regular_price' => '8,34', // Remplacez par le prix régulier de la variation
                    'sku' => $variant[0]['sku'], // Remplacez par le SKU de la variation
                    'stock_quantity'=>'100',
                    'manage_stock'=>'true',
                    'parent_id'=>$product_id,
                    // Ajoutez d'autres propriétés de la variation si nécessaire
                );
             
                // Créer la nouvelle variation
                $variation = new WC_Product_Variation();
                $variation->set_props($variation_data);
                $variation->set_parent_id($product_id);
                $variation->save();
            }
            // Rendre la variation visible en définissant la visibilité du produit parent
            $product = wc_get_product($product_id);
            $product->set_catalog_visibility('visible');
            $product->save();
            die;
        }
         else {
            var_dump("ne contient pas l'attribut");
            die;
            // Créer une nouvelle variation pour l'attribut de couleur
            $variation = new \WC_Product_Variation();
            $variation->set_regular_price('8,34'); // Spécifier ici le prix de la variation
            $variation->set_stock_quantity(100); // Spécifier ici la quantité de stock de la variation
            $variation->set_manage_stock(true); // Activer la gestion du stock pour la variation
            $variation->set_parent_id($product_id);

            // Associer l'attribut au produit parent
            wp_set_object_terms($product_id, $term, $nom_attribut);
            $variation->set_attributes(array($nom_attribut => $term));

            // Sauvegarder la variation
            $variation_id = $variation->save();

            // Vérifier si la variation a été correctement ajoutée
            if ($variation_id) {
                echo "La variation avec l'attribut '$nom_attribut' et le terme '$term' a été ajoutée avec succès.";
            } else {
                echo "Erreur lors de l'ajout de la variation.";
            }
            // Rendre la variation visible en définissant la visibilité du produit parent
            $product = wc_get_product($product_id);
            $product->set_catalog_visibility('visible');
            $product->save();

        }
    }

    // Fonction pour ajouter un nouvel attribut
    static function ajouter_nouvel_attribut($nom_attribut,$attribut_slug) {
        if (!taxonomy_exists($attribut_slug)) {
            // Nom de l'attribut
            $attribut = array(
                'slug' => $attribut_slug,
                'name' => $nom_attribut,
                'type' => 'select', // type de champ (select, radio, etc.)
                'order_by' => 'menu_order', // tri des termes
                'has_archives' => true,
            );
            // Ajout de l'attribut
            wc_create_attribute($attribut);
        }
        
    }

    // Fonction pour ajouter des termes à l'attribut
    static function ajouter_termes_a_attribut($slug, $term) {
        // Vérifier si le terme existe déjà
        $term_id = term_exists($term, sanitize_title($slug));
        // Si le terme n'existe pas, l'ajouter
        if ($term_id==null) {
            wp_insert_term($term, $slug);
        }

    }

    // Fonction pour associer l'attribut au produit parent
    static function associer_attribut_produit($product_id, $attribut_slug,$term) {
        // Obtenez l'ID de l'attribut en fonction de son slug
        $attribut_id = wc_attribute_taxonomy_id_by_name( $attribut_slug );
       // var_dump($attribut_id);
        // Obtenez l'ID du terme en fonction de son slug et de l'ID de l'attribut
        $term_id = get_term_by( 'slug', $term, $attribut_slug );
       // var_dump($term_id);

        // Vérifiez si le terme existe
        if ( $term_id ) {
            // Associez l'attribut et le terme au produit
            $result=wp_set_object_terms( $product_id, $term, $attribut_slug, true );
             // Associer l'attribut et le terme au produit
            $product_attributes = get_post_meta( $product_id, '_product_attributes', true );
           // var_dump($product_attributes);
            // Si les attributs du produit sont vides, initialiser comme un tableau vide
            if ( empty( $product_attributes ) ) {
                $product_attributes = array();
            }

            $product_attributes[$attribut_slug] = array(
                'name' => $attribut_slug,
                'value' => $term,
                'position' => 1,
                'is_visible' => 1,
                'is_variation' => 1,
                'is_taxonomy' => 1
            );
            $resulte = update_post_meta( $product_id, '_product_attributes', $product_attributes );

           /* var_dump($product_id);
            var_dump($term);
            var_dump($attribut_slug);
            var_dump($result);
            var_dump($resulte);
            die;*/
        } 
    }

    // Fonction pour obtenir l'ID de la variation avec les mêmes attributs
    static function get_variation_id_with_attributes($product_id, $nom_attribut, $term) {
            $variation_ids = [];
            $product = wc_get_product($product_id);
            $variations = $product->get_children();

            var_dump($variations);

            if (empty($variations)){
                var_dump('Variation pour ce produit existe pas');
                return false;
            }
            else{
                var_dump('Variation pour ce produit existe');
                foreach ($variations as $variation_id) {
                    // Instancier la variation en utilisant son ID
                    $variation = new WC_Product_Variation($variation_id);
                    $attributes = $variation->get_attributes();
                    $sku = $variation->get_sku();
                    var_dump($attributes[$nom_attribut]);
                    if (isset($attributes) && $attributes[$nom_attribut] == sanitize_title($term)) {
                        $variation_ids[] =$variation_id;
                    }

                }
                // Retourner les IDs des variations
                return $variation_ids;
            }
    }
}