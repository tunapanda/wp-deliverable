<?php

namespace wpdeliverable;
use \Exception;

require_once ABSPATH.'wp-admin/includes/plugin.php';

/**
 * Pragmatic groupt user checking.
 */
class WpGroup {

	private static $allGroups;

	/**
	 * Constructor.
	 */
	private function __construct($type, $params) {
		$this->type=$type;
		$this->params=$params;
	}

	/**
	 * Get label for showing the group in a list or so.
	 */
	public function getLabel() {
		switch ($this->type) {
			case "user-groups":
				return $this->params["name"];
				break;
		}
	}

	/**
	 * Get slug.
	 */
	public function getSlug() {
		switch ($this->type) {
			case "user-groups":
				return "user-groups:".$this->params["slug"];
				break;
		}
	}

	/**
	 * Get users in this group.
	 */
	public function getUsers() {
		global $wpdb;

		$users=array();

		switch ($this->type) {
			case "user-groups":
				$q=$wpdb->prepare(
					"SELECT   object_id ".
					"FROM     {$wpdb->prefix}term_relationships ".
					"WHERE    term_taxonomy_id=%s",
					$this->params["term_taxonomy_id"]
				);

				$ids=$wpdb->get_col($q);
				if ($wpdb->last_error)
					throw new Exception($wpdb->last_error);

				foreach ($ids as $id) {
					$user=get_user_by("id",$id);
					if ($user)
						$users[]=$user;
				}

				break;
		}

		return $users;
	}

	/**
	 * Get group by slug.
	 */
	public static function getGroupBySlug($slug) {
		$groups=WpGroup::getAllGroups();

		foreach ($groups as $group)
			if ($group->getSlug()==$slug)
				return $group;

		return NULL;
	}

	/**
	 * Find all groups.
	 */
	public static function getAllGroups() {
		if (isset(WpGroup::$allGroups))
			return WpGroup::$allGroups;

		global $wpdb;
		$groups=array();

		if (is_plugin_active("user-groups/user-groups.php")) {
			$rows=$wpdb->get_results(
				"SELECT    * FROM {$wpdb->prefix}term_taxonomy AS x ".
				"LEFT JOIN {$wpdb->prefix}terms AS t ".
				"ON        x.term_id=t.term_id ".
				"WHERE     x.taxonomy='user-group'",
				ARRAY_A);

			if ($wpdb->last_error)
				throw new Exception($wpdb->last_error);

			foreach ($rows as $row)
				$groups[]=new WpGroup("user-groups",$row);
		}

		WpGroup::$allGroups=$groups;
		return WpGroup::$allGroups;
	}
}