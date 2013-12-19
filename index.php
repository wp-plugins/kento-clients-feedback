<?php
/*
Plugin Name: Kento Clients Feedback
Plugin URI: http://kentothemes.com
Description: Display Cleants Feedback or Testimonials 
Version: 1.0
Author: KentoThemes
Author URI: http://kentothemes.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/*Some Set-up*/
define('KENTO_CF_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
/* Adding Latest jQuery from Wordpress */
wp_enqueue_script('jquery');
wp_enqueue_script('kento_cf_jquery', KENTO_CF_PLUGIN_PATH.'js/kento_clients_feedback.js');
wp_enqueue_style('kento_cf_style', KENTO_CF_PLUGIN_PATH.'css/kento_cf_style.css');
add_filter('widget_text', 'do_shortcode');
add_filter('mce_external_plugins', "kentocf_register");
add_filter('mce_buttons', 'kentocf_add_button', 0);
function kentocf_add_button($buttons){
array_push($buttons, "separator", "kentocf_button_plugin");
return $buttons;
}
function kentocf_register($plugin_array){
$url = KENTO_CF_PLUGIN_PATH."/js/editor_plugin.js";
$plugin_array['kentocf_button_plugin'] = $url;
return $plugin_array;
}

/*Files to Include*/
/* Some setup */
define('KENTO_CF_NAME', "Kento CF");
define('KENTO_CF_SINGLE', "Clients Feedback");
define('KENTO_CF_TYPE', "kento-cf");
define('KENTO_CF_ADD_NEW_ITEM', "Add New Clients Feedback");
define('KENTO_CF_EDIT_ITEM', "Edit Clients Feedback");
define('KENTO_CF_NEW_ITEM', "New Clients Feedback");
define('KENTO_CF_VIEW_ITEM', "View Clients Feedback");

/* Register custom post for Testimonial*/
function Kento_CF_Post_Register() {  
    $args = array(  
        'labels' => array (
			'name' => __( KENTO_CF_NAME ),
			'singular_label' => __(KENTO_CF_SINGLE),  
			'add_new_item' => __(KENTO_CF_ADD_NEW_ITEM),
			'edit_item' => __(KENTO_CF_EDIT_ITEM),
			'new_item' => __(KENTO_CF_NEW_ITEM),
			'view_item' => __(KENTO_CF_VIEW_ITEM),
		), 
        'public' => true,  
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => false,  
        'rewrite' => true,  
        'supports' => array('title', 'editor', 'thumbnail'),
		'menu_icon' => KENTO_CF_PLUGIN_PATH.'/kento-cf.png',
       );
    register_post_type(KENTO_CF_TYPE , $args );  
}
add_action('init', 'Kento_CF_Post_Register');

/* Testimonial Loop */
function KentoCF_list($current,$postcount,$bgcolor){

$input = $bgcolor;
	  
		$col = Array(
			hexdec(substr($input,1,2)),
			hexdec(substr($input,3,2)),
			hexdec(substr($input,5,2))
		);
		$darker = Array(
			$col[0]/2,
			$col[1]/2,
			$col[2]/2
		);
		$lighter = Array(
			255-(255-$col[0])/2,
			255-(255-$col[1])/2,
			255-(255-$col[2])/2
		);
		$darker = "#".sprintf("%02X%02X%02X", $darker[0], $darker[1], $darker[2]);
		$lighter = "#".sprintf("%02X%02X%02X", $lighter[0], $lighter[1], $lighter[2]);
	
echo "<style type='text/css'>


.kento-clients-feedback .current .kento-cf-author-img {
	border: 1px solid ".$bgcolor.";
-webkit-transition:all 1s ease 0.3s;
   -moz-transition:all 1s ease 0.3s;
     -o-transition:all 1s ease 0.3s;
        transition:all 1s ease 0.3s;
	}
.kento-clients-feedback .current .kento-cf-author-img:hover{
	border: 1px solid ".$darker.";
	}
.kento-clients-feedback ul.tabs li.current:before {
	  border-top: 10px solid ".$bgcolor.";
	  }
.kento-clients-feedback .tab-content.current {
	background-color: ".$bgcolor.";
	border-bottom: 3px solid ".$darker.";
	}
</style>";
	$KentoCF= '<div class="kento-clients-feedback"><ul class="tabs">';
	query_posts('post_type=kento-cf&posts_per_page='.$postcount);
	$count = 1;
	if (have_posts()) : while (have_posts()) : the_post(); 
		$author= get_the_title();
		$content= get_the_content();
		$published_posts = wp_count_posts("kento-cf")->publish;
		$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
		if($count==$current){
		$KentoCF.='<li class="tab-link current"  data-tab="tab-'.$count.'"><div class="kento-testimonial-author"><img class="kento-cf-author-img" width="150px" height="150px" src="'.$url.'" /><p class="kento-cf-author-name">'.$author.'</p></div>
		</li>';
			}
		else {
$KentoCF.='<li class="tab-link"  data-tab="tab-'.$count.'"><div class="kento-testimonial-author"><img class="kento-cf-author-img" width="150px" height="150px" src="'.$url.'" /><p class="kento-cf-author-name">'.$author.'</p></div>
		</li>';
		}
		$count++;
	endwhile;
	
	
	endif;
	$KentoCF.='</ul>';
	 wp_reset_query();
	
	query_posts('post_type=kento-cf&posts_per_page='.$postcount);
	$count = 1;
	if (have_posts()) : while (have_posts()) : the_post(); 

		$content = get_the_content();
		
		if($count==$current){
		$KentoCF.='<div id="tab-'.$count.'" class="tab-content current"><p>'.$content.'</p></div>';
			}
		else {
		$KentoCF.='<div id="tab-'.$count.'" class="tab-content"><p>'.$content.'</p></div>';
		}
		$count++;
	endwhile;
	
	
	endif;
	$KentoCF.='</div>';	
wp_reset_query();
	return $KentoCF;
}
/**add the shortcode for the Testimonial- for use in editor**/
function KentoCF_shortcodes($atts, $content=null){
$atts = shortcode_atts(
			array(
				'current' => '',	
				'postcount' => '',
				'bgcolor' => '',	
				), $atts);
	$KentoCF= KentoCF_list($atts['current'],$atts['postcount'],$atts['bgcolor']);
	return $KentoCF;
}
add_shortcode('KentoCF', 'KentoCF_shortcodes');

/**add template tag- for use in themes**/
function Kento_CF(){
	print KentoCF_list();
}

?>