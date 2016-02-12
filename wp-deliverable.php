<?php

require_once __DIR__."/src/controller/DeliverableController.php";
require_once __DIR__."/src/controller/DeliverableShortcode.php";
require_once __DIR__."/src/model/Deliverable.php";
require_once __DIR__."/src/model/DeliverableSubmission.php";

use wpdeliverable\Deliverable;
use wpdeliverable\DeliverableSubmission;
use wpdeliverable\DeliverableController;
use wpdeliverable\DeliverableShortcode;

/*
Plugin Name: Deliverable
Plugin URI: http://github.com/tunapanda/wp-deliverable
Description: Lets learners submit deliverables and have coaches review them.
Version: 0.0.1
*/

/**
 * Create review page.
 */
function deliverable_create_review_page() {
	echo "hello...";
}

/**
 * Listener for the admin_menu action.
 */
function deliverable_admin_menu() {
	add_menu_page(
	    "Deliverables",
	    "Deliverables",
	    "manage_options",
	    "deliverables"
	);

	DeliverableController::admin_menu();

	add_submenu_page(
	    'deliverables',
	    'Review Submissions',
	    'Review Submissions',
	    'manage_options',
	    'manage_deliverables',
	    'deliverable_create_review_page'
	);
}

add_action('admin_menu','deliverable_admin_menu');

/**
 * Handle deliverable shortcode.
 */
function deliverable_deliverable($params) {
	return DeliverableShortcode::process($params);
}

add_shortcode("deliverable","deliverable_deliverable");

/**
 * Enqueue scripts and styles.
 */
function deliverable_enqueue_scripts() {
	wp_enqueue_style('wp-deliverable', plugin_dir_url(__FILE__)."/wp-deliverable.css");
}

add_action("wp_enqueue_scripts","deliverable_enqueue_scripts");

/** 
 * Activation hook.
 */
function deliverable_activate() {
	Deliverable::install();
	DeliverableSubmission::install();

	$deliverableDir=wp_upload_dir()["basedir"]."/deliverables";
	if (!is_dir($deliverableDir)) {
		if (!mkdir($deliverableDir,0777,TRUE))
			wp_die("Unable to create directory for storing uploaded deliverables: ".$deliverableDir);
	}

	if (!touch($deliverableDir."/install.tmp"))
		wp_die("Delivery directory is not writeable: ".$deliverableDir);

	unlink($deliverableDir."/install.tmp");
}

register_activation_hook(__FILE__,'deliverable_activate');

/**
 * Uninstall hook.
 */
function deliverable_uninstall() {
	Deliverable::uninstall();
}

register_uninstall_hook(__FILE__,'deliverable_uninstall');
