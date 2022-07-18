<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\MultiPurge\Services;

use Config;
use MediaWiki\Http\HttpRequestFactory;
use MWHttpRequest;

class Cloudflare implements PurgeServiceInterface {
	private $extensionConfig;
	private $requestFactory;

	public function __construct( Config $extensionConfig, HttpRequestFactory $requestFactory ) {
		$this->extensionConfig = $extensionConfig;
		$this->requestFactory = $requestFactory;
	}

	public function setup(): void {
		wfDebugLog( 'MultiPurge', 'Setup Cloudflare' );
	}

	/**
	 * Returns as array of purge requests
	 * Chunks the request of count($urls) > 30
	 *
	 * @param $urls
	 * @return MWHttpRequest[]
	 */
	public function getPurgeRequest( $urls ): array {
		if ( !is_array( $urls ) ) {
			$urls = [ $urls ];
		}

		// Protocolize urls
		$urls = array_map( static function ( string $url ) {
			if ( substr( $url, 0, 2 ) === '//' ) {
				$url = sprintf( 'https:%s', $url );
			}

			return $url;
		}, $urls );

		$requests = [];

		foreach ( array_chunk( $urls, 30 ) as $chunk ) {
			$requests = array_merge( $requests, $this->makeRequest( $chunk ) );
		}

		return $requests;
	}

	/**
	 * Create the actual request for the given urls
	 *
	 * @param array $urls
	 * @return MWHttpRequest[]
	 */
	private function makeRequest( array $urls ): array {
		$zoneId = $this->extensionConfig->get( 'MultiPurgeCloudFlareZoneId' );
		$apiToken = $this->extensionConfig->get( 'MultiPurgeCloudFlareApiToken' );

		$request = $this->requestFactory->create(
			"https://api.cloudflare.com/client/v4/zones/$zoneId/purge_cache",
			[
				'method' => 'POST',
				'userAgent' => 'MediaWiki/ext-multipurge',
				'postData' => json_encode( [ 'files' => $urls ] ),
			]
		);

		$request->setHeader( 'Authorization', sprintf( 'Bearer %s', $apiToken ) );
		$request->setHeader( 'Content-Type', 'application/json' );

		wfDebugLog( 'MultiPurge', sprintf( 'Added %d files to Cloudflare request: %s', count( $urls ), json_encode( $urls ) ) );

		return [ $request ];
	}
}
