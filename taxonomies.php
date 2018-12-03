<?php
add_action( 'init', 'cmm_create_tax_meeting_type', 0 );

function cmm_create_tax_meeting_type()
{
  $labels_types = array(
    'name' => _x( 'Meeting Types', 'taxonomy general name' ),
    'singular_name' => _x( 'Meeting Type', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Meeting Types' ),
    'all_items' => __( 'All Meeting Types' ),
    'parent_item' => __( 'Parent Type' ),
    'parent_item_colon' => __( 'Parent Type:' ),
    'edit_item' => __( 'Edit Type' ),
    'update_item' => __( 'Update Type' ),
    'add_new_item' => __( 'Add Type' ),
    'new_item_name' => __( 'New Type' ),
    'menu_name' => __( 'Meeting Types' ),
/*
    'capabilities' => array(
			'manage_terms' => 'listing_admin',
			'edit_terms' => 'listing_admin',
			'delete_terms' => 'listing_admin',
			'assign_terms' => 'listing_admin'
		),
*/
  ); 	

  register_taxonomy('meeting_types',array('meetings'), array(
    'hierarchical' => true,
    'labels' => $labels_types,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'meetings' ),
    'show_in_graphql' => true,
    'graphql_single_name' => 'meetingType',
    'graphql_plural_name' => 'meetingTypes'
  ));
}
