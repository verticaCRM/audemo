<?php
/*
  Plugin Name: Business Brokers CRM Integration for WordPress
  Plugin URI: http://businessbrokerscrm.com
  Description: integration plugin for the BusinessBrokersCRM platform
  Version: 1.0
  Author: BusinessBrokersCRM
  Author URI: http://businessbrokerscrm.com
  Text Domain: bbcrm
*/
//ini_set('display_errors',1);
//error_reporting(E_ALL);

global $wp_query;
include_once ("_auth.php");
include_once ("functions-bbcrm_wp.php");
include_once ("functions-bbcrm_api.php");
include_once ("class.plugintemplates.php");
include_once ("options-bbcrm.php");

show_admin_bar(false);


function bbcrm_load_textdomain() {
  load_plugin_textdomain( 'bbcrm', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'bbcrm_load_textdomain' );

function bbcrm_set_wp_title(){
global $pagetitle;
return $pagetitle;
}
add_filter('wp_head','bbcrm_set_wp_title');

function bbcrm_enqueue_scripts(){
	//wp_enqueue_script( 'ajaxform', get_stylesheet_directory_uri() . '/js/ajaxform.js', array(), '1.0.0', true );
	wp_enqueue_script('my_script',plugin_dir_url(__FILE__)."js/lib.js", array('jquery'), '1.0.0');
	wp_enqueue_script('web-tracker',get_bloginfo('url').'/crm/webTracker.php');
	wp_enqueue_style('bbcrm',plugin_dir_url(__FILE__)."css/style.css");
	wp_enqueue_style('bbcrm',plugin_dir_url(__FILE__)."css/wp_properties.css");
 	wp_register_script( 'jquery-form', '/wp-includes/js/jquery/jquery.form.js', array('jquery') );
}
add_action( 'wp_enqueue_scripts', 'bbcrm_enqueue_scripts' );


function bbcrm_set_listing_meta(){
   global $wp_query,$listing,$listingtags;

   $is_listing = get_query_var('listing');
    if($is_listing){
      $html = '<meta name="Keywords" content="'.join(',',$listingtags).'" />'.
     '<meta name="Description" content="'.$listing->description.'" />';

      $title = $listing->c_name_generic_c;
     }
}

function bbcrm_get_loginbar(){
bbcrm_load_textdomain();
	$inctemp = plugin_dir_path(__FILE__)."templates/loginbar.php";
	ob_start();
   include($inctemp);
   return ob_get_clean();
}
add_shortcode('bbcrm_loginbar','bbcrm_get_loginbar');


function get_featured_search( $atts ){
  $a = shortcode_atts( array(
'num'=>'4',    
'title' => 'Business for Sale',
    'type' => 'all',
    'broker'=>'',
    'featured'=>1,
    'franchise'=>false
  ), $atts );
  $search = plugin_dir_path(__FILE__)."templates/home-search.php";
  ob_start();
        include($search);
        return ob_get_clean();
}
add_shortcode('featuredsearch','get_featured_search');





/**
  *Developer: Theo@BioeliteVert
  *Shortcode to get Real Estates
  *for the Commercial Page (for Sale; for Lease)
*/

function get_featured_real_estates( $atts ){

  $a = shortcode_atts( array(
    'num'=>'4',    
    'title' => 'Comercial Search',
    'type' => 'all',
    'broker'=>'',
    'featured'=>1,
    'franchise'=>false
    ), $atts );

  $search = plugin_dir_path(__FILE__)."templates/realestate-search.php";
  ob_start();
    include($search);
  return ob_get_clean();
}
add_shortcode('featuredsearch_realestate','get_featured_real_estates');




function get_featured_listings($atts){
global $a;

$a = shortcode_atts( array(
'num'=>'4',    
'franchise'=>0,
    'broker'=>'',
    'featured'=>1,
    ), $atts );

	$search = plugin_dir_path(__FILE__)."templates/home-featured.php";
	ob_start();
        include($search);
        return ob_get_clean();

}
add_shortcode('featuredlistings','get_featured_listings');



function get_id_search($atts){
global $a;

$a = shortcode_atts( array(
	'num'=>'4',    
	'franchise'=>0,
	'broker'=>'',
	'featured'=>1,
	'addbutton'=>true,
    ), $atts );

	$search = plugin_dir_path(__FILE__)."templates/portfolio-search.php";
	ob_start();
        include($search);
        return ob_get_clean();

}
add_shortcode('searchbyid','get_id_search');



add_filter( 'no_texturize_shortcodes', 'shortcodes_to_exempt_from_wptexturize' );
function shortcodes_to_exempt_from_wptexturize( $shortcodes ) {
    $shortcodes[] = 'featuredlistings';
	$shortcodes[] = 'featuredsearch';
    return $shortcodes;
}


add_action( 'wp_ajax_contact_to_crm', 'contact_to_crm' );
add_action( 'wp_ajax_nopriv_contact_to_crm', 'contact_to_crm' );

wp_enqueue_script('jquery'); // I assume you registered it somewhere else
wp_localize_script('jquery', 'ajax_custom', array(
   'ajaxurl' => admin_url('admin-ajax.php')
));

//////////
function contact_to_crm(){

parse_str($_REQUEST["data"],$params);

$model = ($_REQUEST["model"])?$_REQUEST["model"]:"Contacts";

if(isset($params["_wpnonce"])){

$res = x2apipost(array("_class"=>$model."/","_data"=>$params));
//print_r($res);
exit;
}
}
//////////



function XXcontact_to_crm(){

parse_str($_REQUEST["data"],$params);
  
if(isset($params["_wpnonce"])){   
include("/home/bbrokers/mndemo/crm/protected/models/APIModel.php");
$attributes=$params;

$contact = new APIModel($username,$userkey,$apiserver);

foreach($attributes as $key=>$value){
   if(isset($fieldMap[$key])){
        $contact->{$fieldMap[$key]}=$value; // Found in field map, used mapped attribute
    }else{
        $contact->$key=$value; // No match anywhere, assume it's a Contact attribute
    }
}
if(isset($data['x2_key'])){
    $contact->trackingKey=$data['x2_key'];
}
$res = $contact->contactCreate(); // Call API to create contact

//$res = $contact->modelCreateUpdate('Contact','create',$attributes); // Call API to create contact
print_r($res);
exit;
}
}



/**
  *Developer: Theo@BioeliteVert
  *Shortcode to get Categories
  *for the 'For Sale By Industry' Page
*/
function get_for_sale_by_industry($atts){

  { // Creating a new array to get the parent categories for the child categories
    // Get all categories (kids and parents)
    $json = x2apicall(array('_class'=>'dropdowns/1000.json'));
    $buscats = json_decode($json);
    // Get parent Categories
    $json = x2apicall(array('_class'=>'dropdowns/1085.json'));
    $buscats_par = json_decode($json);
    // Get Clistings Number
    $business_categories = 'c_businesscategories=["'.trim($v).'"]';
    $json_for_clistings = x2apicall(array('_class'=>'Clistings'));
    $decoded_clistings = json_decode($json_for_clistings);

    // echo '<pre>'; print_r($buscats); echo '</pre>';

    $parent_cat = '';
    $result = '';
    $new_array = array();

    foreach ($buscats->options as $k=>$v)
    {
      foreach ($buscats_par->options as $kk=>$vv)
      {
        if($v == $vv)
        {
          $parent_cat = $vv;
          $class_cat = str_replace( '/', '_', strtolower(stripslashes($parent_cat)) );
        }
      }

      $i_listings = 0;
      foreach($decoded_clistings as $dec_k=>$dec_v)
      {
        if( strpos( $dec_v->c_businesscategories, '"'.$v.'"' ) !== false )
        {
          $i_listings++;
        }
      }


      if( $parent_cat == $v )
      {
        $result .= '<div class="for_sale_by_industry_parent">'.$v.' Business For Sale'.'</div>';
      }
      else
      {
        $result .= '<a class="for_sale_by_industry_category '.$class_cat.'" data-cat="'.$v.'" href="/search/?c_businesscategories[]='.$v.'">'.$v.'('.$i_listings.')</a>';          
      }

      $new_val = $parent_cat;
      $new_array[$v] = $new_val;
    }
  }

  return $result;

}
add_shortcode('for_sale_by_industry_cats','get_for_sale_by_industry');
