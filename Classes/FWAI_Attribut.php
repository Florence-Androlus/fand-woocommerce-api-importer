<?php
namespace fwai\Classes;

class FWAI_ATTRIBUT {

    static public function ajouter_nouvel_attribut($nom_attribut,$attribut_slug)
    {
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
            return wc_create_attribute($attribut);
        }
    }

    // Fonction pour ajouter des termes à l'attribut
    static function ajouter_termes_a_attribut($slug,$term,$description) {

        // Vérifier si le terme existe déjà
        $term_id = term_exists($term, sanitize_title($slug));
        $args=[
            'description'=>$description,
        ];
        // Si le terme n'existe pas, l'ajouter
        if ($term_id==null) {
           return wp_insert_term($term, $slug,$args);
        }

    }

}