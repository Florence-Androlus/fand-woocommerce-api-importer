<?php

namespace fwai\Classes;

class Categorys {

    static function add_categorys($product_id, $variant, $category_keys){
        // Afficher les catégories avec une boucle foreach
       // var_dump("Catégories:\n");
        //crée le tableau des categories du produit
        $categories = array();
        foreach ($category_keys as $key) {
         //   var_dump($variant[$key]);
            $category_name = $variant[$key];
            $category_slug = sanitize_title($category_name);

            // Vérifier si la catégorie existe déjà
            $category_id = term_exists($category_name, 'product_cat');

            if (is_array($category_id)) {
                // La catégorie existe déjà
                $category_id = $category_id['term_id'];
            } else {
                // La catégorie n'existe pas, la créer
                $category_id = wp_insert_term(
                    $category_name,
                    'product_cat',
                    array(
                        'slug' => $category_slug,
                    )
                );

                if (is_wp_error($category_id)) {
                    // Erreur lors de la création de la catégorie
                    echo "Erreur lors de la création de la catégorie : " . $category_id->get_error_message() . "\n";
                    return;
                }
            }

            // Ajouter la catégorie à la liste des catégories
            $categories[] = $category_name;
        }

        //var_dump($categories);
        //die;
        // La catégorie existe ou a été créée, associer les produits à toutes les catégories
        wp_set_object_terms($product_id, $categories, 'product_cat');
    }
}
