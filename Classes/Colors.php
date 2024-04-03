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
            // Obtenez l'ID de la variation existante avec les mêmes attributs
            $variation_id = self::get_variation_id_with_attributes($product_id, $attribut_slug, $term);
            var_dump($variation_id);
            var_dump($product_id);
            var_dump($attribut_slug);
            var_dump($term);
            if ($variation_id) {
                var_dump('existe');
                die;
                // Mettez à jour la variation existante
                $variation_data = new WC_Product_Variation($variation_id);
                $variation_data->set_regular_price('8,34'); // Mettez à jour les autres attributs si nécessaire
                $variation_data->save();
            } else {
                var_dump('existe pas');
                die;
                // Créez une nouvelle variation
                $variation = new \WC_Product_Variation();
                // Configurez les attributs de la nouvelle variation
                $variation->set_regular_price('8,34');
                $variation->set_stock_quantity(100);
                $variation->set_manage_stock(true);
                $variation->set_parent_id($product_id);
                $taxonomy = wc_attribute_taxonomy_name(strtolower($nom_attribut));
                $variation->set_attributes(array($taxonomy => $term));
                // Enregistrez la nouvelle variation
                $variation_id = $variation->save();
                if ($variation_id) {
                    echo "La variation avec l'attribut '$nom_attribut' et le terme '$term' a été ajoutée avec succès.";
                } else {
                    echo "Erreur lors de l'ajout de la variation.";
                }
            }
            // Rendre la variation visible en définissant la visibilité du produit parent
            $product = wc_get_product($product_id);
            $product->set_catalog_visibility('visible');
            $product->save();
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
            // Vérifier si le terme existe déjà
            $term_id = term_exists($term, sanitize_title($nom_attribut));
            //var_dump($nom_attribut);
            //var_dump( $term_id);
            // Si le terme n'existe pas, l'ajouter
            if (!$term_id) {
                $term_id = wp_insert_term($term, sanitize_title($nom_attribut));
            }
        var_dump($term_id);
        if ($term_id) {
            $variation_data = new \WC_Product_Variable;
        //    var_dump($variation_data);
            $variations =$variation_data->get_available_variations();
            var_dump($variations);
            die;
            foreach ($variations as $variation) {
                //var_dump($variation['attributes']);
                if (isset($variation['attributes'][$nom_attribut]) && $variation['attributes'][$nom_attribut] == $term) {
                    return $variation['variation_id'];
                }
            }
        }
        else{
            var_dump('on crée le term de l\'attribut');

            die;
        }
        return false;
    }
}





/*
class Colors {
    // Fonction pour ajouter un attribut de couleur à une variation de produit WooCommerce
    public static function add_colors($product_id,$variant) {

        // Trouver les clés contenant le mot "couleur"
        $color_keys = array_filter(array_keys($variant[0]), function($key) {
            return strpos($key, 'color_group') === 0;
        });

        // Récupére l'attribut
        $nom_attribut = current($color_keys); 
        // Ajout de l'attribut existe 
        self::ajouter_nouvel_attribut($nom_attribut);

        // Récupére le term
        $term = $variant[0][$nom_attribut]; 
        //var_dump($terms);

        // Ajoutez ici les termes pour cet attribut si nécessaire
        self::ajouter_termes_a_attribut($nom_attribut, $term);

        if (has_term($term, 'pa_' . sanitize_title($nom_attribut), $product_id)) {
            echo "Le produit contient l'attribut '$nom_attribut' avec le terme '$term'.";
        } 
        else 
        {
            echo "Le produit ne contient pas l'attribut '$nom_attribut' avec le terme '$term'.";
            // Ajouter le terme à l'attribut spécifié du produit
            // Obtient le nom de la taxonomie associée à l'attribut
            $taxonomy = wc_attribute_taxonomy_name(strtolower($nom_attribut));
            $couleur = $variant[0]['color_group'];
           // var_dump($taxonomy);
          //  var_dump($variant[0]['color_group']);
 
            // Obtient l'objet produit variable
            $product = wc_get_product($product_id);
           // var_dump($product_id);
            //var_dump($product);
            // Crée une nouvelle variation pour l'attribut de couleur 'silver'
            $variation = new \WC_Product_Variation();
            $variation->set_regular_price('10'); // Spécifiez ici le prix de la variation
            $variation->set_stock_quantity(100); // Spécifiez ici la quantité de stock de la variation
            $variation->set_manage_stock(true); // Activez la gestion du stock pour la variation
            $variation->set_parent_id($product_id);
           // var_dump($variation);
            // Associe l'attribut de couleur à la variation
            $result = $variation->set_attributes(array($taxonomy => $couleur));
          //  var_dump($result);
            // Sauvegarde la variation
            $result = $variation->save();
            var_dump($result);
            // Actualise les métadonnées des variations avec l'ID du produit variable
            $result = $product->sync($product_id);
            var_dump($result);
            //die;

            // Obtient l'objet produit variable
      /*      $product = wc_get_product($product_id);
            var_dump($product);
        
            // Récupérer toutes les variations du produit
            $variations = get_posts(array(
                'post_type' => 'product_variation',
                'post_parent' => $product_id,
                'numberposts' => -1
            ));
            var_dump($variations);
            // Vérifier si des variations existent
            if ($variations) {
            // var_dump($variations);

                foreach ($variations as $variation) {
                    $variation_id = $variation->ID;
                    $variation_data = new WC_Product_Variation($variation_id);
                    var_dump($variation_data);

                    // Afficher les détails de la variation
        
                    // Vérifie si l'attribut existe déjà, sinon le crée
                    //$attribute_name = 'Couleur'; // Nom de l'attribut
                        
                    // Obtient le nom de la taxonomie associée à l'attribut
                    $taxonomy = wc_attribute_taxonomy_name(strtolower($attribute_name));
                    var_dump($taxonomy);

                    // Obtient l'objet produit variable
                    $product = wc_get_product($product_id);

                    // met a jour la variation pour l'attribut de couleur 'silver'

                    $variation_data->set_regular_price('8,34'); // Spécifiez ici le prix de la variation
                    $variation_data->set_stock_quantity(100); // Spécifiez ici la quantité de stock de la variation
                    $variation_data->set_manage_stock(true); // Activez la gestion du stock pour la variation
                    $variation_data->set_parent_id($product_id);

                    // Associe l'attribut de couleur à la variation
                    $variation_data->set_attributes(array('pa_' . $attribute_name => 'silver'));

                    // Sauvegarde la variation
                    $variation_data->save();

                    // Actualise les métadonnées des variations avec l'ID du produit variable
                    $product->sync($product_id);
                }

            }

        } 
        else {
            var_dump('Aucune variation trouvée pour ce produit.');
            // Vérifie si l'attribut existe déjà, sinon le crée
           /* $attribute_name = 'Couleur'; // Nom de l'attribut
                    
            // Obtient le nom de la taxonomie associée à l'attribut
            $taxonomy = wc_attribute_taxonomy_name(strtolower($attribute_name));

            if (!taxonomy_exists($taxonomy)) {
                // Crée l'attribut
                $attribute = new \WC_Product_Attribute();
                $attribute->set_name($attribute_name);
                $attribute->set_options(['silver']); // Ajoutez ici d'autres options de couleur si nécessaire
                $attribute->set_position(0);
                $attribute->set_visible(true);
                $attribute->set_variation(true);

                // Ajoute l'attribut au produit parent
                $product = wc_get_product($product_id);
                $product->set_attributes([$attribute]);

                // Sauvegarde le produit pour enregistrer l'attribut
                $product->save();
            }

            // Crée le terme 'silver' s'il n'existe pas déjà
            if (!term_exists('silver', $taxonomy)) {
                wp_insert_term('silver', $taxonomy);
            }

            // Obtient l'objet produit variable
            $product = wc_get_product($product_id);

            // Crée une nouvelle variation pour l'attribut de couleur 'silver'
            $variation = new \WC_Product_Variation();
            $variation->set_regular_price('8,34'); // Spécifiez ici le prix de la variation
            $variation->set_stock_quantity(100); // Spécifiez ici la quantité de stock de la variation
            $variation->set_manage_stock(true); // Activez la gestion du stock pour la variation
            $variation->set_parent_id($product_id);

            // Associe l'attribut de couleur à la variation
            $variation->set_attributes(array('pa_' . $attribute_name => 'silver'));

            // Sauvegarde la variation
            $variation->save();

            // Actualise les métadonnées des variations avec l'ID du produit variable
            $product->sync($product_id);*/

 /*       }
        die;
    }

    // Fonction pour ajouter un nouvel attribut
    static function ajouter_nouvel_attribut($nom_attribut) {
        // Nom de l'attribut
        //var_dump($nom_attribut);

        // Options de l'attribut
        $args = array(
            'slug' => sanitize_title($nom_attribut),
            'name' => $nom_attribut,
            'type' => 'select', // type de champ (select, radio, etc.)
            'order_by' => 'menu_order', // tri des termes
            'has_archives' => true,
        );

        // Ajout de l'attribut
        if (taxonomy_exists('pa_' . $args['slug']) === false) {
            wc_create_attribute($args);
        }
    }
    
    // Fonction pour ajouter des termes à l'attribut
    static function ajouter_termes_a_attribut($nom_attribut, $term) {
        global $wpdb;

        //var_dump($terme);
        // Vérifier si le terme existe déjà
        $term_id = term_exists($term, 'pa_' . sanitize_title($nom_attribut));

        // Si le terme n'existe pas, l'ajouter
        if (!$term_id) {
            // Insérer le terme dans la table 'terms'
            $wpdb->insert(
                $wpdb->terms,
                array(
                    'name' => $term,
                    'slug' => sanitize_title($term),
                )
            );

            // Récupérer l'ID du terme inséré
            $term_id = $wpdb->insert_id;
        }

        //var_dump($term_id);
        //die;
        // Assurez-vous que l'ID du terme est valide
        if ($term_id) {
            // Associer le terme à l'attribut
            wp_set_object_terms($term_id, $term, 'pa_' . sanitize_title($nom_attribut));
            // Mettre à jour le compteur de termes pour cet attribut
            $wpdb->update(
                $wpdb->term_taxonomy,
                array('count' => 1),
                array('term_id' => $term_id)
            );
        }
        
    }
}*/




