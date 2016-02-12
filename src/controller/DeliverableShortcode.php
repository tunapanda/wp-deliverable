<?php

namespace wpdeliverable;

use \Exception;

/**
 * Deliverable shortcode.
 */
class DeliverableShortcode {

	/**
	 * Process submission.
	 */
	private static function processSubmission($deliverable) {
		$user=wp_get_current_user();
		if (!$user|| !$user->ID)
			throw new Exception("Not logged in");

		$submission=DeliverableSubmission::findOneBy(array(
			"deliverable_id"=>$deliverable->id,
			"user_id"=>$user->ID
		));

		if (!$submission) {
			$submission=new DeliverableSubmission();
			$submission->deliverable_id=$deliverable->id;
			$submission->user_id=$user->ID;
		}

		$submission->type=$deliverable->type;

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

		$m=
			'<div class="deliverable notification">'.
			'Your work has been submitted! Please have patience while it is being reviewed.'.
			'</div>';

		echo $m;
	}

	/**
	 * Deliverable shortcode.
	 */
	public static function process($params) {
		$deliverable=Deliverable::findOneBy("slug",$params["slug"]);
		if (!$deliverable)
			throw new Exception("Deliverable not found");

		if (isset($_REQUEST["deliverable"]) || isset($_FILES["deliverable"])) {
			DeliverableShortcode::processSubmission($deliverable);
		}

		$template=new Template(__DIR__."/../template/deliverable.php");

		$template->set("deliverable",$deliverable);
		$template->set("submission",$deliverable->getSubmissionForCurrentUser());

		switch ($deliverable->type) {
			case "url":
				$template->set("submitLabel","Submit url:");
				$template->set("submitType","text");
				$template->set("showSubmitButton",TRUE);
				break;

			case "zip":
				$template->set("submitLabel","Upload zip file:");
				$template->set("submitType","file");
				$template->set("accept",".zip");
				break;

			case "pdf":
				$template->set("submitLabel","Upload pdf file:");
				$template->set("submitType","file");
				$template->set("accept",".pdf");
				break;

			default:
				throw new Exception("unknown type: ".$deliverable->type);
				break;
		}

		return $template->render();
	}
}