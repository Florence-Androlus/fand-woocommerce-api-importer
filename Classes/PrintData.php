<?php
namespace fwai\Classes;

class PrintData {

    static public function getPrintData($master_code, $color_code, $apiPrintData)
    {
        $result = [
            'images' => [], // Stocke les données des images filtrées
            'techniques' => [], // Stocke les techniques uniques
            'technique_positions' => [] // Stocke les positions associées aux techniques
        ];

        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {
            if ($master_code === $product['master_code']) {
    
                // Tableau pour stocker les positions et les techniques associées
                $technique_positions = [];
                $unique_techniques = []; // Pour stocker les techniques uniques
    
                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    $position_id = $position['position_id'];
    
                    // Initialiser la structure pour chaque position
                    if (!isset($technique_positions[$position_id])) {
                        $technique_positions[$position_id] = [
                            'rule' => $position_id,
                            'printing_techniques' => []
                        ];
                    }
    
                    // Parcourir les images et récupérer celles correspondant à 'variant_color' = $color_code
                    foreach ($position['images'] as $image) {
                        if ($image['variant_color'] === $color_code) {
                            // Ajout de l'image au résultat
                            $result['images'][] = [
                                'position_id' => $position_id,
                                'image_url' => $image['print_position_image_with_area']
                            ];
                        }
                    }
    
                    // Parcourir les techniques d'impression
                    foreach ($position['printing_techniques'] as $technique) {
                        $technique_id = $technique['id']; // Récupérer l'ID de la technique
    
                        // Ajouter la technique unique si elle n'existe pas encore
                        if (!isset($unique_techniques[$technique_id])) {
                            $image_data = self::getImageDataForTechnique($technique_id);
                            if ($image_data != null) {
                                // Ajouter la technique unique au tableau
                                $unique_techniques[$technique_id] = [
                                    'technique_id' => $technique_id,
                                    'image_id' => $image_data['id'],
                                    'image_url' => $image_data['url'],
                                    'max_colours' => $technique['max_colours']
                                ];
                            }
                        }
    
                        // Assurer que la clé existe dans `technique_positions`
                        if (!isset($technique_positions[$position_id]['printing_techniques'])) {
                            $technique_positions[$position_id]['printing_techniques'] = [];
                        }

                        $apiPrintPriceList = Api::getApiPrintPriceList();
                        //var_dump($apiPrintPriceList['print_techniques']);
                        foreach($apiPrintPriceList['print_techniques'] as $price){
                           // var_dump($price);

                            if($technique_id==$price['id']){
                                $price=$price['setup'];
                                break;
                            }
                        }
                        
                        // Ajouter la technique uniquement si elle n'est pas déjà présente dans 'printing_techniques'
                        $technique_positions[$position_id]['printing_techniques'][] = [
                            'technique_id' => $technique_id,
                            'image_id' => $unique_techniques[$technique_id]['image_id'] ?? '',
                            'image_url' => $unique_techniques[$technique_id]['image_url'] ?? '',
                            'price' => $price
                        ];
                    }
                }

                // Réindexation des positions
                $result['technique_positions'] = array_values($technique_positions);
    
                // Ajouter les techniques uniques au résultat final
                $result['techniques'] = array_values($unique_techniques);
            }
        }
    
        // Débogage pour vérifier le contenu du résultat
        //var_dump($result['technique_positions'][1]['printing_techniques']);
        //die;
    
        return $result; // Retourne les données combinées des images, techniques et positions
    }
 
    


/*    static public function getPrintData($master_code,$color_code,$apiPrintData)
    {
        $result = [
            'images' => [], // Stocke les données des images filtrées
            'techniques' => [], // Stocke les techniques uniques
            'technique_positions' => []
        ];

        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {

            if ($master_code === $product['master_code']) {

                // Tableau associatif pour éviter les doublons de techniques
                $unique_techniques = [];
                // Tableau pour stocker les relations entre les techniques et les positions
                $technique_positions = [];
                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    $position_id = $position['position_id'];

                    // Parcourir les images et récupérer celles correspondant à 'variant_color' = 'AG'
                    foreach ($position['images'] as $image) {
                        if ($image['variant_color'] === $color_code) {
                            $result['images'][] = [
                                'position_id' => $position_id,
                                'image_url' => $image['print_position_image_with_area']
                            ];
                        }
                    }

                    // Parcourir les techniques d'impression
                    foreach ($position['printing_techniques'] as $technique) {
                        // recupere l'id de la technique 
                        $technique_id = $technique['id']; // Récupérer l'ID de la technique (ex: L3, P5)


            
                        // Ajouter la position à la technique
                        $technique_positions[$technique_id]['positions'][] = $position_id;
                        // Ajouter uniquement les techniques uniques basées sur leur ID
                        if (!isset($unique_techniques[$technique['id']])) {
                            $image_data  = self::getImageDataForTechnique($technique_id);
                            if($image_data==null){
                                continue;
                            }
    
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
                $result['technique_positions'] = array_values($technique_positions);
            }
        }
        var_dump($result);
        die;
        return $result; // Retourne les données combinées des images et des techniques
    }*/

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