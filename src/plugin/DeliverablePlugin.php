<?php

namespace wpdeliverable;

require __DIR__."/../utils/Xapi.php";

/**
 * Common base functions.
 */
class DeliverablePlugin {

	private static $instance;

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Get xapi endpoint, if configured.
	 */
	public function getXapi() {
		$endpoint=get_option("deliverable_xapi_endpoint_url");
		$username=get_option("deliverable_xapi_username");
		$password=get_option("deliverable_xapi_password");

		if ($endpoint)
			return new Xapi($endpoint,$username,$password);

		else
			return NULL;
	}

	/**
	 * Get sinleton instance.
	 */
	public static function instance() {
		if (!DeliverablePlugin::$instance)
			DeliverablePlugin::$instance=new DeliverablePlugin();

		return DeliverablePlugin::$instance;
	}
}