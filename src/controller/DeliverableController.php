<?php

namespace wpdeliverable;

require_once __DIR__."/../model/Deliverable.php";
require_once __DIR__."/../utils/WpCrud.php";
require_once __DIR__."/../utils/WpGroup.php";

class DeliverableController extends WpCrud {

	public function __construct() {
		parent::__construct("Deliverables");

		$groupOptions=array();
		foreach (WpGroup::getAllGroups() as $group)
			$groupOptions[$group->getSlug()]=$group->getLabel();

		$this->addField("title")->label("Title")->description("Choose a title for the deliverable.");
		$this->addField("description")->label("Description")->type("textarea")
			->description("Enter a description for the deliverable that is to be submitted.");
		$this->addField("reviewGroup")->label("Review Group")
			->description("Which group of users is responsible for reviewing work submitted?")
			->options($groupOptions);

		$this->addField("type")->label("Type")
			->description("How should the work be submitted?")
			->options(array(
				"url"=>"Url",
				"zip"=>"Uploaded Zip File",
				"pdf"=>"Uploaded Pdf File",
			));

		$this->setListFields(array("slug","title","reviewGroup","type"));
	}

	protected function createItem() {
		return new Deliverable();
	}

	protected function getFieldValue($item, $field) {
		return $item->$field;
	}

	protected function setFieldValue($item, $field, $value) {
		$item->$field=$value;
	}

	protected function saveItem($item) {
		$item->slug=Deliverable::getUniqueSlug($item->title,$item->id);
		$item->save();
	}

	protected function validateItem($item) {
		if (!$item->title)
			return "You need to specify a title for the deliverable.";
	}

	protected function deleteItem($item) {
		$item->delete();
	}

	protected function getItem($id) {
		return Deliverable::findOne($id);
	}

	protected function getAllItems() {
		return Deliverable::findAll();
	}
}