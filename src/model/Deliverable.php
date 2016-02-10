<?php

namespace wpdeliverable;

require_once __DIR__."/../../ext/smartrecord/SmartRecord.php";

use \SmartRecord;

/**
 * A cashgame.
 */
class Deliverable extends SmartRecord {

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Initialize database.
	 */
	public static function initialize() {
		self::field("id","integer not null auto_increment");
		self::field("title","varchar(255)");
	}
}