<?php

namespace fwai\Classes;


class fand_ppom{

  // ajout groupe ppom
  static function ppom_exist()
  {
    global $wpdb;

    // Valeur à rechercher dans la colonne 'productmeta_name'
    $productmeta_name = 'essaie';

    // Nom de la table
    $table_name = $wpdb->prefix . 'nm_personalized';

    // Requête SQL pour vérifier si la valeur existe déjà
    $result = $wpdb->prepare("SELECT productmeta_id FROM $table_name WHERE productmeta_name = %s", $productmeta_name);

    // Exécuter la requête
    $ppom_id = $wpdb->get_var($result);
  //  var_dump($ppom_id);
    // Vérifier si la valeur existe déjà
    if ($ppom_id == null) {
      $ppom_id = self::add_groupe($productmeta_name,$table_name);
    }
    else{
      self::update_ppom_field($ppom_id);
    }
    
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
   static function update_ppom_field($ppom_id){

   // var_dump($ppom_id);
     // Données à insérer dans le champ meta
        $json_data ='{"1":{"type":"collapse","title":"ZONE DE MARQUAGES DU GOODIES PUBLICITAIRE","data_name":"zone_de_marquages_du_goodies_publicitaire","collapse_type":"start","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"2":{"type":"image","title":"option","data_name":"option","description":"","error_message":"","class":"","width":"12","selected_img_bordercolor":"","images":[{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/AR1249-16_POS1.jpg","id":"2436","title":"AR1249-16_POS1","price":"","stock":"","url":""},{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/imageZone-2249578-1.jpg","id":"2443","title":"imageZone-2249578-1","price":"","stock":"","url":""},{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/AR1249-16_POS3.jpg","id":"2438","title":"AR1249-16_POS3","price":"","stock":"","url":""},{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/AR1249-16_POS4.jpg","id":"2437","title":"AR1249-16_POS4","price":"","stock":"","url":""}],"selected":"","image_width":"","image_height":"","min_checked":"","max_checked":"","visibility":"everyone","visibility_role":"","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"3":{"type":"collapse","title":"TYPE DE MARQUAGE","data_name":"type_de_marquage","collapse_type":"start","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"4":{"type":"image","title":"laser","data_name":"laser","description":"","error_message":"","class":"","width":"12","selected_img_bordercolor":"","images":[{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/L0.webp","id":"2439","title":"L0","price":"","stock":"","url":""}],"selected":"","image_width":"","image_height":"","min_checked":"","max_checked":"","visibility":"everyone","visibility_role":"","logic":"on","conditions":{"visibility":"Show","bound":"Any","rules":[{"elements":"option","operators":"is","element_values":"AR1249-16_POS1"},{"elements":"option","operators":"is","element_values":"AR1249-16_POS3"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"5":{"type":"image","title":"Tampographie","data_name":"tampographie","description":"","error_message":"","class":"","width":"12","selected_img_bordercolor":"","images":[{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/L0.webp","id":"2439","title":"L0","price":"","stock":"","url":""},{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/P0.webp","id":"2442","title":"P0","price":"","stock":"","url":""}],"selected":"","image_width":"","image_height":"","min_checked":"","max_checked":"","visibility":"everyone","visibility_role":"","logic":"on","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"option","operators":"is","element_values":"imageZone-2249578-1"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"6":{"type":"image","title":"Impression num\u00e9rique","data_name":"impression_numerique","description":"","error_message":"","class":"","width":"12","selected_img_bordercolor":"","images":[{"link":"http:\/\/localhost\/ecommerce\/wp-content\/uploads\/2024\/03\/PD1.webp","id":"2441","title":"PD1","price":"","stock":"","url":""}],"selected":"","image_width":"","image_height":"","min_checked":"","max_checked":"","visibility":"everyone","visibility_role":"","logic":"on","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"option","operators":"is","element_values":"AR1249-16_POS4"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"7":{"type":"collapse","title":"NOMBRE DE COULEURS","data_name":"nombre_de_couleurs","collapse_type":"start","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"8":{"type":"select","title":"Nombre de couleurs","data_name":"laser_select","description":"","error_message":"","options":[{"option":"gravure laser","price":"","weight":"","stock":"","id":"gravure_laser"}],"selected":"","first_option":"S\u00e9lectionner votre choix","class":"","width":"12","visibility":"everyone","visibility_role":"","logic":"on","conditions":{"visibility":"Show","bound":"Any","rules":[{"elements":"laser","operators":"is","element_values":"L0"},{"elements":"tampographie","operators":"is","element_values":"L0"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"9":{"type":"select","title":"Nombre de couleurs","data_name":"tampographie_select","description":"","error_message":"","options":[{"option":"1","price":"","weight":"","stock":"","id":"1"}],"selected":"","first_option":"S\u00e9lectionner votre choix","class":"","width":"12","visibility":"everyone","visibility_role":"","logic":"on","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"tampographie","operators":"is","element_values":"P0"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"10":{"type":"select","title":"Nombre de couleurs","data_name":"quadrichromie_select","description":"","error_message":"","options":[{"option":"Quadrichromie","price":"","weight":"","stock":"","id":"quadrichromie"}],"selected":"","first_option":"S\u00e9lectionner votre choix","class":"","width":"12","visibility":"everyone","visibility_role":"","logic":"on","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"impression_numerique","operators":"is","element_values":"PD1"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"11":{"type":"collapse","title":"VOTRE FICHIER","data_name":"votre_fichier","collapse_type":"start","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"12":{"type":"textarea","title":"Commentaires","data_name":"commentaires_gravure","description":"","placeholder":"","error_message":"","default_value":"","max_length":"","price":"","class":"","width":"12","visibility":"everyone","visibility_role":"","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"13":{"type":"section","data_name":"et\/ou","width":"12","description":"","html":"et\/ou","visibility":"everyone","visibility_role":"","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"14":{"type":"file","title":"votre fichier ou logo d\'entreprise","data_name":"votre_fichier_ou_logo_d_entreprise","description":"","error_message":"","file_cost":"","class":"","width":"12","button_label_select":"","button_class":"","files_allowed":"","file_types":"jpg,jpeg,gif","file_size":"10mb","min_img_h":"","max_img_h":"","min_img_w":"","max_img_w":"","img_dimension_error":"","visibility":"everyone","visibility_role":"","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"15":{"type":"collapse","title":"COMMENTAIRES","data_name":"commentaires","collapse_type":"start","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"16":{"type":"textarea","title":"Commentaires","data_name":"commentaires_textarea","description":"","placeholder":"","error_message":"","default_value":"","max_length":"","price":"","class":"","width":"12","visibility":"everyone","visibility_role":"","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"17":{"type":"collapse","title":"fin","data_name":"fin","collapse_type":"end","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"off","ppom_id":"' . $ppom_id . '"}}';
      
    if (!is_wp_error($ppom_id)) {
      global $wpdb;
      $table_name = $wpdb->prefix . 'nm_personalized';
 // Requête SQL pour vérifier si la valeur existe déjà
 $query = $wpdb->prepare(
  "SELECT `the_meta` FROM `$table_name` WHERE `productmeta_id` = %d",
  $ppom_id
);
//var_dump($query);
 // Exécuter la requête
 $ppom_meta = $wpdb->get_var($query);
 //var_dump($ppom_meta);

      // Insérer les données dans le champ meta
    //  $json_data = '{"1":{"type":"collapse","title":"ZONE DE MARQUAGES DU GOODIES PUBLICITAIRE","data_name":"zone_de_marquages_du_goodies_publicitaire","collapse_type":"start","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"},"2":{"type":"image","title":"option","data_name":"option","description":"","error_message":"","class":"","width":"12","selected_img_bordercolor":"","images":[{"link":"http://localhost/ecommerce/wp-content/uploads/2024/03/AR1249-16_POS1.jpg","id":"2436","title":"AR1249-16_POS1","price":"","stock":"","url":""},{"link":"http://localhost/ecommerce/wp-content/uploads/2024/03/imageZone-2249578-1.jpg","id":"2443","title":"imageZone-2249578-1","price":"","stock":"","url":""},{"link":"http://localhost/ecommerce/wp-content/uploads/2024/03/AR1249-16_POS3.jpg","id":"2438","title":"AR1249-16_POS3","price":"","stock":"","url":""},{"link":"http://localhost/ecommerce/wp-content/uploads/2024/03/AR1249-16_POS4.jpg","id":"2437","title":"AR1249-16_POS4","price":"","stock":"","url":""}],"selected":"","image_width":"","image_height":"","min_checked":"","max_checked":"","visibility":"everyone","visibility_role":"","conditions":{"visibility":"Show","bound":"All","rules":[{"elements":"zone_de_marquages_du_goodies_publicitaire","operators":"is"}]},"status":"on","ppom_id":"' . $ppom_id . '"}}';
  //  $result = $wpdb->prepare("UPDATE `wp_nm_personalized` SET `the_meta` = '{"2": {"type":"collapse", "title":"fin", "data_name":"fin", "collapse_type":"end", "conditions": {"visibility":"Show", "bound":"All", "rules": [ {"elements":"zone_de_marquages_du_goodies_publicitaire", "operators":"is" } ] }, "status":"off", "ppom_id":"' . $ppom_id . '" } }' WHERE `wp_nm_personalized`.`productmeta_id` = 5");
    // Exécuter la requête
 //   $ppom_id = $wpdb->get_var($result);
// Convertir le tableau en chaîne JSON
//$json_data_string = json_encode($new_merged_data, JSON_FORCE_OBJECT);
$productmeta_created = date('Y-m-d H:i:s');
// Mettre à jour la base de données avec les nouvelles données JSON
$query_update = $wpdb->prepare(
    "UPDATE `$table_name` SET `the_meta` = %s, `productmeta_created` = %s WHERE `productmeta_id` = %d",
    $json_data,
    $productmeta_created,
    $ppom_id
);

$result_update = $wpdb->query($query_update);
//      var_dump($result_update);
  

    if ($result_update === false) {
        // Une erreur s'est produite lors de l'exécution de la requête SQL
        // Gérer l'erreur ici
    } else {
        // La requête SQL a été exécutée avec succès
        // Faire quelque chose ici si nécessaire
    }
   }
}
}