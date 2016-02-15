<?php

namespace wpdeliverable;

require_once __DIR__."/../src/utils/WpUtil.php";
require_once WpUtil::getWpLoadPath();
require_once __DIR__."/../src/utils/WpGroup.php";

/*$groups=WpGroup::getAllGroups();

foreach ($groups as $group)
	echo $group->getSlug()." - ".$group->getLabel()."<br>";

$users=$groups[0]->getUsers();

print_r($users);*/

print_r(WpGroup::getGroupBySlug("groups:programmers-group")->getUsers());

//print_r(WpGroup::getGroupsForCurrentUser());
