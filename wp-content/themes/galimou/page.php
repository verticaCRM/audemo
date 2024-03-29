<?php
/*
Template Name: Post page
*/
?>
<?php get_header();?>


<div class='content'>
		<div class='container'>
		
		<div class="row ">

				<div class="col-md-3 sidebar_content">
				
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
				
		</div>

				<div  id="business_container" class="col-md-9 searchlists_container">
                       <h1 style="text-align:left; padding-top: 22px;"> <?php echo get_the_title( $ID ); ?></h1>
		
		
		
		
		
		
		

					 <?php if (have_posts()): while (have_posts()): the_post(); ?>
					 <p><?php the_content(); ?></p>
					 <?php endwhile; else : ?>
					 <p><?php _e( 'Sorry No Pages Found.');?></p>
					 <?php endif; ?>
									 
      </div>
</div>
 
			<p style="text-align: center !important;"><a style="text-align:center; margin-right: 3%;" href="/free-business-appraisal/"><img class="aligncenter" src="/wp-content/uploads/2016/07/abs-970X90.jpg" width="970" height="90" /></a></p>		 
					 
      </div>
</div>

<?php get_footer();?>
