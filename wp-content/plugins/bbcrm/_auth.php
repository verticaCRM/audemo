<?php
//global $url;
global $username;
global $userkey;
global $apiserver;
include('/var/www/audemo/wp-config.php');
$username = API_USER;
$userkey = API_PASS;
$apiserver = API_HOST;
$url = "$username:$userkey@$apiserver/index.php/api2/";

if(isset($_GET["query"]) && $_GET["query"]=="AJAX"){
	// I LOVE THIS SYSTEM
		//$_GET["data"] should be a querystring
	$qy = $_GET;
	print call_user_func($_GET["action"], $qy);
	exit;
}

function x2apicall($param) {
	global $url, $apiserver, $username, $userkey;
        $curl_request = curl_init();
			
			if(isset($param["_url"]) ){

//echo $param["_url"];
				
				if(strpos("/crm",$param["_url"])>=0){
	//				echo "<p>true</p>";
					$param["_url"] = substr($param["_url"],4); //special from /crm
					//echo $param["_url"];
				}
				
		//		}
			curl_setopt($curl_request, CURLOPT_URL, "$username:$userkey@$apiserver".$param["_url"]);			
			}else{		
			curl_setopt($curl_request, CURLOPT_URL, $url.$param['_class']);

//echo '>>>'.$url.'<<< <br>';
			}
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl_request);
//print_r($result);
        if($result === false){
				echo '<br>Curl error: ' . curl_error($curl_request) . $apiserver.$param["_url"].'||'.$url.$param["_class"];
			}else{
				return $result;
			}
			curl_close($curl_request);
}

function x2apipost($param) {
	global $url;
			//_data is sent as object by php, or sent as object by jQuery. This just keeps getting better!	
			$jsondata = json_encode($param['_data']);
			$format = json_encode($param['_format']);
			$curl_request = curl_init($url.$param['_class']);		
			if(isset($param["_method"])){
			curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, $param["_method"]);			
			}else{		
			curl_setopt($curl_request, CURLOPT_POST, true);
			}
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $jsondata);
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: '. strlen($jsondata)));
			$result = curl_exec($curl_request);
			if($result === false){
				return 'Curl error: ' . curl_error($curl_request);
			}else{
				$code = curl_getinfo($curl_request, CURLINFO_HTTP_CODE);
				if($param['_format']=='json'){
					return json_encode(array($code,$result));				
				}else{
					return array($code,$result);
				}
        }
			curl_close($curl_request);
}


function buyerreg_ajax_getnda($qy) {
	global $url;	
	$json = x2apicall(array('_class'=>'Docs/by:name=Buyer_NDA.json'));
	$nda =json_decode($json);
	
	//$qy is a serialized string. We put it into vars
	parse_str($qy["data"],$vars); //backasswards, but it made sense to someone at some point.

	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($vars["c_broker"]).".json"));
	$broker =json_decode($json);

	$nda->text = str_replace("[brokername]", $broker->name, $nda->text);
	$nda->text = str_replace("[date]", date("F d, Y"), $nda->text);
	 		
	foreach ($vars as $vark=>$varv){
		$nda->text = str_replace("[$vark]", urldecode($varv), $nda->text);			
	}
	return json_encode( array("text"=>$nda->text) );
	//exit;
}

function setpagelistingid($data){
session_start();
//unset($_SESSION["listingid"]);
$_SESSION["listingid"]=$data["_id"];
return json_encode($_SESSION);
//die;
header("Location:".$data["_href"]);
}

function isvalidemail($data){
	if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL) === false) {
		return true;
	} else {
		return false;
	}
	exit;
}

function putfile($data){
			global $url,$username,$userkey,$apiserver;
			
			$files = array();
			$tempimg = imagecreatefromstring(base64_decode($data["image"]));

			imagepng( sys_get_temp_dir()."/temp.png", "TEMP");
		
			
			//$cFile = new CurlFile(sys_get_temp_dir()."/temp.png", 'image/png', "whatEVer.png");
    		$files = array('file' => $cFile);
			$curl_request = curl_init("http://$apiserver/uploads/");
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_request, CURLOPT_BINARYTRANSFER,1);
			curl_setopt($curl_request, CURLOPT_SAFE_UPLOAD,1);
			curl_setopt($curl_request, CURLOPT_POST, true);
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $files );
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Content-Type: image/png') );
        $result = curl_exec($curl_request);
			if($result === false){
				echo 'Curl error: ' . curl_error($curl_request);
			}else{
				echo $result;
			}
        curl_close($curl_request);
        return $result;
}
?>
