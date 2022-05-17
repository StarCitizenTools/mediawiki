<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

// Many addition properties on RCCacheEntry
$cfg['suppress_issue_types'][] = 'PhanUndeclaredProperty';

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		'../../extensions/cldr',
	]
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		'../../extensions/cldr',
	]
);

return $cfg;
