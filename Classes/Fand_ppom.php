<?php

namespace fwai\Classes;
use \NM_PersonalizedProduct;

class fand_ppom{
// ajout groupe ppom
static function add_groupe()
{
    $json_data = '{
        "groups": [
          {
            "name": "Group 1",
            "description": "Description du groupe 1",
            "fields": [
              {
                "type": "text",
                "title": "Champ de texte 1",
                "name": "text_field_1"
              },
              {
                "type": "checkbox",
                "title": "Case à cocher 1",
                "name": "checkbox_1"
              }
            ]
          },
          {
            "name": "Group 2",
            "description": "Description du groupe 2",
            "fields": [
              {
                "type": "select",
                "title": "Sélection 1",
                "name": "select_1",
                "options": ["Option 1", "Option 2", "Option 3"]
              }
            ]
          }
        ]
      }';
      
    $data = json_decode( $json_data, true );

        // Vérifiez si PPOM est actif
        if ( class_exists( 'NM_PersonalizedProduct' ) ) {
            // Obtenez l'instance de l'objet PPOM
            $ppom_instance = NM_PersonalizedProduct::get_instance();
        
            // Parcourez chaque groupe dans le JSON
            foreach ( $data['groups'] as $group ) {
                // Ajoutez le groupe
                $group_id = $ppom_instance->add_group( $group['name'], $group['description'] );
        
                // Parcourez chaque champ dans le groupe
                foreach ( $group['fields'] as $field ) {
                    // Ajoutez le champ au groupe
                    $ppom_instance->add_field( $group_id, $field['type'], $field['title'], $field['name'], $field['options'] );
                }
            }
        }
	}   
}