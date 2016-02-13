<?php

namespace wpdeliverable;

require_once __DIR__."/src/utils/WpUtil.php";
require_once WpUtil::getWpLoadPath();
require_once __DIR__."/src/controller/DeliverableShortcodeController.php";
require_once __DIR__."/src/model/Deliverable.php";
require_once __DIR__."/src/utils/Template.php";

use \Exception;

$slug=str_replace($_SERVER["SCRIPT_NAME"],"",$_SERVER["REQUEST_URI"]);
$slug=str_replace("/","",$slug);

if (!$slug)
	exit("Expected slug");

$deliverable=Deliverable::findOneBy("slug",$slug);
if (!$deliverable) 
	exit("Not found");

$controller=new DeliverableShortcodeController();
$deliverableContent=$controller->deliverable(array(
	"slug"=>"submit-your-p5js-game"
));

$template=new Template(__DIR__."/src/template/deliverablestandalone.php");
$template->set("title",$deliverable->title);
$template->set("deliverable",$deliverableContent);
$template->set("base",plugins_url()."/wp-deliverable/");
$template->set("jquerylink",get_site_url()."/wp-includes/js/jquery/jquery.js");
$template->show();
