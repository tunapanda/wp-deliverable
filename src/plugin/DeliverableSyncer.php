<?php

namespace wpdeliverable;

require_once __DIR__."/../model/Deliverable.php";

class DeliverableSyncer {

	/**
	 * Get resource slugs.
	 */
	public function listResourceSlugs() {
		$deliverables=Deliverable::findAll();
		$slugs=array();

		foreach ($deliverables as $deliverable)
			$slugs[]=$deliverable->slug;

		return $slugs;
	}

	/**
	 * Get resource.
	 */
	public function getResource($slug) {
		$deliverable=Deliverable::findOneBy("slug",$slug);
		if (!$deliverable)
			return NULL;

		return array(
			"slug"=>$deliverable->slug,
			"title"=>$deliverable->title,
			"description"=>$deliverable->description,
			"reviewGroup"=>$deliverable->reviewGroup,
			"type"=>$deliverable->type,
		);
	}

	/**
	 * Update/create resource.
	 */
	public function updateResource($slug, $data) {
		$deliverable=Deliverable::findOneBy("slug",$slug);
		if (!$deliverable)
			$deliverable=new Deliverable();

		$deliverable->slug=$slug;
		$deliverable->title=$data["title"];
		$deliverable->description=$data["description"];
		$deliverable->reviewGroup=$data["reviewGroup"];
		$deliverable->type=$data["type"];

		$deliverable->save();
	}

	/**
	 * Delete resource.
	 */
	public function deleteResource($slug) {
		$deliverable=Deliverable::findOneBy("slug",$slug);
		if ($deliverable)
			$deliverable->delete();
	}
}