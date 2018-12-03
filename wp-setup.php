<?php
/**
 *
 * Register Post Types
 *
 */

function nwspnc_admin__assets() {
	wp_enqueue_style('custom_meta_css', _TEMPLATEURL . '/functions/custom_meta/meta.css');

	$custom_meta = _TEMPLATEURL . "/functions/custom_meta/";
	wp_register_script('maskedinput', get_bloginfo('template_directory') .'/lib/js/jquery.maskedinput-1.3.min.js', array('jquery'));
	wp_register_script('xpsp-directory', $custom_meta .'meta.js', array('jquery','maskedinput'));
	wp_enqueue_script('xpsp-directory');

	add_filter( 'enter_title_here', 'xpsp_change_directory_title' );
}

/* Define custom meta for Directory admin. */
$busdir_meta = new WPAlchemy_MetaBox(array
(
	'id' => '_', // underscore prefix hides fields from the custom fields area
	'title' => 'Directory Listing',
	'template' => TEMPLATEPATH . '/functions/custom_meta/dir_meta.php',
	'types' => array('directory'),
	'hide_editor' => FALSE,
	'hide_title' => FALSE,
	'hide_screen_option' => TRUE,
	'lock' => WPALCHEMY_LOCK_AFTER_POST_TITLE,
	'view' => WPALCHEMY_VIEW_ALWAYS_OPENED,
	'init_action' => 'nwspnc_admin__assets',
));

/**
 *
 * Custom Post Type: Directory
 *
 */
add_theme_support('post-thumbnails');

add_action( 'init', 'create_xpsp_directory' );

function create_xpsp_directory() {
	$labels = array(
		'name' => _x('Directory', 'post type general name'),
		'singular_name' => _x('Directory Listing', 'post type singular name'),
		'menu_name' => _x('Directory', 'post type general name'),
		'add_new' => _x('Add New', ' Listing'),
		'add_new_item' => __('Add New Listing'),
		'edit_item' => __('Edit Directory Listing'),
		'new_item' => __('New Listing'),
		'view_item' => __('View Listing'),
		'search_items' => __('Search Listings'),
		'not_found' =>  __('No Listings Found'),
		'not_found_in_trash' => __('No Listings Found in Trash'),
		'parent_item_colon' => ''
	);
	
	$supports = array(
	                 'title',
	                 'editor',
	                 'thumbnail',
	                 'revisions',
	                 );
	
	$icon_url = get_bloginfo('stylesheet_directory') . "/lib/img/dir-icon.png";
	
	register_post_type( 'directory',
		array(
			'labels' => $labels,
			'public' => true,
//			'menu_position' => 54,
			'supports' => $supports,
//			'menu_icon' => $icon_url,
			'rewrite' => array( 'slug' => 'listing' ),
			'capabilities' => array(
				'publish_posts' => 'listing_merchant',
				'edit_post' => 'listing_merchant',
				'edit_posts' => 'listing_merchant',
				'edit_others_posts' => 'listing_admin',
				'edit_private_posts' => 'listing_admin',
				'edit_published_posts' => 'listing_admin',
				'delete_post' => 'listing_admin',
				'delete_posts' => 'listing_merchant',
				'delete_others_posts' => 'listing_admin',
				'delete_private_posts' => 'listing_admin',
				'delete_published_posts' => 'listing_admin',
				'read_private_posts' => 'listing_admin',
				'read_post' => 'listing_admin',
				'read' => 'listing_merchant',
			),
		)
	);
}

add_action( 'init', 'xpsp_directory_taxonomies', 0 );

function xpsp_directory_taxonomies() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels_cat = array(
    'name' => _x( 'Directory Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Categories' ),
    'all_items' => __( 'All Categories' ),
    'parent_item' => __( 'Parent Category' ),
    'parent_item_colon' => __( 'Parent Category:' ),
    'edit_item' => __( 'Edit Category' ), 
    'update_item' => __( 'Update Category' ),
    'add_new_item' => __( 'Add Category' ),
    'new_item_name' => __( 'New Category' ),
    'menu_name' => __( 'Categories' ),
    'capabilities' => array(
		'manage_terms' => 'listing_admin',
		'edit_terms' => 'listing_admin',
		'delete_terms' => 'listing_admin',
		'assign_terms' => 'listing_admin'
	),
  ); 	

  register_taxonomy('directory_cat',array('directory'), array(
    'hierarchical' => true,
    'labels' => $labels_cat,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'directory' ),
  ));

  $labels_local = array(
    'name' => _x( 'Local Resources', 'taxonomy general name' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Local Resources' ),
    'all_items' => __( 'All Resources' ),
    'parent_item' => __( 'Parent Resource' ),
    'parent_item_colon' => __( 'Parent Resource:' ),
    'edit_item' => __( 'Edit Resource' ), 
    'update_item' => __( 'Update Resource' ),
    'add_new_item' => __( 'Add Resource' ),
    'new_item_name' => __( 'New Resource' ),
    'menu_name' => __( 'Resources' ),
    'capabilities' => array(
		'manage_terms' => 'listing_admin',
		'edit_terms' => 'listing_admin',
		'delete_terms' => 'listing_admin',
		'assign_terms' => 'listing_admin'
	),
  ); 	

  register_taxonomy('directory_local',array('directory'), array(
    'hierarchical' => true,
    'labels' => $labels_local,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'resources' ),
  ));

  $labels_key = array(
    'name' => _x( 'Directory Keywords', 'taxonomy general name' ),
    'singular_name' => _x( 'Keyword', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Keywords' ),
    'all_items' => __( 'All Keywords' ),
    'edit_item' => __( 'Edit Keyword' ), 
    'update_item' => __( 'Update Keyword' ),
    'add_new_item' => __( 'Add Keyword' ),
    'new_item_name' => __( 'New Keyword' ),
    'menu_name' => __( 'Keywords' ),
    'capabilities' => array(
		'manage_terms' => 'listing_admin',
		'edit_terms' => 'listing_admin',
		'delete_terms' => 'listing_admin',
		'assign_terms' => 'listing_admin'
	),
  ); 	

  register_taxonomy('directory_key',array('directory'), array(
    'hierarchical' => false,
    'labels' => $labels_key,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'keywords' ),
  ));
}

add_filter("manage_edit-directory_columns", "xpsp_directory_edit_columns");
add_action("manage_posts_custom_column",  "xpsp_directory_custom_columns");

function xpsp_directory_edit_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Listings",
			"listing-email" => "E-Mail Address",
			"listing-year" => "Listed Since",
			"business_type" => "Business Type",
			"listing-logo" => "Logo",
		);

		return $columns;
}

function xpsp_directory_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "listing-email":
				$email = get_post_meta($post->ID, '_xpsp_directory', TRUE);
				echo $email['email'];
				break;
			case "listing-year":
				$listyear = get_post_meta($post->ID, '_xpsp_directory', TRUE);
				echo $listyear['listyear'];
				break;
			case "business_type":
				$list_thingsToDo = get_the_term_list($post->ID, 'directory_cat', '', ', ','');
				$list_localDirectory = get_the_term_list($post->ID, 'directory_local', '', ', ','');
				
				if($list_thingsToDo){
					echo "<p style='margin:0;'>Things To Do:</p>";
					echo $list_thingsToDo;
				}
				if($list_localDirectory){
					echo "<p style='margin:0;'>Local Directory:</p>";
					echo $list_localDirectory;
				}
				break;
			case "listing-logo":
				$src = get_post_meta($post->ID, '_xpsp_directory', TRUE);
				$src = $src['logo'];
				if ($src == NULL) { echo "<p>No logo uploaded.</p>"; } else { echo "<img style='height:40px; width:auto;' src='$src' />"; }
				break;
		}
}

// Register the column as sortable
function price_column_register_sortable( $columns ) {
	$columns['title'] = 'title';
	return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'price_column_register_sortable' );
add_filter( 'manage_edit-page_sortable_columns', 'price_column_register_sortable' );

add_action('wp', 'add_sortable_price_for_custom_post_types');

function add_sortable_price_for_custom_post_types(){
	$args=array(
		'public' => true,
		'_builtin' => false
	);
	$post_types=get_post_types($args);
	foreach ($post_types as $post_type ) {
		add_filter( 'manage_edit-'.$post_type.'_sortable_columns', 'price_column_register_sortable' );
	}
}

/*
 * Hide 'Add New' button from merchants.
*/

function modify_capabilities()
{
  // get the role you want to change: editor, author, contributor, subscriber
  $editor_role = get_role('s2member_level1');
  $editor_role->remove_cap('publish_pages');
  
  // for posts it should be:
  // $editor_role->remove_cap('publish_posts');
  
  // to add capabilities use add_cap()
}

function modify_menu()
{
//  global $submenu;
//  echo '<pre>';
//  print_r($submenu);
//  echo '</pre>';
  if (!current_user_can('listing_admin')) {

	remove_menu_page('edit.php?post_type=directory');

	global $current_user;
	get_currentuserinfo();
	$loggedin_username = $current_user->display_name;
	$loggedin_userid = $current_user->ID;

	//Get user post count
	$loggedin_postcount = count_user_posts_by_type($loggedin_userid, 'directory');

	if($loggedin_postcount > 0):
		add_submenu_page('index.php','Directory','Manage Listings','read','edit.php?post_type=directory');
	endif;

  }else{
	add_submenu_page('edit.php?post_type=directory','Directory','Manage Listings','read','edit.php?post_type=directory');
  }
}

function hide_buttons()
{
  global $current_screen;
  
  if($current_screen->id == 'edit-directory' && !current_user_can('listing_admin'))
  {
    echo '<style>.add-new-h2{display: none;}.subsubsub{display: none;}</style>';  
  }
  
  // for posts the if statement would be:
  // if($current_screen->id == 'edit-post' && !current_user_can('publish_posts'))
}

function permissions_admin_redirect() {
  $result = stripos($_SERVER['REQUEST_URI'], 'post-new.php?post_type=directory');
  
  // for posts result should be:  
  // $result = stripos($_SERVER['REQUEST_URI'], 'post-new.php');
  
  if ($result!==false && !current_user_can('listing_admin')) {
    wp_redirect(get_option('siteurl') . '/wp-admin/index.php?permissions_error=true');
  }
  
  // for posts the if statement should be:
  // if ($result!==false && !current_user_can('publish_posts')) {
}

function permissions_admin_notice()
{
  // use the class "error" for red notices, and "update" for yellow notices
  echo "<div id='permissions-warning' class='error fade'><p><strong>".__('You do not have permission to access that page.')."</strong></p></div>";
}

function permissions_show_notice()
{
  if($_GET['permissions_error'])
  {
    add_action('admin_notices', 'permissions_admin_notice');  
  }
}

// Hook the functions to the relavant actions
//add_action('admin_init','modify_capabilities');
add_action('admin_init','permissions_show_notice');
add_action('admin_menu','modify_menu');
add_action('admin_menu','permissions_admin_redirect');
add_action('admin_head','hide_buttons');

add_action( 'add_meta_boxes', 'my_remove_post_meta_boxes' );
function my_remove_post_meta_boxes() {

	remove_meta_box( 'ws-plugin--s2member-security', 'directory', 'side' );

	/* Additional calls to remove_meta_box() go here. */
}

add_action("gform_after_submission", "xpsp_save_directory_meta", 10, 2);
function xpsp_save_directory_meta($entry, $form){
  if($form["id"] != 14)
     return;

  $post_id = $entry["post_id"];

	$dir_meta['address1'] = $entry["4.1"];
	$dir_meta['address2'] = $entry["4.2"];
	$dir_meta['city'] = $entry["4.3"];
	$dir_meta['state'] = $entry["4.4"];
	$dir_meta['zip'] = $entry["4.5"];
	$dir_meta['phone']  = $entry["10"];
	$dir_meta['fax']       = $entry["11"];
	$dir_meta['email']     = $entry["12"];
	$dir_meta['url']  = $entry["13"];
	$dir_meta['contact']  = $entry["28"];
	
	$update_fb = $entry["30.1"]
	
	if ($update_fb) {
	
		$fb_status = '';
		$fb_link = '';
		$fb_picture = '';
		$fb_name = '';
		$fb_caption = '';
		$fb_description = '';
		xpsp_post_to_facebook($fb_message);
	}
	
	$dir_meta_array = serialize($dir_meta);
	update_post_meta($post_id, '_xpsp_directory', maybe_unserialize(stripslashes($dir_meta_array)));
	
	
}
