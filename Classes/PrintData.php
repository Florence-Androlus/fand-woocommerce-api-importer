<?php
namespace fwai\Classes;

class PrintData {

    static public function getPrintData($master_code, $apiPrintData)
    {
        $result = [
            'images' => [], // Stocke les données des images filtrées
            'techniques' => [] // Stocke les techniques uniques
        ];
        $desired_color = "AG";

        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {

            if ($master_code === $product['master_code']) {

                // Tableau associatif pour éviter les doublons de techniques
                $unique_techniques = [];

                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    $position_id = $position['position_id'];

                    // Parcourir les images et récupérer celles correspondant à 'variant_color' = 'AG'
                    foreach ($position['images'] as $image) {
                        if ($image['variant_color'] === $desired_color) {
                            $result['images'][] = [
                                'position_id' => $position_id,
                                'image_url' => $image['print_position_image_with_area']
                            ];
                        }
                    }

                    // Parcourir les techniques d'impression
                    foreach ($position['printing_techniques'] as $technique) {
                        // Ajouter uniquement les techniques uniques basées sur leur ID
                        if (!isset($unique_techniques[$technique['id']])) {
                            // Recherche de l'image dans la base de données
                            $technique_id=$technique['id'];
                            $image_data  = self::getImageDataForTechnique($technique_id);
                            $unique_techniques[$technique['id']] = [
                                'technique_id' => $technique['id'],
                                'image_id'=>  $image_data['id'],
                                'image_url'=>  $image_data['url'],
                                'max_colours' => $technique['max_colours']
                            ];
                        }
                    }
                }

                // Ajouter les techniques uniques au résultat final
                $result['techniques'] = array_values($unique_techniques);
            }
        }

        return $result; // Retourne les données combinées des images et des techniques
    }

    // Fonction pour obtenir le nom de la technique
    static function getTechniqueNameById($technique_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'printing_technique_descriptions';
        
        $query = $wpdb->prepare(
            "SELECT `name` FROM `$table_name` WHERE `id` = %s",
            $technique_id
        );

        return $wpdb->get_var($query);
    }

    // Fonction pour obtenir l'URL de l'image par titre
    static function getImageDataByTitle($image_title) {
        global $wpdb;
        
        // Obtenez l'ID et le lien de l'image via WP Media
        $query = $wpdb->prepare(
            "SELECT ID, guid FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_title = %s",
            $image_title
        );
    
        // Récupérer l'ID et l'URL de l'image
        $image_data = $wpdb->get_row($query);
    
        // Vérifier si l'image existe
        if ($image_data) {
            return [
                'id' => $image_data->ID,
                'url' => $image_data->guid
            ];
        }
    
        return null;  // Si aucune image trouvée
    }

    static function getImageDataForTechnique ($technique_id) {
        // Étape 1: Obtenez le nom de la technique
        $technique_name = self::getTechniqueNameById($technique_id);
    
        if ($technique_name) {
            // Étape 2: Obtenez l'URL de l'image par titre
            $image_data  = self::getImageDataByTitle($technique_name);
    
            return $image_data ;
        }
    
        return null;
    }

}