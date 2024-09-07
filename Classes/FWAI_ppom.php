<?php

namespace fwai\Classes;


class FWAI_ppom{

  // ajout groupe ppom
  static function ppom_exist($productmeta_name)
  {
    global $wpdb;

    // Nom de la table
    $table_name = $wpdb->prefix . 'nm_personalized';

    // Requête SQL pour vérifier si la valeur existe déjà
    // Valeur à rechercher dans la colonne 'productmeta_name'
    $result = $wpdb->prepare("SELECT productmeta_id FROM $table_name WHERE productmeta_name = %s", $productmeta_name);

    // Exécuter la requête
    $ppom_id = $wpdb->get_var($result);
  //  var_dump($ppom_id);
    // Vérifier si la valeur existe déjà
    if ($ppom_id == null) {
      $ppom_id = self::add_groupe($productmeta_name,$table_name);
    }
    return $ppom_id;
  }
  
  // ajout groupe ppom
  static function add_groupe($productmeta_name,$table_name)
  {
    global $wpdb;

         // La valeur n'existe pas encore, vous pouvez insérer les nouvelles données
        // Tableau des données à insérer
        $data = array(
            'productmeta_name' => $productmeta_name,
            'dynamic_price_display' => 'no'
        );

        // Format des données pour insertion sécurisée
        $format = array('%s', '%s'); // %s pour les chaînes de caractères

        // Insérer les données dans la table
        $ppom_id = $wpdb->insert($table_name, $data, $format);

        // Vérifier si l'insertion a réussi
        if ($wpdb->insert_id) {
            echo "Données insérées avec succès!";
            return $ppom_id;
        } else {
            echo "Erreur lors de l'insertion des données.";
        }
    
  }   

   // ajout champs ppom
   static function update_ppom_field($ppom_id, $zonemarquage,$printingtechniques) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nm_personalized';

        $query = $wpdb->prepare(
            "SELECT `the_meta` FROM `$table_name` WHERE `productmeta_id` = %d",
            $ppom_id
        );
        $existing_data = $wpdb->get_var($query);
        $existing_data_array = json_decode($existing_data, true);

        // Label zone de marquages
        $labelzonedemarquages = [
            "1" => [
                "type" => "collapse",
                "title" => "ZONE DE MARQUAGES DU GOODIES PUBLICITAIRE",
                "data_name" => "zone_de_marquages_du_goodies_publicitaire",
                "collapse_type" => "start",
                "conditions" => [
                    "visibility" => "Show",
                    "bound" => "All",
                    "rules" => [
                        [
                            "elements" => "zone_de_marquages_du_goodies_publicitaire",
                            "operators" => "is"
                        ]
                    ]
                ],
                "status" => "on",
                "ppom_id" => $ppom_id
            ]
        ];

        // Ajouter les zones de marquages
        $zonedemarquages = [
            "2" => [
                "type" => "image",
                "title" => "option",
                "data_name" => "option",
                "description" => "",
                "error_message" => "",
                "class" => "",
                "width" => "12",
                "selected_img_bordercolor" => "",
                "images" => [],
                "selected" => "",
                "image_width" => "",
                "image_height" => "",
                "min_checked" => "",
                "max_checked" => "",
                "visibility" => "everyone",
                "visibility_role" => "",
                "conditions" => [
                    "visibility" => "Show",
                    "bound" => "All",
                    "rules" => [
                        [
                            "elements" => "zone_de_marquages_du_goodies_publicitaire",
                            "operators" => "is"
                        ]
                    ]
                ],
                "status" => "on",
                "ppom_id" => $ppom_id
            ]
        ];

        foreach ($zonemarquage as $item) {
            $zonedemarquages["2"]["images"][] = [
                "link" => $item['image_url'],
                "id" => "",
                "title" => $item['position_id'],
                "price" => "",
                "stock" => "",
                "url" => ""
            ];
        }

        $labeltypedemarquages = [
            "3" => [
                "type" => "collapse",
                "title" => "TYPE DE MARQUAGE",
                "data_name" => "type_de_marquage",
                "collapse_type" => "start",
                "conditions" => [
                    "visibility" => "Show",
                    "bound" => "All",
                    "rules" => [
                        [
                            "elements" => "type_de_marquage",
                            "operators" => "is"
                        ]
                    ]
                ],
                "status" => "on",
                "ppom_id" => $ppom_id
            ]
        ];


        // Parcourir chaque technique d'impression
        // on Récupére l'URL de l'image à partir de la bibliothèque des médias
        foreach ($printingtechniques as $technique) {
           // var_dump($technique);
            // ID du fichier média WordPress (l'ID de l'image dans les médias WP)
            $technique_id = $technique['technique_id']; // Cela suppose que tu as l'ID du média lié à ta technique
           // var_dump($technique_id);
            // Récupérer l'URL de l'image à partir de la bibliothèque des médias

            // Recherche de l'image dans la base de données
            $image_url = self::getImageUrlForTechnique($technique_id);
           // var_dump($image_url);
        }
        //die;

        // Vérification de la présence de data_name
        $data_name_to_check = $zonedemarquages[2]['data_name'];
        $data_name_exists = false;

        if (is_array($existing_data_array)) {
            foreach ($existing_data_array as $item) {
                if ($item['data_name'] === $data_name_to_check) {
                    $data_name_exists = true;
                    break;
                }
            }
        }

        if (!$data_name_exists) {
            if (is_array($existing_data_array)) {
                $merged_data = array_replace($labelzonedemarquages, $zonedemarquages, $labeltypedemarquages);
            } else {
                $merged_data = $labelzonedemarquages;
            }

            // Créer un nouveau tableau commençant à l'index 0
            $new_merged_data = array();
            $i = 0;
            foreach ($merged_data as $key => $value) {
                $new_merged_data[$i++] = $value;
            }

            $json_data_string = json_encode($new_merged_data, JSON_FORCE_OBJECT);

            $query_update = $wpdb->prepare(
                "UPDATE `$table_name` SET `the_meta` = %s WHERE `productmeta_id` = %d",
                $json_data_string,
                $ppom_id
            );

            $result_update = $wpdb->query($query_update);

            if ($result_update === false) {
                // Gérer l'erreur
            }
        } else {
            // Mêmes modifications
            $merged_data = array_replace($labelzonedemarquages, $zonedemarquages, $labeltypedemarquages);

            $new_merged_data = array();
            $i = 0;
            foreach ($merged_data as $key => $value) {
                $new_merged_data[$i++] = $value;
            }

            $json_data_string = json_encode($new_merged_data, JSON_FORCE_OBJECT);

            $query_update = $wpdb->prepare(
                "UPDATE `$table_name` SET `the_meta` = %s WHERE `productmeta_id` = %d",
                $json_data_string,
                $ppom_id
            );

            $result_update = $wpdb->query($query_update);
        }
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
    static function getImageUrlByTitle($image_title) {
        global $wpdb;
        
        // Obtenez le lien de l'image via WP Media
        $query = $wpdb->prepare(
            "SELECT guid FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_title = %s",
            $image_title
        );

        $image_url = $wpdb->get_var($query);

        return $image_url;
    }

    static function getImageUrlForTechnique($technique_id) {
        // Étape 1: Obtenez le nom de la technique
        $technique_name = self::getTechniqueNameById($technique_id);
    
        if ($technique_name) {
            // Étape 2: Obtenez l'URL de l'image par titre
            $image_url = self::getImageUrlByTitle($technique_name);
    
            return $image_url;
        }
    
        return null;
    }

}