<div class="deliverable container">
	<h2><?php echo htmlspecialchars($deliverable->title); ?></h2>
	<p><?php echo nl2br(htmlspecialchars($deliverable->description)); ?></p>

	<?php if ($submission) { ?>
		<?php if ($submission->getState()=="approved") { ?>
			<img src="<?php echo plugins_url(); ?>/wp-deliverable/img/approved.png" class="state-icon">
		<?php } else if ($submission->getState()=="pending") { ?>
			<img src="<?php echo plugins_url(); ?>/wp-deliverable/img/pending.png" class="state-icon">
		<?php } else if ($submission->getState()=="rejected") { ?>
			<img src="<?php echo plugins_url(); ?>/wp-deliverable/img/rejected.png" class="state-icon">
		<?php } ?>
	<?php } ?>

	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>" id="deliverable-form" enctype="multipart/form-data">
		<b><?php echo htmlspecialchars($submitLabel); ?></b>
		<?php if ($submission && $submission->getState()!="rejected") { ?>
			<a href="<?php echo $submission->getUrl(); ?>"><?php echo $submission->getLinkLabel(); ?></a>
		<?php } else { ?>
			<input
				id="deliverable-input"
				type="<?php echo $submitType; ?>"
				name="deliverable"
				<?php if (isset($accept)) { ?>
					accept="<?php echo $accept; ?>"
				<?php } ?>
			/>
			<?php if (isset($showSubmitButton)) { ?>
				<input type="submit"/>
			<?php } ?>
		<?php } ?>
	</form>
</div>

<?php if ($submission) { ?>
	<div class="deliverable comment">
		<?php echo $submission->getUserAvatar(); ?>
		<div class="header">
			<b><?php echo $submission->getUser()->display_name; ?></b>
			submitted
			<?php echo $submission->getSubmittedHumanTimeDiff(); ?> ago
		</div>
		<div class="body">
			<a href="<?php echo $submission->getUrl(); ?>"><?php echo $submission->getLinkLabel(); ?></a>
		</div>
	</div>

	<?php if ($submission->isReviewed()) { ?>
		<div class="deliverable comment">
			<?php echo $submission->getReviewUserAvatar(); ?>
			<div class="header">
				<b><?php echo $submission->getReviewUser()->display_name; ?></b>
				<?php echo $submission->getState(); ?>
				<?php echo $submission->getReviewedHumanTimeDiff(); ?> ago
			</div>
			<div class="body">
				<?php echo nl2br(htmlspecialchars($submission->comment)); ?>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<script>
	jQuery(function($) {
		$("#deliverable-input").change(function() {
			$("#deliverable-form").submit();
		});
	});
</script>