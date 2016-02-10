<?php

namespace wpdeliverable;

class WpCrudFieldSpec {

	public $field;
	public $label;
	public $type;
	public $options;
	public $description;

	public function __construct($field) {
		$this->field=$field;
		$this->label=$field;
		$this->type="text";
		$this->description=NULL;
	}

	public function description($description) {
		$this->description=$description;

		return $this;
	}

	public function label($label) {
		$this->label=$label;

		return $this;
	}

	public function type($type) {
		$this->type=$type;

		return $this;
	}

	public function options($options) {
		$this->options=$options;
		$this->type="select";

		return $this;
	}
}