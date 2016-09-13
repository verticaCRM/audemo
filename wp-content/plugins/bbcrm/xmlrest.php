<?php

global $wp_query;

include_once ("_auth.php");

function xml2array ( $xmlObject, $out = array () )
{
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ||  is_array ( $node ) ) ? xml2array ( $node ) : $node;
		return $out;
}

function xmlstring2array($string)
{
    $xmlFile   = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
    $array = json_decode(json_encode($xmlFile), TRUE);
    return $array;
}


$json = x2apicall(array('_class'=>'Brokers/13.json'));
$defaultBroker = json_decode($json);

$json = x2apicall(array('_class'=>'users?emailAddress='.$defaultBroker->c_email));
$defaultUser = json_decode($json);

$myXMLFile = plugin_dir_path(__FILE__).'assets/absbusi_ALL_ACTIVE_2016-08-02_08-54-50.xml';
$myXMLFilePath = '/home/absbus/public_html/xml/';
$getfile = file_get_contents($myXMLFile);

// GET $_REQUEST
$xmlListing = xmlstring2array($getfile);
//echo dirname(__DIR__.'/assets/').'/assets/';
$dir = new DirectoryIterator(dirname(__DIR__.'/assets/').'/assets/');
//print_r($dir);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        var_dump($fileinfo->getFilename());
    }
}

exit;


foreach($xmlListing["business"] AS $idx=>$business){

echo "<h3>".$business["uniqueID"]."</h3>";
echo 'Clistings/by:c_uniqueID='.$business["uniqueID"].';.json';
	$json = x2apicall(array('_class'=>'Clistings/by:c_uniqueID='.$business["uniqueID"].';.json'));
	$listing = json_decode($json);

print_r($listing);

	if($listing->status =="404"){ //it does not exist; create.
//print_r($business);
	$json = x2apicall(array('_class'=>'users?emailAddress='.$business["listingAgent"]["email"]));
	$user = json_decode($json);
	$assignedTo = $user[0]->username;
	$brokerName = $user[0]->fullName;
if(empty($assignedTo)){
	$assignedTo = $defaultUser[0]->username;
	$brokerName = $defaultUser[0]->fullName;
}

//Set the data array
$towncodeAr = array(
	'qld'=>"Queensland",
);

$salesStage = ($business["@attributes"]["status"]=="current")?"Active":"Pending";
$salesStage = ($business["underOffer"]["@attributes"]["value"]=="yes")?"LETTER OF INTENT":$salesStage;

$address = (is_array($business["address"]["streetNumber"])?$business["address"]["streetNumber"][0]:$business["address"]["streetNumber"])." ".$business["address"]["street"];
$buscats = array();

foreach($business["businessCategory"] AS $idx=>$buscat){
	$buscats[] = '"'.$buscat["businessSubCategory"]["name"].'"';
}

$data = array(
	'c_uniqueID'=>$business["uniqueID"],
	'name'=>"BUSINESS NAME REQUIRED",
	'assignedTo'=>$assignedTo,
	'c_currency_id'=>'$',
	'c_listing_askingprice_c'=>$business["price"],
	'c_listing_exclusive_c'=>($business["exclusivity"]["@attributes"]["value"]=="exclusive")?"Exclusive Listing":"Open Listing",
	'c_listing_franchise_c'=>($business["franchise"]["@attributes"]["value"]=="no")?"No":"Yes",
	'description'=>$business["description"],
	'c_name_generic_c'=>$business["headline"],
	'c_listing_address_c'=>$address,
	'c_listing_city_c'=>$business["address"]["suburb"],
	'c_listing_town_c'=>$towncodeAr[$business["address"]["state"]],
	'c_listing_postal_c'=>$business["address"]["postcode"],
	'c_businesscategories'=>"[".join(",",$buscats)."]",
	'c_sales_stage'=>$salesStage,
	'c_date_modified'=>strtotime(substr_replace($business["@attributes"]["modTime"],' ',10,1)),
	'c_commercialListingType'=>ucfirst($business["commercialListingType"]["@attributes"]["value"]),
	'c_priceView'=>$business["priceView"],
	'c_ExpiryDateOfAppointment'=>'',
	'c_ListingPrice'=>$business["price"]	
);
echo"<BR><BR>!!!!!!!!!<BR>";
print_r($data);

echo"<BR><BR>!!!!!!!!!<BR>";
//x2apipost
$json = x2apipost( array('_class'=>'Clistings/','_data'=>$data ) );
print_r($json);

	}else{ //it exists; modify?

	}


}//end business for loop

?>
