<?php

/*
  Plugin Name: Committee Manager
  Description: Manage agendas, minutes & resolutions in an easy-to-use interface.
  Version: 0.3
  Author: Vince Kurzawa
  Author URI: http://allvk.com
  License: Private
 */

//Define a starting point for plugin assets to
define('CMM_PLUGIN_PATH', WP_PLUGIN_URL . '/wp-committee-meeting-manager/');
define('CMM_LIB_PATH', WP_PLUGIN_URL . '/wp-committee-meeting-manager/lib/');

define('WP_THEME_DIR', get_theme_root() . '/');
define('WP_USER_THEME_URI', get_stylesheet_directory_uri());
define('CMM_CALENDAR_FEED_SLUG', 'calendar-feed');

include 'post-types.php';
include 'routes.php';
include 'taxonomies.php';
include_once 'admin-meta/MetaBox.php';
include_once 'admin-meta/MediaAccess.php';

add_theme_support('post-thumbnails');

$wpalchemy_media_access = new WPAlchemy_MediaAccess();

$cmm_meeting_details = new WPAlchemy_MetaBox(array
(
    'id' => '_cmm_meeting_details', // underscore prefix hides fields from the custom fields area
    'title' => 'Meeting Details',
    'template' => 'templates/meeting_details.php',
    'types' => array('meetings'),
    'hide_title' => FALSE,
    'hide_editor' => TRUE,
    'lock' => WPALCHEMY_LOCK_BEFORE_POST_TITLE,
    'view' => WPALCHEMY_VIEW_ALWAYS_OPENED,
    'save_filter' => 'cmm_meeting_save_filter',
    'init_action' => 'cmm_meeting_metabox_init',
));

$cmm_meeting_files = new WPAlchemy_MetaBox(array
(
    'id' => '_cmm_meeting_files', // underscore prefix hides fields from the custom fields area
    'prefix' => '_cmm_file_',
    'title' => 'Meeting Files',
    'template' => 'templates/meeting_files.php',
    'types' => array('meetings'),
    'mode' => WPALCHEMY_MODE_EXTRACT,
    'lock' => WPALCHEMY_LOCK_BEFORE_POST_TITLE,
    'view' => WPALCHEMY_VIEW_ALWAYS_OPENED,
));

$cmm_location = new WPAlchemy_MetaBox(array
(
    'id' => '_cmm_meeting_locations', // underscore prefix hides fields from the custom fields area
    'title' => 'Location',
    'template' => WP_PLUGIN_URL . '/meeting-manager/metabox_html/meeting_locations.php',
    'types' => array('locations'),
    'hide_editor' => FALSE,
    'hide_title' => FALSE,
    'lock' => WPALCHEMY_LOCK_AFTER_POST_TITLE,
    'view' => WPALCHEMY_VIEW_ALWAYS_OPENED,
));

$cmm_event_details = new WPAlchemy_MetaBox(array
(
    'id' => '_cmm_event_details', // underscore prefix hides fields from the custom fields area
    'title' => 'Event Details',
    'template' => 'templates/event_details.php',
    'types' => array('events'),
    'hide_editor' => TRUE,
    'lock' => WPALCHEMY_LOCK_AFTER_POST_TITLE,
    'view' => WPALCHEMY_VIEW_ALWAYS_OPENED,
    'save_filter' => 'cmm_event_save_filter',
    'init_action' => 'cmm_event_metabox_init',
));

$cmm_event_files = new WPAlchemy_MetaBox(array
(
    'id' => '_cmm_event_files', // underscore prefix hides fields from the custom fields area
    'prefix' => '_cmm_event_file_',
    'title' => 'Event Files',
    'template' => 'templates/event_files.php',
    'types' => array('events'),
    'mode' => WPALCHEMY_MODE_EXTRACT,
    'view' => WPALCHEMY_VIEW_ALWAYS_OPENED,
));

function cmm_admin_post_assets()
{
    wp_enqueue_style('custom_meta_admin_css', CMM_LIB_PATH . 'css/admin.css');
    wp_enqueue_style('custom_meta_css', CMM_LIB_PATH . 'css/meta.css');
    wp_enqueue_style('datepicker-overcast', CMM_LIB_PATH . 'css/overcast/jquery-ui-1.8.17.custom.css');

    wp_register_script('admin-plugins', CMM_LIB_PATH . 'js/admin-plugins.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-slider'));
    wp_register_script('chophouse-directory', CMM_LIB_PATH . 'js/meta.js', array('jquery', 'jquery-ui-datepicker', 'admin-plugins'));
    wp_enqueue_script('chophouse-directory');
}

function cmm_meeting_metabox_init() {
    cmm_admin_post_assets();
    add_filter('title_save_pre', 'cmm_set_meeting_title');
}

function cmm_event_metabox_init() {
    cmm_admin_post_assets();
    add_filter('enter_title_here', 'cmm_new_post_event_title');
}

function cmm_new_post_event_title($title) {
  $screen = get_current_screen();
    $title = 'Enter Event Title';
  return $title;
}

function remove_post_custom_fields()
{
    remove_meta_box('meeting_typesdiv', 'meetings', 'normal');
}

add_action('admin_menu', 'remove_post_custom_fields');

function cmm_meeting_save_filter($meta, $post_id)
{
    if (!isset($meta['meetingDate']))
        return false; // returning false stops WPAlchemy from saving

    //we have to set timezone to California
    date_default_timezone_set(get_option('timezone_string'));

    $meetingType = $_POST['meeting-type'];
    $meta['meetingDate'] = ($meta['meetingDate'] ? strtotime($meta['meetingDate']) : '');
    $meta['meetingOpen'] = ($meta['meetingOpen'] ? strtotime(date('d-m-Y ' , $meta['meetingDate']) . $meta['meetingOpen']) : '');
    $meta['meetingStart'] = ($meta['meetingStart'] ? strtotime(date('d-m-Y ' , $meta['meetingDate']) . $meta['meetingStart']) : '');

    update_post_meta($post_id, "meeting-type", $meetingType);
    update_post_meta($post_id, "eventdate", $meta['meetingStart']);

    wp_set_post_terms($post_id, $meetingType, 'meeting_types');

    return $meta;
}

function cmm_event_save_filter($meta, $post_id)
{
    if (!isset($meta['eventDate']))
        return false; // returning false stops WPAlchemy from saving

    //we have to set timezone to California
    date_default_timezone_set(get_option('timezone_string'));

    $meta['eventDate'] = ($meta['eventDate'] ? strtotime($meta['eventDate']) : '');
    $meta['eventOpen'] = ($meta['eventOpen'] ? strtotime(date('d-m-Y ' , $meta['eventDate']) . $meta['eventOpen']) : '');
    $meta['eventStart'] = ($meta['eventStart'] ? strtotime(date('d-m-Y ' , $meta['eventDate']) . $meta['eventStart']) : '');

    update_post_meta($post_id, "eventdate", $meta['eventStart']);

    return $meta;
}

function cmm_set_meeting_title($postTitle)
{
    global $post;
    if (!$post)
        return $postTitle;
    $meetingTypeID = $_POST['meeting-type'];
    if (!$meetingTypeID)
        return $postTitle;

    $meetingTypeObj = get_term($meetingTypeID, 'meeting_types');
    $meetingName = $meetingTypeObj->name;
    return $meetingName;
}

function cmm_set_meeting_slug($postName)
{
    global $post;
    if (!$post)
        return $postName;
//  var_dump($_POST);
//  exit();
    $meeting_details = $_POST['_cmm_meeting_details'];
    $meetingDay = strtotime($meeting_details['meetingDate']);
    $meetingTypeID = $_POST['meeting-type'];
    if (!$meetingDay || !$meetingTypeID)
        return $postName;

    $meetingTypeObj = get_term($meetingTypeID, 'meeting_types');
    $meetingTypeSlug = $meetingTypeObj->slug;
    $postName = date('Y-m-d-', $meetingDay) . $meetingTypeSlug;
    return $postName;
}

add_filter('name_save_pre', 'cmm_set_meeting_slug');

function cmm_display_events()
{
    //we have to set timezone to California
    date_default_timezone_set(get_option('timezone_string'));
    $tonight = strtotime('12:00am');
    $args = array(
        "posts_per_page" => -1,
        "post_type" => array('meetings','events'),
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
    $event_list = new WP_Query($args);

    $output = '<h2 class="icon detail calendar">Meetings &amp; Events</h2>';
    $output .= '<ul class="events">';
    while ($event_list->have_posts()) : $event_list->the_post();
        global $post;
        $type = (get_post_type() == 'meetings' ? 'meeting' : (get_post_type() == 'events' ? 'event' : ''));
        $event_date = get_post_meta($post->ID, "eventdate", TRUE);
        $event_details = get_post_meta($post->ID, '_cmm_'.$type.'_details', TRUE);

        if (get_post_type() == 'meetings'):
            $event_time = (isset($event_details['meetingStart']) ? $event_details['meetingStart'] : '');
            $event_open = (isset($event_details['meetingOpen']) ? date('g:iA', $event_details['meetingOpen']) : '');
        elseif (get_post_type() == 'events'):
            $event_time = (isset($event_details['eventStart']) ? $event_details['eventStart'] : '');
            $event_open = (isset($event_details['eventOpen']) ? date('g:iA', $event_details['eventOpen']) : '');
        endif;
        $meeting_agenda = get_post_meta($post->ID, "_cmm_file_agenda", TRUE);
        $event_flyer = get_post_meta($post->ID, "_cmm_event_file_flyer", TRUE);
        $output .= '<li>';
        $output .= '<div class="datebox">';
        $output .= '<span class="month">' . date('M', $event_date) . '</span>';
        $output .= '<span class="day">' . date('d', $event_date) . '</span>';
        $output .= '</div>';
        $output .= '<div class="details">';
        $output .= '<table>';
        $output .= '<tr class="title">';
        $output .= '<th colspan="2"><h3>' . get_the_title() . ($type == 'meeting' ? ' Meeting' : '') .'</h3></th>';
//        $output .= '<th colspan="2"><h3><a href="' . get_permalink($post->ID) . '">' . get_the_title() . ' Meeting</a></h3></th>';
        $output .= '</tr>';
        if (isset($event_details['desc'])) {
            $output .= '<tr class="subtitle">';
            $output .= '<td colspan="2">' . $event_details['desc'] . '</td>';
            $output .= '</tr>';
        }
        if (isset($event_details['venue']) || isset($event_details['address1'])) {
            $venue = (isset($event_details['venue']) ? (isset($event_details['address1']) ? $event_details['venue'] . ', ' : $event_details['venue']) : '');
            $address1 = (isset($event_details['address1']) ? $event_details['address1'] : '');
            $address2 = (isset($event_details['address2']) ? ', ' . $event_details['address2'] : '');
            $city = (isset($event_details['city']) ? ', ' . $event_details['city'] : '');
            $output .= '<tr>';
            $output .= '<td class="where">Where:</td>';
            $output .= '<td>' . $venue . $address1 . $address2 . $city . '</td>';
            $output .= '</tr>';
        }
        if ($event_date) {
            $output .= '<tr>';
            $output .= '<td class="when">When:</td>';
            $output .= ($event_time ? '<td><span class="time">' . date('g:iA', $event_date) . '</span> on ' : '<td>'). '<span class="date">' . date('l, F jS, Y', $event_date) . '</span>'. ($event_open ? '<br>(Doors Open at ' . $event_open . ')' : '') .'</td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
        $output .= '</div>';
        $output .= '<div class="buttons">';
        if ($meeting_agenda)
            $output .= '<a href="' . $meeting_agenda . '" class="button small primary">View Agenda</a>';
        if ($event_flyer)
            $output .= '<a href="' . $event_flyer . '" class="button small primary">View Info</a>';
//    $output .= '<a href="#" class="button small secondary">Share Event</a>';
        $output .= '</div>';
        $output .= '</li>';
    endwhile;
//    $output .= '<li class="more">';
//    $output .= '<a href="#" class="button action">View Past Events</a>';
//    $output .= '</li>';
    $output .= '</ul><!-- .events -->';

    return $output;
}

add_shortcode('display_events', 'cmm_display_events');

// add_action('query_vars', 'cmm_calendar_json_vars');
// add_filter('posts_request', 'cmm_calendar_posts_query' );
// add_action('wp', 'cmm_calendar_json');

function cmm_calendar_json_vars( $qvars ) {
    $qvars[] = 'start';
    $qvars[] = 'end';
    return $qvars;
}


function cmm_calendar_posts_query( $query ) {
    global $wp_query;
    $pagename = ( isset($wp_query->query_vars['pagename']) ? $wp_query->query_vars['pagename'] : '');

    if ($pagename == CMM_CALENDAR_FEED_SLUG) {
        $starttime = $wp_query->query_vars['start'];
        $endtime = $wp_query->query_vars['end'];
        global $wpdb;

        $query = "
            SELECT *
            FROM $wpdb->posts wposts, $wpdb->postmeta eventdate
            WHERE wposts.ID = eventdate.post_id
            AND (eventdate.meta_key = 'eventdate' AND eventdate.meta_value > $starttime )
            AND (eventdate.meta_key = 'eventdate' AND eventdate.meta_value < $endtime )
            AND (wposts.post_type = 'meetings' OR wposts.post_type = 'events')
            AND wposts.post_status = 'publish'
            ORDER BY eventdate.meta_value ASC LIMIT 90
        ";
    }
    return $query;
}

// function cmm_calendar_json() {
//     global $wp_query;
//     $pagename = $wp_query->query_vars['pagename'];
//
//     if ($pagename == CMM_CALENDAR_FEED_SLUG) {
//         $events = $wp_query->posts;
//         $jsonevents = array();
//
//         // - loop -
//         if ($events):
//             global $post;
//             foreach ($events as $post):
//                 // print_r($post);
//                 setup_postdata($post);
//
//                 // - custom post type variables -
//                 $start = get_post_custom_values('eventdate', get_the_ID());
//                 $start = $start[0];
//
//                 date_default_timezone_set(get_option('timezone_string'));
//
//                 $gmts = date('Y-m-d H:i:s', $start);
//                 // $gmts = get_gmt_from_date($gmts); // this function requires Y-m-d H:i:s
//                 $gmts = strtotime($gmts);
//
//                 // - set to ISO 8601 date format -
//                 $stime = date('c', $gmts);
//
//                 // - json items -
//                 $jsonevents[]= array(
//                     'title' => html_entity_decode(get_the_title($post->ID),ENT_QUOTES,'UTF-8'),
//                     'allDay' => false, // <- true by default with FullCalendar
//                     'start' => $stime,
//                     'url' => get_permalink($post->ID)
//                     );
//
//             endforeach;
//
//             echo(json_encode($jsonevents));
//             die();
//         endif;
//     }
// }
//

function cmm_display_calendar()
{
    echo "<div id='calendar'></div>";
}

add_shortcode('display_calendar', 'cmm_display_calendar');

function enqueue_calendar_scripts() {
    wp_register_script('fullcalendar', CMM_LIB_PATH . 'js/fullcalendar.min.js', array('jquery', 'jquery-ui-core'));
    wp_register_script('cmm-calendar', CMM_LIB_PATH . 'js/cmm-scripts.js', array('fullcalendar'));
    wp_enqueue_script('cmm-calendar');
    $data = array('websiteURL' => get_bloginfo( 'wpurl' ) );
    wp_localize_script( 'cmm-calendar', 'wordpressData', $data );
}
add_action('wp_enqueue_scripts', 'enqueue_calendar_scripts');

function enqueue_calendar_style() {

    $active_theme = wp_get_theme();
    $user_style_root = WP_THEME_DIR . $active_theme->template . '/calendar-theme/jquery-ui-1.10.2.custom.min.css';
    $user_style_lib = WP_THEME_DIR . $active_theme->template . '/lib/calendar-theme/jquery-ui-1.10.2.custom.min.css';

    if ( file_exists( $user_style_root ) ) {
        $calendar_css = WP_USER_THEME_URI . '/calendar-theme/jquery-ui-1.10.2.custom.min.css';
    } elseif ( file_exists( $user_style_lib ) ) {
        $calendar_css = WP_USER_THEME_URI . '/lib/calendar-theme/jquery-ui-1.10.2.custom.min.css';
    } else {
        $calendar_css = CMM_LIB_PATH . 'css/calendar-theme/jquery-ui-1.10.0.custom.min.css';
    }

    wp_enqueue_style('fullcalendar', CMM_LIB_PATH . 'css/fullcalendar.css');
    wp_enqueue_style('fullcalendar-print', CMM_LIB_PATH . 'css/fullcalendar.print.css', '', '', 'print');
    wp_enqueue_style('fullcalendar-theme', $calendar_css);
wp_enqueue_style('cmm-calendar', CMM_LIB_PATH . 'css/cmm-styles.css');
}
add_action('wp_enqueue_scripts', 'enqueue_calendar_style');

function cmm_show_agendas()
{
    date_default_timezone_set(get_option('timezone_string'));
    $args = array(
        "posts_per_page" => -1,
        "post_type" => array('meetings'),
        "post_status" => "publish",
        "meta_key" => "eventdate",
        "orderby" => "meta_value_num",
        "order" => "DESC",
        'meta_query' => array(
            array(
                'key' => '_cmm_file_agenda',
            )
        )
    );
    $event_list = new WP_Query($args);

    $output = '<ul class="events">';
    while ($event_list->have_posts()) : $event_list->the_post();
        global $post;
        $event_date = get_post_meta($post->ID, "eventdate", TRUE);
        $meeting_details = get_post_meta($post->ID, "_cmm_meeting_details", TRUE);
        $meeting_agenda = get_post_meta($post->ID, "_cmm_file_agenda", TRUE);
//    $feat_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'pet-thumb-crop');
//    $event_desc = substr(strip_tags($event_info['desc'], ''), 0, 230);
        $output .= '<li>';
        $output .= '<div class="datebox">';
        $output .= '<span class="month">' . date('M', $event_date) . '</span>';
        $output .= '<span class="day">' . date('d', $event_date) . '</span>';
        $output .= '</div>';
        $output .= '<div class="details">';
        $output .= '<table>';
        $output .= '<tr class="title">';
        $output .= '<th colspan="2"><h3><a href="' . get_permalink($post->ID) . '">' . get_the_title() . ' Agenda</a></h3></th>';
        $output .= '</tr>';
        if (isset($meeting_details['desc'])) {
            $output .= '<tr class="subtitle">';
            $output .= '<td colspan="2">' . $meeting_details['desc'] . '</td>';
            $output .= '</tr>';
        }
        if (isset($meeting_details['venue']) || isset($meeting_details['address1'])) {
            $venue = (isset($meeting_details['venue']) ? (isset($meeting_details['address1']) ? $meeting_details['venue'] . ', ' : $meeting_details['venue']) : '');
            $address1 = (isset($meeting_details['address1']) ? $meeting_details['address1'] : '');
            $address2 = (isset($meeting_details['address2']) ? ', ' . $meeting_details['address2'] : '');
            $city = (isset($meeting_details['city']) ? ', ' . $meeting_details['city'] : '');
            $output .= '<tr>';
            $output .= '<td class="where">Where:</td>';
            $output .= '<td>' . $venue . $address1 . $address2 . $city . '</td>';
            $output .= '</tr>';
        }
        if ($event_date) {
            $output .= '<tr>';
            $output .= '<td class="when">When:</td>';
            $output .= '<td><span class="time">' . date('g:iA', $event_date) . '</span> on <span class="date">' . date('l, F jS, Y', $event_date) . '</span></td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
        $output .= '</div>';
    $output .= '<div class="buttons">';
        if ($meeting_agenda)
            $output .= '<a href="' . $meeting_agenda . '" class="button small primary">Open Agenda</a>';
//    $output .= '<a href="' . get_permalink($post->ID) . '" class="button small secondary">View Meeting</a>';
    $output .= '</div>';
        $output .= '</li>';
    endwhile;
    $output .= '</ul><!-- .events -->';

    return $output;
}

add_shortcode('show_agendas', 'cmm_show_agendas');

function cmm_show_minutes()
{
    date_default_timezone_set(get_option('timezone_string'));
    $args = array(
        "posts_per_page" => -1,
        "post_type" => array('meetings'),
        "post_status" => "publish",
        "meta_key" => "eventdate",
        "orderby" => "meta_value_num",
        "order" => "DESC",
        'meta_query' => array(
            array(
                'key' => '_cmm_file_minutes',
            )
        )
    );
    $event_list = new WP_Query($args);

    $output = '<ul class="events">';
    while ($event_list->have_posts()) : $event_list->the_post();
        global $post;
        $event_date = get_post_meta($post->ID, "eventdate", TRUE);
        $meeting_details = get_post_meta($post->ID, "_cmm_meeting_details", TRUE);
        $meeting_minutes = get_post_meta($post->ID, "_cmm_file_minutes", TRUE);
//    $feat_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'pet-thumb-crop');
//    $event_desc = substr(strip_tags($event_info['desc'], ''), 0, 230);
        $output .= '<li>';
        $output .= '<div class="datebox">';
        $output .= '<span class="month">' . date('M', $event_date) . '</span>';
        $output .= '<span class="day">' . date('d', $event_date) . '</span>';
        $output .= '</div>';
        $output .= '<div class="details">';
        $output .= '<table>';
        $output .= '<tr class="title">';
        $output .= '<th colspan="2"><h3><a href="' . get_permalink($post->ID) . '">' . get_the_title() . ' Minutes</a></h3></th>';
        $output .= '</tr>';
        if (isset($meeting_details['desc'])) {
            $output .= '<tr class="subtitle">';
            $output .= '<td colspan="2">' . $meeting_details['desc'] . '</td>';
            $output .= '</tr>';
        }
        if (isset($meeting_details['venue']) || isset($meeting_details['address1'])) {
            $venue = (isset($meeting_details['venue']) ? (isset($meeting_details['address1']) ? $meeting_details['venue'] . ', ' : $meeting_details['venue']) : '');
            $address1 = (isset($meeting_details['address1']) ? $meeting_details['address1'] : '');
            $address2 = (isset($meeting_details['address2']) ? ', ' . $meeting_details['address2'] : '');
            $city = (isset($meeting_details['city']) ? ', ' . $meeting_details['city'] : '');
            $output .= '<tr>';
            $output .= '<td class="where">Where:</td>';
            $output .= '<td>' . $venue . $address1 . $address2 . $city . '</td>';
            $output .= '</tr>';
        }
        if ($event_date) {
            $output .= '<tr>';
            $output .= '<td class="when">When:</td>';
            $output .= '<td><span class="time">' . date('g:iA', $event_date) . '</span> on <span class="date">' . date('l, F jS, Y', $event_date) . '</span></td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
        $output .= '</div>';
        $output .= '<div class="buttons">';
        $output .= '<a href="' . $meeting_minutes . '" target="_blank" class="button small primary">Open Minutes</a>';
        // $output .= '<a href="' . get_permalink($post->ID) . '" class="button small secondary">View Meeting</a>';
        $output .= '</div>';
        $output .= '</li>';
    endwhile;
    $output .= '</ul><!-- .events -->';

    return $output;
}

add_shortcode('show_minutes', 'cmm_show_minutes');

function cmm_display_single_meeting($content)
{
    if (!(is_single() && 'meetings' == get_post_type()))
        return $content;

    date_default_timezone_set(get_option('timezone_string'));

    global $post; //We need this to "get_the_title" and $post->ID.
    //$meeting_meta = get_post_meta($post->ID, "_cmm_meeting_details", TRUE); //Get the data from the "Pet Information" metabox in the Add/Edit pet admin page.
    //Set the true or false checkboxes from the admin to human readable labels for the front-end.
    //$output= print_r($meeting_meta);
    $event_date = get_post_meta($post->ID, "eventdate", TRUE);
    $meeting_details = get_post_meta($post->ID, "_cmm_meeting_details", TRUE);
    $meeting_agenda = get_post_meta($post->ID, "_cmm_file_agenda", TRUE);
    $meeting_minutes = get_post_meta($post->ID, "_cmm_file_minutes", TRUE);
    $output = '<div class="details">';
    if (isset($meeting_details['desc'])) {
        $output .= '<h3 class="sub-title" style="text-align: center;">' . $meeting_details['desc'] . '</h3>';
    }
    $output .= '<div class="datebox">';
    $output .= '<span class="month">' . date('M', $event_date) . '</span>';
    $output .= '<span class="day">' . date('d', $event_date) . '</span>';
    $output .= '</div>';

    $output .= '<table>';
            if (isset($meeting_details['venue']) || isset($meeting_details['address1'])) {
                $venue = (isset($meeting_details['venue']) ? (isset($meeting_details['address1']) ? $meeting_details['venue'] . ', ' : $meeting_details['venue']) : '');
                $address1 = (isset($meeting_details['address1']) ? $meeting_details['address1'] : '');
                $address2 = (isset($meeting_details['address2']) ? ', ' . $meeting_details['address2'] : '');
                $city = (isset($meeting_details['city']) ? ', ' . $meeting_details['city'] : '');
                $output .= '<tr>';
                $output .= '<td class="where">Where:</td>';
                $output .= '<td>' . $venue . $address1 . $address2 . $city . '</td>';
                $output .= '</tr>';
            }
            if ($event_date) {
                $output .= '<tr>';
                $output .= '<td class="when">When:</td>';
                $output .= '<td><span class="time">' . date('g:iA', $event_date) . '</span> on <span class="date">' . date('l, F jS, Y', $event_date) . '</span></td>';
                $output .= '</tr>';
            }
            $output .= '</table>';
            $output .= '</div>';
            $output .= '<div class="buttons">';
            if ($meeting_agenda) {
                $output .= '<a href="' . $meeting_agenda . '" target="_blank" class="button small primary">Open Agenda</a><br><br>';
            }
            if ($meeting_minutes) {
                $output .= '<a href="' . $meeting_minutes . '" target="_blank" class="button small secondary">Open Minutes</a>';
            }
            $output .= '</div>';

    $content .= $output;

    return $content;
}

add_filter('the_content', 'cmm_display_single_meeting', 20);

function cmm_display_single_event($content)
{
    if (!(is_single() && 'events' == get_post_type()))
        return $content;

    date_default_timezone_set(get_option('timezone_string'));

    global $post; //We need this to "get_the_title" and $post->ID.
    //$meeting_meta = get_post_meta($post->ID, "_cmm_meeting_details", TRUE); //Get the data from the "Pet Information" metabox in the Add/Edit pet admin page.
    //Set the true or false checkboxes from the admin to human readable labels for the front-end.
    //$output= print_r($meeting_meta);
    $event_date = get_post_meta($post->ID, "eventdate", TRUE);
    $event_details = get_post_meta($post->ID, '_cmm_event_details', TRUE);
    $event_flyer = get_post_meta($post->ID, "_cmm_event_file_flyer", TRUE);
    $output = '<div class="details">';
    if (isset($event_details['desc'])) {
        $output .= '<h3 class="sub-title" style="text-align: center;">' . $event_details['desc'] . '</h3>';
    }
    $output .= '<div class="datebox">';
    $output .= '<span class="month">' . date('M', $event_date) . '</span>';
    $output .= '<span class="day">' . date('d', $event_date) . '</span>';
    $output .= '</div>';

    $output .= '<table>';
            if (isset($event_details['venue']) || isset($event_details['address1'])) {
                $venue = (isset($event_details['venue']) ? (isset($event_details['address1']) ? $event_details['venue'] . ', ' : $event_details['venue']) : '');
                $address1 = (isset($event_details['address1']) ? $event_details['address1'] : '');
                $address2 = (isset($event_details['address2']) ? ', ' . $event_details['address2'] : '');
                $city = (isset($event_details['city']) ? ', ' . $event_details['city'] : '');
                $output .= '<tr>';
                $output .= '<td class="where">Where:</td>';
                $output .= '<td>' . $venue . $address1 . $address2 . $city . '</td>';
                $output .= '</tr>';
            }
            if ($event_date) {
                $output .= '<tr>';
                $output .= '<td class="when">When:</td>';
                $output .= '<td><span class="time">' . date('g:iA', $event_date) . '</span> on <span class="date">' . date('l, F jS, Y', $event_date) . '</span></td>';
                $output .= '</tr>';
            }
            $output .= '</table>';
            $output .= '</div>';
            $output .= '<div class="buttons">';
            if ($event_flyer) {
                $output .= '<a href="' . $event_flyer . '" class="button small primary">View Info</a>';
            }

    $content .= $output;

    return $content;
}

add_filter('the_content', 'cmm_display_single_event', 20);

function cmm_single_meeting_title($title)
{
    return $title . ((is_single() && 'meetings' == get_post_type() && in_the_loop()) ? ' Meeting' : '');
}

add_filter('the_title', 'cmm_single_meeting_title', 20);
