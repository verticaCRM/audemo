<?php
/*
Template Name: Broker Search
*/
//ini_set('display_errors','on');
//error_reporting(E_ALL);
global $wp_query,$url;
if(is_user_logged_in()){
//...only if logged in?...
$userdata = get_userdata(get_current_user_id());
}
$bbcrm_option=get_option("bbcrm_settings");
get_header();
?>
<section id="content" class="container" style="font-size: 0;">
     
     
				   <div class="col-md-3 sidebar_content "id="sidebar"  style="vertical-align:top;width:25%; height:1100px;" role="complementary">
				
							  <div class="panel-group" id="accordion">
										<div class="panel panel-default">
										  <div class="panel-heading">
											<h4 style="line-height: 40px;" class="panel-title">
											  <a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
												Business Search
											  </a>
											</h4>
										  </div>
										  <div id="collapseOne" class="panel-collapse collapse">
											<div class="panel-body">
											   <?php echo do_shortcode('[featuredsearch]');?>
											</div>
										  </div>
										</div>
 
 
									  </div>
						
									  <br> 
									<div class="sidebar_search_by_id_container" >
										<h3 class="panel-title">
										  Find by ID
										</h3>
										<?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	
									</div>
									  <br>
										<div class="" >
									  <p class="align-center">
										  <a href="/free-business-appraisal"><img class="img-responsive" src="/abs-336X280.jpg" ></a>
										  </p>
							  </div>			
				
					  </div>



    
       
             <div style="vertical-align:top;width:100%; margin-top:40px; margin-bottom:40px;">   <h1 class="article-page-head"><?php echo get_the_title();?></h1></div>
                
                     <?php
                     ////////////////////
                      echo get_the_content();
$json = x2apicall(array('_class'=>'Brokers/?_order=-c_position'));
//                     $json = x2apicall(array('_class'=>'Brokers/'));
                     $brokers =json_decode($json);
                     if($brokers){
             echo "<div style='display:inline-block; width:220px; height:250px;'>"; //this is so the page doesn't scroll endlessly.
                     //////////////////
                     $altcss = "#ffffff";
                     foreach ($brokers AS $broker){ //The l
                         if("Active" == $broker->c_status){
                             $phone_mobile = $broker->c_mobile;
                             $phone_office = $broker->c_office;
                             $broker_position = $broker->c_position;	
                             $altcss = ($altcss == "#ffffff")?"#bababa":"#ffffff";
                             $altclass = ($altcss == "#ffffff")?"theme-background":"";
                             $butclass = ($altcss == "#ffffff")?"altbrokerprofilebutton":"brokerprofilebutton";
                     ?>
                        
         <div id="broker-<?php echo $broker->id;?>" class="brokeritem" style="display:inline-block;width:220px;padding:10px;min-height:250px;vertical-align: top;">
        
						 <form method=POST action="<?php echo get_permalink($bbcrm_option["bbcrm_pageselect_broker"]);?>">
                             <?php
                                 if($broker->c_profilePicture){
                                     $json = x2apicall(array('_class'=>'Media/by:fileName='.$broker->c_profilePicture.".json"));
                                     $brokerimg =json_decode($json);
                                     echo '<div style="display:inline-block;width:200px;height:180px;overflow:hidden;"><input type=image src="http://'.$apiserver.'/uploads/media/'.$brokerimg->uploadedBy.'/'.$brokerimg->fileName.'" style="max-height:180px;" class="pImg"  /></div>';  
                                 }else{
                             //print_r($broker);
                                     echo '<div style="display:inline-block;width:200px;height:180px;overflow:hidden;"><img src="http://'.$apiserver.'/uploads/media/marc/broker-'.$broker->c_gender.'.png" style="width:100%" class="pImg" /></div>';
    
                                 }
                             ?>
       
						 <div style="font-size:14px;font-weight: 200;color: #999;clear:both">
						<h4><?php echo $broker->name;?></h4> 
						<?php echo $broker_position; ?>
						 </div>
						<br clear=all>
						 <div style="font-size: 7.5px;font-weight: 100;margin-top:-20px;" class="property_detail">
		              <input type=hidden name=eid value="<?php echo $broker->nameId; ?>">
		              <input style="background-color:#fff;color:#fff;" class="brokerprofilebutton" 
		              type=submit value="-">
                       </form>	
						 
						    
            
            
            </div>     
     </div>
    </div>

<!--portfolioitem-->
<?php 
        } //end if active
    }
}
?>

 <div style="vertical-align:bottom;" class="col-lg-12">
		  <p class="align-center"><a href="/free-business-appraisal"><img class="img-responsive" src="http://audemo.businessbrokerscrm.com/wp-content/uploads/2016/07/abs-970X90.jpg"></a></p>
		  </div>
  
</section><!-- #primary .widget-area -->
<?php
get_footer();
?>
