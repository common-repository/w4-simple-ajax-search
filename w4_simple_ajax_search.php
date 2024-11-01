<?php
/**
 * Plugin Name:       W4 Simple Ajax Search

 * Description:       Simple ajax results search input. Searches in pages and posts titles and returns a list of links to those pages/posts
 * Version:           1.0
 * Author:            theW4
 * Author URI:        https://thew4.co
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


include_once "options_page.php"; 


add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'w4_sas_salcode_add_plugin_page_settings_link');
function w4_sas_salcode_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'options-general.php?page=w4-sas-admin' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}



add_action( 'init', 'w4_sas_script_enqueuer' );


function w4_sas_script_enqueuer() {
    
    

    wp_register_style( 'w4_sas_search', plugins_url('includes/w4_sas_search.css',__FILE__),false,'1','all');
    
    
   wp_register_script( 'w4_sas_search',  plugins_url('includes/w4_sas_search.js',__FILE__), array ( 'jquery' ), 1, true);
   wp_localize_script( 'w4_sas_search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   
   wp_enqueue_style( 'w4_sas_search' );

    
    
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'w4_sas_search' );
}



add_action("wp_ajax_w4_sas_search", "w4_sas_search");
add_action("wp_ajax_nopriv_w4_sas_search", "w4_sas_search");

// define the function to be fired for logged in users
function w4_sas_search() {
        
    $noResults;   
    $op=get_option('w4_sas_option');
     if(!isset( $op['no_results_text'] )||$op['no_results_text']==''){
            $noResults='No results';
        } else{
         $noResults=$op['no_results_text'];
     }      
          
  
   $searchQuery = sanitize_text_field($_REQUEST['searchQuery']);
  
 
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      
       $post_types=$op['post_types'];
       $args = array(
        'post_type' =>  $post_types,
        'post_status' => 'publish',
    'posts_per_page'=>-1
       
    );
       
       
       $the_query = new WP_Query($args);
$hasResults=0;
$output;
// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) :
	$the_query->the_post();
	
	$title = get_the_title();
    
    
	if (stripos($title,$searchQuery) !== false&&stripos($title,$searchQuery) == 0) {
		$hasResults=1;
		
		$output.='<li><a href="'.get_permalink().'">';
		
		$output.=  get_the_title();
		
		$output.='</a></li>';
		
		
	}
		endwhile;
	}
	if($hasResults==1){echo $output;}else{
		
		echo '<li class="notFound">'.$noResults.'</li>';
      
	}
       
       wp_reset_postdata();
       
       
       
       
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }

  
   die();
}



function w4_sas_search_field_func( $atts ){
	?>
     <div class="w4SearchArea">
      <?php
            
    $placeholder;   
    $op=get_option('w4_sas_option');
    
  
    
     if(!isset( $op['placholder'] )||$op['placholder']==''){
            $placeholder='Default: Type search term here';
        } else{
         $placeholder=$op['placholder'];
     }      
            ?>
    <input type="text" class="w4SearchBar" placeholder="<?=$placeholder;?>"/>    
        <div class="searchResults" style="touch-action: none;">
					<ul></ul>	
        </div>
        
    </div> 

<?php
}
add_shortcode( 'w4_sas_search_field', 'w4_sas_search_field_func' );