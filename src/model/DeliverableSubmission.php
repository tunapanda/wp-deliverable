<?php

namespace wpdeliverable;

require_once __DIR__."/../../ext/smartrecord/SmartRecord.php";

use \SmartRecord;

/**
 * A cashgame.
 */
class DeliverableSubmission extends SmartRecord {

	private $user;
	private $reviewUser;

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
		self::field("deliverable_id","integer not null");
		self::field("user_id","integer not null");
		self::field("content","text not null");
		self::field("type","varchar(255) not null");
		self::field("review_user_id","integer not null");
		self::field("state","varchar(255) not null");
		self::field("comment","text not null");
		self::field("submitStamp","integer not null");
		self::field("reviewStamp","integer not null");
	}

	/**
	 * Get link label.
	 */
	public function getLinkLabel() {
		return $this->content;
	}

	/**
	 * Get url.
	 */
	public function getUrl() {
		switch ($this->type) {
			case "url":
				return $this->content;

			case "pdf":
			case "zip":
				return wp_upload_dir()["baseurl"]."/deliverables/".$this->content;
				break;

			default:
				return "asd";
		}
	}

	/**
	 * Get user avatar.
	 */
	public function getUserAvatar() {
		return get_avatar($this->user_id,48);
	}

	/**
	 * Get user.
	 */
	public function getUser() {
		if (!$this->user)
			$this->user=get_user_by("id",$this->user_id);

		return $this->user;
	}

	/**
	 * Get review user.
	 */
	public function getReviewUser() {
		if (!$this->reviewUser)
			$this->reviewUser=get_user_by("id",$this->review_user_id);

		return $this->reviewUser;
	}

	/**
	 * Get review user.
	 */
	public function getReviewUserAvatar() {
		return get_avatar($this->review_user_id,48);
	}

	/**
	 * Submitted human time diff.
	 */
	public function getSubmittedHumanTimeDiff() {
		return human_time_diff($this->submitStamp);
	}

	/**
	 * Submitted human time diff.
	 */
	public function getReviewedHumanTimeDiff() {
		return human_time_diff($this->reviewStamp);
	}

	/**
	 * Get state.
	 */
	public function getState() {
		switch ($this->state) {
			case "approved":
			case "rejected":
				return $this->state;
				break;

			default:
				return "pending";
				break;
		}
	}

	/**
	 * Is it reviewed?
	 */
	public function isReviewed() {
		if (!$this->getReviewUser())
			return FALSE;

		if ($this->getState()!="approved" && $this->getState()!="rejected")
			return FALSE;

		return TRUE;
	}
}