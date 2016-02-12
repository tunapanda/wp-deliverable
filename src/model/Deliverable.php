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

	/**
	 * Get submission for current user.
	 */
	public function getSubmissionForCurrentUser() {
		$user=wp_get_current_user();
		if (!$user|| !$user->ID)
			return NULL;

		$submission=DeliverableSubmission::findOneBy(array(
			"deliverable_id"=>$this->id,
			"user_id"=>$user->ID
		));

		return $submission;
	}

	/**
	 * Create or update submission record for the current user.
	 * Relies on the current wordpress user, $_REQUEST and $_FILES.
	 */
	public function processSubmission() {
		$user=wp_get_current_user();
		if (!$user|| !$user->ID)
			throw new Exception("Not logged in");

		$submission=DeliverableSubmission::findOneBy(array(
			"deliverable_id"=>$this->id,
			"user_id"=>$user->ID
		));

		if (!$submission) {
			$submission=new DeliverableSubmission();
			$submission->deliverable_id=$this->id;
			$submission->user_id=$user->ID;
		}

		$submission->type=$this->type;

		switch ($submission->type) {
			case "url":
				$submission->content=$_REQUEST["deliverable"];
				break;

			case "zip":
			case "pdf":
				$deliverableDir=wp_upload_dir()["basedir"]."/deliverables";

				$prefix=md5(uniqid());
				$fileName=$prefix."-".$_FILES["deliverable"]["name"];
				$submission->content=$fileName;

				$res=move_uploaded_file($_FILES["deliverable"]["tmp_name"],$deliverableDir."/".$fileName);
				if (!$res)
					throw new Exception("Unable to move uploaded file.");

				$submission->save();
				break;

			default:
				throw new Exception("Unknown submission type");
				break;
		}

		$submission->review_user_id=0;
		$submission->reviewStamp=0;
		$submission->comment=0;
		$submission->state="pending";

		$submission->submitStamp=current_time("timestamp");
		$submission->save();
	}

	/**
	 * Get pending submissions for this deliverable.
	 */
	public function getPendingSubmissions() {
		return DeliverableSubmission::findAllBy(array(
			"deliverable_id"=>$this->id,
			"state"=>"pending"
		));
	}
}