<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['file_list'][] = 'CldrCurrencies.php';
$cfg['file_list'][] = 'rebuild.php';

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		'CldrCurrency/',
		'CldrNames/',
		'CldrSupplemental/',
		'LocalNames/',
	]
);

return $cfg;
