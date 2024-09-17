<?php

namespace fwai\Classes;

class Api {
    // Définir les propriétées statiques pour stocker les données de l'API
    static private $apiData = null;
    static private $apiStock = null;
    static private $apiPrice = null;
    static private $apiPrintData = null;
    static private $apiPrintPriceList = null;
    

    // Méthode pour obtenir les données Produits de l'API
    // Ajout des paramètres limit et offset à la méthode getApiData
    static public function getApiData($limit = null, $offset = null) {
        // Si l'API a déjà été initialisée
        if (self::$apiData !== null) {
            // Si un limit est défini, appliquer la pagination
            if ($limit !== null && $offset !== null) {
                // Retourne seulement une portion de l'array en fonction du limit et de l'offset
                return array_slice(self::$apiData, $offset, $limit);
            }
            // Retourner toutes les données stockées si pas de pagination
            return self::$apiData;
        }
        return []; // Retourner un tableau vide si les données ne sont pas encore disponibles
    }

    // Méthode pour obtenir les données du stock de l'API
    static public function getApiStock() {
        // Retourner les données stockées
        return self::$apiStock;
    }
    // Méthode pour obtenir les données du prix de l'API
    static public function getApiPrice() {
        // Retourner les données stockées
        return self::$apiPrice;
    }
    // Méthode pour obtenir les données printable de l'API
    static public function getApiPrintData() {
        // Retourner les données stockées
        return self::$apiPrintData;
    }
    // Méthode pour obtenir les données printable de l'API
    static public function getApiPrintPriceList() {
        // Retourner les données stockées
        return self::$apiPrintPriceList;
    }

    // récupéres les données de l'API 
    static function init(){
        
        // Si non, récupérer les données de l'API et les stocker dans la propriété statique
        /*$file = "produituniqueP.json";//"produits.json";//
        self::$apiData = Api::json_product($file);*/
        //self::$apiData = Api::json_api_test_product();
        self::$apiData = Api::json_api_product();

        // Si non, récupérer les données de l'API et les stocker dans la propriété statique
        // Chemin vers votre fichier JSON
        /*$file = "stock.json";//"stockunique.json";
        self::$apiStock = Api::json_stock($file);*/
        //self::$apiStock = Api::json_api_test_stock();
        self::$apiStock = Api::json_api_stock();

        // Si non, récupérer les données de l'API et les stocker dans la propriété statique
        // Chemin vers votre fichier JSON
        /*$file = "printpricelist.json";//"stockunique.json";
        self::$apiPrice = Api::json_price($file);*/
        //self::$apiPrice = Api::pricelist();
        self::$apiPrice = Api::json_api_pricelist();

        // Si non, récupérer les données de l'API et les stocker dans la propriété statique
        // Chemin vers votre fichier JSON
       /* $file = "printdata.json";//"stockunique.json";
        self::$apiPrintData = Api::json_printdata($file);*/
        //self::$apiPrintData = Api::json_api_test_printdata();
        self::$apiPrintData = Api::json_api_printdata();

        // Si non, récupérer les données de l'API et les stocker dans la propriété statique
        // Chemin vers votre fichier JSON
       /* $file = "printdata.json";//"stockunique.json";
        self::$apiPrintData = Api::json_printdata($file);*/
        //self::$apiPrintData = Api::json_api_test_printdata();
        self::$apiPrintPriceList = Api::json_api_printpricelist();

    }

    // json produit test
    static function json_product($file)
    {
        // Chemin vers votre fichier JSON
        $chemin_fichier_json = FWAI_PLUGIN_DIR.$file;

        // Vérification de l'existence du fichier
        if (file_exists($chemin_fichier_json)) {
            // Lecture du contenu du fichier JSON
            $body = file_get_contents($chemin_fichier_json);

            // Décodage du JSON en tableau associatif
            $data = json_decode($body, true);

            // retour fichier json 
             return $data;
        }
    }

    // json stock test
    static function json_stock($file)
    {
        // Chemin vers votre fichier JSON
        $chemin_fichier_json = FWAI_PLUGIN_DIR.$file;

        // Vérification de l'existence du fichier
        if (file_exists($chemin_fichier_json)) {
            // Lecture du contenu du fichier JSON
            $body = file_get_contents($chemin_fichier_json);

            // Décodage du JSON en tableau associatif
            $data = json_decode($body, true);

            // retour fichier json 
            return $data;
        }
    }

    // json price test
    static function json_price($file)
    {
        // Chemin vers votre fichier JSON
        $chemin_fichier_json = FWAI_PLUGIN_DIR.$file;

        // Vérification de l'existence du fichier
        if (file_exists($chemin_fichier_json)) {
            // Lecture du contenu du fichier JSON
            $body = file_get_contents($chemin_fichier_json);

            // Décodage du JSON en tableau associatif
            $data = json_decode($body, true);

            // retour fichier json 
            return $data;
        }
    }

    // json printdata test
    static function json_printdata($file)
    {
        // Chemin vers votre fichier JSON
        $chemin_fichier_json = FWAI_PLUGIN_DIR.$file;
        //var_dump($chemin_fichier_json);
        // Vérification de l'existence du fichier
        if (file_exists($chemin_fichier_json)) {
            // Lecture du contenu du fichier JSON
            $body = file_get_contents($chemin_fichier_json);

            // Décodage du JSON en tableau associatif
            $data = json_decode($body, true);
            //var_dump($data);
            //die;
            // retour fichier json 
            return $data;
        }
    }


    // api test
    static function json_api_test_product()
    {
        // recuperation des données via l'api
        $url = 'https://apitest.midocean.com/gateway/products/2.0?language=fr';
            $headers = array(
                'x-Gateway-APIKey' => '79e92b0e-680d-4eef-a972-513ac187d9a0',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

    static function json_api_test_stock()
    {
        // recuperation des données via l'api
        $url = 'https://apitest.midocean.com/gateway/stock/2.0';
            $headers = array(
                'x-Gateway-APIKey' => '79e92b0e-680d-4eef-a972-513ac187d9a0',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

    static function json_api_test_printpricelist()
    {
        // recuperation des données via l'api
        $url = 'https://apitest.midocean.com/gateway/printpricelist/2.0';
            $headers = array(
                'x-Gateway-APIKey' => '79e92b0e-680d-4eef-a972-513ac187d9a0',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

    static function json_api_test_printdata()
    {
        // recuperation des données via l'api
        $url = 'https://apitest.midocean.com/gateway/printdata/1.0';
            $headers = array(
                'x-Gateway-APIKey' => '79e92b0e-680d-4eef-a972-513ac187d9a0',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

    static function json_api_product()
    {
        // recuperation des données via l'api
        $url = 'https://api.midocean.com/gateway/products/2.0?language=fr';
            $headers = array(
                'x-Gateway-APIKey' => 'fc72edb7-5123-4293-a612-db1cb6797576',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }

    }

    static function json_api_stock()
    {
        // recuperation des données via l'api
        $url = 'https://api.midocean.com/gateway/stock/2.0';
            $headers = array(
                'x-Gateway-APIKey' => 'fc72edb7-5123-4293-a612-db1cb6797576',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

    static function json_api_pricelist()
    {
        // recuperation des données via l'api
        $url = 'https://api.midocean.com/gateway/pricelist/2.0/';
            $headers = array(
                'x-Gateway-APIKey' => 'fc72edb7-5123-4293-a612-db1cb6797576',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }

    }
    
    static function json_api_printdata()
    {
        // recuperation des données via l'api
        $url = 'https://api.midocean.com/gateway/printdata/1.0';
            $headers = array(
                'x-Gateway-APIKey' => 'fc72edb7-5123-4293-a612-db1cb6797576',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

    static function json_api_printpricelist()
    {
        // recuperation des données via l'api
        $url = 'https://api.midocean.com/gateway/printpricelist/2.0';
            $headers = array(
                'x-Gateway-APIKey' => 'fc72edb7-5123-4293-a612-db1cb6797576',
                'Accept' => 'text/json'
            );

            $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 20));

            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
               // retour fichier json 
                return $data;
            }
    }

}