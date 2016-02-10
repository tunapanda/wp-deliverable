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
		self::field("slug","varchar(255) not null");
		self::field("title","varchar(255)");
		self::field("description","text");
		self::field("reviewGroup","varchar(255)");
		self::field("type","varchar(255)");
	}

	/**
	 * Get unique slug.
	 */
	public static function getUniqueSlug($title, $id) {
		$slug=strtolower($title);
		$slug=preg_replace('/[^A-Za-z0-9]+/','-',$slug);
		$originalSlug=$slug;

		$deliverable=Deliverable::findOneBy("slug",$slug);
		$counter=2;

		while ($deliverable) {
			if ($deliverable->id==$id)
				return $slug;

			$slug=$originalSlug."-".$counter;
			$deliverable=Deliverable::findOneBy("slug",$slug);
			$counter++;
		}

		return $slug;
	}
}