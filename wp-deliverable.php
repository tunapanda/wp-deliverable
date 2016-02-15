<?php

require_once __DIR__."/src/controller/DeliverableController.php";
require_once __DIR__."/src/controller/ReviewPageController.php";
require_once __DIR__."/src/controller/DeliverableShortcodeController.php";
require_once __DIR__."/src/model/Deliverable.php";
require_once __DIR__."/src/model/DeliverableSubmission.php";
require_once __DIR__."/src/utils/Xapi.php";
require_once __DIR__."/src/utils/Template.php";
require_once __DIR__."/src/plugin/DeliverableSyncer.php";

use wpdeliverable\Deliverable;
use wpdeliverable\DeliverableSubmission;
use wpdeliverable\DeliverableController;
use wpdeliverable\DeliverableShortcodeController;
use wpdeliverable\ReviewPageController;
use wpdeliverable\Xapi;
use wpdeliverable\Template;
use wpdeliverable\DeliverableSyncer;

/*
Plugin Name: Deliverable
Plugin URI: http://github.com/tunapanda/wp-deliverable
GitHub Plugin URI: http://github.com/tunapanda/wp-deliverable
Description: Lets learners submit deliverables and have coaches review them.
Version: 0.0.2
*/

/**
 * Create review page.
 */
function deliverable_create_review_page() {
	$reviewPageController=new ReviewPageController();
	$reviewPageController->process();
}

/**
 * Create xapi settings page.
 */
function deliverable_create_xapi_settings_page() {
	$t=new Template(__DIR__."/src/template/xapisettings.php");
	$t->show();
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

	add_submenu_page(
	    'deliverables',
	    'xAPI Settings',
	    'xAPI Settings',
	    'manage_options',
	    'deliverable_xapi_settings',
	    'deliverable_create_xapi_settings_page'
	);
}

add_action('admin_menu','deliverable_admin_menu');

/**
 * Admin init.
 */
function deliverable_admin_init() {
	register_setting("deliverable","deliverable_xapi_endpoint_url");
	register_setting("deliverable","deliverable_xapi_username");
	register_setting("deliverable","deliverable_xapi_password");
}

add_action('admin_init','deliverable_admin_init');

/**
 * Handle deliverable shortcode.
 */
function deliverable_deliverable($params) {
	$shortcodeController=new DeliverableShortcodeController();
	return $shortcodeController->deliverable($params);
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
add_action("admin_enqueue_scripts","deliverable_enqueue_scripts");

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

/**
 * Syncer.
 */
add_filter("remote-syncers",function($syncers) {
	$syncers[]=new DeliverableSyncer();
	return $syncers;
});
