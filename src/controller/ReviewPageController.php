<?php

namespace wpdeliverable;

use \Exception;

/**
 * Controller for the review page.
 */
class ReviewPageController {

	/**
	 * List pending submissions for current user.
	 */
	function process() {
		$wordsNeeded=20;

		if (isset($_REQUEST["submissionId"])) {
			if (str_word_count($_REQUEST["comment"])<$wordsNeeded) {
				echo "<div class='error'><p>Please write a comment with at least $wordsNeeded words.</p></div>";
			}

			else {
				$submission=DeliverableSubmission::findOne($_REQUEST["submissionId"]);

				if (isset($_REQUEST["approve"]))
					$submission->setReviewed("approved",$_REQUEST["comment"]);

				else if (isset($_REQUEST["reject"]))
					$submission->setReviewed("rejected",$_REQUEST["comment"]);

				else
					throw new Exception("Rejected or approved?");

				$submission->save();

				echo "<div class='updated'><p>The submission was marked as {$submission->state}.</p></div>";
			}
		}

		$groups=WpGroup::getGroupsForCurrentUser();
		$submissions=array();

		foreach ($groups as $group) {
			$deliverables=Deliverable::findAllBy("reviewGroup",$group->getSlug());
			foreach ($deliverables as $deliverable) {
				foreach ($deliverable->getPendingSubmissions() as $submission) {
					$submissions[]=$submission;
				}
			}
		}

		$template=new Template(__DIR__."/../template/review.php");
		$template->set("groups",$groups);
		$template->set("submissions",$submissions);
		$template->set("wordsNeeded",$wordsNeeded);

		if (isset($_REQUEST["submissionId"])) {
			$template->set("submissionId",$_REQUEST["submissionId"]);
			$template->set("comment",$_REQUEST["comment"]);
		}

		$template->show();
	}
}