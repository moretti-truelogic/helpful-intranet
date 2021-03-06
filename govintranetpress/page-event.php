<?php
/* Template name: Event page */
	$cdir=$_GET['cdir'];
	$eventcat = $_GET['cat'];

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>


		<div class="row">

			<div class="eightcol white last" id='content'>
				<div class="row">
							<div class='breadcrumbs'>
							<?php if(function_exists('bcn_display') && !is_front_page()) {
								bcn_display();
							}?>
							</div>
							
				</div>
				<div class="content-wrapper">
											<?php 
							echo "<h1>";
							if ($cdir!='b') {
								echo "Events";
							} else { 
							echo "Past events" ;
							}
			if ($eventcat==""){
				if ($cdir=="b"){
					$timetravel = "<div class='futureevents'><a href='".home_url( '/' )."events/?cdir=f'>Future events &raquo;</a></div>";
				}
				else
				{
					$timetravel = "<div class='pastevents'><a href='".home_url( '/' )."events/?cdir=b'>&laquo; Past events</a></div>";
				}
			} else {
				if ($cdir=="b"){
					$timetravel = "<div class='futureevents'><a href='".home_url( '/' )."events/?cdir=f&cat=".$eventcat."'>Future events &raquo;</a></div>";
				}
					else
				{
					$timetravel = "<div class='pastevents'><a href='".home_url( '/' )."events/?cdir=b&cat=".$eventcat."'>&laquo; Past events</a></div>";
				}																
			}

							$pub = get_terms( 'category', 'orderby=count&hide_empty=1' );
							//print_r($pub);
							$cat_id = $_GET['cat'];;
							if (count($pub)>0 and $cat_id!=''){
								foreach ($pub as $sc) { 
									if ($cat_id == $sc->slug) { echo ' - '.$sc->name; } 
								}
							}
							echo "</h1>";
							?>
							

<!--
<ul class="nav nav-pills">

    <li<?php if (!$cat_id) { echo ' class="active"'; } ?>><a href="<?php echo home_url( '/' ); ?>about/events/<?php if ($cdir=='b'){echo '?cdir=b';} ?>">All events</a></li>
<?php
	foreach ($pub as $sc) {
	//print_r($sc);
	$cat_desc = get_term($sc, 'category');
		if ($sc->name != 'Uncategorized'){
	?>
		    <li<?php if ($cat_id == $sc->slug ) { echo ' class="active"'; } ?>><a href="<?php echo home_url( '/' ); ?>events/?cat=<?php echo $sc->slug; if ($cdir=='b'){echo '&cdir=b';} ?>"><?php echo $sc->name; ?></a></li>

<?php
		}
	}
?>
</ul>
-->
<?php the_content(); ?>
							
							<?php
							echo "<br>".$timetravel;							
							$tdate= getdate();
							$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
							$tday = date( 'd' , strtotime($tdate) );
							$tmonth = date( 'm' , strtotime($tdate) );
							$tyear= date( 'Y' , strtotime($tdate) );
							$sdate=$tyear."-".$tmonth."-".$tday." 00:00";


							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			if ($cat_id!=''){ // show individual theme conferences
				if ($cdir=="b"){  //past events

				$cquery = array(

				    'tax_query' => array(
				        array(
				            'taxonomy' => 'category',
				            'field' => 'slug',
				            'terms' => $cat_id,
					       ),
					    ),
	
				   'meta_query' => array(
							       array(
						           		'key' => 'event_start_date',
						        	   'value' => $sdate,
						    	       'compare' => '<=',
						    	       'type' => 'DATE' )  
					    	        ),   
								    'orderby' => 'meta_value',
								    'meta_key' => 'event_start_date',
								    'order' => 'DESC',
								    'post_type' => 'event',
									'posts_per_page' => 10,
								    'paged' => $paged												
								)
								;
				}
				else { //future events, single theme
					$cquery = array(

				    'tax_query' => array(
				        array(
				            'taxonomy' => 'category',
				            'field' => 'slug',
				            'terms' => $cat_id,
					       ),
					    ),	

				   'meta_query' => array(
							       array(
						           		'key' => 'event_start_date',
						        	   'value' => $sdate,
						    	       'compare' => '>=',
						    	       'type' => 'DATE' ) 
					    	        ),   
								    'orderby' => 'meta_value',
								    'meta_key' => 'event_start_date',
								    'order' => 'ASC',
								    'post_type' => 'event',
									'posts_per_page' => 10,
								    'paged' => $paged												
								)
								;

				}	
				}else { //all themes
			if ($cdir=="b"){ //past events
			$cquery = array(
					'post_type' => 'event',
					'posts_per_page' => 10,
				   'meta_query' => array(
			       array(
		           		'key' => 'event_start_date',
		        	   'value' => $sdate,
		    	       'compare' => '<=',
		    	       'type' => 'DATE' )
		    	        ),   
				    'orderby' => 'meta_value',
				    'meta_key' => 'event_start_date',
				    'order' => 'DESC',
				    'paged' => $paged
					);
			}
			else { // future events, all themes
			$cquery = array(
					'post_type' => 'event',
					'posts_per_page' => 10,
				   'meta_query' => array(
			       array(
		           		'key' => 'event_start_date',
		        	   'value' => $sdate,
		    	       'compare' => '>=',
		    	       'type' => 'DATE' )
		    	        ),   
				    'orderby' => 'meta_value',
				    'meta_key' => 'event_start_date',
				    'order' => 'ASC',
				    'paged' => $paged
					);
			
			}
			}
			//print_r($cquery);
							$customquery = new WP_Query($cquery);
							
							if (!$customquery->have_posts()){
								echo "<p>Nothing to show.</p>";
							}
							
								
							if ( $customquery->have_posts() ) {
				
								while ( $customquery->have_posts() ) {
									$customquery->the_post();
								echo "<div class='featured_story clearfix'>";
									if ( has_post_thumbnail( $post->ID ) ) {
										the_post_thumbnail('thumbnail',"class=alignleft");
									}	
									
									echo "<h2><a href='" .get_permalink() . "'>" . get_the_title() . "</a></h2>";
									//$thisdate = date('d F',get_post_meta($post->ID,'start_date'));
									$thisdate =  get_post_meta($post->ID,'event_start_date',true); //print_r($thisdate);
									$thisyear = substr($thisdate[0], 0, 4); 
									$thismonth = substr($thisdate[0], 4, 2); 
									$thisday = substr($thisdate[0], 6, 2); 
//									$thisdate = $thisyear."-".$thismonth."-".$thisday;
									echo "<strong>".date('j M Y',strtotime($thisdate))."</strong>";
									the_excerpt();
									echo "</div><div class='clearfix'></div>";
								}
							}
							
							wp_reset_query();
							
							echo $timetravel;
							?>
					
						
						<?php if (  $customquery->max_num_pages > 1 ) : ?>
							<?php if (function_exists(wp_pagenavi)) : ?>
								<?php wp_pagenavi(array('query' => $customquery)); ?>
	 						<?php else : ?>
								<?php next_posts_link('&larr; Older items', $customquery->max_num_pages); ?>
								<?php previous_posts_link('Newer items &rarr;', $customquery->max_num_pages); ?>						
							<?php endif; ?>
						<?php endif; ?>



				</div>
			</div>
			<div class="fourcol last">
			<?php
				$taxonomies=array();
				$post_type = array();
				$taxonomies[] = 'category';
				$post_type[] = 'event';
				$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
				if ($post_cat){
					echo "<div class='widget-box'><h3 class='widget-title'>Categories</h3>";
					echo "<p class='taglisting {$post->post_type}'>";
					echo "<span class='wptag t'><a href='/events/?cdir=".$cdir."'>All</a></span> ";
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						echo "<span class='wptag t".$cat->term_id."'><a href='/events/?cat=".$cat->slug."&cdir=".$cdir."'>".$newname."</a></span> ";
					}
					}
					echo "</p></div>";
				}
			?>
			
			<div class="widget-box">
					<h3 class='widget-title'>Search by tag</h3>
						<?php 
					echo my_colorful_tag_cloud('', 'category' , 'event'); 
					echo "<br>";
					?>

			</div>			
			
			</div>
		</div>
<?php endwhile; ?>

<?php get_footer(); ?>