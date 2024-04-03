<?php

namespace fwai;

use fwai\Classes\Database;
use fwai\Classes\Products;
use fwai\Classes\Router;

class fwaiSettingsPage {

    public function __construct() {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1'); 
     //   add_action( 'init', [$this,'woocommerce_api_import_products'] );
		// Register the settings page.
		add_action( 'admin_menu', [$this, 'register_settings' ] );
		// on ajoute nos URL custom
		add_action('init', [$this, 'registerCustomRewrites']);
    }
	
	// Fonction ajout des URL custom
    public function registerCustomRewrites()
    {
        Router::init();
    }

    // Register settings.
	public function register_settings(){
		add_menu_page(
			'fwai-settings', // The title of your settings page.
			'fwaiProducts', // The name of the menu item.
			'manage_options', // The capability required for this menu to be displayed to the user.
			'fwai-settings', // The slug name to refer to this menu by (should be unique for this menu).
			array( $this, 'render_settings_page' ), // The callback function used to render the settings page.
			'dashicons-database-remove', // The icon to be used for this menu.
			59 // The position in the menu order this one should appear.
		);
	}


	// Render the settings page.
	public function render_settings_page(){
		wp_enqueue_style('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
		$compteur = isset($_GET['compteur']) && is_numeric($_GET['compteur']) ? intval($_GET['compteur']) : 0;
        $action = isset($_GET['action']) ? $_GET['action'] : '';
?>
		<div style="margin-top:5em;">
			<ul class="nav nav-tabs" role="tablist">
			<!--li class="nav-item">
				<a id="li-add" class="nav-link active" href="#add" role="tab" data-toggle="tab">Ajouter</a>
			</li>
			<li class="nav-item">
				<a id="li-delete" class="nav-link" href="#delete" role="tab" data-toggle="tab">Supprimer</a>
			</li-->			
			<li style="display: none;">
				<a href="https://fan-develop.fr/woo-update-products/" >Création de plugin custom php MySQL Javasript Automatisation de mise à jour du champ de produits "_purchase_price" et attribut personalisé "poids" ainsi que la mise en statuts brouillon de masse via le fichier CSV d’un fournisseur des produits dans la boutique du client avec redirection de ceux-ci vers la catégorie parent dans le plugin Redirection.</a>
			</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="add">		
					<div class="div_conteneur_parent">
						<div class="div_conteneur_page"  >
							<div class="div_int_page">			
								<div class="div_h1" >
								<h1>Ajouter les produits fournisseur Midocean via API</h1>
								</div>
					
								<div class="div_saut_ligne">
								</div>	
								
								<div style="width:100%;height:auto;text-align:center;">
											
									<div style="width:800px;display:inline-block;" id="conteneur">
									
										<div class="centre">
											<div class="titre_centre">
												<form id="form" name="form" enctype="multipart/form-data" method="post" action="<?= home_url('product') ?>">
													<input type="hidden" name="action" value="add">
													<button type="submit">Ajout des produits</button>
													</div>						
												</form>	
											</div>	
										</div>
                                        <div class="div_saut_ligne" style="height:50px;">
                                        <div class="centre">
											<div class="titre_centre">
												<form id="form" name="form" enctype="multipart/form-data" method="post" action="<?= home_url('variations') ?>">
													<input type="hidden" name="action" value="add">
													<button type="submit">Ajout variations produit</button>
													</div>						
												</form>	
											</div>	
										</div>
                                        <div class="div_saut_ligne" style="height:50px;">
                                        <div class="centre">
											<div class="titre_centre">
												<form id="form" name="form" enctype="multipart/form-data" method="post" action="<?= home_url('category') ?>">
													<input type="hidden" name="action" value="add">
													<button type="submit">Mettre à jour category</button>
													</div>						
												</form>	
											</div>	
										</div>
                                        <div class="div_saut_ligne" style="height:50px;">
                                        <div class="centre">
											<div class="titre_centre">
												<form id="form" name="form" enctype="multipart/form-data" method="post" action="<?= home_url('images') ?>">
													<input type="hidden" name="action" value="add">
													<button type="submit">Mettre à jour images produit</button>
													</div>						
												</form>	
											</div>	
										</div>
                                        <div class="div_saut_ligne" style="height:50px;">
								</div>

								<?php 

                                if ($compteur === 1)	
                                {
                                    echo '<div id="resultadd" style="width:auto;display:block;height:auto;text-align:center;background-color:#ccccff;border:#7030a0 1px solid;padding-top:12px;box-shadow: 6px 6px 0px #aaa;color:#7030a0;">';
                                                        
                                    echo "<h2>".$compteur." ".$action." a été mis à jour</h2>"; 

                                    echo '</div>';
                                }
								else if($compteur>0 ){
									echo '<div id="resultadd" style="width:auto;display:block;height:auto;text-align:center;background-color:#ccccff;border:#7030a0 1px solid;padding-top:12px;box-shadow: 6px 6px 0px #aaa;color:#7030a0;">';
									
									echo "<h2>".$compteur." ".$action." ont été mis à jour</h2>"; 

									echo'</div>';
			
								}
								else if ($compteur === 0 ){
									echo '<div id="resultadd" style="width:auto;display:block;height:auto;text-align:center;background-color:#ccccff;border:#7030a0 1px solid;padding-top:12px;box-shadow: 6px 6px 0px #aaa;color:#7030a0;">';
														
									echo "<h2> aucun ".$action." n'a été mis à jour</h2>"; 

									echo '</div>';
								}
								?>	
										
									</div>
								
								</div>

						</div>
					</div>	
				</div>
			</div>
		</div>
<?php
	
	}

    public function woocommerce_api_import_products() {

      //  Api::json_api_test();
       // fand_ppom::ppom_exist();
      //  die;
        // JSON data containing product information
        $file_path = ABSPATH . 'wp-content/plugins/fand-woocommerce-api-importer/produits.json'; //produitunique.json';
        $json = file_get_contents($file_path);
        
        $data = json_decode($json, true);

        if (is_array($data)) {
            foreach ($data as $product) {
                // Access the product data
                if (array_key_exists('product_name', $product)) {
                    $productName = $product['product_name'];
                } else {
                    // Gérer l'erreur ici, par exemple, attribuer une valeur par défaut à $productName
                    break;
                }
                // Check if the product already exists
                $existing_product = get_page_by_title($productName, OBJECT, 'product');

                if ($existing_product) {
                    $product_id=$existing_product->ID;
                    Products::update_product($product,$product_id);
                    continue; // Skip to the next product
                }

                else
                {
                    Products::add_product( $product);
                }
               // die;
            }
        }
    }

}


