<?php

require_once __DIR__."/src/controller/DeliverableController.php";
require_once __DIR__."/src/model/Deliverable.php";

use wpdeliverable\DeliverableController;
use wpdeliverable\Deliverable;

/*
Plugin Name: Deliverable
Plugin URI: http://github.com/tunapanda/wp-deliverable
Description: Lets learners submit deliverables and have coaches review them.
Version: 0.0.1
*/

DeliverableController::init();

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
/*	add_menu_page(
		"Deliverables",
		"Deliverables",
		"manage_options",
		"Deliverables_list",
		"deliverable_create_deliverables_page"
	);*/

	add_submenu_page(
		'Deliverables',
		'Review Submissions',
		'Review Submissions',
		'manage_options',
		'manage_deliverables',
		'deliverable_create_review_page'
	);
}

add_action('admin_menu','deliverable_admin_menu');

/** 
 * Activation hook.
 */
function deliverable_activate() {
	Deliverable::install();
}

register_activation_hook(__FILE__,'deliverable_activate');

/**
 * Uninstall hook.
 */
function deliverable_uninstall() {
	Deliverable::uninstall();
}

register_uninstall_hook(__FILE__,'deliverable_uninstall');
