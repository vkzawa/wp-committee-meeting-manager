<?php

function cmm_get_calendar_route(WP_REST_Request $request) {
  $start_range = $request->get_param('startRange');
  $end_range = $request->get_param('endRange');

  $args = array(
      "posts_per_page" => -1,
      "post_type" => array('meetings', 'events'),
      "meta_key" => "eventdate",
      "orderby" => "meta_value_num",
      "order" => "ASC",
      'meta_query' => array(
          array(
              'key' => 'eventdate',
              'value' => $start_range,
              'compare' => '>',
          ),
          array(
              'key' => 'eventdate',
              'value' => $end_range,
              'compare' => '<',
          )
      )
  );
  $event_list = new WP_Query($args);
  $events = [];

  foreach ($event_list->posts as $event) {
    if ($event->post_status === 'publish') {

      // Add template name to object
      $event_date = get_post_meta($event->ID, "eventdate", TRUE);

      $name = array(
        'id'   => $event->ID,
        'title' => $event->post_title,
        'path' => get_page_uri($event->ID),
        'slug' => $event->post_name,
        'eventdate' => $event_date,
        'type' => $event->post_type
      );

      array_push($events, $name);
    }
  }

  return $events;
}

function cmm_get_meetings_route(WP_REST_Request $request) {
  $posts_per_page = $request->get_param('posts_per_page');
  $page_number = $request->get_param('page');
  $tonight = strtotime('12:00am');

  $args = array(
      "posts_per_page" => $posts_per_page ?? -1,
      "page" => 3,
      "post_type" => array('meetings'),
      "meta_key" => "eventdate",
      "orderby" => "meta_value_num",
      "order" => "ASC",
      'meta_query' => array(
        array(
            'key' => 'eventdate',
            'value' => $tonight,
            'compare' => '>',
        )
      )
  );
  $meeting_list = new WP_Query($args);
  $meetings = [];

  foreach ($meeting_list->posts as $meeting) {
    if ($meeting->post_status === 'publish') {

      // Add template name to object
      $meeting_date = get_post_meta($meeting->ID, "eventdate", TRUE);
      $meeting_details = get_post_meta($meeting->ID, '_cmm_meeting_details', TRUE);

      $name = array(
        'id'   => $meeting->ID,
        'title' => $meeting->post_title,
        'path' => get_page_uri($meeting->ID),
        'slug' => $meeting->post_name,
        'eventdate' => $meeting_date,
        'details' => $meeting_details
      );

      array_push($meetings, $name);
    }
  }

  return $meetings;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'cmm-committee', '/calendar/list', array(
    'methods' => 'GET',
    'callback' => 'cmm_get_calendar_route',
  ) );
  register_rest_route( 'cmm-committee', '/meetings/list', array(
    'methods' => 'GET',
    'callback' => 'cmm_get_meetings_route',
  ) );
} );
