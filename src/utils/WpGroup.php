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
	 * Get groups where the currently logged in user belong.
	 */
	public static function getGroupsForCurrentUser() {
		return WpGroup::getGroupsForUser(wp_get_current_user());
	}

	/**
	 * Get groups that the user belong to.
	 */
	public static function getGroupsForUser($user) {
		global $wpdb;

		if (!$user->ID)
			return array();

		$q=$wpdb->prepare(
			"SELECT     slug ".
			"FROM       {$wpdb->prefix}term_relationships AS r ".
			"LEFT JOIN  {$wpdb->prefix}term_taxonomy AS x ".
			"ON         r.term_taxonomy_id=x.term_taxonomy_id ".
			"LEFT JOIN  wp_terms AS t ".
			"ON         x.term_id=t.term_id ".
			"WHERE      r.object_id=%d ".
			"AND        taxonomy='user-group'",
			$user->ID);

		$slugs=$wpdb->get_col($q);
		if ($wpdb->last_error)
			throw new Exception($wpdb->last_error);

		$groups=array();
		foreach ($slugs as $slug)
			$groups[]=WpGroup::getGroupBySlug("user-groups:".$slug);

		return $groups;
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