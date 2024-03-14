<?php

namespace fwai\Classes;
use fwai\Classes\Readfile;

class FWAI_ATTRIBUT {

    static public function create_attribut($variationId,$variation)
    {
        // Set the variation attributes
        update_post_meta($variationId, '_attributes', array(
            'pa_size_textile' => $variation['color_code'],
            'pa_color' => $variation['color_description']
        ));
        
    }
    static public function update_attribut($variationId,$variation)
    {
        // Set the variation attributes
        update_post_meta($variationId, '_attributes', array(
            'pa_size_textile' => $variation['color_code'],
            'pa_color' => $variation['color_description']
        ));
        
    }

    
    public function update_custom_product_attribute() {
        $attribute_name = 'Nom de l\'attribut';
        $attribute_slug = 'slug-de-l-attribut';
        $attribute_type = 'text'; // ou 'select', 'textarea', etc.
        $attribute_description = 'Description de l\'attribut';

        $attribute_id = term_exists($attribute_name, 'pa_' . $attribute_slug);

        if ($attribute_id === 0 || !$attribute_id) {
            // L'attribut n'existe pas encore, on l'ajoute
            $attribute_id = wp_insert_term(
                $attribute_name,
                'pa_' . $attribute_slug,
                array(
                    'description' => $attribute_description,
                    'slug' => $attribute_slug,
                )
            );

            if (is_wp_error($attribute_id)) {
                // Une erreur s'est produite lors de l'ajout de l'attribut
                // Vous pouvez afficher le message d'erreur ici, si nécessaire
                return;
            }
        }

        // L'attribut existe, on met à jour sa description
        wp_update_term($attribute_id, 'pa_' . $attribute_slug, array(
            'description' => $attribute_description,
        ));

        // Vous pouvez ajouter des termes à l'attribut ici, si nécessaire
    }

}