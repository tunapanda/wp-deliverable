<?php

namespace wpdeliverable;
use \Exception;

require_once ABSPATH.'wp-admin/includes/plugin.php';

/**
 * Pragmatic group user checking.
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

			case "groups":
				return $this->params["name"];
				break;

			default:
				throw new Exception("unknown group type");
				break;
		}
	}

	/**
	 * Generate a slug based on a name.
	 */
	private static function getSlugByName($name) {
		$slug=strtolower($name);
		$slug=preg_replace('/[^A-Za-z0-9]+/','-',$slug);

		return $slug;
	}

	/**
	 * Get slug.
	 */
	public function getSlug() {
		switch ($this->type) {
			case "user-groups":
				return "user-groups:".$this->params["slug"];
				break;

			case "groups":
				return "groups:".WpGroup::getSlugByName($this->params["name"]);
				break;

			default:
				throw new Exception("unknown group type");
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

			case "groups":
				$q=$wpdb->prepare(
					"SELECT  user_id ".
					"FROM    {$wpdb->prefix}groups_user_group ".
					"WHERE   group_id=%s ",
					$this->params["group_id"]
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

			default:
				throw new Exception("bad group type");
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
	 * Is this group provider available on the current system.
	 */
	private static function isProviderAvailable($provider) {
		switch ($provider) {
			case 'user-groups':
				return is_plugin_active("user-groups/user-groups.php");
				break;

			case 'groups':
				return is_plugin_active("groups/groups.php");
				break;
			
			default:
				return FALSE;
				break;
		}
	}

	/**
	 * Get groups that the user belong to.
	 */
	public static function getGroupsForUser($user) {
		global $wpdb;

		if (!$user->ID)
			return array();

		$groups=array();

		if (WpGroup::isProviderAvailable("user-groups")) {
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

			foreach ($slugs as $slug)
				$groups[]=WpGroup::getGroupBySlug("user-groups:".$slug);
		}

		if (WpGroup::isProviderAvailable("groups")) {
			$q=$wpdb->prepare(
				"SELECT     g.name ".
				"FROM       {$wpdb->prefix}groups_user_group AS ug ".
				"LEFT JOIN  {$wpdb->prefix}groups_group as g ".
				"ON         ug.group_id=g.group_id ".
				"WHERE      ug.user_id=%s",
				$user->ID);

			$names=$wpdb->get_col($q);
			if ($wpdb->last_error)
				throw new Exception($wpdb->last_error);

			foreach ($names as $name)
				$groups[]=WpGroup::getGroupBySlug("groups:".WpGroup::getSlugByName($name));
		}

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

		if (WpGroup::isProviderAvailable("user-groups")) {
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

		if (WpGroup::isProviderAvailable("groups")) {
			$rows=$wpdb->get_results(
				"SELECT    * FROM {$wpdb->prefix}groups_group",
				ARRAY_A);

			if ($wpdb->last_error)
				throw new Exception($wpdb->last_error);

			foreach ($rows as $row)
				$groups[]=new WpGroup("groups",$row);
		}

		WpGroup::$allGroups=$groups;
		return WpGroup::$allGroups;
	}
}