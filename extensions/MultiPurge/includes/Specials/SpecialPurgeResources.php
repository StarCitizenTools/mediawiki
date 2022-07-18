<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\MultiPurge\Specials;

use ConfigException;
use HTMLForm;
use MediaWiki\Extension\MultiPurge\MultiPurgeJob;
use MediaWiki\MediaWikiServices;
use OOUIHTMLForm;
use PermissionsError;
use SpecialPage;

class SpecialPurgeResources extends SpecialPage {

	public function __construct() {
		parent::__construct( 'PurgeResources', 'editinterface' );
	}

	/**
	 * Show the page to the user
	 *
	 * @throws PermissionsError
	 */
	public function execute( $sub ) {
		$this->checkPermissions();
		$out = $this->getOutput();

		$out->setPageTitle( $this->msg( 'multipurge-form-title' ) );

		$formDescriptor = [
			'target' => [
				'section' => 'title',
				'class' => 'HTMLTextField',
				'required' => true,
				'default' => !empty( $sub ) ? $sub : null,
			],
		];

		$showPurge = false;

		if ( !empty( $sub ) ) {
			$content = $this->loadContent( $sub );

			if ( $content !== false ) {
				$formDescriptor = array_merge( $formDescriptor, $this->makeSelects( $this->parseLoads( $content ) ) );
				$showPurge = true;
			} else {
				$out->prependHTML( wfMessage( 'multipurge-special-invalid-title' )->plain() );
			}
		}

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext(), 'purge-resources-form' );

		if ( $showPurge ) {
			$htmlForm->setSubmitText( wfMessage( 'multipurge-special-purge-submit' )->plain() );
		} else {
			$htmlForm->setSubmitText( wfMessage( 'multipurge-special-load-submit' )->plain() );
		}

		$htmlForm->setSubmitCallback( [ __CLASS__, 'trySubmit' ] );

		$htmlForm->show();
	}

	/**
	 * Load the actual raw page content for a given title
	 * Returns false on failure
	 *
	 * @param string $sub
	 * @return false|string
	 */
	private function loadContent( string $sub ) {
		if ( empty( $sub ) ) {
			return false;
		}

		$title = MediaWikiServices::getInstance()->getTitleFactory()->newFromText( $sub );

		if ( $title === null || !$title->exists() ) {
			return false;
		}

		$content = MediaWikiServices::getInstance()->getHttpRequestFactory()->get(
			$title->getFullURL( '', false, PROTO_HTTPS )
		);

		return $content ?? false;
	}

	/**
	 * @param array $formData
	 * @param OOUIHTMLForm $form
	 * @return string|void
	 */
	public static function trySubmit( $formData, $form ) {
		if ( !isset( $formData['styles'] ) ) {
			$form->getOutput()->redirect(
				MediaWikiServices::getInstance()->getTitleFactory()
					->makeTitle( NS_SPECIAL, sprintf( 'PurgeResources/%s', $formData['target'] ) )
					->getFullUrlForRedirect()
			);

			return;
		}

		$server = MediaWikiServices::getInstance()->getMainConfig()->get( 'Server' );

		$mapper = static function ( string $url ) use ( $server ) {
			return html_entity_decode( sprintf( '%s/%s', $server, ltrim( $url, '/' ) ) );
		};

		$urls = array_filter( array_unique( array_merge(
			array_map( $mapper, $formData['styles'] ?? [] ),
			array_map( $mapper, $formData['scripts'] ?? [] ),
			array_map( $mapper, $formData['thumbs'] ?? [] ),
			array_map( $mapper, $formData['statics'] ?? [] ),
		) ) );

		wfDebugLog( 'MultiPurge', sprintf( 'Purging urls from Special Page: %s', json_encode( $urls ) ) );

		if ( !empty( $urls ) ) {
			$job = new MultiPurgeJob( [
				'urls' => $urls,
			] );

			if ( $job->run() ) {
				# TODO How to return success?
				return 'multipurge-special-purge-success';
			}

			return 'multipurge-special-purge-error';
		}

		return 'multipurge-special-no-urls';
	}

	/**
	 * Parse the page content and extract load.php calls and images
	 *
	 * @param string $content
	 * @return array
	 */
	private function parseLoads( string $content ): array {
		// Styles
		$styleCount = preg_match_all( '/href="(\/load.php\?.*)"\/>/U', $content, $styles );

		// Scripts
		$scriptCount = preg_match_all( '/src="(\/load.php\?.*)"><\/script>/U', $content, $scripts );

		// Thumbs
		$imageCount = preg_match_all( '/<img alt=".+" src="(.*)" decoding/U', $content, $images );

		if ( $styleCount !== false && $styleCount > 0 ) {
			array_shift( $styles );
			$styles = array_filter( $styles[0] ?? [] );
		} else {
			$styles = [];
		}

		if ( $scriptCount !== false && $scriptCount > 0 ) {
			array_shift( $scripts );
			$scripts = array_filter( $scripts[0] ?? [] );
		} else {
			$scripts = [];
		}

		if ( $imageCount !== false && $imageCount > 0 ) {
			array_shift( $images );
			$images = array_filter( $images[0] ?? [] );
		} else {
			$images = [];
		}

		$data = [
			'scripts' => $scripts,
			'styles' => $styles,
			'images' => $images,
		];

		wfDebugLog(
			'MultiPurge',
			sprintf(
				'Parsed - Scripts: %d; Styles %d; Thumbs %d',
				count( $data['scripts'] ),
				count( $data['styles'] ),
				count( $data['images'] )
			)
		);

		return $data;
	}

	/**
	 * Map the loads to form select
	 *
	 * @param array $loads
	 * @return array[]
	 */
	private function makeSelects( array $loads ): array {
		try {
			$statics = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'MultiPurge' )->get( 'MultiPurgeStaticPurges' );
		} catch ( ConfigException $e ) {
			$statics = [];
		}

		$selects = [];

		foreach ( [ 'styles', 'scripts', 'thumbs' ] as $group ) {
			if ( empty( $loads[$group] ) ) {
				continue;
			}

			$selects[$group] = [
				'section' => 'loads',
				'class' => 'HTMLMultiSelectField',
				'label-message' => sprintf( 'multipurge-%s-label', $group ),
				'options' => array_combine( $loads[$group], $loads[$group] ),
			];
		}

		if ( !empty( $selects ) ) {
			// Use path as key
			if ( !is_string( array_keys( $statics )[0] ) ) {
				$selects = array_combine( $statics, $statics );
			}

			$selects['statics'] = [
				'section' => 'loads',
				'class' => 'HTMLMultiSelectField',
				'label-message' => 'multipurge-static-label',
				'options' => $statics,
			];
		}

		return $selects;
	}

	protected function getGroupName() {
		return 'other';
	}
}
