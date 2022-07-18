<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\MultiPurge\Services;

use Config;
use MediaWiki\Http\HttpRequestFactory;
use RuntimeException;

class Varnish implements PurgeServiceInterface {
	private $extensionConfig;
	private $requestFactory;

	public function __construct( Config $extensionConfig, HttpRequestFactory $requestFactory ) {
		$this->extensionConfig = $extensionConfig;
		$this->requestFactory = $requestFactory;
	}

	public function setup(): void {
		wfDebugLog( 'MultiPurge', 'Setup Varnish' );
	}

	public function getPurgeRequest( $urls ): array {
		$varnishServers = $this->extensionConfig->get( 'MultiPurgeVarnishServers' );

		if ( !is_array( $urls ) ) {
			$urls = [ $urls ];
		}

		$requests = [];

		foreach ( $urls as $url ) {
			$parsedUrl = parse_url( $url );

			foreach ( $varnishServers as $varnishServer ) {
				if ( filter_var( $varnishServer, FILTER_VALIDATE_IP ) ) {
					$parsedUrl['scheme'] = 'http';
					$parsedUrl['host'] = $varnishServer;
				} else {
					$parsedVarnish = parse_url( $varnishServer );
					$parsedUrl['scheme'] = $parsedVarnish['scheme'] ?? 'http';
					$parsedUrl['host'] = $parsedVarnish['host'] ?? $varnishServer;
				}

				try {
					$requests[] = $this->requestFactory->create(
						$this->buildUrl( $parsedUrl ),
						[
							'method' => 'PURGE',
							'userAgent' => 'MediaWiki/ext-multipurge'
						]
					);
				} catch ( RuntimeException $e ) {
					wfLogWarning( sprintf( '[MultiPurge] %s', $e->getMessage() ) );
					continue;
				}
			}
		}

		wfDebugLog( 'MultiPurge', sprintf( 'Created %d Varnish Purge Requests', count( $requests ) ) );

		return $requests;
	}

	/**
	 * Replaces the wikis host with the configured varnish host
	 *
	 * @param array $components
	 * @return string
	 */
	private function buildUrl( array $components ): string {
		$url = $components['scheme'] . '://';

		if ( isset( $components['username'], $components['password'] ) ) {
			$url .= $components['username'] . ':' . $components['password'] . '@';
		}

		$url .= $components['host'];

		if ( isset( $components['port'] ) &&
			( ( $components['scheme'] === 'http' && $components['port'] !== 80 ) ||
				( $components['scheme'] === 'https' && $components['port'] !== 443 ) )
		) {
			$url .= ':' . $components['port'];
		}

		if ( isset( $components['path'] ) ) {
			$url .= $components['path'];
		}

		if ( isset( $components['query'] ) ) {
			$url .= '?' . $components['query'];
		}

		if ( isset( $components['fragment'] ) ) {
			$url .= '#' . $components['fragment'];
		}

		return $url;
	}
}
