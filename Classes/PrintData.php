<?php
namespace fwai\Classes;

class PrintData {
    static public function getPrintData($master_code, $apiPrintData)
    {
        $result = []; // Tableau pour stocker les données
        $desired_color = "AG";
        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {

            if ($master_code === $product['master_code']) {

                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    $position_id = $position['position_id'];

                    // Parcourir les images et récupérer uniquement celles qui correspondent à 'variant_color' = 'AQ'
                    foreach ($position['images'] as $image) {
                        if ($image['variant_color'] === $desired_color) {
                            $result[] = [
                                'position_id' => $position_id,
                                'image_url' => $image['print_position_image_with_area']
                            ];
                        }
                    }
                }
            }
        }

        return $result; // Retourner le tableau avec les données filtrées
    }

    static public function getPrintingTechniques($master_code, $apiPrintData)
    {
        $result = []; // Tableau pour stocker les données
    
        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {
            if ($master_code === $product['master_code']) {
    
                // Tableau associatif pour éviter les doublons de techniques
                $unique_techniques = [];
    
                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    // Parcourir les techniques d'impression
                    foreach ($position['printing_techniques'] as $technique) {
                        // Ajouter uniquement les techniques uniques basées sur leur ID
                        if (!isset($unique_techniques[$technique['id']])) {
                            $unique_techniques[$technique['id']] = [
                                'technique_id' => $technique['id'],
                                'max_colours' => $technique['max_colours']
                            ];
                        }
                    }
                }
    
                // Ajouter les techniques uniques au résultat final
                $result = array_values($unique_techniques);
            }
        }
    
        return $result; // Retourner le tableau avec les données filtrées
    }
}