<?php

class CCFilters {

	/**
	 * Hook: ChangesListSpecialPageQuery
	 * @param string $name
	 * @param array &$tables
	 * @param array &$fields
	 * @param array &$conds
	 * @param array &$query_options
	 * @param array &$join_conds
	 * @param FormOptions $opts
	 */
	public static function user(
		$name,
		&$tables,
		&$fields,
		&$conds,
		&$query_options,
		&$join_conds,
		FormOptions $opts
	) {
		global $wgRequest, $wgCCUserFilter;

		if ( !$wgCCUserFilter ) {
			return;
		}

		$opts->add( 'users', '' );
		$users = $wgRequest->getVal( 'users' );
		if ( $users === null ) {
			return;
		}

		$rawUserArr = explode( '|', $users );
		$userArr = UserArray::newFromNames( $rawUserArr );
		if ( $userArr->count() ) {
			$conds['actor_name'] = iterator_to_array( $userArr );
		} else {
			// Unknown user, make the query return no result.
			$conds['actor_name'] = $rawUserArr;
		}

		$opts->setValue( 'users', $users );
	}

	/**
	 * Hook: SpecialRecentChangesPanel
	 * @param array &$items
	 * @param FormOptions $opts
	 */
	public static function userForm( &$items, FormOptions $opts ) {
		global $wgRequest, $wgCCUserFilter;

		if ( !$wgCCUserFilter ) {
			return;
		}

		$opts->consumeValue( 'users' );

		$default = $wgRequest->getVal( 'users', '' );
		$items['users'] = Xml::inputLabelSep(
			wfMessage( 'cleanchanges-users' )->text(),
			'users',
			'mw-users',
			40,
			$default
		);
	}

	/**
	 * Hook: ChangesListSpecialPageQuery
	 * @param string $name
	 * @param array &$tables
	 * @param array &$fields
	 * @param array &$conds
	 * @param array &$query_options
	 * @param array &$join_conds
	 * @param FormOptions $opts
	 */
	public static function trailer(
		$name,
		&$tables,
		&$fields,
		&$conds,
		&$query_options,
		&$join_conds,
		FormOptions $opts
	) {
		global $wgRequest, $wgCCTrailerFilter;

		if ( !$wgCCTrailerFilter ) {
			return;
		}

		$opts->add( 'trailer', '' );
		$trailer = $wgRequest->getVal( 'trailer' );
		if ( $trailer === null ) {
			return;
		}

		$dbr = wfGetDB( DB_REPLICA );
		$conds[] = 'rc_title ' . $dbr->buildLike( $dbr->anyString(), $trailer );
		$opts->setValue( 'trailer', $trailer );
	}

	/**
	 * Hook: SpecialRecentChangesPanel
	 * @param array &$items
	 * @param FormOptions $opts
	 */
	public static function trailerForm( &$items, FormOptions $opts ) {
		/**
		 * @var Language $wgLang
		 */
		global $wgLang, $wgRequest, $wgCCTrailerFilter;

		if ( !$wgCCTrailerFilter ) {
			return;
		}

		$opts->consumeValue( 'trailer' );

		$default = $wgRequest->getVal( 'trailer', '' );

		if ( is_callable( [ 'LanguageNames', 'getNames' ] ) ) {
			$languages = LanguageNames::getNames( $wgLang->getCode(),
				LanguageNames::FALLBACK_NORMAL,
				LanguageNames::LIST_MW
			);
		} else {
			$languages = Language::fetchLanguageNames( null, 'mw' );
		}
		ksort( $languages );
		$options = Xml::option( wfMessage( 'cleanchanges-language-na' )->text(), '', $default === '' );
		foreach ( $languages as $code => $name ) {
			$selected = ( "/$code" === $default );
			$options .= Xml::option( "$code - $name", "/$code", $selected ) . "\n";
		}
		$str =
		Xml::openElement( 'select', [
			'name' => 'trailer',
			'class' => 'mw-language-selector',
			'id' => 'sp-rc-language',
		] ) .
		$options .
		Xml::closeElement( 'select' );

		$items['tailer'] = [ wfMessage( 'cleanchanges-language' )->escaped(), $str ];
	}

	/**
	 * Hook: FetchChangesList
	 * @param User $user
	 * @param Skin $skin
	 */
	public static function hook( User $user, Skin $skin ): void {
		global $wgCCTrailerFilter;

		if ( $wgCCTrailerFilter && defined( 'ULS_VERSION' ) ) {
			$skin->getOutput()->addModules( 'ext.cleanchanges.uls' );
		}
	}
}
