<?php
namespace fwai\Classes;

class PrintData {
    static public function getPrintData($master_code, $apiPrintData)
    {
        $result = []; // Tableau pour stocker les données

        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {

            if ($master_code === $product['master_code']) {

                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    $position_id = $position['position_id'];

                    // Récupérer la première image avec une zone d'impression
                    if (!empty($position['images'])) {
                        $first_image = $position['images'][0]['print_position_image_with_area'];
                        
                        // Ajouter les données au tableau résultat
                        $result[] = [
                            'position_id' => $position_id,
                            'image_url' => $first_image
                        ];
                    }
                }
            }
        }

        return $result; // Retourner le tableau avec les données
    }
    /*static public function getPrintData($master_code,$apiPrintData)
    {
        // Parcourir les produits
        foreach ($apiPrintData['products'] as $product) {

            if ($master_code===$product['master_code']){

                echo "Master Code: " . $master_code . "<br>";

                // Parcourir les positions d'impression
                foreach ($product['printing_positions'] as $position) {
                    $position_id = $position['position_id'];
                    echo "Position ID: " . $position_id . "<br>";

                    // Parcourir les techniques d'impression
                 echo "Printing Techniques:<br>";
                    foreach ($position['printing_techniques'] as $technique) {
                        echo "- ID: " . $technique['id'] . "<br> Max Colors: " . $technique['max_colours'] . "<br>";
                    }

                    // Récupérer la première image avec une zone d'impression
                    if (!empty($position['images'])) {
                        $first_image = $position['images'][0]['print_position_image_with_area'];
                        echo "First Image with Print Area: " . $first_image . "<br>";
                    }

                    echo "<br>";
                }
            }
        }
    }*/
}