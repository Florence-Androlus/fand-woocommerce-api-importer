<?php

namespace fwai\Classes;



class Price {



    public static function get_price($sku) {



        //var_dump($sku);

        //recupere le price via Sku

        $data_price = router::getApiPrice();
      //  var_dump( $data_price);
        // Rechercher le SKU dans la liste de prices

        $price = null;

        foreach ($data_price['price'] as $item) {
          //  var_dump( $item);

            if ($item['sku'] === $sku) {

                $price = $item['price'];
             //   var_dump( $price);

                break;

            }

        }



        // Afficher le prix si elle a été trouvée

        if ($price !== null) {

        //    echo "Prix pour le SKU $sku : $price";

            return $price;

        } else {

        //    echo "Aucun élément trouvé pour le SKU $sku";

        }

    }



}