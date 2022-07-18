<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\MultiPurge\Hooks;

use MediaWiki\Extension\MultiPurge\PurgeEventRelayer;

class MainHooks {

	public static function setup(): void {
		global $wgEventRelayerConfig;

		$wgEventRelayerConfig['cdn-url-purges'] = [
			'class' => PurgeEventRelayer::class,
		];
	}
}
