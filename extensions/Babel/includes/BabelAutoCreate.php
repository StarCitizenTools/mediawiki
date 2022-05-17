<?php
/**
 * Code for automatic creation of categories.
 *
 * @file
 * @author Robert Leverington
 * @author Robin Pepermans
 * @author Niklas Laxström
 * @author Brian Wolff
 * @author Purodha Blissenbach
 * @author Sam Reed
 * @author Siebrand Mazeland
 * @license GPL-2.0-or-later
 */

use MediaWiki\MediaWikiServices;

/**
 * Class for automatic creation of Babel category pages.
 */
class BabelAutoCreate {
	/**
	 * @var User|false
	 */
	protected static $user = false;

	public static function onUserGetReservedNames( &$names ) {
		$names[] = 'msg:babel-autocreate-user';

		return true;
	}

	/**
	 * Create category.
	 *
	 * @param string $category Name of category to create.
	 * @param string $code Code of language that the category is for.
	 * @param string|null $level Level that the category is for.
	 */
	public static function create( $category, $code, $level = null ) {
		$category = strip_tags( $category );
		$title = Title::makeTitleSafe( NS_CATEGORY, $category );
		if ( $title === null || $title->exists() ) {
			return;
		}
		DeferredUpdates::addCallableUpdate( function () use ( $code, $level, $title ) {
			global $wgLanguageCode;
			$language = BabelLanguageCodes::getName( $code, $wgLanguageCode );
			$params = [ $language, $code ];
			if ( $level === null ) {
				$text = wfMessage( 'babel-autocreate-text-main', $params )->inContentLanguage()->text();
			} else {
				array_unshift( $params, $level );
				$text = wfMessage( 'babel-autocreate-text-levels', $params )->inContentLanguage()->text();
			}

			$user = self::user();
			# Do not add a message if the username is invalid or if the account that adds it, is blocked
			if ( !$user || $user->getBlock() ) {
				return;
			}

			if ( !MediaWikiServices::getInstance()->getPermissionManager()
				->quickUserCan( 'create', $user, $title ) ) {
				return; # The Babel AutoCreate account is not allowed to create the page
			}

			$url = wfMessage( 'babel-url' )->inContentLanguage()->plain();
			$article = new WikiPage( $title );

			$content = ContentHandler::makeContent( $text, $title );
			$editSummary = wfMessage( 'babel-autocreate-reason', $url )->inContentLanguage()->text();
			if ( method_exists( $article, 'doUserEditContent' ) ) {
				// MW 1.36+
				$article->doUserEditContent(
					$content,
					$user,
					$editSummary,
					EDIT_FORCE_BOT
				);
			} else {
				// doUserEditContent not available yet, only introduced in 1.36
				$article->doEditContent(
					$content,
					$editSummary,
					EDIT_FORCE_BOT,
					false,
					$user
				);
			}
		} );
	}

	/**
	 * Get user object.
	 *
	 * @return User User object for autocreate user.
	 */
	public static function user() {
		if ( !self::$user ) {
			$userName = wfMessage( 'babel-autocreate-user' )->inContentLanguage()->plain();
			self::$user = User::newSystemUser( $userName, [ 'steal' => true ] );
		}

		return self::$user;
	}
}
