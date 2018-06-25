<?php

/*
Plugin Name: WC Importer
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: WC - Product Importer.
Version: 1.0
Author: buddyboss
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
// echo  plugins_url() .'/product-csv-import-export-for-woocommerce/includes/importer/class-wf-csv-parser.php';

// WF_CSV_Parser
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if (!class_exists('WP_Importer'))
    return;

$parser = new WF_CSV_Parser('product');
class ImportTest extends WP_Importer
{
    public function test (){
        $this->parser = new WF_CSV_Parser('product');
        return $this->parser;
    }
}
class WC_Product_CLI_Importer extends WP_CLI_Command {

    public function __construct( ) {
        require_once ABSPATH . 'wp-admin/includes/import.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
        require_once(ABSPATH . 'wp-content/plugins/product-csv-import-export-for-woocommerce/includes/importer/class-wf-csv-parser.php');
        require_once(ABSPATH . 'wp-content/plugins/product-csv-import-export-for-woocommerce/includes/importer/class-wf-prodimpexpcsv-product-import.php');
    }
    public function arrange_product_images($postimages) {
        if (!empty($postimages)) {
            $image_details[] = explode('!', $postimages);

            foreach ($image_details as $image_detail) {
                $i = isset($images) ? count($images) : 0;
                $j = 0;
                foreach ($image_detail as $current_image_detail) {
                    if ($j == 0) {
                        $images[$i]['url'] = trim($current_image_detail);
                        $j++;
                        continue;
                    }
                    @list($image['key'], $image['data']) = explode(':', $current_image_detail);
                    $images[$i][trim(strtolower($image['key']))] = trim($image['data']);
                }
            }
            $postimages = $images;
            unset($temp_images, $image_details, $image_detail, $current_image_detail, $image, $images, $i, $j);
        }
        return $postimages;
    }

	/**
	 * Add 301 Redirect for old article from CSV.
	 *
	 * CSV
	 * ----
	 * redirect_from_url,redirect_to_url
	 * ----
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *      wp wc-product product_import_from_csv --url=example.com
	 *
	 * @param array $args Store all the positional arguments.
	 * @param array $assoc_args Store all the associative arguments.
	 */
	public function product_import_from_csv( $args = array(), $assoc_args = array() ) {

		if ( empty( $assoc_args['csv'] ) ) {
			WP_CLI::error( __( "Please pass .csv file in command.", 'wc_importer' ) );
		} elseif ( ! file_exists( $assoc_args['csv'] ) ) {
			WP_CLI::error( __( "Given .csv file does not exists.", 'wc_importer' ) );
		}
        wp_suspend_cache_invalidation(true);
		echo '<pre>';
		var_dump( time() );
		echo '</pre>';

		WP_CLI::log( __( "Stated...", 'wc_importer' ) );

		$csv_file = $assoc_args['csv'];

        $count        = 0;
        $post_updated = 0;


        $this->parser = new WF_CSV_Parser('product');
        $this->WF_ProdImpExpCsv_Product_Import = new WF_ProdImpExpCsv_Product_Import();
        $GLOBALS['WF_CSV_Product_Import'] =  $this->WF_ProdImpExpCsv_Product_Import;
        // print_r($this->WF_ProdImpExpCsv_Product_Import);exit;
        $mapping = '{"id":"ID","post_title":"post_title","post_name":"post_name","post_status":"post_status","post_content":"post_content","post_excerpt":"post_excerpt","post_date":"post_date","sku":"sku","post_parent":"post_parent","parent_sku":"parent_sku","children":"children","post_password":"post_password","post_author":"post_author","menu_order":"menu_order","comment_status":"comment_status","downloadable":"downloadable","virtual":"virtual","visibility":"visibility","featured":"featured","stock":"stock","stock_status":"stock_status","backorders":"backorders","manage_stock":"manage_stock","sale_price":"sale_price","regular_price":"regular_price","sale_price_dates_from":"sale_price_dates_from","sale_price_dates_to":"sale_price_dates_to","weight":"weight","length":"length","width":"width","height":"height","tax_status":"tax_status","tax_class":"tax_class","upsell_ids":"upsell_ids","crosssell_ids":"crosssell_ids","file_paths":"","downloadable_files":"downloadable_files","download_limit":"download_limit","download_expiry":"download_expiry","product_url":"product_url","button_text":"button_text","images":"images","meta:total_sales":"meta:total_sales","tax:product_type":"tax:product_type","tax:product_cat":"tax:product_cat","tax:product_tag":"tax:product_tag","tax:product_shipping_class":"tax:product_shipping_class","_yoast_wpseo_focuskw":"","_yoast_wpseo_title":"","_yoast_wpseo_metadesc":"","_yoast_wpseo_metakeywords":"","tax:product_visibility":"tax:product_visibility","attribute:pa_design-set":"attribute:pa_design-set","attribute:pa_format":"attribute:pa_format","attribute:pa_language":"attribute:pa_language","attribute:pa_legacy-code-tweak":"attribute:pa_legacy-code-tweak","attribute:pa_product-set":"attribute:pa_product-set","attribute:pa_size":"attribute:pa_size","meta:_yoast_wpseo_focuskw":"meta:_yoast_wpseo_focuskw","meta:_yoast_wpseo_title":"meta:_yoast_wpseo_title","meta:_yoast_wpseo_metadesc":"meta:_yoast_wpseo_metadesc","meta:_yoast_wpseo_metakeywords":"meta:_yoast_wpseo_metakeywords","meta:wc_productdata_options":"meta:wc_productdata_options","meta:attribute_pa_format":"meta:attribute_pa_format","meta:attribute_pa_language":"meta:attribute_pa_language","meta:attribute_pa_size":"meta:attribute_pa_size","attribute_data:pa_design-set":"attribute_data:pa_design-set","attribute_default:pa_design-set":"attribute_default:pa_design-set","attribute_data:pa_format":"attribute_data:pa_format","attribute_default:pa_format":"attribute_default:pa_format","attribute_data:pa_language":"attribute_data:pa_language","attribute_default:pa_language":"attribute_default:pa_language","attribute_data:pa_legacy-code-tweak":"attribute_data:pa_legacy-code-tweak","attribute_default:pa_legacy-code-tweak":"attribute_default:pa_legacy-code-tweak","attribute_data:pa_product-set":"attribute_data:pa_product-set","attribute_default:pa_product-set":"attribute_default:pa_product-set","attribute_data:pa_size":"attribute_data:pa_size","attribute_default:pa_size":"attribute_default:pa_size"}';
        $eval_field =$fields;
        // echo "<pre>";
        // print_r($GLOBALS);

        /*************************/
        // Load Importer API
        /*require_once ABSPATH . 'wp-admin/includes/import.php';

         if (!class_exists('WP_Importer')) {
             $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
             if (file_exists($class_wp_importer)) {
                 require $class_wp_importer;
             }
         }
         $GLOBALS['WF_CSV_Product_Import'] = new WF_ProdImpExpCsv_Product_Import();
         $mapping = '';
         $eval_field = '';
         $start_pos = 0;
         $end_pos = '';
         if ($this->settings['pro_auto_import_profile'] !== '') {
             $profile_array = get_option('wf_prod_csv_imp_exp_mapping');
             $mapping = $profile_array[$this->settings['pro_auto_import_profile']][0];
             $eval_field = $profile_array[$this->settings['pro_auto_import_profile']][1];
             $start_pos = 0;
             $end_pos = '';
         } else {
             $this->error_message = 'Please set a mapping profile';
             $GLOBALS['WF_CSV_Product_Import']->hf_log_data_change('csv-import', __('Failed processing import. Reason:' . $this->error_message, 'wf_csv_import_export'));
         }

         $_GET['merge'] = (($this->settings['pro_auto_import_merge']) ? 1 : 0 );

         $_GET['skip_new'] = (($this->settings['pro_auto_import_skip']) ? 1 : 0);

         $_GET['delete_products'] = (($this->settings['pro_auto_delete_products']) ? 1 : 0);

         //echo wp_next_scheduled('wf_woocommerce_csv_im_ex_auto_import_products').'<br/>';
         //echo date('Y-m-d H:i:s' , wp_next_scheduled('wf_woocommerce_csv_im_ex_auto_import_products'));
         //echo $_GET['merge'];exit;

         $GLOBALS['WF_CSV_Product_Import']->import_start($csv_file, $mapping, $start_pos, $end_pos, $eval_field);
         $GLOBALS['WF_CSV_Product_Import']->import();
         $GLOBALS['WF_CSV_Product_Import']->import_end();

         if ($_GET['delete_products'] == 1) {
             $GLOBALS['WF_CSV_Product_Import']->delete_products_not_in_csv();
         }
         //do_action('wf_new_scheduled_import');
         //wp_clear_scheduled_hook('wf_woocommerce_csv_im_ex_auto_import_products');
         //do_action('wf_new_scheduled_import');

         die();
         echo '</pre>';exit; */
        // $this->WF_ProdImpExpCsv_Product_Import->import_start($csv_file, $mapping, 0, 100, $eval_field);
        $this->WF_ProdImpExpCsv_Product_Import->delimiter = ',';
        list( $this->parsed_data, $this->raw_headers, $position ) = $this->parser->parse_data($csv_file, ",", json_decode($mapping, true), 0, null, $eval_field);
        // echo 'here';
        // print_r($this->parsed_data);
        // exit;
        foreach ($this->parsed_data as $key => $item) {
             $count++;
            /* echo '<pre>';
             print_r($item);
             print_r($this->merge_empty_cells);
             print_r($this->use_sku_upsell_crosssell);
             echo '</pre>'; */
            $product = $this->parser->parse_product($item, $this->merge_empty_cells, $this->use_sku_upsell_crosssell);
            // print_r($product);exit;
            $images = $row [ $fields['images'] ];
            // print_r($row [ $fields['images'] ]);
            if (!is_wp_error($product))
                $this->WF_ProdImpExpCsv_Product_Import->process_product($product, null);
            //exit;
            // print_r($product);
            if(is_array($product) && isset($product) && isset($product['post_id'])){
                $post_id = $product['post_id'];
                $processing_product_title = $product['post_title'];
                if (!empty($product['images']) && is_array($product['images'])) {
                    foreach ($product['images'] as $image_key => $image) {
                        $image = $this->arrange_product_images($image);
                        $parts = parse_url($image[0]['url']);
                        $query_string = $parts['query'];
                        parse_str($query_string, $output);
                        // print_r($image);
                        if(isset($output['attachment_id']) && !empty($output['attachment_id'])){
                            $attachment_id = $output['attachment_id'];
                            update_post_meta($attachment_id, '_wp_attachment_image_alt', ( isset($image[0]['alt']) ? $image[0]['alt'] : $processing_product_title));
                            update_post_meta($post_id, '_thumbnail_id', $attachment_id);
                            // WP_CLI::runcommand( 'post meta update '.$post_id.' _thumbnail_id '.$attachment_id );
                        }
                    }
                }
                WP_CLI::log( $post_id . ' -----> ' . get_site_url().'/product/'.$product['post_name'] );
            }

            unset($item, $product);
            $post_updated++;
            if ( 0 === ( $count % 100 ) ) {
                WP_ CLI::log( 'Sleep for 2 sec' );
                sleep( 2 );
            }
           // exit;
        }
        wp_suspend_cache_invalidation(false);
        WP_CLI::log( sprintf( __( 'Total %d Redirection done.', 'wc_importer' ), $post_updated ) );

        WP_CLI::success( __( "Completed...", 'wc_importer' ) );
        // $WF_ProdImpExpCsv_Product_Import->process_product($product, $this->new_prod_status);
        echo "</pre>";
        exit;
        /*
		$handle = fopen( $csv_file, 'r' );

		if ( false === $handle ) {
			WP_CLI::error( __( "Please pass a valid .csv file in command..", 'wc_importer' ) );
		}

		$count        = 0;
		$post_updated = 0;
		$columns = $fields = array();

		while ( ( $row = fgetcsv( $handle, 0, ',' ) ) !== false ) {
			$count++;

			if ( 1 == $count ){
				$columns = $fields = $row;
				$fields = array_flip( $fields );
				continue;
			}

			if ( 0 === ( $count % 100 ) ) {
				sleep( 2 );
			}
			$cat = get_categories();
            echo '<pre>';
            // print_r($cat);
            echo '</pre>';
            // exit;

			//check post already existed
			$post = get_page_by_path( $row [ $fields['post_name'] ], OBJECT, 'product' );
            /* echo '<pre>';
            var_dump($post);
            echo '</pre>'; */
            /*$state = "create";
            if(!empty( $post )){
                $state = "update ".$post->ID;
            }
			if ( true) {

				/*$cats = explode( '|', $row [ $fields['tax:product_cat'] ] );
				$j_cats = array();
				foreach ( $cats as $cat ){
					$j_cats[] = array(
						'name' => $cat,
					);
				}

				--categories="'. json_encode( $j_cats ) .'"
			   --tags="'. $row [ $fields['tax:product_tag'] ] .'"
				*/

				/*$attributes = array();

				$data         = explode( '|', $row [ $fields['attribute_data:pa_design-set'] ] );
				$attributes[] = array(
					'name'      => 'design-set',
					'position'  => $data[0],
					'visible'   => $data[1],
					'variation' => $data[2],
					'options'   => explode( '|', $row [ $fields['attribute:pa_design-set'] ] ),
				);

				$data         = explode( '|', $row [ $fields['attribute_data:pa_format'] ] );
				$attributes[] = array(
					'name'      => 'format',
					'position'  => $data[0],
					'visible'   => $data[1],
					'variation' => $data[2],
					'options'   => explode( '|', $row [ $fields['attribute:pa_format'] ] ),
				);

				$data         = explode( '|', $row [ $fields['attribute_data:pa_language'] ] );
				$attributes[] = array(
					'name'      => 'language',
					'position'  => $data[0],
					'visible'   => $data[1],
					'variation' => $data[2],
					'options'   => explode( '|', $row [ $fields['attribute:pa_language'] ] ),
				);

				$data         = explode( '|', $row [ $fields['attribute_data:pa_legacy-code-tweak'] ] );
				$attributes[] = array(
					'name'      => 'legacy-code-tweak',
					'position'  => $data[0],
					'visible'   => $data[1],
					'variation' => $data[2],
					'options'   => explode( '|', $row [ $fields['attribute:pa_legacy-code-tweak'] ] ),
				);

				$data         = explode( '|', $row [ $fields['attribute_data:pa_product-set'] ] );
				$attributes[] = array(
					'name'      => 'product-set',
					'position'  => $data[0],
					'visible'   => $data[1],
					'variation' => $data[2],
					'options'   => explode( '|', $row [ $fields['attribute:pa_product-set'] ] ),
				);

				$data         = explode( '|', $row [ $fields['attribute_data:pa_size'] ] );
				$attributes[] = array(
					'name'      => 'size',
					'position'  => $data[0],
					'visible'   => $data[1],
					'variation' => $data[2],
					'options'   => explode( '|', $row [ $fields['attribute:pa_size'] ] ),
				);

				foreach ( $row as $key => $value ) {
					$row[ $key ] = wp_specialchars( $value );
				}


				if ( empty( $row [ $fields['tax:product_visibility'] ] ) ) {
					$row [ $fields['tax:product_visibility'] ] = 'visible';
				}

				if ( empty( $row [ $fields['tax:product_type'] ] ) ) {
					$row [ $fields['tax:product_type'] ] = 'simple';
				}

				if ( empty( $row [ $fields['tax_status'] ] ) ) {
					$row [ $fields['tax_status'] ] = 'none';
				}

				if ( empty( $row [ $fields['backorders'] ] ) ) {
					$row [ $fields['backorders'] ] = 'no';
				}

				if ( empty( $row [ $fields['stock_status'] ] ) ) {
					$row [ $fields['stock_status'] ] = 'instock';
				}
				$img = $this->arrange_product_images($row [ $fields['images'] ]);
				print_r($img);

				$command = '--allow-root --user=1 wc product  '.$state.
				' --name="'. $row [ $fields['post_title'] ] .'"'.
				' --slug="'. $row [ $fields['post_name'] ] .'"'.
				' --parent_id="'. (int)$row [ $fields['Parent'] ] .'"'.
				' --description="'. wp_specialchars_decode($row [ $fields['post_content'] ]) .'"'.
				' --short_description="'.wp_specialchars_decode( $row [ $fields['post_excerpt'] ]) .'"'.
				' --status="'. $row [ $fields['post_status'] ] .'"'.
				' --menu_order="'. $row [ $fields['menu_order'] ] .'"'.
				' --sku="'. $row [ $fields['sku'] ] .'"'.
				' --downloadable="'. ( 'yes' === $row [ $fields['downloadable'] ] ? 'true' : 'false' ) .'"'.
				' --stock_quantity="'. (int) $row [ $fields['stock'] ] .'"'.
				' --regular_price="'. $row [ $fields['regular_price'] ] .'"'.
				' --sale_price="'. $row [ $fields['sale_price'] ] .'"'.
				' --weight="'. $row [ $fields['weight'] ] .'"'.
				' --dimensions="{ "length" : '. $row [ $fields['length'] ] .', "width": '. $row [ $fields['width'] ] .', "height":'. $row [ $fields['height'] ] .'  }"'.
				' --tax_class="'. $row [ $fields['tax_class'] ] .'"'.
				' --tax_class="'. $row [ $fields['tax_class'] ] .'"'.
				' --in_stock="'. ( "instock" == $row [ $fields['stock_status'] ] ? 'true' : 'false' ) .'"'.
				' --backorders="'. $row [ $fields['backorders'] ] .'"'.
				' --manage_stock="'. ( 'yes' === $row [ $fields['manage_stock'] ] ? 'true' : 'false' ) .'"'.
				' --tax_status="'. $row [ $fields['tax_status'] ] .'"'.
				' --upsell_ids="'. (int) $row [ $fields['upsell_ids'] ] .'"'.
				' --cross_sell_ids="'. (int)$row [ $fields['crosssell_ids'] ] .'"'.
				' --featured="'. ( 'yes' === $row [ $fields['featured'] ] ? 'true' : 'false' ) .'"'.
				' --date_on_sale_from="'. $row [ $fields['sale_price_dates_from'] ] .'"'.
				' --date_on_sale_to="'. $row [ $fields['sale_price_dates_to'] ] .'"'.
				' --download_limit="'. (int)$row [ $fields['download_limit'] ] .'"'.
				' --download_expiry="'. (int)$row [ $fields['download_expiry'] ] .'"'.
				' --external_url="'. $row [ $fields['product_url'] ] .'"'.
				' --button_text="'. $row [ $fields['button_text'] ] .'"'.
				' --meta_data="[ { "key":"_yoast_wpseo_focuskw", "value":'. $row [ $fields['meta:_yoast_wpseo_focuskw'] ] .' },'.
							   '{ "key":"_yoast_wpseo_metadesc", "value":'. $row [ $fields['meta:_yoast_wpseo_metadesc'] ] .'},'.
							   '{ "key":"_yoast_wpseo_metakeywords", "value":'. $row [ $fields['meta:_yoast_wpseo_metakeywords'] ] .'}'.
							   '{ "key":"total_sales", "value":'. $row [ $fields['meta:total_sales'] ] .'}'.
							   '{ "key":"wc_productdata_options", "value":'. $row [ $fields['meta:wc_productdata_options'] ] .'}'.
							   '{ "key":"attribute_pa_format", "value":'. $row [ $fields['meta:attribute_pa_format'] ] .'}'.
							   '{ "key":"attribute_pa_language", "value":'. $row [ $fields['meta:attribute_pa_language'] ] .'}'.
							   '{ "key":"attribute_pa_size", "value":'. $row [ $fields['meta:attribute_pa_size'] ] .'} ]"'.
			    ' --images="[ { "src": "http://localhost:88/creativepivot/wp-content/uploads/2018/06/Crop-and-dropdown@2x.jpg" } ]"'.
			    //' --images="[ { "src": '.json_encode($img) .' } ]"'.
			   // ' --featured_src="'.explode( '!',$row [ $fields['images'] ] )[0] .'"'.
			   ' --downloads="'. json_encode( (object) $row [ $fields['downloads'] ] ) .'"'.
			   ' --type="'. $row [ $fields['tax:product_type'] ] .'"'.
			   ' --catalog_visibility="'. $row [ $fields['tax:product_visibility'] ] .'"'.
			   ' --shipping_class="'. $row [ $fields['tax:product_shipping_class'] ] .'"'.
			   ' --shipping_class="'. $row [ $fields['tax:product_shipping_class'] ] .'"'.
			   '--attributes="'. json_encode( $attributes ) .'"';


				// $command = '--allow-root --user=1 wc product '.$state.' --name="' . $row [ $fields['post_title'] ] . '" --slug="' . $row [ $fields['post_name'] ] . '" --parent_id="' . (int) $row [ $fields['Parent'] ] . '" --description="' . $row [ $fields['post_content'] ] . '" --short_description="' . $row [ $fields['post_excerpt'] ] . '" --status="' . $row [ $fields['post_status'] ] . '" --menu_order="' . $row [ $fields['menu_order'] ] . '" --sku="' . $row [ $fields['sku'] ] . '" --downloadable="' . ( 'yes' === $row [ $fields['downloadable'] ] ? 'true' : 'false' ) . '" --stock_quantity="' . (int) $row [ $fields['stock'] ] . '" --regular_price="' . $row [ $fields['regular_price'] ] . '" --sale_price="' . $row [ $fields['sale_price'] ] . '" --weight="' . $row [ $fields['weight'] ] . '" --dimensions="{ "length" : ' . $row [ $fields['length'] ] . ', "width": ' . $row [ $fields['width'] ] . ', "height":' . $row [ $fields['height'] ] . '  }" --tax_class="' . $row [ $fields['tax_class'] ] . '" --tax_class="' . $row [ $fields['tax_class'] ] . '" --in_stock="' . ( "instock" == $row [ $fields['stock_status'] ] ? 'true' : 'false' ) . '" --backorders="' . $row [ $fields['backorders'] ] . '" --manage_stock="' . ( 'yes' === $row [ $fields['manage_stock'] ] ? 'true' : 'false' ) . '" --tax_status="' . $row [ $fields['tax_status'] ] . '" --upsell_ids="' . (int) $row [ $fields['upsell_ids'] ] . '" --cross_sell_ids="' . (int) $row [ $fields['crosssell_ids'] ] . '" --featured="' . ( 'yes' === $row [ $fields['featured'] ] ? 'true' : 'false' ) . '" --date_on_sale_from="' . $row [ $fields['sale_price_dates_from'] ] . '" --date_on_sale_to="' . $row [ $fields['sale_price_dates_to'] ] . '" --download_limit="' . (int) $row [ $fields['download_limit'] ] . '" --download_expiry="' . (int) $row [ $fields['download_expiry'] ] . '" --external_url="' . $row [ $fields['product_url'] ] . '" --button_text="' . $row [ $fields['button_text'] ] . '" --meta_data="[ { "key":"_yoast_wpseo_focuskw", "value":' . $row [ $fields['meta:_yoast_wpseo_focuskw'] ] . ' }, { "key":"_yoast_wpseo_metadesc", "value":' . $row [ $fields['meta:_yoast_wpseo_metadesc'] ] . '},{ "key":"_yoast_wpseo_metakeywords", "value":' . $row [ $fields['meta:_yoast_wpseo_metakeywords'] ] . '},{ "key":"total_sales", "value":' . $row [ $fields['meta:total_sales'] ] . '},{ "key":"wc_productdata_options", "value":' . $row [ $fields['meta:wc_productdata_options'] ] . '},{ "key":"attribute_pa_format", "value":' . $row [ $fields['meta:attribute_pa_format'] ] . '},{ "key":"attribute_pa_language", "value":' . $row [ $fields['meta:attribute_pa_language'] ] . '},{ "key":"attribute_pa_size", "value":' . $row [ $fields['meta:attribute_pa_size'] ] . '} ]" --images="[ { "src": ' . explode( '!', $row [ $fields['images'] ] )[0] . ' } ]" --downloads="' . json_encode( (object) $row [ $fields['downloads'] ] ) . '" --type="' . $row [ $fields['tax:product_type'] ] . '" --catalog_visibility="' . $row [ $fields['tax:product_visibility'] ] . '" --shipping_class="' . $row [ $fields['tax:product_shipping_class'] ] . '" --shipping_class="' . $row [ $fields['tax:product_shipping_class'] ] . '" --attributes="' . json_encode( $attributes ) . '"';

				WP_CLI::log( $command  );

				// WP_CLI::runcommand( $command );
                if (empty( $post ) ){
                    $post = get_page_by_path( $row [ $fields['post_name'] ], OBJECT, 'product' );
                }
                $id = $post->ID;
                $this->parser = new WF_CSV_Parser('product');
                $this->WF_ProdImpExpCsv_Product_Import = new WF_ProdImpExpCsv_Product_Import();
                $GLOBALS['WF_CSV_Product_Import'] =  $this->WF_ProdImpExpCsv_Product_Import;
                // print_r($this->WF_ProdImpExpCsv_Product_Import);exit;
                $mapping = '{"id":"ID","post_title":"post_title","post_name":"post_name","post_status":"post_status","post_content":"post_content","post_excerpt":"post_excerpt","post_date":"post_date","sku":"sku","post_parent":"post_parent","parent_sku":"parent_sku","children":"children","post_password":"post_password","post_author":"post_author","menu_order":"menu_order","comment_status":"comment_status","downloadable":"downloadable","virtual":"virtual","visibility":"visibility","featured":"featured","stock":"stock","stock_status":"stock_status","backorders":"backorders","manage_stock":"manage_stock","sale_price":"sale_price","regular_price":"regular_price","sale_price_dates_from":"sale_price_dates_from","sale_price_dates_to":"sale_price_dates_to","weight":"weight","length":"length","width":"width","height":"height","tax_status":"tax_status","tax_class":"tax_class","upsell_ids":"upsell_ids","crosssell_ids":"crosssell_ids","file_paths":"","downloadable_files":"downloadable_files","download_limit":"download_limit","download_expiry":"download_expiry","product_url":"product_url","button_text":"button_text","images":"images","meta:total_sales":"meta:total_sales","tax:product_type":"tax:product_type","tax:product_cat":"tax:product_cat","tax:product_tag":"tax:product_tag","tax:product_shipping_class":"tax:product_shipping_class","_yoast_wpseo_focuskw":"","_yoast_wpseo_title":"","_yoast_wpseo_metadesc":"","_yoast_wpseo_metakeywords":"","tax:product_visibility":"tax:product_visibility","attribute:pa_design-set":"attribute:pa_design-set","attribute:pa_format":"attribute:pa_format","attribute:pa_language":"attribute:pa_language","attribute:pa_legacy-code-tweak":"attribute:pa_legacy-code-tweak","attribute:pa_product-set":"attribute:pa_product-set","attribute:pa_size":"attribute:pa_size","meta:_yoast_wpseo_focuskw":"meta:_yoast_wpseo_focuskw","meta:_yoast_wpseo_title":"meta:_yoast_wpseo_title","meta:_yoast_wpseo_metadesc":"meta:_yoast_wpseo_metadesc","meta:_yoast_wpseo_metakeywords":"meta:_yoast_wpseo_metakeywords","meta:wc_productdata_options":"meta:wc_productdata_options","meta:attribute_pa_format":"meta:attribute_pa_format","meta:attribute_pa_language":"meta:attribute_pa_language","meta:attribute_pa_size":"meta:attribute_pa_size","attribute_data:pa_design-set":"attribute_data:pa_design-set","attribute_default:pa_design-set":"attribute_default:pa_design-set","attribute_data:pa_format":"attribute_data:pa_format","attribute_default:pa_format":"attribute_default:pa_format","attribute_data:pa_language":"attribute_data:pa_language","attribute_default:pa_language":"attribute_default:pa_language","attribute_data:pa_legacy-code-tweak":"attribute_data:pa_legacy-code-tweak","attribute_default:pa_legacy-code-tweak":"attribute_default:pa_legacy-code-tweak","attribute_data:pa_product-set":"attribute_data:pa_product-set","attribute_default:pa_product-set":"attribute_default:pa_product-set","attribute_data:pa_size":"attribute_data:pa_size","attribute_default:pa_size":"attribute_default:pa_size"}';
                $eval_field =$fields;
                echo "<pre>";
                // print_r($GLOBALS);

                /*************************/
                // Load Importer API
               /*require_once ABSPATH . 'wp-admin/includes/import.php';

                if (!class_exists('WP_Importer')) {
                    $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
                    if (file_exists($class_wp_importer)) {
                        require $class_wp_importer;
                    }
                }
                $GLOBALS['WF_CSV_Product_Import'] = new WF_ProdImpExpCsv_Product_Import();
                $mapping = '';
                $eval_field = '';
                $start_pos = 0;
                $end_pos = '';
                if ($this->settings['pro_auto_import_profile'] !== '') {
                    $profile_array = get_option('wf_prod_csv_imp_exp_mapping');
                    $mapping = $profile_array[$this->settings['pro_auto_import_profile']][0];
                    $eval_field = $profile_array[$this->settings['pro_auto_import_profile']][1];
                    $start_pos = 0;
                    $end_pos = '';
                } else {
                    $this->error_message = 'Please set a mapping profile';
                    $GLOBALS['WF_CSV_Product_Import']->hf_log_data_change('csv-import', __('Failed processing import. Reason:' . $this->error_message, 'wf_csv_import_export'));
                }

                $_GET['merge'] = (($this->settings['pro_auto_import_merge']) ? 1 : 0 );

                $_GET['skip_new'] = (($this->settings['pro_auto_import_skip']) ? 1 : 0);

                $_GET['delete_products'] = (($this->settings['pro_auto_delete_products']) ? 1 : 0);

                //echo wp_next_scheduled('wf_woocommerce_csv_im_ex_auto_import_products').'<br/>';
                //echo date('Y-m-d H:i:s' , wp_next_scheduled('wf_woocommerce_csv_im_ex_auto_import_products'));
                //echo $_GET['merge'];exit;

                $GLOBALS['WF_CSV_Product_Import']->import_start($csv_file, $mapping, $start_pos, $end_pos, $eval_field);
                $GLOBALS['WF_CSV_Product_Import']->import();
                $GLOBALS['WF_CSV_Product_Import']->import_end();

                if ($_GET['delete_products'] == 1) {
                    $GLOBALS['WF_CSV_Product_Import']->delete_products_not_in_csv();
                }
                //do_action('wf_new_scheduled_import');
                //wp_clear_scheduled_hook('wf_woocommerce_csv_im_ex_auto_import_products');
                //do_action('wf_new_scheduled_import');

                die();
                echo '</pre>';exit; */
                // $this->WF_ProdImpExpCsv_Product_Import->import_start($csv_file, $mapping, 0, 100, $eval_field);
                /*$this->WF_ProdImpExpCsv_Product_Import->delimiter = ',';
                list( $this->parsed_data, $this->raw_headers, $position ) = $this->parser->parse_data($csv_file, ",", json_decode($mapping, true), 0, 100, $eval_field);
                // echo 'here';
                // print_r($this->parsed_data);
                // exit;
                foreach ($this->parsed_data as $key => $item) {
                    /* echo '<pre>';
                     print_r($item);
                     print_r($this->merge_empty_cells);
                     print_r($this->use_sku_upsell_crosssell);
                     echo '</pre>'; */
                    /* $product = $this->parser->parse_product($item, $this->merge_empty_cells, $this->use_sku_upsell_crosssell);
                    // print_r($product);exit;
                    // $item['images'] = $row [ $fields['images'] ];
                    // print_r($row [ $fields['images'] ]);
                    if (!is_wp_error($product))
                        $this->WF_ProdImpExpCsv_Product_Import->process_product($product, null);
                    //exit;
                    unset($item, $product);
                }
                // $WF_ProdImpExpCsv_Product_Import->process_product($product, $this->new_prod_status);
                echo "</pre>";
               //  WP_CLI::runcommand( 'post meta update '.$id.' _thumbnail_id 888938379' );
                // exit;
				$post_updated++;
			}
		}
        wp_suspend_cache_invalidation(false);
		WP_CLI::log( sprintf( __( 'Total %d Redirection done.', 'wc_importer' ), $post_updated ) );

		WP_CLI::success( __( "Completed...", 'wc_importer' ) );

		echo '<pre>';
		var_dump( time() );
		echo '</pre>';*/

	}
}
