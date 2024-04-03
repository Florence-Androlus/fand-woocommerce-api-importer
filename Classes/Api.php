<?php

namespace fwai\Classes;

class Api {

    // json produit test
    static function json_product()
    {
        // Chemin vers votre fichier JSON
        $chemin_fichier_json = FWAI_PLUGIN_DIR."produitunique.json";

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
    static function json_stock()
    {
        // Chemin vers votre fichier JSON
        $chemin_fichier_json = FWAI_PLUGIN_DIR."stockunique.json";

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