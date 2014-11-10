<?php
// Ajax Get Social Media Details
function ajax_nova_sm_get_social_media_details() {
	global $wpdb;
	$id = $_POST['id'];
	
	// Set Query
	$sql = "SELECT * FROM nova_social_media WHERE id = $id";
	
	// Get Results
	$result = $wpdb->get_row($sql, ARRAY_A);
	
	// Return Data
	echo json_encode($result);
	die();
}
add_action('wp_ajax_nova_sm_get_social_media_details', 'ajax_nova_sm_get_social_media_details');

// Ajax Quick Edit Update
function ajax_nova_sm_quick_edit_update() {
	global $wpdb;

	$wpdb->update(
		'nova_social_media',
		array(
			'name' => trim($_POST['name']),
			'link_address' => $_POST['link_address'],
			'visibility' => $_POST['visibility'],
			'updated' => current_time('mysql')
		),
		array( 'id' =>  $_POST['id'] )
	);

	// Set Query
	$sql = "SELECT * FROM nova_social_media WHERE id = " . $_POST['id'];
	
	// Get Results
	$results = $wpdb->get_results($sql, ARRAY_A);
	$results[0]['link_address_modified'] = '<a href="' . $results[0]['link_address'] . '" target="_blank">' . $results[0]['link_address'] . '</a>';
	$date_diff = ( $results[0]['updated'] != '' ? human_time_diff( strtotime($results[0]['updated']), current_time('timestamp') ) : human_time_diff( strtotime($results[0]['created']), current_time('timestamp') ) ) . ' ago';
	$date_action = $results[0]['updated'] != '' ? 'Updated' : 'Created';
	$results[0]['date'] = $date_diff . '<br>' . $date_action;
	
	// Return Data
	echo json_encode($results[0]);
	die();
}
add_action('wp_ajax_nova_sm_quick_edit_update', 'ajax_nova_sm_quick_edit_update');