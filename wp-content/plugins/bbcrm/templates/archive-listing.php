<?php
global $pagetitle;
session_start();

//ini_set('display_errors','on');
//error_reporting(E_ALL);

global $wp_query;
//print_r($wp_query);

$inportfolio = false;
$crmid = 0;
if(isset($_POST["id"])){
	$crmid = $_POST["id"];
//echo "post:$crmid";
}elseif(isset($_SESSION["listingid"]) ){
	$crmid = $_SESSION["listingid"];
//echo "session:$crmid";
}else{}


if($crmid>0){
$json = x2apicall(array('_class'=>'Clistings/'.$crmid.'.json'));
//echo "byid";
}else{
//print_r($_SERVER);

//echo substr($_SERVER["REQUEST_URI"],6);

$trailing = (substr($_SERVER["REQUEST_URI"],-1)=="/")?"":"/";
$json = x2apicall(array('_class'=>'Clistings/by:c_listing_frontend_url='.substr($_SERVER["REQUEST_URI"].$trailing,6).'.json'));
//echo "byurl";
}
$listing = json_decode($json);

$json = x2apicall(array('_class'=>'Clistings/'.$crmid.'/tags'));
$tags = json_decode($json);
$listingtags = array();
foreach ($tags as $idx=>$tag){
	$listingtags[] = urldecode(substr($tag, 1));
}
//print_r($listingtags);
/* Failsafe. Need to move to create flow */

if(empty($listing->c_listing_frontend_url)){
$json = x2apipost( array('_method'=>'PUT','_class'=>'Clistings/'.$listing->id.'.json','_data'=>array('c_listing_frontend_url'=>'/listing/'.sanitize_title($listing->c_name_generic_c)."/") ) );

//print_r($json);
}

$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($listing->c_assigned_user_id).".json"));
$listingbroker =json_decode($json);

if(!$listingbroker->nameId){
$json = x2apicall(array('_class'=>'Brokers/by:nameId=House%20Broker_5.json'));
$listingbroker =json_decode($json);
}

if(is_user_logged_in() ){

unset($_SESSION["listingid"]);
	
	$json = x2apicall(array('_class'=>'Contacts/by:email='.urlencode($userdata->user_email).".json"));
	$buyer =json_decode($json);

$isuserregistered = ($buyer->c_buyer_status=="Registered")?true:false;
	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($buyer->c_broker).".json"));
	$buyerbroker =json_decode($json);	

if(isset($_POST["add_to_portfolio"]) || isset($_POST['action']) && $_POST["action"]=="add_to_portfolio"){

	$json = x2apicall(array('_class'=>'Portfolio/by:c_listing_id='.$listing->id.";c_buyer=".urlencode($buyer->nameId).".json"));
	$prevlisting =json_decode($json);	

	if(!$prevlisting->status || $prevlisting->status=="404"){
	$data = array(
		'name'	=>	'Portfolio listing for '.$listing->name,
		'c_listing'	=>	$listing->name,
		'c_listing_id'	=>	$listing->id,
		'c_buyer'	=>	$buyer->nameId,
		'c_buyer_id'	=>	$buyer->id,
		'c_release_status'	=>	'Added',
		'assignedTo'	=>	$buyerbroker->assignedTo,
	);

//print_r($data);
	$json = x2apipost( array('_class'=>'Portfolio/','_data'=>$data ) );
	$portfoliolisting =json_decode($json[1]);

//print_r($portfoliolisting);

	$json = x2apicall(array('_class'=>'Portfolio/'.$portfoliolisting->id.'.json'));
	$portfoliorelationships =json_decode($json);
	
	$json = x2apicall( array('_class'=>'Portfolio/'.$portfoliorelationships->id."/relationships?secondType=Contacts" ) );
	$rel = json_decode($json);
//echo "!!!";
//print_r($rel);

	$json = x2apipost( array('_method'=>'PUT','_class'=>'Portfolio/'.$portfoliolisting->id.'/relationships/'.$rel[0]->id.'.json','_data'=>$data ) );

//	print_r(json_decode($json));

	}
}
//print_r($buyer);
//print_r($listing);

//Is this listing in the user's portfolio?	
	$json = x2apicall(array('_class'=>'Portfolio/by:c_listing_id='.$listing->id.';c_buyer='.urlencode($buyer->nameId).'.json'));
	
	$portfoliolisting =json_decode($json);	
//echo"<br><br>".'Portfolio/by:c_listing_id='.$listing->id.';c_buyer='.urlencode($buyer->nameId).'.json';
//print_r($portfoliolisting);
	if($portfoliolisting->id){
	$inportfolio=true;		
		}
}
//////////////////
//print_r($listing);

		$status =$listing->c_sales_stage;
		$listing_id =$listing->id;
		$listing_dateapproved = $listing->c_listing_date_approved_c;
		$generic_name =$listing->c_name_generic_c;
		$description =$listing->description;
		$region=$listing->c_listing_region_c;
		$terms=$listing->c_listing_terms_c;
		$currency_symbol=$listing->c_currency_id;
		$grossrevenue=number_format($listing->c_financial_grossrevenue_c);
		$amount=number_format($listing->c_listing_askingprice_c);
		$downpayment=number_format($listing->c_listing_downpayment_c);
		$ownercashflow=number_format($listing->c_ownerscashflow);
		$brokername = $listing->assignedTo;
		$brokerid = substr($listing->c_assigned_user_id, strpos($listing->c_assigned_user_id, "_") + 1);;
		$categories = 	join(",",json_decode($listing->c_businesscategories)); //

$_SESSION["viewed_listings"][$listing_id] = array("brokerid"=>$listingbroker->name,"listingname"=>$generic_name);

	$cssclass = '';

if("Active"!=$status){
	//this listing is marked as inactive. This shouldn't be visible. Fail gracefully.
//echo $status;	
}
if( is_user_logged_in() ){
if($portfoliolisting->c_release_status== "Released"){
	$isaddressreleased = true;
	$cssclass = 'nareq_released';
	$generic_name = $listing->name_dba_c.' "'.$generic_name.'" ';
	$address = $listing->listing_address_c."<br>";
	$city = $listing->listing_city_c." ";
	$postal = $listing->listing_postal_c."<br>";
}

}

//wp_enqueue_script('googlemap',get_stylesheet_directory_uri().'/js/google.js',array('jquery'));
wp_enqueue_script('galleria',get_stylesheet_directory_uri().'/js/galleria-1.4.2.min.js',array('jquery'),'1.4.2');
wp_enqueue_script('galleriatheme',get_stylesheet_directory_uri().'/themes/classic/galleria.classic.min.js',array('jquery'));
wp_enqueue_style('galleriacss',get_stylesheet_directory_uri().'/themes/classic/galleria.classic.css');

$pagetitle = get_bloginfo('name')." - ".$listing->c_name_generic_c;
//add_filter( 'the_title', function( $title ) { return get_bloginfo('name')." - ".$listing->c_name_generic_c; },1 );

get_header();
?>


<div class="container-fluid">

<div class="row" >
	<div class="col-md-12  col-sm-12 ">
        <ol class="breadcrumb ">
          <?php ariwoo_breadcrumbs(); ?>
        </ol>
          <h2 class="theme-blue"><?php the_title(); ?></h2>
      </div>
</div>   


<?php 
if( is_user_logged_in() ){
if($portfoliolisting->c_release_status== "Deleted"){
	echo '<div class="portfoliostatus deleted">&#10006; ' .	__("This property was removed from your portfolio",'bbcrm') . "</div>";
}elseif($isaddressreleased){
echo '<div class="portfoliostatus released"> &#9733; ' .	__("The address of this business is available to you",'bbcrm') . "</div>";
}elseif($inportfolio){
echo '<div class="portfoliostatus added">&#10003; ' .	__("This propery is in your portfolio",'bbcrm') . "</div>";
}
}
?>
<div style="width:100% !important" id="" role="main">
<div style="padding-top:10px;">
<h1 style="line-height: 12px !important; padding-left: 18px;" class="theme-color1 property-title  entry-title <?php echo $cssclass;?>"><?php echo $generic_name; ?></h1>
<h3 style="line-height: 12px !important; padding-left: 18px;" class="theme_gray "><?php if($userdata){ if($isaddressreleased): ?>
<?php echo $listing->name." ".$address;
 endif; 
} 
echo $city." ".$region;
?>
</h3>
</div>
				
		



      
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Search Businesses
					</h3>
				</div>
				<div class="panel-body">
					sidebar
					<?php } ?>						
<?php echo wp_get_attachment_image( 5575, 'full', 0, array('class'=>'contactbroker','data-buyerid'=>$buyer->id,'data-listingid'=>$listing->id,'data-portfolioid'=>$portfoliolisting->id) ); ?>						
<?php
	        dynamic_sidebar( "property-registered" ); 
		}else{
			get_sidebar("visitor");
			dynamic_sidebar( "property-unregistered" ); 
		}     
            dynamic_sidebar("content-sidebar");   
 ?>
				</div>
			   </div>
		</div>
		<div class="col-md-9">
			<div class="jumbotron">
				
				<p>
					Info Stats top box here
					
					
					<div class="property_detail" id="property_listing_id" data-id="<?php echo $listing_id;?>"><label><?php _e("Listing ID:", 'bbcrmint');?></label> #<?php echo $listing_id; ?></div>	
					<div class="property_detail"><label><?php _e("State:", 'bbcrmint');?></label> <?php echo $region;?></div>
					<div class="property_detail"><label><?php _e("Categories:", 'bbcrmint');?></label> <?php echo $categories; ?></div>
					<div style="width:100% !important; display:inline-block; float: left; border-bottom: 1px solid #6b9f67; text-align:left !important;margin-bottom: 5px;" class=""><label style="width: 100px !important;"><?php _e("Asking Price:", 'bbcrmint');?></label> <div style="background-color: #6b9f67; padding-left:7px;float:right; width:95px; "><?php echo $currency_symbol." ".$amount;?></div></div>
				</p>
				<p><button style="background-color:cornflowerblue;text-shadow: 0 1px 0 cornflowerblue !important; color:white; font-weight:normal !important; border-radius:0px" type="button" class="btn btn-default btn-med">
                       <i class="fa fa-save"></i> Save
                   </button>
					<a class="btn btn-primary btn-large" href="#">Save request</a>
				</p>
			</div>
			
			<div class="jumbotron">
				
				<p>
				    Image here
				    <?php
global $wpdb;
$results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.galleryId = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$listing->id, OBJECT );

//print_r($results);

if(!empty($results[0]->id)):
?>
						
						
<div class="galleria">
<?php
foreach ($results as $image){
//echo $image->file_name;
//echo substr($image->file_name,-3);
//echo "<div style='display:inline-block;padding:4px;width:200px;height:200px;overflow:hidden;vertical-align:middle;margin-right:2px;'><img style='width:100%' src='/crm/uploads/gallery/_".$image->id.".jpg' /></div>";
echo "<img src='/crm/uploads/gallery/_".$image->id.".jpg' />";
}

?>
</div>
<script>
    //Galleria.loadTheme('/wp-content/');
    Galleria.run('.galleria', {
    height: 400,
width:750,
debug:true
});
</script>
<?php 
endif; ?>	
	
				</p>
				
			</div>
			
			<div class="row">
				<div class="col-md-6">
					<h2 class="theme-color1"><?php _e("Business Description","bbcrm");?></h2>
					<p>
						details here
						

					<div class=""><?php echo nl2br($description); ?></div>
					<hr>

<?php 
	$detailsheader = __("Detailed Information", 'bbcrmint');
if($isuserregistered && $inportfolio){
	$detailsheader = __("Complete Business Profile", 'bbcrm');
}
 ?>
					<h3 class=detailheader onclick='jQuery("#propertydetails ").slideToggle()'><?php echo $detailsheader;?></h3>
					</p>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">
								Your Business Broker
							</h3>
						</div>
						<div class="panel-body">
							Panel content
							<?php
if($brokerimg->fileName){
?>							
							<img src="<?php echo "http://".$apiserver."/uploads/media/".$brokerimg->uploadedBy."/".$brokerimg->fileName;?>" height=150 align=left />
<?php } ?>                  <h5 class=detailheader style="cursor:pointer; width:100%; color:#ffffff !important;margin-bottom:0;" onclick='jQuery("#brokerdetails ").slideToggle()'><?php _e('Business Listed By:');?></h5>
							<h4 style="color:darkorange !important; margin:1px 0; "><?php echo $listingbroker->name ;?></h4>
							Cell:<?php echo $listingbroker->c_mobile;?><br>
							Office:<?php echo $listingbroker->c_office;?><br>
<?php if(is_user_logged_in()){ 
//print_r($buyer);
?>	

<?php if(is_user_logged_in()){ 

$json = x2apicall(array('_class'=>'Media/by:fileName='.$buyerbroker->c_profilePicture.".json"));
$brokerimg =json_decode($json);
?>						

							<button style="background-color:cornflowerblue;text-shadow: 0 1px 0 cornflowerblue !important; color:white; font-weight:normal !important; border-radius:0px" type="button" class="btn btn-default btn-med">
                       <i class="fa fa-save"></i> Save
                   </button>
						</div>
					   </div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">
								Business Links Tools
							</h3>
						</div>
						<div class="panel-body">
							Panel content
							<button style="background-color:cornflowerblue;text-shadow: 0 1px 0 cornflowerblue !important; color:white; font-weight:normal !important; border-radius:0px" type="button" class="btn btn-default btn-med">
					<i class="fa fa-print"></i> Print
				    </button>
						</div>
						
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">
								Map
							</h3>
						</div>
						<div class="panel-body">
						
							Google map here
							
							<?php if( $inportfolio ): 
					//print_r($listing);
					if($isaddressreleased){
					?>
                    <br />
					<h4 class=detailheader><?php _e("Location", 'bbcrm');?></h4>
					<div class="property_detail"><label><?php _e("Address:", 'bbcrmint');?></label> <?php echo $listing->c_listing_address_c;?></div>
					<div class="property_detail"><label><?php _e("City:", 'bbcrmint');?></label> <?php echo $listing->c_listing_city_c;?></div>
					<div class="property_detail"><label><?php _e("State:", 'bbcrmint');?></label> <?php echo $listing->c_listing_region_c;?></div>					
					<div class="property_detail"><label><?php _e("Zip/Postal:", 'bbcrmint');?></label> <?php echo $listing->c_listing_postal_c;?></div>
						</div>
					
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script>
document.title = '<?php echo $pagetitle;?>';
</script>
<?php
get_footer();
?>
</div>

