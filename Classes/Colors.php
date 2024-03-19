<?php
namespace fwai\Classes;
use \WC_Product_Variation;
use \WC_Product_Attribute;

class Colors {
    // Fonction pour ajouter un attribut de couleur à une variation de produit WooCommerce
    public static function add_colors($product_id) {
        // Obtient l'objet produit variable
        $product = wc_get_product($product_id);
        //var_dump($product);
        //die;
        // Récupérer toutes les variations du produit
        $variations = get_posts(array(
            'post_type' => 'product_variation',
            'post_parent' => $product_id,
            'numberposts' => -1
        ));

    // Vérifier si des variations existent
    if ($variations) {

        foreach ($variations as $variation) {
            $variation_id = $variation->ID;
            $variation_data = new WC_Product_Variation($variation_id);

            // Afficher les détails de la variation
 
            // Vérifie si l'attribut existe déjà, sinon le crée
            $attribute_name = 'Couleur'; // Nom de l'attribut
                
            // Obtient le nom de la taxonomie associée à l'attribut
            $taxonomy = wc_attribute_taxonomy_name(strtolower($attribute_name));

            // Obtient l'objet produit variable
            $product = wc_get_product($product_id);

            // met a jour la variatioi pour l'attribut de couleur 'silver'

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
        else {
           // echo 'Aucune variation trouvée pour ce produit.';
            // Vérifie si l'attribut existe déjà, sinon le crée
            $attribute_name = 'Couleur'; // Nom de l'attribut
                    
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
            $product->sync($product_id);

        }
//die;

    }
}




