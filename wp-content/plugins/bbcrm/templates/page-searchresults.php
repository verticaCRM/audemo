<?php
/*
Template Name: Search Results
*/
session_start();
//ini_set('display_errors',true);
//error_reporting(E_ALL);

$postmeta = get_post_meta( get_the_ID() );

foreach($_REQUEST as $k=>$v){
	if( $v=='' ){
		unset($_REQUEST[$k]);
	}
}

// echo '<pre>'; print_r($_REQUEST); echo '</pre>';
// Grab our filters

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
	//$home = 'id='.$_REQUEST["id"];
	$home = 'c_listing_frontend_id_c='.$_REQUEST["id"]; // based on Ref ID not on the ID from DB
	$get_params .= '&'.$home;
}

if(isset($_REQUEST["c_listing_region_c"]) && !empty($_REQUEST["c_listing_region_c"])){
	$home = 'c_listing_town_c=:multiple:__'.$_REQUEST["c_listing_region_c"];
	$get_params .= '&'.$home;
}

if(isset($_REQUEST["c_listing_town_c"]) && !empty($_REQUEST["c_listing_town_c"])){
	$home = 'c_listing_town_c='.$_REQUEST["c_listing_town_c"];
	$get_params .= '&'.$home;
}



/**
	LAST ADDED FIELDS
*/
if(isset($_REQUEST["c_keyword_c"]) && !empty($_REQUEST["c_keyword_c"])){
	$keyword = trim($_REQUEST["c_keyword_c"]);
	$get_params .= '&c_name_generic_c=:multiple:__'.$keyword;
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
		$business_categories[] = trim($v);	
	}
	
	if (count($business_categories)>1)
	{
		$get_params .= '&c_businesscategories=';
		$get_params .= ':multiple:__'.implode('__',$business_categories);	
	}
	elseif (count($business_categories) == 1)
	{
		$get_params .= '&c_businesscategories=:multiple:__'.$business_categories[0];	
	}
}
/**
*/


// echo '<pre>'; print_r($businesscategories); echo '</pre>';


if(isset($_REQUEST["c_listing_askingprice_c"]) && !empty($_REQUEST["c_listing_askingprice_c"])){
	$askingprice_params = explode("|",$_REQUEST["c_listing_askingprice_c"]);
	//$get_params .= '&c_listing_askingprice_c=<'.$askingprice_params[0];
	//$get_params .= '&c_listing_askingprice_c=>'.$askingprice_params[1];
	
	$betweenParam = urlencode('between_'.$askingprice_params[0].'_'.$askingprice_params[1]);
	$get_params .= '&c_listing_askingprice_c='.$betweenParam;
}

if(isset($_REQUEST["c_ownerscashflow"]) && !empty($_REQUEST["c_ownerscashflow"])){
	$ownerscashflow_params = explode("|",$_REQUEST["c_ownerscashflow"]);
	//$get_params .= '&c_ownerscashflow=<'.$ownerscashflow_params[0];
	//$get_params .= '&c_ownerscashflow=>'.$ownerscashflow_params[1];
	
	$betweenParam = urlencode('between_'.$ownerscashflow_params[0].'_'.$ownerscashflow_params[1]);
	$get_params .= '&c_ownerscashflow='.$betweenParam;
}

if(isset($_REQUEST["c_listing_downpayment_c"]) && !empty($_REQUEST["c_listing_downpayment_c"])){
	$listing_downpayment_params = explode("|",$_REQUEST["c_listing_downpayment_c"]);
	//$get_params .= '&c_listing_downpayment_c=<'.$$listing_downpayment_params[0];
	//$get_params .= '&c_listing_downpayment_c=>'.$listing_downpayment_params[1];
	
	$betweenParam = urlencode('between_'.$listing_downpayment_params[0].'_'.$listing_downpayment_params[1]);
	$get_params .= '&c_listing_downpayment_c='.$betweenParam;
}


/**
	*Make the apicall
	*and filter the the decoded json object
*/
//print_r('<pre>');print_r($get_params);print_r('</pre>');
$json = x2apicall(array('_class'=>'Clistings?'.$get_params));
$decoded_json_All = json_decode($json);

//print_r('<pre>');print_r($json);print_r('</pre>');

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
$order_column = '-createDate';
	
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
		$order_column = '+c_listing_askingprice_c';
	}
	elseif ($_GET['sort_order'] == 'sortPriceh')
	{
		$order_column = '-c_listing_askingprice_c';
	}   
}
$sort_order = '&_order='.$order_column;

$get_params = $get_params.'&_limit='.$maxPerPage.'&_page='.$jsonPage.$sort_order;
$jsonLimit = x2apicall(array('_class'=>'Clistings?'.$get_params));
$decoded_jsonLimit = json_decode($jsonLimit);

//print_r('<pre>');print_r($jsonLimit);print_r('</pre>');

//print_r('<pre>');print_r($get_params);print_r('</pre>');

//echo '<pre>'; print_r(count($decoded_jsonLimit)); echo '</pre>';



// Filter Results
//$decoded_json = filter_listings_obj($decoded_json);

$results = $decoded_jsonLimit;
$totalposts = count($decoded_json_All);
$maxPages = ceil(count($decoded_json_All) / $maxPerPage);
$lpm1 = $maxPages - 1;

//print_r('<pre>');print_r('total - '.$totalposts .' - '.$limit);print_r('</pre>');

get_header();

echo do_shortcode('[listmenu menu="Sub Business" menu_id="sub_business" menu_class="au_submenu"]');

?>
<section id="content" data="property"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

				<div class="col-md-3 sidebar_content">
					<?php get_sidebar('page'); ?>
				</div>

				<div id="business_container" class="col-md-9 searchlists_container">
					
	
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


if(count((array)$results) > 0 && $results->status != "404"  &&  $results_false_flag){
	$listingids = array();	

	$listingids = array();	
	{ // Creating a new array to get the parent categories for the child categories
		// Get all categories (kids and parents)
		$json = x2apicall(array('_class'=>'dropdowns/1000.json'));
		$buscats = json_decode($json);
		// Get parent Categories
		$json = x2apicall(array('_class'=>'dropdowns/1085.json'));
		$buscats_par = json_decode($json);
		$parent_cat = '';
		$new_array = array();
		foreach ($buscats->options as $k=>$v)
		{
			foreach ($buscats_par->options as $kk=>$vv)
			{
				if($v == $vv)
				{
					$parent_cat = $vv;
				}
			}
			$new_val = $parent_cat;
			$new_array[$v] = $new_val;
		}
	}

	//echo '<pre>'; print_r($new_array); echo '</pre>';

	foreach ($results as $searchlisting){

		if($searchlisting) {

			if(!in_array($searchlisting->id,$listingids)){
				$listingids[]= $searchlisting->id;
			}
			if(!empty($searchlisting->c_businesscategories)){
				$categories = substr($searchlisting->c_businesscategories,1,-1);
				$categories = explode(',',str_replace('"', '', $categories));
				$cats = '';
				//echo '<pre>'; print_r($categories); echo '</pre>';
				foreach($categories as $cat){
					$cat = trim($cat);
					foreach($new_array as $k=>$v)
					{
						$k = str_replace( array('\/','/'), '', trim($k) );
						$v = trim($v);
						
						//echo '<pre>'; print_r(str_replace( array('\/','/'), '', trim($cat)) . ' => ' .$k); echo '</pre>';
						
						if(str_replace( array('\/','/'), '', trim($cat) ) == $k)
						{
							$class_cat = $v;
						}
					}
					$class_cat = str_replace( array('\/','/'), array('_', '_'), strtolower(stripslashes($class_cat)) );
					$cat = str_replace( '\/', '/', $cat);
					if ($cat != '')
					{
					$cats .='<a class="'.$class_cat.'" href="?c_businesscategories[]='.urlencode(stripslashes($cat)).'">'.$cat.'</a> ';
					}
				}
			}
$json = x2apicall(array('_class'=>'Media/by:associationId='.$searchlisting->id.'.json'));
$thumbnail = json_decode($json);
//print_r('<pre>');print_r($thumbnail);print_r('</pre>');
if (count($thumbnail) > 1)
{
	$thumbnailImg = $thumbnail[0];
}
else
{
	$thumbnailImg = $thumbnail;
}

$img_div = "<div class='searchlisting_featured_image'>";
if(!$thumbnailImg->fileName){
                        $img_div .= '';//<a href="/listing/'.sanitize_title($listing->c_name_generic_c).'" class="listing_link" data-id="'.$listing->id.'"><img src="'.plugin_dir_url(__DIR__).'images/noimage.png"></a>';
                }else{
                        $img_div .= '<a href="/listing/'.sanitize_title($listing->c_name_generic_c).'--'.$listing->id.'" class="listing_link" data-id="'.$listing->id.'"><img src="'.get_bloginfo('url').'/crm/uploads/media/'.$thumbnailImg->uploadedBy.'/'.$thumbnailImg->fileName.'" style="width:100%" /></a>';

                }
$img_div .= "</div>";

                        $html .= "<div class='listing_search_result searchresult'>";
                        $html .= "      <div class='row'>";
                        $html .= "              <div class='col-md-3 searchlisting_photo_box'>";
                $html .=                        $img_div;

	        $html .= "		</div>";
			$html .= "		<div class='col-md-9 searchlisting_content_box'>";
		//Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :) In addition to the only float on the page ( kind you used for the "more button" on home-featured php file )
        	$html .= "<div class='searchlisting_save_ca'>&nbsp;<span class='glyphicon glyphicon-ok-circle'></span>&nbsp;".__("Save/Request CA","bbcrm").$searchlisting->c_listing_id_c."</div>";		
		    $html .= "			<a class='searchlisting_name' href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c).'--'.$searchlisting->id ."\" class=\"listing_link\" data-id=\"". $searchlisting->id ."\">".$searchlisting->c_name_generic_c."</a>";

			// if(is_user_logged_in() ){
			// 	$html .= '<form action="/listing/'.sanitize_title($searchlisting->c_name_generic_c).'" method="post" class="listing_add_to_port_buttton">'.
			// 				'<input type=hidden name="action" value="add_to_portfolio" />'.
			// 				'<input type=hidden name="id" value="'. $searchlisting->id.'" />'.
			// 				'<input type=submit value="'. __('Add to my portfolio','bbcrm').' &#10010;" class="portfolio_action_button portfolio-add"  />'.
			// 			'</form>';
			// }

		    $html .= "			<div class='searchlisting_region'>".__("","bbcrm").$searchlisting->c_listing_region_c;
		    //$html .= "          	<span class='searchlisting_currency_id'>".__("",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_listing_askingprice_c)."</span>";
		    $html .= "          	<span class='searchlisting_currency_id'>".__("",'bbcrm').$searchlisting->c_priceView."</span>";
		    $html .= "			</div>";
			//$html .= "		<div>".__("Cash Flow: ",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_ownerscashflow)."</div>";
		    $html .= "			<div class='searchlisting_description'>".$searchlisting->description."</div>";
			//$html .= "		<div>".__("Contact Seller",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
			//$html .= "		<div>".__("More Info",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
		    $html .= "			<div class='searchlisting_bottom_category'>".$cats."</div>";
			//$html .= "		<div>".$searchlisting->c_listing_businesscat_c."</div>";
			
			 //Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :)
            //$html .= "<div class='searchlisting_bottom_ref'>".__("Reference","bbcrm").$searchlisting->c_listing_id_c."</div>";
			$html .= "		</div>";
	        $html .= "	</div>";

	        $html .= "</div>";
				
				
				

				
				
			if(is_user_logged_in() ){
				$html .= '<form action="/listing/'.sanitize_title($searchlisting->c_name_generic_c).'--'.$searchlisting->id.'" method=post><input type=hidden name="action" value="add_to_portfolio" /><input type=hidden name="id" value="'. $searchlisting->id.'" /><input type=submit style="display:none; margin-bottom:18px;" value="'. __('Add to my portfolio','bbcrm').' &#10010;" class="portfolio_action_button portfolio-add"  /></form>';
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


if(!empty($listingids)){
echo '<h2>'.get_the_title().'</h2>';
echo __("Your search ",'bbcrm');

if(is_array($qy)){
	echo join(",",$qy);
} else {
	echo $qy;
}
_e(" returned ",'bbcrm');
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
								</form>
							<div class="pull-right">
								<?php echo pagination($maxPages,$pageNo,$lpm1,$prev,$next,$maxPerPage, $totalposts); ?>
							</div>
						</div>
					</div>
					
<?php 
}	
	echo $html;
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
								</form>
							<div class="pull-right">
								<?php echo pagination($maxPages,$pageNo,$lpm1,$prev,$next,$maxPerPage, $totalposts); ?>
							</div>
						</div>
					</div>
					
<?php 
}	
//get_template_part("home","search");
?>  

       
				</div>
			</div>      
		</div>
	</div>      
</section>

<?php get_footer(); ?>
