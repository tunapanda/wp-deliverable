<?php

namespace wpdeliverable;

use \Exception;

/**
 * Deliverable shortcode.
 */
class DeliverableShortcodeController {

	/**
	 * Deliverable shortcode.
	 */
	public function deliverable($params) {
		$deliverable=Deliverable::findOneBy("slug",$params["slug"]);
		if (!$deliverable)
			throw new Exception("Deliverable not found");

		if (isset($_REQUEST["deliverable"]) || isset($_FILES["deliverable"])) {
			$deliverable->processSubmission();

			$m=
				'<div class="deliverable notification">'.
				'Your work has been submitted! Please have patience while it is being reviewed.'.
				'</div>';

			echo $m;
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