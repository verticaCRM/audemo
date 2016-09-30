<?php
/*
Template Name: SearchResults_1Image_CA_BusMenu 
*/
session_start();
//ini_set('display_errors',true);
//error_reporting(E_ALL);

//for page Businesses for Sale => we need both available for sale and sold Businesses
$postmeta = get_post_meta( get_the_ID() );

foreach($_REQUEST as $k=>$v){
	if( $v=='' ){
		unset($_REQUEST[$k]);
	}
}

// Grab our filters
$status_sale = urlencode('Active,Need Refresh');
//$get_params = '_partial=1&_escape=0&c_is_real_estate=0&c_sales_stage='.$status_sale.'&_limit=2&_page=2';
$get_params = '_partial=1&_escape=0&c_is_real_estate=0';

if(isset($_REQUEST["c_listing_franchise_c"]) && !empty($_REQUEST["c_listing_franchise_c"])){
	$franch = 'c_listing_franchise_c='.$_REQUEST["c_listing_franchise_c"];
	$get_params .= '&'.$franch;
}

if(isset($_REQUEST["c_listing_exclusive_c"]) && !empty($_REQUEST["c_listing_exclusive_c"])){
	$exclus = 'c_listing_exclusive_c='.$_REQUEST["c_listing_exclusive_c"];
	$get_params .= '&'.$exclus;
}

if(isset($_REQUEST["c_listing_homebusiness_c"]) && !empty($_REQUEST["c_listing_homebusiness_c"])){
	$home = 'c_listing_homebusiness_c='.$_REQUEST["c_listing_homebusiness_c"];
	$get_params .= '&'.$home;
}


if(isset($_REQUEST["id"]) && !empty($_REQUEST["id"])){
	$home = 'id='.$_REQUEST["id"];
	$get_params .= '&'.$home;
}

if(isset($_REQUEST["c_listing_region_c"]) && !empty($_REQUEST["c_listing_region_c"])){
	$home = 'c_listing_region_c='.$_REQUEST["c_listing_region_c"];
	$get_params .= '&'.$home;
}

if(isset($_REQUEST["c_listing_town_c"]) && !empty($_REQUEST["c_listing_town_c"])){
	$home = 'c_listing_town_c='.$_REQUEST["c_listing_town_c"];
	$get_params .= '&'.$home;
}

// if(isset($_REQUEST["c_Broker"]) && !empty($_REQUEST["c_Broker"])){
// 	$home = 'c_Broker='.$_REQUEST["c_Broker"];
// 	$get_params .= '&'.$home;
// }


/**
	LAST ADDED FIELDS
*/
if(isset($_REQUEST["c_keyword_c"]) && !empty($_REQUEST["c_keyword_c"])){
	$keyword = trim($_REQUEST["c_keyword_c"]);
	$get_params .= '&c_keyword_c='.$keyword;
}

if(isset($_REQUEST["c_minimum_investment_c"]) && !empty($_REQUEST["c_minimum_investment_c"]) && isset($_REQUEST["c_maximum_investment_c"]) && !empty($_REQUEST["c_maximum_investment_c"]) )
{
	$betweenParam = urlencode('between_'.$_REQUEST["c_minimum_investment_c"].'_'.$_REQUEST["c_maximum_investment_c"]);
	$get_params .= '&c_listing_askingprice_c='.$betweenParam;
}
else
{
	if(isset($_REQUEST["c_minimum_investment_c"]) && !empty($_REQUEST["c_minimum_investment_c"]))
	{
		$minimum_investment = $_REQUEST["c_minimum_investment_c"];
		$get_params .= '&c_listing_askingprice_c=<'.$minimum_investment;
	}

	if(isset($_REQUEST["c_maximum_investment_c"]) && !empty($_REQUEST["c_maximum_investment_c"]))
	{
		$maximum_investment = $_REQUEST["c_maximum_investment_c"];
		$get_params .= '&c_listing_askingprice_c=>'.$maximum_investment;
	}
}


if(isset($_REQUEST["c_adjusted_net_profit_c"]) && !empty($_REQUEST["c_adjusted_net_profit_c"])){
	$adjusted_net_profit = explode("|",$_REQUEST["c_adjusted_net_profit_c"]);
	//$get_params .= '&c_financial_net_profit_c=>'.$adjusted_net_profit[0];
	//$get_params .= '&c_financial_net_profit_c=<'.$adjusted_net_profit[1];
	$betweenParam = urlencode('between_'.$adjusted_net_profit[0].'_'.$adjusted_net_profit[1]);
	$get_params .= '&c_financial_net_profit_c='.$betweenParam;
}

if(isset($_REQUEST["c_franchise_c"]) && !empty($_REQUEST["c_franchise_c"])){
	$franchise = trim($_REQUEST["c_franchise_c"]);
	$get_params .= '&c_franchise_c='.$franchise;
}
global $wpdb;
if(isset($_REQUEST["c_Broker"]) && !empty($_REQUEST["c_Broker"] && $_REQUEST["c_Broker"][0] != '' )){
	//print_r('<pre>');print_r($_REQUEST["c_Broker"]);print_r('</pre>');
	foreach($_REQUEST["c_Broker"] as $broker) {
		$borkers_name = explode('_',$broker);
		$results = $wpdb->get_results( "SELECT * FROM x2_users WHERE CONCAT(firstName, ' ', lastName)='".$borkers_name[0]."'", OBJECT );
		$brokers[] = $results[0]->userAlias;	
		//$get_params .= '&assignedTo='.$results[0]->userAlias;
	}
	if (count($brokers)>1)
	{
		$get_params .= '&assignedTo=';
		$get_params .= ':multiple:__'.implode('__',$brokers);	
	}
	elseif (count($brokers) == 1)
	{
		$get_params .= '&assignedTo=:multiple:__'.$brokers[0];	
	}
}

// If we have Categories
if(isset($_REQUEST["c_businesscategories"]) && !empty($_REQUEST["c_businesscategories"]) && $_REQUEST["c_businesscategories"][0] != ''){
	//$get_params .= '&c_businesscategories='. '%25'.urlencode($_REQUEST["c_businesscategories"]).'%25';
	foreach($_REQUEST["c_businesscategories"] as $k=>$v)
	{
		$business_categories[] = urlencode(trim($v));	
	}
	
	
	if (count($business_categories)>1)
	{
		$get_params .= '&c_businesscategories=';
		$get_params .= ':multiple:__'.implode('__',$business_categories);	
	}
	elseif (count($business_categories) == 1)
	{
		$get_params .= '&c_businesscategories=:multiple:__'.$$business_categories[0];	
	}
}

// Define function for applying filters
//function filter_listings_obj($obj) => was moved into audemo/wp-content/plugins/bbcrm/bbcrm.php

// echo '<pre>'; print_r(filter_listings_obj($obj, $k, $v)); echo '</pre>';

//echo '<pre>'; print_r(count($decoded_json)); echo '</pre>';

$sold_selection = false;


$json = x2apicall(array('_class'=>'Clistings?'.$get_params));
$decoded_json_All = json_decode($json);

//echo '<pre>'; print_r(count($decoded_json_All)); echo '</pre>';

$maxPerPage = MAX_LISTING_PER_PAGE;
/*Get the current page eg index.php?pg=4*/

if(isset($_GET['page_no'])){
    $pageNo = abs(intval($_GET['page_no']));
}else{
    $pageNo = 1;
}

$limit = ($pageNo - 1) * $maxPerPage;
$prev = $pageNo - 1;
$next = $pageNo + 1;
$limits = (int)($pageNo - 1) * $maxPerPage;

$jsonPage = (int)($pageNo - 1);

$sort_order_param = 'sortRecent';
$order_column = '+createDate';
	
if(isset($_GET['sort_order'])){
	$sort_order_param = $_GET['sort_order'];
	if ($_GET['sort_order'] == 'sortRecent')
	{
		$order_column = '-createDate';
	}
	elseif ($_GET['sort_order'] == 'sortOldest')
	{
		$order_column = '+createDate';
	}
	elseif ($_GET['sort_order'] == 'sortPricel')
	{
		$order_column = '-c_listing_askingprice_c';
	}
	elseif ($_GET['sort_order'] == 'sortPricel')
	{
		$order_column = '+c_listing_askingprice_c';
	}   
}
$sort_order = '&_order='.$order_column;

$get_params = $get_params.'&_limit='.$maxPerPage.'&_page='.$jsonPage;
$jsonLimit = x2apicall(array('_class'=>'Clistings?'.$get_params));
$decoded_jsonLimit = json_decode($jsonLimit);

//print_r('<pre>');print_r($get_params);print_r('</pre>');

//echo '<pre>'; print_r(count($decoded_jsonLimit)); echo '</pre>';



// Filter Results
//$decoded_json = filter_listings_obj($decoded_json);

$results = $decoded_jsonLimit;
$totalposts = count($decoded_json_All);
$maxPages = ceil(count($decoded_json_All) / $maxPerPage);
$lpm1 = $maxPages - 1;



get_header();

?>



<?php echo do_shortcode('[listmenu menu="Sub Business" menu_id="sub_business" menu_class="au_submenu"]');?>


<section id="content" data="property" style="min-height:500px;"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

				<div class="col-12 col-sm-4 col-lg-3" style=" margin-top: 45px;">
				
				<div class="panel-group" id="accordion">
						  <div class="panel panel-default">
							<div class="panel-heading">
							  <h4 style="line-height: 40px;" class="panel-title">
								<a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								  Business Search
								</a>
							  </h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse in">
							  <div class="panel-body">
								 <?php echo do_shortcode('[featuredsearch]');?>
							  </div>
							</div>
						  </div>
 
 
						</div>
						
							<br>
							<div class="thumbnail center well well-small text-center" style="padding-bottom:18px;">
								<h2 style="text-align:center !important;">Confidentiality Agreement</h2>
								<p style="text-align:center;">
									Do you need to sign a Confidentiality Agreement (CA Form) to assist your broker ?
								</p>
								<a href="/registration" class="btn btn-large btn-primary">CA Form</a>
								<br> 
							</div>
							<br> 
							<div class="sidebar_search_by_id_container" >
								<h3 class="panel-title">
								  Find by ID
								</h3>
								<?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	

							</div>
					
				
		</div>

				<div  id="business_container" class="col-12 col-sm-8 col-lg-9 searchlists_container">
                      
                       

<?php


global $wpdb;


$results_false_flag = 0;

foreach( $results as $result )
{
	if($result !== false) {
		$results_false_flag = 1;
	}
}


$html = '';

$cjson = x2apicall(array('_class'=>'dropdowns/1086.json'));
$colorjson = json_decode($cjson);
$colors = (array) $colorjson->options;
// print_r($colors);


if(count((array)$results) > 0 && $results->status != "404"  &&  $results_false_flag){
	$listingids = array();

	foreach ($results as $searchlisting){

		if($searchlisting) {

			if(!in_array($searchlisting->id,$listingids)){
				$listingids[]= $searchlisting->id;

				if(!empty($searchlisting->c_businesscategories)){
				$categories = substr($searchlisting->c_businesscategories,1,-1);
				$categories = explode(',',str_replace('"', '', $categories));
				$cats = '';
				foreach($categories as $cat){
					$cat = stripslashes( preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {   return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');}, $cat));
$color = '';

					foreach($colors as $col_k=>$col_v)
					{
						if($col_k == $cat) 
						{
							$color = $col_v;
						}
					}
					$cats .='<a style="background:' . $color. ';" href="?find='.urlencode($cat).'">'.$cat.'</a> ';
				}
			}
 //echo '<pre>'; print_r($searchlisting->id. ' - '.$searchlisting->c_name_generic_c); echo '</pre>';
			 //echo '<pre>'; print_r($images_results); echo '</pre>';
			$images_results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.galleryId = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$searchlisting->id, OBJECT );

			$img_div = '';
			if( !empty($images_results[0]) && $images_results[0]->id > 0)
			{
				 $img_div = "<div class='searchlisting_featured_image'><img src='/crm/uploads/gallery/_".$images_results[0]->id.".jpg' /></div>" ;
			}

			$html .= "<div class='listing_search_result searchresult'>";
			$html .= "	<div class='row'>";
			$html .= "		<div class='col-md-3 searchlisting_photo_box'>";		    
	        $html .= 			$img_div; 
	        $html .= "		</div>";
			$html .= "		<div class='col-md-9 searchlisting_content_box'>";
		//Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :) In addition to the only float on the page ( kind you used for the "more button" on home-featured php file )

//print_r($searchlisting);
/* Failsafe. Need to move to create flow */
if(empty($searchlisting->c_listing_frontend_url)){
$json = x2apipost( array('_method'=>'PUT','_class'=>'Clistings/'.$searchlisting->id.'.json','_data'=>array('c_listing_frontend_url'=>'/listing/'.sanitize_title($searchlisting->c_name_generic_c)."/") ) );
}
		    $html .= "<div class='searchlisting_save_ca'>&nbsp;  <a data-toggle='tooltip'  title='Save this property to' data-original-title='Save this property to your watch list and / or sign a Confidentiality Agreement Form to request more information' style='color:#333;font-family:'Roboto',Tahoma,Verdana,Segoe,sans-serif !important;font-weight:600;' href='registration/'><span class='glyphicon glyphicon-ok-circle'></span> Save/Request CA </a>&nbsp;".__("","bbcrm")."</div>";		
            $html .= "<a class='searchlisting_name' href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" class=\"listing_link\" data-id=\"". $searchlisting->id ."\">".$searchlisting->c_name_generic_c."</a>";
		    $html .= "<br>";
		    $html .= "<div class='searchlisting_region'>".__("","bbcrm").$searchlisting->c_listing_region_c;
		    $html .= "<span class='searchlisting_currency_id'>". $searchlisting->c_priceView . "</span>";
		    
		     
		     
		    $html .= "</div>";
			//$html .= "		<div>".__("Cash Flow: ",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_ownerscashflow)."</div>";
		    $html .= "			<div class='searchlisting_description'><a href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" data-id=\"". $searchlisting->id ."\">".$searchlisting->description."</a></div>";
			//$html .= "		<div>".__("Contact Seller",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
			//$html .= "		<div>".__("More Info",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
		    $html .= "			<div class='searchlisting_bottom_category'>".$cats."</div>";
		    
			//$html .= "		<div>".$searchlisting->c_listing_businesscat_c."</div>";	
			
			
            $html .= "<div class='searchlisting_bottom_ref'>".__("Reference ","bbcrm").preg_replace("/[^0-9,.]/","",$searchlisting->c_name_generic_c)."</div>";
            
            
             
             
			$html .= "		</div>";
			
			
	        $html .= "	</div>";
	       
	        $html .= "</div>";
				
				
				

				
				
			if(is_user_logged_in() ){
				$html .= '<form action="/listing/'.sanitize_title($searchlisting->c_name_generic_c).'" method=post><input type=hidden name="action" value="add_to_portfolio" /><input type=hidden name="id" value="'. $searchlisting->id.'" /><input type=submit style="display:none; margin-bottom:18px;" value="'. __('Add to my portfolio','bbcrm').' &#10010;" class="portfolio_action_button portfolio-add"  /></form>';
				}
			}
		}
	}
}
else{
	$qy = (empty($qy))?"your search":'"'.$qy.'"';
	$html .= "<h2>No results were found for ".$qy."</h2>";
	$html .= "<p>Please check your spelling or try a search with different parameters.</p>";
	// $html .= do_shortcode('[featuredsearch]');
}



echo '<h1 style="padding-top: 22px;">'.get_the_title().'</h1>';
echo '<p>'.get_the_content().'</p>';

if(!empty($listingids)){
	echo __("Your search returned ",'bbcrm');
	echo $totalposts;
	echo ($totalposts===1)?__(' result.','bbcrm'):__(' results.','bbcrm');
}

if ($maxPages > 1) {
?>

<div class="clearFix row-listing col-md-12">
							<div class="clearFix pagination-header">
								<form class="sortForm pull-left" id="orderByForm">
									<select id="sort_listings" name="sort" data-title="Sort By" data-header="Sort By" class="selectpicker show-menu-arrow show-tick" >
										<option class="removeThis"></option>
										<option value="sortRecent" <?php if ($sort_order_param == 'sortRecent') { ?>selected="selected"<?php } ?>>Most Recent</option>
										<option value="sortOldest" <?php if ($sort_order_param == 'sortOldest') { ?>selected="selected"<?php } ?>>Oldest Listings</option>
										<option value="sortPricel" <?php if ($sort_order_param == 'sortPricel') { ?>selected="selected"<?php } ?>>Price (Low - High)</option>
										<option value="sortPriceh" <?php if ($sort_order_param == 'sortPriceh') { ?>selected="selected"<?php } ?>>Price (High - Low)</option>
									</select>
									<input name="orderType" value="sale" type="hidden">
								</form>
							<div class="pull-right">
								<?php echo pagination($maxPages,$pageNo,$lpm1,$prev,$next,$maxPerPage, $totalposts); ?>
							</div>
						</div>
					</div>
					
<?php
}
echo $html;

//get_template_part("home","search");
?>  

       
				</div>
			</div>      
		</div>
	</div>  
	
		 
</section>
 <div style="vertical-align:bottom; margin-top: 18px;" class="col-lg-12">
		  <p class="align-center"><a href="/free-business-appraisal"><img class="img-responsive" src="/wp-content/uploads/2016/07/abs-970X90.jpg"></a></p>
		  </div>

<?php get_footer(); ?>
