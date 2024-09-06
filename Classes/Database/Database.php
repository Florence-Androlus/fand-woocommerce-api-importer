<?php

namespace fwai\Classes\Database;

class Database {

    static public function init()
    {
        global $wpdb;

        // Préfixe des tables WordPress
        $table_name = $wpdb->prefix . 'printing_technique_descriptions';
    
        // Structure SQL pour créer la table
        $charset_collate = $wpdb->get_charset_collate();
    
        $sql = "CREATE TABLE $table_name (
            id VARCHAR(10) NOT NULL,
            name VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
    
        // Inclure le fichier qui contient la fonction dbDelta()
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
        // Créer ou mettre à jour la table
        dbDelta($sql);
    }

    static public function insert_printing_techniques($data,$compteur) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'printing_technique_descriptions';
    
        // Parcourir les techniques d'impression
        foreach ($data['printing_technique_descriptions'] as $technique) {
            $id = $technique['id'];
            $name_fr = '';
            
            // Extraire la description en français uniquement
            foreach ($technique['name'] as $entry) {
                if (isset($entry['fr'])) {
                    $name_fr = $entry['fr'];
                    $compteur++;
                    break; // On sort de la boucle une fois qu'on a trouvé la version française
                }
            }
    
            // Vérifier que l'entrée en français existe
            if (!empty($name_fr)) {
                // Insérer les données dans la table
                $wpdb->insert(
                    $table_name,
                    array(
                        'id' => $id,
                        'name' => $name_fr,
                    ),
                    array(
                        '%s', // id
                        '%s', // name (version française)
                    )
                );
            }
        }
        return $compteur;
    }
    
}