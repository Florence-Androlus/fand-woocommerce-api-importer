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
        if ($productmeta_name != null) {
        // Insérer les données dans la table
        $ppom_id = $wpdb->insert($table_name, $data, $format);
        }
        // Vérifier si l'insertion a réussi
        if ($wpdb->insert_id) {
            echo "Données insérées avec succès!".$productmeta_name;
            return $ppom_id;
        } else {
            echo "Erreur lors de l'insertion des données.".$productmeta_name;
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

        /*if ($existing_data != null) {
        $existing_data_array = json_decode($existing_data, true);
        }*/

        $counter=1;
        // Label zone de marquages
        $labelzonedemarquages = [
            $counter => [
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

        $counter++;
        // Ajouter les zones de marquages
        $zonedemarquages = [
            $counter => [
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

        $counter++;

        /*$labeltypedemarquages = [
            $counter => [
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
        $counter++;*/
        $result=self::TechniquePrintData($zonemarquage,$counter,$ppom_id);

        $typedemarquages=$result['typedemarquages'];
        if($typedemarquages==null){
            echo $typedemarquages ."est null";
        }else{
            $counter=$result['counter'];

        /* $labelnombredecouleurs = [
                $counter => [
                    "type" => "collapse",
                    "title" => "NOMBRE DE COULEURS",
                    "data_name" => "nombre_de_couleurs",
                    "collapse_type" => "start",
                    "conditions" => [
                        "visibility" => "Show",
                        "bound" => "All",
                        "rules" => [
                            [
                                "elements" => "nombre_de_couleurs",
                                "operators" => "is"
                            ]
                        ]
                    ],
                    "status" => "on",
                    "ppom_id" => $ppom_id
                ]
            ];
            $counter++;
            */
            
            $result=self::formatPrintData($zonemarquage['techniques'],$counter,$ppom_id);
            
            $nombrecouleurs_options=$result['nombrecouleurs_options'];

            $counter=$result['counter'];

            $labelfichier = [
                $counter=>
                [
                    "type"=>"collapse",
                    "title"=>"VOTRE FICHIER",
                    "data_name"=>"votre_fichier",
                    "collapse_type"=>"start",
                    "conditions"=>
                    [
                        "visibility"=>"Show",
                        "bound"=>"All",
                        "rules"=>
                        [
                            [
                                "elements"=>"zone_de_marquages_du_goodies_publicitaire",
                                "operators"=>"is"
                                ]
                        ]
                    ],
                    "status"=>"on",
                    "ppom_id"=>$ppom_id
                ]        
            ];
            $counter++;
            $Commentaire = [
                $counter=>
                [
                    "type"=>"textarea",
                    "title"=>"Commentaires",
                    "data_name"=>"commentaires_gravure",
                    "description"=>"",
                    "placeholder"=>"",
                    "error_message"=>"",
                    "default_value"=>"",
                    "max_length"=>"",
                    "price"=>"",
                    "class"=>"",
                    "width"=>"12",
                    "visibility"=>"everyone",
                    "visibility_role"=>"",
                    "conditions"=>
                    [
                        "visibility"=>"Show",
                        "bound"=>"All",
                        "rules"=>
                        [
                            [
                                "elements"=>"zone_de_marquages_du_goodies_publicitaire",
                                "operators"=>"is"
                                ]
                        ]
                    ],
                    "status"=>"on",
                    "ppom_id"=>$ppom_id
                ]        
            ];
            $counter++;
            $section = [
                $counter=>
                [
                    "type"=>"section",
                    "data_name"=>"et/ou",
                    "width"=>"12",
                    "description"=>"",
                    "html"=>"et/ou",
                    "visibility"=>"everyone",
                    "visibility_role"=>"",
                    "conditions"=>
                    [
                        "visibility"=>"Show",
                        "bound"=>"All",
                        "rules"=>
                        [
                            [
                                "elements"=>"zone_de_marquages_du_goodies_publicitaire",
                                "operators"=>"is"
                                ]
                        ]
                    ],
                    "status"=>"on",
                    "ppom_id"=>$ppom_id
                ]
            ];
            $counter++;
            $file = [
                $counter=>
                [
                    "type"=>"file",
                    "title"=>"votre fichier ou logo d'entreprise",
                    "data_name"=>"votre_fichier_ou_logo_d_entreprise",
                    "description"=>"",
                    "error_message"=>"",
                    "file_cost"=>"",
                    "class"=>"",
                    "width"=>"12",
                    "button_label_select"=>"",
                    "button_class"=>"",
                    "files_allowed"=>"",
                    "file_types"=>"jpg,jpeg,gif",
                    "file_size"=>"10mb",
                    "min_img_h"=>"",
                    "max_img_h"=>"",
                    "min_img_w"=>"",
                    "max_img_w"=>"",
                    "img_dimension_error"=>"",
                    "visibility"=>"everyone",
                    "visibility_role"=>"",
                    "conditions"=>
                    [
                        "visibility"=>"Show",
                        "bound"=>"All",
                        "rules"=>
                        [
                            [
                                "elements"=>"zone_de_marquages_du_goodies_publicitaire",
                                "operators"=>"is"
                                ]
                        ]
                    ],
                    "status"=>"on",
                    "ppom_id"=>$ppom_id
                ]            
            ];
            $counter++;
            $commentaires = [
                $counter=>
                [
                    "type"=>"collapse",
                    "title"=>"COMMENTAIRES",
                    "data_name"=>"commentaires",
                    "collapse_type"=>"start",
                    "conditions"=>
                    [
                        "visibility"=>"Show",
                        "bound"=>"All",
                        "rules"=>
                        [
                            [
                                "elements"=>"zone_de_marquages_du_goodies_publicitaire",
                                "operators"=>"is"
                            ]
                        ]
                    ],
                    "status"=>"on",
                    "ppom_id"=>$ppom_id
                ]
            ];
            $counter++;
            $commentaires_textarea = [
                $counter=>
                [
                    "type"=>"textarea",
                    "title"=>"Commentaires",
                    "data_name"=>"commentaires_textarea",
                    "description"=>"",
                    "placeholder"=>"",
                    "error_message"=>"",
                    "default_value"=>"",
                    "max_length"=>"",
                    "price"=>"",
                    "class"=>"",
                    "width"=>"12",
                    "visibility"=>"everyone",
                    "visibility_role"=>"",
                    "conditions"=>
                    [
                        "visibility"=>"Show",
                        "bound"=>"All",
                        "rules"=>
                        [
                            [
                                "elements"=>"zone_de_marquages_du_goodies_publicitaire",
                                "operators"=>"is"
                            ]
                        ]
                    ],
                    "status"=>"on",
                    "ppom_id"=>$ppom_id
                ]
            ];

            // Mêmes modifications
            $merged_data = array_replace($labelzonedemarquages, $zonedemarquages,$typedemarquages,$nombrecouleurs_options,$labelfichier,$Commentaire,$section,$file,$commentaires,$commentaires_textarea);
            //  , $labeltypedemarquages,$labelnombredecouleurs,
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


    static function TechniquePrintData($zonemarquage, $counter, $ppom_id)
    {
        // Initialiser la variable
        $typedemarquages = [];
        //var_dump($zonemarquage);
        $technique_positions=$zonemarquage['technique_positions'];

        foreach ($technique_positions as $item) {
            //$typedemarquages[$counter]=[];
            // Extraire les règles et images
            $rules[] = [
                "elements" => "option",
                "operators" => "is",
                "element_values"=>$item['rule']
            ];
            $typemarquage_options = [];
            
            foreach ($item['printing_techniques'] as $technique) {
                //var_dump($technique);
                //die;
                // Vérifier si image_id et image_url ne sont pas vides
                if (!empty($technique['image_id']) && !empty($technique['image_url'])) {
                    //$techniqueprice=$technique['price'];
                    //$techniqueprice=floatval($technique['price']);
                    $typemarquage_options[] = [
                        'link' => $technique['image_url'],  // Lien de l'image
                        'id' => $technique['image_id'],     // ID de l'image
                        'title' => $technique['technique_id'], // Nom de la technique (ex: Broderie)
                        'price' => '',//$techniqueprice,  // Laisser vide si non applicable
                        'stock' => '',  // Laisser vide si non applicable
                        'url' => ''     // Laisser vide si non applicable
                    ];
                }
            }
           
            // Construire la structure JSON pour chaque technique
            $typedemarquages[$counter] = [
                'type' => 'image',
                'title' => 'TYPE DE MARQUAGE',
                'data_name' => 'option_de_marquage',
                'description' => '',
                'error_message' => '',
                'class' => '',
                'width' => '12',
                'selected_img_bordercolor' => '',
                'images' => $typemarquage_options,  // Utiliser les images associées
                'selected' => '',
                'image_width' => '',
                'image_height' => '',
                'min_checked' => '',
                'max_checked' => '',
                'visibility' => 'everyone',
                'visibility_role' => '',
                'logic' => 'on',
                'conditions' => [
                    'visibility' => 'Show',
                    'bound' => 'Any',
                    'rules' => $rules  // Ajouter les règles ici
                ],
                'status' => 'on',
                'ppom_id' => $ppom_id  // Assurez-vous que $ppom_id est défini
            ];
            $rules=[];
            $counter++;
        }

        //var_dump($typedemarquages);
        //die;
        // Retourner le tableau contenant typedemarquages et counter
        return [
            'typedemarquages' => $typedemarquages,
            'counter' => $counter
        ];
    }

    static function formatPrintData($zonemarquage,$counter, $ppom_id)
    {
        $formattedData = [];
        // Tableau pour stocker les options de marquage
        $nombrecouleurs_options = [];

        foreach ($zonemarquage as $technique) {
            //var_dump($technique);
            // Récupérer les informations sur la technique
            $technique_id = $technique['technique_id'];
            if($technique['technique_id']=="PD1"||$technique['technique_id']=="PD2"||$technique['technique_id']=="PD3"||$technique['technique_id']=="PD4"||$technique['technique_id']=="PD5"||$technique['technique_id']=="PD6"||$technique['technique_id']=="PD7"||$technique['technique_id']=="DO1"){
                $nombrecouleurs_options[] = [
                    "option"=>"Quadrichromie",
                    "price"=>"",// Laisser vide si non applicable
                    "weight"=>"",// Laisser vide si non applicable
                    "stock"=>"",// Laisser vide si non applicable
                    "id"=>"Quadrichromie"            
                ];
            }            
            elseif($technique['technique_id']=="TD"||$technique['technique_id']=="TD1"){
                $nombrecouleurs_options[] = [
                    "option"=>"Quadrichromie avec à plat",
                    "price"=>"",// Laisser vide si non applicable
                    "weight"=>"",// Laisser vide si non applicable
                    "stock"=>"",// Laisser vide si non applicable
                    "id"=>"quadrichromieavecaplat"            
                ];
            }
            else{

            $i = 1;
            while ($i <= $technique['max_colours']) {
               // Ajouter chaque technique dans le tableau sous forme d'option avec les informations de l'image
               $nombrecouleurs_options[] = [
                   "option"=>$i,
                   "price"=>"",// Laisser vide si non applicable
                   "weight"=>"",// Laisser vide si non applicable
                   "stock"=>"",// Laisser vide si non applicable
                   "id"=>$i            
               ];
               $i++;
           }
        }
           //var_dump($nombrecouleurs_options);
            // Formater les données pour chaque technique
            $formattedData[$counter] = [
                "type"=>"select",
                "title"=>"Nombre de couleurs",
                "data_name"=>"nombres_de_couleurs",
                "description"=>"",
                "error_message"=>"",
                "options"=>$nombrecouleurs_options,
                "selected"=>"Sélectionner votre choix",
                "first_option"=>"Sélectionner votre choix",
                "class"=>"",
                "width"=>"12",
                "visibility"=>"everyone",
                "visibility_role"=>"",
                "logic"=>"on",
                "conditions"=>[
                    "visibility"=>"Show",
                    "bound"=>"Any",
                    "rules"=>[
                        [
                            "elements"=>"option_de_marquage",
                            "operators"=>"is",
                            "element_values"=> $technique_id
                        ]
                    ]
                ],
                "status"=>"on",
                "ppom_id"=>$ppom_id
            ];
            $nombrecouleurs_options = [];
            $counter++; // Incrémenter pour le prochain élément
        }

        return [
            'nombrecouleurs_options'=>$formattedData,
            'counter' => $counter
        ];
    }

    // Fonction pour générer un titre basé sur l'ID de la technique (peut être personnalisée)
    static function getTitleFromTechnique($technique_id)
    {

        // Globaliser l'objet wpdb pour accéder à la base de données
        global $wpdb;

        // Définir la table et l'ID pour lequel vous souhaitez récupérer la description
        $table_name = $wpdb->prefix . 'printing_technique_descriptions';
        
        // Préparer la requête pour récupérer la description correspondant à l'ID
        $description = $wpdb->get_var( $wpdb->prepare(
            "SELECT name FROM $table_name WHERE id = %s", $technique_id
        ));

        return $description ?? 'Technique inconnue';
    }

}