<?php

/*-------------------*
* Post Type: Meeting *
*--------------------*/

function cmm_remove_meeting_tax_box() {remove_meta_box('meeting_typesdiv', 'meeting', 'side');}
add_action( 'admin_menu', 'cmm_remove_meeting_tax_box' );

/*--------------------*
* Post Type: Meetings *
*---------------------*/
add_action( 'init', 'cmm_create_type_meeting' );
function cmm_create_type_meeting() {
	$labels = array(
		'name' => _x('Meetings', 'post type general name'),
		'singular_name' => _x('Meeting', 'post type singular name'),
		'menu_name' => _x('Meetings', 'post type general name'),
		'add_new' => _x('Add New', ' Meeting'),
		'add_new_item' => __('Add New Meeting'),
		'edit_item' => __('Edit Meeting'),
		'new_item' => __('New Meeting'),
		'view_item' => __('View Meeting'),
		'search_items' => __('Search Meetings'),
		'not_found' =>  __('No Meetings Found'),
		'not_found_in_trash' => __('No Deleted Meetings'),
		'parent_item_colon' => ''
	);

	// $icon_url = get_bloginfo('stylesheet_directory') . "/lib/img/dir-icon.png";

	register_post_type( 'meetings',
		array(
			'labels' 								=> $labels,
			'public' 								=> true,
			'publicly_queryable'  	=> true,
//			'menu_position' 	=> 54,
			'supports' 							=> array('thumbnail', 'editor'),
//			'menu_icon' 	=> $icon_url,
			'rewrite' 							=> array( 'slug' => 'meeting' ),
			'show_in_rest' 					=> true,
			'rest_base'							=> 'meetings',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'query_var'							=> true
		)
	);
}

/*------------------*
* Post Type: Events *
*-------------------*/
add_action( 'init', 'cmm_create_type_events' );
function cmm_create_type_events() {
    $labels = array(
        'name' => _x('Events', 'post type general name'),
        'singular_name' => _x('Event', 'post type singular name'),
        'menu_name' => _x('Events', 'post type general name'),
        'add_new' => _x('Add New', ' Event'),
        'add_new_item' => __('Add New Event'),
        'edit_item' => __('Edit Event'),
        'new_item' => __('New Event'),
        'view_item' => __('View Event'),
        'search_items' => __('Search Events'),
        'not_found' =>  __('No Events Found'),
        'not_found_in_trash' => __('No Deleted Events'),
        'parent_item_colon' => ''
    );

    // $icon_url = get_bloginfo('stylesheet_directory') . "/lib/img/dir-icon.png";

    register_post_type( 'events',
        array(
            'labels' => $labels,
            'public' => true,
//			'menu_position' => 54,
            'supports' => array('thumbnail','title','editor'),
//			'menu_icon' => $icon_url,
            'rewrite' => array( 'slug' => 'event' ),
						'show_in_rest' 					=> true,
						'rest_base'							=> 'events',
						'rest_controller_class' => 'WP_REST_Posts_Controller',
						'query_var'							=> true
        )
    );
}

/*--------------------*
* Post Type: Location *
*---------------------*/
add_action( 'init', 'cmm_create_type_location' );
function cmm_create_type_location() {
	$labels = array(
		'name' => _x('Locations', 'post type general name'),
		'singular_name' => _x('Location', 'post type singular name'),
		'menu_name' => _x('Locations', 'post type general name'),
		'add_new' => _x('Add New', ' Location'),
		'add_new_item' => __('Add New Location'),
		'edit_item' => __('Edit Location'),
		'new_item' => __('New Location'),
		'view_item' => __('View Location'),
		'search_items' => __('Search Locations'),
		'not_found' =>  __('No Locations Found'),
		'not_found_in_trash' => __('No Deleted Locations'),
		'parent_item_colon' => ''
	);

	$supports = array(
	                 'editor',
	                 'thumbnail',
	                 );

	$icon_url = get_bloginfo('stylesheet_directory') . "/lib/img/dir-icon.png";

	register_post_type( 'location',
		array(
			'labels' => $labels,
			'public' => true,
//			'menu_position' => 54,
			'show_in_menu' => 'edit.php?post_type=meetings',
			'supports' => $supports,
//			'menu_icon' => $icon_url,
			'rewrite' => array( 'slug' => 'location' ),
/*
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
*/
		)
	);
}


add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
	add_submenu_page( 'edit.php?post_type=page', 'Locations', 'Locations', 'manage_options', 'hello', 'my_custom_submenu_page_callback' );
}

function my_custom_submenu_page_callback() {
	echo '<h3>My Custom Submenu Page</h3>';

}

add_filter("manage_edit-meetings_columns", "cmm_meetings_edit_columns");

function cmm_meetings_edit_columns($columns) {
  $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => "Meeting Type",
      "meeting-date" => "Meeting Date",
      "date" => "Last Edited"
  );
  return $columns;
}

add_action("manage_posts_custom_column", "cmm_meetings_custom_columns");

function cmm_meetings_custom_columns($column) {
  global $post;
  switch ($column) {
    case "meeting-date":
      date_default_timezone_set(get_option('timezone_string'));
      $meetingDate = get_post_meta($post->ID, 'eventdate', TRUE);
      echo date('m/d/Y',$meetingDate);
      break;
    case "species":
      $species = get_post_meta($post->ID, '_lff_species', TRUE);
      if ($species != NULL) {
        echo '<div class="species ' . $species . '">' . $species . '</div>';
      } else {
        echo 'No species set.';
      }
      break;
  }
}

//Define custom column values for Pet Manager admin display table.
add_filter('manage_edit-pets_sortable_columns', 'sort_column_register_sortable');

function sort_column_register_sortable($columns) {
  $columns['species'] = 'species';

  return $columns;
}

function species_column_orderby($vars) {
  if (isset($vars['orderby']) && 'species' == $vars['orderby']) {
    $vars = array_merge($vars, array(
        'meta_key' => '_lff_species',
        'orderby' => 'meta_value'
            ));
  }
  return $vars;
}

add_filter('request', 'species_column_orderby');
