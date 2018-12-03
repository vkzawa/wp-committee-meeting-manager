<?php
/**
 * @package WordPress
 * @subpackage thexpsp
 */

// custom constant (opposite of TEMPLATEPATH)
define('_TEMPLATEURL', WP_CONTENT_URL . '/' . stristr(TEMPLATEPATH, 'themes'));

/**
 *
 * Includes: Include functions in separate files for organizational purposes.
 *
 */
	include_once 'functions/MetaBox.php';
	include_once 'functions/MediaAccess.php';
	$wpalchemy_media_access = new WPAlchemy_MediaAccess();
	
	/* Include file for defining custom meta for Backgrounds admin. */
	//include(TEMPLATEPATH. '/functions/backgrounds.php');
	
	/* Include file for defining custom meta for Directory. */
	include(TEMPLATEPATH. 'directory.php');

	/* Include file for defining custom meta for Property. */
	include(TEMPLATEPATH. '/functions/property.php');

	/* Include file for defining custom meta for Job. */
	include(TEMPLATEPATH. '/functions/job.php');
	
	/* Include file for defining custom meta for Media admin. */
	include(TEMPLATEPATH. '/functions/media.php');
	
	/* Include file for archive pagination. */
	include(TEMPLATEPATH. '/functions/wp-paginate.php');

	/* Include file for defining custom meta for Subscriptions admin. */
	include(TEMPLATEPATH. '/functions/subscriptions.php');

	/* Include file for defining custom meta for RSS Page Template. */
	include(TEMPLATEPATH. '/functions/RSS_template.php');


/**
 *
 * Listing Image Size
 *
 */
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'listing-thumb-crop', 250, 250, true ); //(cropped)
	add_image_size( 'listing-thumb', 250, 250, false ); //(cropped)
}

/**
 *
 * Custom Meta Boxes: Parent Navigation
 *
 */
	/* Make sure the CSS styles for the custom meta box are applied. */

function xpsp_admin_meta_assets() {
	wp_enqueue_style('custom_meta_css', _TEMPLATEURL . '/functions/custom_meta/meta.css');
}
	
	/* Define custom meta for Parent Navigation admin. */
	$busdir_meta = new WPAlchemy_MetaBox(array
	(
		'id' => '_xpsp_parentnav', // underscore prefix hides fields from the custom fields area
		'title' => 'Sub-Page Menu',
		'template' => TEMPLATEPATH . '/functions/custom_meta/parentnav_meta.php',
		'include_template' => 'parent-navigation.php',
		//'include_post_id' => 12,
		'init_action' => 'xpsp_admin_meta_assets',
	));
add_filter("gform_field_value_uuid", "get_unique");

/**
 *
 * Create a Menu to edit on the Appearances>Menus administration screen.
 *
 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'xpsp' ),
		'footer' => __( 'Footer Menu', 'xpsp' ),
	) );

/**
 *
 * Add default posts and comments RSS feed links to head.
 *
 */
	add_theme_support( 'automatic-feed-links' );

/**
 *
 * Create widget areas for the template to display.
 *
 */
	function xpsp_sidebar_init() {
		register_sidebar( array (
			'name' => __( 'Main Sidebar', 'xpsp' ),
			'id' => 'main-sidebar',
			'before_widget' => '<nav class="widget %2$s">',
			'after_widget' => "</nav>",
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		) );
		register_sidebar( array (
			'name' => __( 'Front Top (1 Space)', 'xpsp' ),
			'id' => 'front-top',
			'before_widget' => '<nav class="widget %2$s">',
			'after_widget' => "</nav>",
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		) );
		register_sidebar( array (
			'name' => __( 'Front Mid (4 Spaces)', 'xpsp' ),
			'id' => 'front-mid',
			'before_widget' => '<nav class="widget %2$s">',
			'after_widget' => "</nav>",
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		) );
		register_sidebar( array (
			'name' => __( 'Front Footer (3 Spaces)', 'xpsp' ),
			'id' => 'front-foot',
			'before_widget' => '<nav class="footer-widget %2$s">',
			'after_widget' => "</nav>",
			'before_title' => '<h2 class="footer-widget-title">',
			'after_title' => '</h2>',
		) );
		register_sidebar( array (
			'name' => __( 'Above Copyright', 'xpsp' ),
			'id' => 'footer-area',
			'before_widget' => '<nav class="footer-widget %2$s">',
			'after_widget' => "</nav>",
			'before_title' => '<h2 class="footer-widget-title">',
			'after_title' => '</h2>',
		) );
		register_sidebar( array (
			'name' => __( 'Group Deal Widgets', 'xpsp' ),
			'id' => 'deals',
			'before_widget' => '<nav class="deal-widget %2$s">',
			'after_widget' => "</nav>",
			'before_title' => '<h2 class="deal-widget-title">',
			'after_title' => '</h2>',
		) );
	}
	add_action( 'init', 'xpsp_sidebar_init' );

/**
 *
 * Register custom scripts used for the theme,
 *
 */
	function xpsp_theme_scripts_init() {
		$theme_js_library = get_bloginfo('template_directory') . "/lib/js/";
	
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"), false, '1.7');
		wp_enqueue_script('jquery');
   
		wp_register_script( 'xpsp-shadowbox', $theme_js_library."shadowbox.js", array( 'jquery' ) );
		wp_enqueue_script( 'xpsp-shadowbox');
		wp_register_script( 'xpsp-nivo', $theme_js_library."nivo.slider.js", array( 'jquery' ) );
		wp_enqueue_script( 'xpsp-nivo');
	
		wp_register_script( 'xpsp-custom-js', $theme_js_library."custom.js", array( 'jquery', 'xpsp-nivo' ) );
		wp_enqueue_script( 'xpsp-custom-js');
		
		wp_dequeue_style('events-manager');
	    wp_dequeue_style('wp_sidebarlogin_css_styles');
	}
	add_action( 'wp_enqueue_scripts', 'xpsp_theme_scripts_init' );

/**
 *
 * Register custom styles used for the theme,
 *
 */
	function xpsp_theme_style_init() {
		if( is_admin() ) return;
		$theme_css_library = get_bloginfo('template_directory') . "/lib/css/";
	
		wp_register_style('nivo-style', $theme_css_library . "nivo-slider.css");
		wp_enqueue_style( 'nivo-style');
	
		if ( is_front_page() ) {
			wp_register_style('frontPageStylesheet', $theme_css_library . "front-page.css");
			wp_enqueue_style( 'frontPageStylesheet');
		}
		if ( is_page('listing') ) {
			gravity_form_enqueue_scripts(4, true);
			gravity_form_enqueue_scripts(1, true);
		}
	
	}
	add_action('wp_print_styles', 'xpsp_theme_style_init');

/**
 *
 * Register custom scripts/styles used for the admin area,
 *
 */
	function xpsp_admin_scripts_init() {
		$template = get_bloginfo('template_directory');
	
		wp_register_style('admin_css', $template . "/lib/css/admin.css");
		wp_enqueue_style( 'admin_css');
	}
	add_action( 'admin_init', 'xpsp_admin_scripts_init' );

/**
 * Custom Function:
 ******************
 * Return a page ID for the given page slug.
 *
 */
	function get_ID_by_slug($page_slug) {
	    $page = get_page_by_path($page_slug);
	    if ($page) {
	        return $page->ID;
	    } else {
	        return null;
	    }
	}

/**
 * Custom Function:
 ******************
 * Return true if current page is child of entered value.
 * Accept's page ID, page slug or page title as parameters.
 */
function is_child( $parent = '' ) {
	global $post;
 
	$parent_obj = get_page( $post->post_parent, ARRAY_A );
	$parent = (string) $parent;
	$parent_array = (array) $parent;
 
	if ( in_array( (string) $parent_obj['ID'], $parent_array ) ) {
		return true;
	} elseif ( in_array( (string) $parent_obj['post_title'], $parent_array ) ) {
		return true;	
	} elseif ( in_array( (string) $parent_obj['post_name'], $parent_array ) ) {
		return true;
	} else {
		return false;
	}
}
/**
 * Custom Function:
 ******************
 * Return the requested value for the current page's term.
 *
 */
	function get_current_term($value){
		/* Values: term_id, name, slug, termp_group, term_taxonomy_id, 
		taxonomy, description, parent, count*/
		global $wp_query;
		if(is_tax()){
			$tax_name = get_query_var('taxonomy');
			$tax_term = get_query_var('term');
			$term = get_term_by('slug', $tax_term, $tax_name);
			$output = $term->$value;
		}
		return $output;
	}

/**
 * Custom Function:
 ******************
 * Return the thumbnail for the requested term.
 * Requires "Taxonomy Images" plugin.
 */
	function get_tax_thumb_url($term_tax_id, $size = 'thumbnail') {
	  global $taxonomy_images_plugin;
	  if( isset( $taxonomy_images_plugin->settings ) ) {
		  if( array_key_exists( $term_tax_id, (array) $taxonomy_images_plugin->settings ) ) {
			  $image_id = $taxonomy_images_plugin->settings[$term_tax_id];
			  return wp_get_attachment_image_src( $image_id, $size );
		  }
	  }
	}

/**
 * Custom Function:
 ******************
 * Return the number of posts a user has in the given post type.
 *
 */
	function count_user_posts_by_type($userid, $post_type='post') {
	  global $wpdb;
	  $where = get_posts_by_author_sql($post_type, TRUE, $userid);
	  $count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );
	  return apply_filters('get_usernumposts', $count, $userid);
	}

/**
 *
 * Adds the background image for the current page inline with the <body> tag.
 *
 */
	function body_style() {
	
		//global $wp_query;
		//var_dump($wp_query);
	
		if(!is_front_page()):
			$styles = '';
			
			$bg_image = 'url(' . get_bloginfo('stylesheet_directory') . '/lib/img/xpsp_bg1.jpg)';
			
			$styles .= 'background-image:' . $bg_image . ';';
			
			$css_style = 'style="'.$styles.'"';
			
			echo $css_style;
		endif;
	}

/**
 *
 * Disable and hide the "admin bar".
 *
 */
/*
	add_filter( 'show_admin_bar', '__return_false' );
	
	add_action( 'admin_print_scripts-profile.php', 'hide_admin_bar_prefs' );
	function hide_admin_bar_prefs() { ?>
	<style type="text/css">
	    .show-admin-bar { display: none; }
	</style>
	<?php
	}
*/


/**
 *
 * Add XPSP Dashboard Widget.
 *
 */
	function xpsp_dashboard_widget_shortcuts(){
		$shortcuts = array (
			array(
				'name' => 'Directory',
				'id' => 'shortcut_directory',
				'links' => array(
						array(
						'name' => 'View All',
						'url' => 'edit.php?post_type=directory',
						),
						array(
						'name' => 'Create Listing',
						'url' => 'post-new.php?post_type=directory',
						),
					),
				),
			array(
				'name' => 'Events',
				'id' => 'shortcut_events',
				'links' => array(
						array(
						'name' => 'View All',
						'url' => 'edit.php?post_type=event',
						),
						array(
						'name' => 'Schedule Event',
						'url' => 'post-new.php?post_type=event',
						),
					),
				),
			array(
				'name' => 'Public Media',
				'id' => 'shortcut_media',
				'links' => array(
						array(
						'name' => 'View All',
						'url' => 'edit.php?post_type=public_media',
						),
						array(
						'name' => 'Add Media Asset',
						'url' => 'post-new.php?post_type=public_media',
						),
					),
				),
			array(
				'name' => 'Subscriptions',
				'id' => 'shortcut_subscription',
				'links' => array(
						array(
						'name' => 'View All',
						'url' => 'edit.php?post_type=subscriptions',
						),
						array(
						'name' => 'Create Form',
						'url' => 'post-new.php?post_type=subscriptions',
						),
					),
				),
			);
			
		echo "<ul>";
			foreach($shortcuts as $shortcut){
				$links = $shortcut['links'];

				echo "<li id=".$shortcut['id'].">";
					echo "<a class='icon' href='".$links[0]['url']."'>".$shortcut['name']."</a>";
					echo "<a class='title' href='".$links[0]['url']."'>".$shortcut['name']."</a>";

					echo "<ul>";
						$count = 0;
						foreach($links as $link){
							if($count > 0)
								echo " | ";
							echo "<li>";
								echo "<a href='".$link['url']."'>".$link['name']."</a>";
							echo "</li>";
							$count++;
						}
						echo "</ul>";

				echo "</li>";
			}
		echo "</ul>";
	}
	
	function xpsp_add_dashboard_widgets(){

		if ( is_admin() && current_user_can( 'listing_admin' ) ) {
			$version_identifier = WPSC_VERSION . "." . WPSC_MINOR_VERSION;

			// Add the dashboard widgets
			wp_add_dashboard_widget('xpsp_dashboard_shortcuts', 'ExperienceSP.com Shortcuts', 'xpsp_dashboard_widget_shortcuts');
	
			// Sort the Dashboard widgets so ours it at the top
			global $wp_meta_boxes;
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			// Backup and delete our new dashbaord widget from the end of the array
			$wpsc_widget_backup = array( 'xpsp_dashboard_shortcuts' => $normal_dashboard['xpsp_dashboard_shortcuts'] );
			//$wpsc_widget_backup += array( 'wpsc_dashboard_widget' => $normal_dashboard['wpsc_dashboard_widget'] );
	
			unset( $normal_dashboard['xpsp_dashboard_shortcuts'] );
	
			// Merge the two arrays together so our widget is at the beginning
	
			$sorted_dashboard = array_merge( $wpsc_widget_backup, $normal_dashboard );
	
			// Save the sorted array back into the original metaboxes
	
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
		}

	}
	add_action('wp_dashboard_setup', 'xpsp_add_dashboard_widgets');

/**
 *
 * Disable default dashboard widgets.
 *
 */
	function disable_default_dashboard_widgets() {
		if( ! is_admin() ) return;
		// disable default dashboard widgets
		remove_meta_box('dashboard_right_now', 'dashboard', 'core');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'core');
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'core');
		remove_meta_box('dashboard_plugins', 'dashboard', 'core');
	
		remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'core');
		remove_meta_box('dashboard_primary', 'dashboard', 'core');
		remove_meta_box('dashboard_secondary', 'dashboard', 'core');
	
		// disable plugin dashboard widget
		remove_meta_box('meandmymac_rss_widget', 'dashboard', 'normal');
		remove_meta_box('wpsc_dashboard_news', 'dashboard', 'normal');
		remove_meta_box('yoast_db_widget', 'dashboard', 'normal');
		remove_meta_box('feedwordpress_dashboard', 'dashboard', 'normal');
	}
	add_action('admin_menu', 'disable_default_dashboard_widgets');
	
function xpsp_change_event_title( $title ){
	$screen = get_current_screen();
	
	if  ( 'event' == $screen->post_type ) {
		$title = 'Enter Event Title';
	}
	return $title;
}


/**
 *
 * Facebook OpenGraph.
 *
 */

	//Adding the Open Graph in the Language Attributes
function add_opengraph_doctype( $output ) {
		return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
	}
add_filter('language_attributes', 'add_opengraph_doctype');

//Lets add Open Graph Meta Info

function insert_fb_in_head() {
	global $post;
	if ( !is_singular()) //if it is not a post or a page
		return;
        echo '<meta property="fb:admins" content="221045757919532"/>';
        echo '<meta property="og:title" content="' . get_the_title() . '"/>';
        echo '<meta property="og:type" content="article"/>';
        echo '<meta property="og:url" content="' . get_permalink() . '"/>';
        echo '<meta property="og:site_name" content="Experience San Pedro"/>';
	if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
//		$default_image="http://example.com/image.jpg"; //replace this with a default image on your server or an image in your media library
//		echo '<meta property="og:image" content="' . $default_image . '"/>';
	}
	else{
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
		echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
	}
	echo "\n";
}
add_action( 'wp_head', 'insert_fb_in_head', 5 );

function autoset_featured() {
          global $post;
          $already_has_thumb = has_post_thumbnail($post->ID);
              if (!$already_has_thumb)  {
              $attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );
                          if ($attached_image) {
                                foreach ($attached_image as $attachment_id => $attachment) {
                                set_post_thumbnail($post->ID, $attachment_id);
                                }
                           }
                        }
      }  //end function
add_action('the_post', 'autoset_featured');
add_action('save_post', 'autoset_featured');
add_action('draft_to_publish', 'autoset_featured');
add_action('new_to_publish', 'autoset_featured');
add_action('pending_to_publish', 'autoset_featured');
add_action('future_to_publish', 'autoset_featured');
