<?php
namespace fwai\Classes;
use \WC_Product_Variation;
use \WC_Product_Attribute;

class Colors {
    // Fonction pour ajouter un attribut de couleur à une variation de produit WooCommerce
    public static function add_colors($product_id) {
        // Obtient l'objet produit variable
        $product = wc_get_product($product_id);
        // Récupérer toutes les variations du produit
        $variations = get_posts(array(
            'post_type' => 'product_variation',
            'post_parent' => $product_id,
            'numberposts' => -1
        ));

    // Vérifier si des variations existent
    if ($variations) {
       // echo 'Variations du produit : <br>';

        foreach ($variations as $variation) {
            $variation_id = $variation->ID;
            $variation_data = new WC_Product_Variation($variation_id);

            // Afficher les détails de la variation
        //    echo 'Variation ID : ' . $variation_id . ', SKU : ' . $variation_data->get_sku() . '<br>';
            // Vérifie si l'attribut existe déjà, sinon le crée
            $attribute_name = 'Couleur'; // Nom de l'attribut
                
            // Obtient le nom de la taxonomie associée à l'attribut
            $taxonomy = wc_attribute_taxonomy_name(strtolower($attribute_name));

        

            // Crée le terme 'autre couleur' s'il n'existe pas déjà
        /* if (!term_exists('silver', $taxonomy)) {
                wp_insert_term('silver', $taxonomy);
            }

            // Obtient l'ID du terme 'autre couleur'
            $term_id = get_term_by('slug', 'autre couleur', $taxonomy)->term_id;*/

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

        // Demander à l'utilisateur de saisir l'ID de la variation à supprimer
    /* echo '<form method="post">';
        echo 'Entrez l\'ID de la variation à supprimer : <input type="number" name="variation_id">';
        echo '<input type="submit" value="Supprimer la variation">';
        echo '</form>';

        // Vérifier si l'ID de la variation à supprimer a été soumis
        if (isset($_POST['variation_id'])) {
            $variation_id_to_delete = $_POST['variation_id'];

            // Supprimer la variation
            $deleted = wp_delete_post($variation_id_to_delete, true);

            if ($deleted) {
                echo 'La variation a été supprimée avec succès.';
            } else {
                echo 'Erreur lors de la suppression de la variation.';
            }
        }*/
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
        // Obtient le nom de l'attribut et sa taxonomie
       /* $attribute_name = 'Couleur';
        $taxonomy = wc_attribute_taxonomy_name(strtolower($attribute_name));

        // Vérifie si une variation avec l'attribut 'silver' existe déjà
        $existing_variation = null;
        // Nom de l'attribut et couleur à vérifier
        $attribute_name = 'Couleur';
        $color_to_check = 'silver';

        // Vérifie si une variation avec l'attribut et la couleur spécifiés existe
        $variation_exists = false;
   
        $i=0;
        foreach ($product->get_available_variations() as $variation) {
            if (isset($variation['attributes'][strtolower($attribute_name)]) && $variation['attributes'][strtolower($attribute_name)] == $color_to_check) {
                $variation_exists = true;
                var_dump($variation_exists);
                break;
            }
            $i++;
        }
        var_dump($i);
       // die;
        // Si une variation existe, met à jour ses propriétés
        if ($existing_variation) {
            $existing_variation_id = $existing_variation['variation_id'];
            $variation = new \WC_Product_Variation($existing_variation_id);
            $variation->set_regular_price('10.00'); // Mettez à jour le prix de la variation si nécessaire
            $variation->set_stock_quantity(100); // Mettez à jour la quantité en stock si nécessaire
            $variation->set_manage_stock(true); // Activez ou désactivez la gestion du stock selon vos besoins
            $variation->save();
        } else {
            // Si aucune variation avec l'attribut 'silver' n'existe, crée une nouvelle variation
            $variation = new \WC_Product_Variation();
            $variation->set_regular_price('10.00'); // Spécifiez ici le prix de la nouvelle variation
            $variation->set_stock_quantity(100); // Spécifiez ici la quantité de stock de la nouvelle variation
            $variation->set_manage_stock(true); // Activez la gestion du stock pour la nouvelle variation
            $variation->set_parent_id($product_id);
            $variation->set_attributes(array('pa_' . $attribute_name => 'silver')); // Associe l'attribut à la nouvelle variation
            $variation->save();
        }*/
    }
}




