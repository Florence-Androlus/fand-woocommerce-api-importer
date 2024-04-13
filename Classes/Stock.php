<?php
namespace fwai\Classes;

class Stock {

    public static function get_stock($sku) {

        //var_dump($sku);
        //recupere le stock via Sku
        $data_stock = router::getApiStock();
        // Rechercher le SKU dans la liste de stocks
        $quantite = null;
        foreach ($data_stock['stock'] as $item) {
            if ($item['sku'] === $sku) {
                $quantite = $item['qty'];
                break;
            }
        }

        // Afficher la quantité si elle a été trouvée
        if ($quantite !== null) {
        //    echo "Quantité pour le SKU $sku : $quantite";
            return $quantite;
        } else {
        //    echo "Aucun élément trouvé pour le SKU $sku";
        }
    }

}