<?php

require 'Application.php';

$app = new Tamajoo\Application;

$app->addDatabase([
	'db' => 'knipex_dev'
]);

$app->addDatabase([
	'db' => 'knipex_live'
]);

$app->compare();