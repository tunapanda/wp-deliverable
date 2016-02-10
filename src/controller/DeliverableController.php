<?php

namespace wpdeliverable;

require_once __DIR__."/../model/Deliverable.php";
require_once __DIR__."/../utils/WpCrud.php";

class DeliverableController extends WpCrud {

	public function __construct() {
		parent::__construct("Deliverables");

		$this->addField("title")->label("Title");

		$this->setListFields(array("title"));
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