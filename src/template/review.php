<div class="wrap">
	<h1>Submitted deliverables pending review</h1>

	<p>
		Showing submissions for the groups you belong to:<br/>
		<?php foreach ($groups as $group) { ?>
			<span class="deliverable group-tag"><?php echo $group->getLabel(); ?></span>
		<?php } ?>
	</p>

	<?php foreach ($submissions as $submission) { ?>
		<div class="deliverable comment">
			<?php echo $submission->getUserAvatar(); ?>
			<div class="header">
				<b><?php echo $submission->getUser()->display_name; ?></b>
				submitted
				<?php echo $submission->getSubmittedHumanTimeDiff(); ?> ago
			</div>
			<div class="body">
				<b>Deliverable: </b>
				<?php echo $submission->getDeliverable()->title; ?><br/>
				<b>Submission: </b>
				<a href="<?php echo $submission->getUrl(); ?>" target="_blank">
					<?php echo $submission->getLinkLabel(); ?>
				</a>
				<br/><br/>
				<b>Your comment:</b><br/>
				<form method="POST" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<input type="hidden" name="submissionId" value="<?php echo $submission->id; ?>"/>
					<textarea class="deliverable review-comment" name="comment"><?php if ($submission->id==$submissionId) echo $comment; ?></textarea>
					<p class="description">
						Please write a comment with <?php echo $wordsNeeded; ?> words or more.
					</p><br/>
					<input type="submit" value="Approve" name="approve" class="button button-primary"/>
					<input type="submit" value="Reject" name="reject" class="button"/>
				</form>
			</div>
		</div>
	<?php } ?>
</div>
