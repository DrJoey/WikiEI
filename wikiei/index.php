<?php

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	
	new UrlControllerMapper('WikiEIImportController', '`^/import$`'),
	
	new UrlControllerMapper('WikiEIController'),
	
	new UrlControllerMapper('AdminWikiEIConfigController', '`^/admin$`')
);

DispatchManager::dispatch($url_controller_mappers);

