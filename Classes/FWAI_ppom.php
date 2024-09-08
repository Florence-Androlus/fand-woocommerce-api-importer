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
   static function update_ppom_field($ppom_id, $zonemarquage) {
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

        // Tableau pour stocker les options de marquage
        $zonemarquage_options = [];
        foreach ($zonemarquage['images'] as $item) {
            $zonemarquage_options[] = [
                "link" => $item['image_url'],
                "id" => "",
                "title" => $item['position_id'],
                "price" => "",
                "stock" => "",
                "url" => ""
            ];
        }
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
                "images" => $zonemarquage_options,
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

        // Tableau pour stocker les options de marquage
        $typemarquage_options = [];
        
        foreach ($zonemarquage['techniques'] as $technique) {

            // Ajouter chaque technique dans le tableau sous forme d'option avec les informations de l'image
            $typemarquage_options[] = [
                'link' => $technique['image_url'],  // Lien de l'image
                'id' =>  $technique['image_id'],     // ID de l'image
                'title' => $technique['technique_id'] , // Nom de la technique (ex: Broderie)
                'price' => '',                 // Laisser vide si non applicable
                'stock' => '',                 // Laisser vide si non applicable
                'url' => ''                    // Laisser vide si non applicable
            ];
            
        }

         // Construction de la structure JSON finale avec les options de marquage et le collapse
        $typedemarquages = [
            '4' => [
                'type' => 'image',
                'title' => 'OPTION DE MARQUAGE',
                'data_name' => 'option_de_marquage',
                'description' => '',
                'error_message' => '',
                'class' => '',
                'width' => '12',
                'selected_img_bordercolor' => '',
                'images' => $typemarquage_options, // Les options de marquage créées précédemment
                'selected' => '',
                'image_width' => '',
                'image_height' => '',
                'min_checked' => '',
                'max_checked' => '',
                'visibility' => 'everyone',
                'visibility_role' => '',
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
                'status' => 'on',
                "ppom_id" => $ppom_id
            ]
        ];

        //$formattedOutput = self::formatPrintData($zonemarquage, $ppom_id);

        //var_dump($formattedOutput);
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
                $merged_data = array_replace($labelzonedemarquages, $zonedemarquages, $labeltypedemarquages,$typedemarquages);
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
            $merged_data = array_replace($labelzonedemarquages, $zonedemarquages, $labeltypedemarquages,$typedemarquages);

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

    static function formatPrintData($inputData, $ppom_id)
    {
        $formattedData = [];
        $counter = 4; // Commence à "4" comme dans l'exemple fourni

        foreach ($inputData['techniques'] as $technique) {
            // Récupérer les informations sur la technique
            $technique_id = $technique['technique_id'];
            $image_id = $technique['image_id'];
            $image_url = $technique['image_url'];

            // Formater les données pour chaque technique
            $formattedData[$counter] = [
                'type' => 'image',
                'title' => self::getTitleFromTechnique($technique_id), // Fonction personnalisée pour générer le titre
                'data_name' => strtolower(str_replace(' ', '_', self::getTitleFromTechnique($technique_id))),
                'description' => '',
                'error_message' => '',
                'class' => '',
                'width' => '12',
                'selected_img_bordercolor' => '',
                'images' => [
                    [
                        'link' => $image_url,
                        'id' => $image_id, 
                        'title' => $technique_id,
                        'price' => '',
                        'stock' => '',
                        'url' => ''
                    ]
                ],
                'selected' => '',
                'image_width' => '',
                'image_height' => '',
                'min_checked' => '',
                'max_checked' => '',
                'visibility' => 'everyone',
                'visibility_role' => '',
                'logic' => 'on',
                "conditions"=>[
                "visibility"=>"Show",
                "bound"=>"Any",
                "rules"=>[[
                        "elements"=>"option",
                        "operators"=>"is",
                        "element_values"=>"FRONT"
                    ],
                    [
                        "elements"=>"option",
                        "operators"=>"is",
                        "element_values"=>"CHEST"
                    ]]
                ],
                'status' => 'on',
                'ppom_id' => $ppom_id
            ];

            $counter++; // Incrémenter pour le prochain élément
        }

        return $formattedData;
    }

    // Fonction pour générer un titre basé sur l'ID de la technique (peut être personnalisée)
    static function getTitleFromTechnique($technique_id)
    {
        $titles = [
            'E' => 'Gravure laser',
            'ST1' => 'Sérigraphie',
            'TDT' => 'Transfert numérique',
            'TT' => 'Tampographie',
            'TR' => 'Thermo-impression'
        ];

        return $titles[$technique_id] ?? 'Technique inconnue';
    }

    // Fonction pour générer un ID unique (basé sur l'URL ou autre méthode)
    static function generateUniqueID($url)
    {
        return crc32($url); // Utilise une simple méthode de hachage pour générer un ID unique
    }

    // Fonction pour générer les règles de visibilité
    static function generateRulesForTechnique($images)
    {
        $rules = [];

        foreach ($images as $image) {
            $rules[] = [
                'elements' => 'option',
                'operators' => 'is',
                'element_values' => $image['position_id'] . '_POS' . $image['position_id']
            ];
        }

        return $rules;
    }




}