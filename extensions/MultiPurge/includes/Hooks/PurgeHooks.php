<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\MultiPurge\Hooks;

use Article;
use File;
use MediaWiki\Extension\MultiPurge\MultiPurgeJob;
use MediaWiki\Hook\LocalFilePurgeThumbnailsHook;
use MediaWiki\MediaWikiServices;
use MediaWiki\Page\Hook\ArticlePurgeHook;
use ReflectionException;
use ReflectionObject;
use Title;
use WikiFilePage;
use WikiPage;

class PurgeHooks implements	LocalFilePurgeThumbnailsHook, ArticlePurgeHook {

	/**
	 * Retrieve a list of thumbnail URLs that needs to be purged
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/LocalFilePurgeThumbnails
	 *
	 * @param File $file The File of which the thumbnails are being purged
	 * @param string $archiveName Name of an old file version or false if it's the current one
	 * @param string[] $urls Array of URLs to purge from the caches, to be manipulated
	 */
	public function onLocalFilePurgeThumbnails( $file, $archiveName, $urls ): void {
		$this->runPurge( $urls );
	}

	/**
	 * @param WikiPage $wikiPage
	 * @return void
	 */
	public function onArticlePurge( $wikiPage ): void {
		if ( $this->isFile( $wikiPage ) ) {
			$files = $this->getThumbnails( $wikiPage->getFile() );
			// Remove mwbackend link
			array_shift( $files );
			$urls = $this->linkThumbnails( $files, $wikiPage->getFile() );
		} elseif ( $wikiPage->getTitle() === null ) {
			return;
		} else {
			$urls = [ $wikiPage->getSourceURL() ];
		}

		$this->runPurge( $urls );
	}

	/**
	 * Returns an array of thumbnail urls for this wiki file
	 * This is an evil hack if you are on <= 1.35.2 as we change the visibility of the method to public
	 *
	 * @param File|WikiFilePage $page
	 * @return array
	 */
	private function getThumbnails( $page ): array {
		if ( $page instanceof File ) {
			$file = $page;
		} else {
			$file = $page->getFile();
		}

		if ( $file === false || !method_exists( $file, 'getThumbnails' ) ) {
			return [];
		}

		$refObject = new ReflectionObject( $file );
		try {
			$refMethod = $refObject->getMethod( 'getThumbnails' );
		} catch ( ReflectionException $e ) {
			return [];
		}

		if ( $refMethod->isPublic() ) {
			return $file->getThumbnails();
		}

		try {
			$refMethod->setAccessible( true );
			$thumbnails = $refMethod->invoke( $file );
		} catch ( ReflectionException $e ) {
			$thumbnails = [];
		}

		return $thumbnails;
	}

	/**
	 * Links relative urls to absolute urls based on wgServer or wgUploadPath
	 *
	 * @param array $files
	 * @param File $baseFile
	 * @return array
	 */
	private function linkThumbnails( array $files, File $baseFile ): array {
		$url = MediaWikiServices::getInstance()->getMainConfig()->get( 'Server' );
		$uploadPath = MediaWikiServices::getInstance()->getMainConfig()->get( 'UploadPath' );
		$parsed = parse_url( $uploadPath );

		if ( $parsed !== false && isset( $parsed['host'] ) ) {
			$url = $uploadPath;
		}

		// Purge the CDN
		$urls = [];
		foreach ( $files as $thumb ) {
			$thumbUrl = ltrim( $baseFile->getThumbUrl( $thumb ), '/' );
			if ( isset( parse_url( $thumbUrl )['host'] ) ) {
				$urls[] = $thumbUrl;
			} else {
				$urls[] = sprintf(
					'%s/%s',
					$url,
					ltrim( $baseFile->getThumbUrl( $thumb ), '/' )
				);
			}

		}

		if ( isset( parse_url( $baseFile->getUrl() )['host'] ) ) {
			$urls[] = $baseFile->getUrl();
		} else {
			$urls[] = sprintf(
				'%s/%s',
				$url,
				ltrim( $baseFile->getUrl(), '/' )
			);
		}

		return $urls;
	}

	/**
	 * Purges an array of urls
	 *
	 * @param array $urls
	 */
	private function runPurge( array $urls ): void {
		$urls = array_unique( $urls );

		if ( empty( $urls ) ) {
			return;
		}

		wfDebugLog( 'MultiPurge', 'Running Job from PurgeHooks' );

		$job = new MultiPurgeJob( [
			'urls' => $urls,
		] );

		$status = $job->run();

		wfDebugLog(
			'MultiPurge',
			sprintf(
				'Job Status: %s',
				( $status === true ? 'success' : 'error' )
			)
		);
	}

	/**
	 * @param Article|WikiPage|Title $page
	 * @return bool
	 */
	private function isFile( $page ): bool {
		if ( $page instanceof Title ) {
			return $page->getNamespace() === NS_FILE;
		}

		return $page instanceof WikiFilePage && $page->getTitle() !== null && $page->getTitle()->getNamespace() === NS_FILE;
	}
}
