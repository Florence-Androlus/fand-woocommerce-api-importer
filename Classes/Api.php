<?php

namespace fwai\Classes;

class Api {
    // api test
    static function json_api_test_product()
    {
        ini_set('memory_limit', '256M');
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
}