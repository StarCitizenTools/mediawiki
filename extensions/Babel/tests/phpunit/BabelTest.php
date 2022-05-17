<?php

namespace Babel\Tests;

use Babel;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use Parser;
use ParserOptions;
use ParserOutput;
use Title;
use TitleValue;
use User;

/**
 * @covers Babel
 *
 * @group Babel
 * @group Database
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
class BabelTest extends MediaWikiIntegrationTestCase {

	public function addDBDataOnce() {
		// The '#babel' parser function normally auto-creates category pages via
		// a DeferredUpdate. In PHPUnit context, because of wgCommandLineMode
		// being true, DeferredUpdates are not actually "deferred". They run
		// immediately. This is a problem because this would mean when we parse
		// wikitext, BabelAutoCreate would parse and save another wiki page,
		// whilst still inside a parser function. This is not allowed in MediaWiki
		// and Parser::parse protects against this with an exception.
		//
		// FIXME: Make BabelAutoCreate less dependent on global state so we can simply
		// disable this feature while testing, we don't need these pages for the test.
		//
		// We cannot simply make DeferredUpdates "deferred" (by disabling wgCommandLineMode),
		// because that also means updates from core itself (such as the saving of category
		// links) would be deferred, which we do need to observe below.
		//
		// Work around this by faking entries in LinkCache so that Title::exists()
		// returns true in BabelAutoCreate and no auto-creation is attempted.
		$this->setMwGlobals( 'wgCapitalLinks', false );

		$linkCache = MediaWikiServices::getInstance()->getLinkCache();
		foreach ( [ 'en', 'en-N', 'en-1', 'es', 'es-2', 'de', 'de-N',
					  'simple', 'simple-1', 'zh-Hant', 'zh-Hant-3'
				  ] as $name ) {
			$page = new TitleValue( NS_CATEGORY, $name );
			// TODO: use LinkCacheTestTrait once the minimum MW version is 1.37
			$linkCache->addGoodLinkObjFromRow( $page, (object)[
				'page_id' => 1,
				'page_namespace' => $page->getNamespace(),
				'page_title' => $page->getDBkey(),
				'page_len' => -1,
				'page_is_redirect' => 0,
				'page_latest' => 0,
				'page_content_model' => null,
				'page_lang' => null,
				'page_restrictions' => null,
				'page_is_new' => 0,
				'page_touched' => '',
			] );
		}

		$user = User::newFromName( 'User-1' );
		$user->addToDatabase();
		$this->insertPage( 'User:User-1', '{{#babel:en-1|es-2|de|SIMPLE-1|zh-hant-3}}' );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->setContentLang( 'qqx' );
		$this->setMwGlobals( [
			// Individual tests may change these
			'wgBabelCentralDb' => false,
			'wgCapitalLinks' => false,
		] );
	}

	/**
	 * @param Title $title
	 * @return Parser
	 */
	private function getParser( Title $title ) {
		$options = ParserOptions::newFromAnon();
		$options->setIsPreview( true );
		$output = new ParserOutput();

		$parser = $this->createMock( Parser::class );
		$parser->method( 'getOptions' )->willReturn( $options );
		$parser->method( 'getTitle' )->willReturn( $title );
		$parser->method( 'getOutput' )->willReturn( $output );
		$parser->method( 'getDefaultSort' )->willReturn( '' );
		return $parser;
	}

	/**
	 * @param int $expectedCount
	 * @param string $haystack
	 */
	private function assertBabelBoxCount( $expectedCount, $haystack ) {
		$this->assertSame( $expectedCount, substr_count( $haystack, '<div class="mw-babel-box' ) );
	}

	/**
	 * @param Parser $parser
	 * @param string $cat
	 * @param string $sortKey
	 */
	private function assertHasCategory( Parser $parser, $cat, $sortKey ) {
		$cats = $parser->getOutput()->getCategories();
		$this->assertArrayHasKey( $cat, $cats );
		$this->assertSame( $sortKey, $cats[$cat] );
	}

	/**
	 * @param Parser $parser
	 * @param string $cat
	 */
	private function assertNotHasCategory( Parser $parser, $cat ) {
		$cats = $parser->getOutput()->getCategories();
		$this->assertArrayNotHasKey( $cat, $cats );
	}

	public function testRenderEmptyBox() {
		$title = Title::newFromText( 'User:User-1' );
		$parser = $this->getParser( $title );
		$wikiText = Babel::Render( $parser, '' );
		$this->assertSame(
			'{|class="mw-babel-wrapper"'
			. "\n"
			. '! class="mw-babel-header" | [[(babel-url)|(babel: User-1)]]'
			. "\n|-\n| \n|-\n"
			. '! class="mw-babel-footer" | [[(babel-footer-url)|(babel-footer: User-1)]]'
			. "\n|}",
			$wikiText
		);
	}

	/**
	 * Provides different page names, such as pages in the Category namespace.
	 */
	public static function providePageNames() {
		return [
			[ 'User:User-1' ],
			[ 'Category:X1' ],
		];
	}

	/**
	 * @dataProvider providePageNames
	 */
	public function testRenderDefaultLevel( $pageName ) {
		$parser = $this->getParser( Title::newFromText( $pageName ) );
		$wikiText = Babel::Render( $parser, 'en' );
		$this->assertBabelBoxCount( 1, $wikiText );
		$this->assertStringContainsString(
			'<div class="mw-babel-box mw-babel-box-N" dir="ltr">'
			. "\n"
			. '{|'
			. "\n"
			. '! dir="ltr" | [[(babel-portal: en)|en]]<span class="mw-babel-box-level-N">-N</span>'
			. "\n"
			. '| dir="ltr" lang="en" | This user has a [[:Category:en-N|native]] understanding of '
			. '[[:Category:en|English]].'
			. "\n|}\n"
			. '</div>',
			$wikiText
		);

		$this->assertHasCategory( $parser, 'en', 'N' );
		$this->assertHasCategory( $parser, 'en-N', '' );
	}

	/**
	 * @dataProvider providePageNames
	 */
	public function testRenderDefaultLevelNoCategory( $pageName ) {
		$this->setMwGlobals( [ 'wgBabelMainCategory' => false ] );

		$parser = $this->getParser( Title::newFromText( $pageName ) );
		$wikiText = Babel::Render( $parser, 'en' );
		$this->assertBabelBoxCount( 1, $wikiText );
		$this->assertStringContainsString(
			'<div class="mw-babel-box mw-babel-box-N" dir="ltr">'
			. "\n"
			. '{|'
			. "\n"
			. '! dir="ltr" | [[(babel-portal: en)|en]]<span class="mw-babel-box-level-N">-N</span>'
			. "\n"
			. '| dir="ltr" lang="en" | This user has a [[:Category:en-N|native]] understanding of '
			. "[[:$pageName|English]]."
			. "\n|}\n"
			. '</div>',
			$wikiText
		);

		$this->assertNotHasCategory( $parser, 'en' );
		$this->assertHasCategory( $parser, 'en-N', '' );
	}

	public function testRenderCustomLevel() {
		$parser = $this->getParser( Title::newFromText( 'User:User-1' ) );
		$wikiText = Babel::Render( $parser, 'EN-1', 'zh-Hant' );
		$this->assertBabelBoxCount( 2, $wikiText );
		$this->assertStringContainsString(
			'<div class="mw-babel-box mw-babel-box-1" dir="ltr">'
			. "\n"
			. '{|'
			. "\n"
			. '! dir="ltr" | [[(babel-portal: en)|en]]<span class="mw-babel-box-level-1">-1</span>'
			. "\n"
			. '| dir="ltr" lang="en" | This user has [[:Category:en-1|basic]] knowledge of '
			. '[[:Category:en|English]].'
			. "\n|}\n"
			. '</div>',
			$wikiText
		);

		$this->assertHasCategory( $parser, 'en', '1' );
		$this->assertHasCategory( $parser, 'en-1', '' );

		$this->assertStringContainsString(
			'<div class="mw-babel-box mw-babel-box-N" dir="ltr">'
			. "\n"
			. '{|'
			. "\n"
			. '! dir="ltr" | [[(babel-portal: zh-Hant)|zh-Hant]]'
			. '<span class="mw-babel-box-level-N">-N</span>'
			. "\n"
			. '| dir="ltr" lang="zh-Hant" | 這位使用者會[[:Category:zh-Hant-N|母語]]水準的 '
			. '[[:Category:zh-Hant|繁體中文]]。'
			. "\n|}\n"
			. '</div>',
			$wikiText
		);

		$this->assertHasCategory( $parser, 'zh-Hant', 'N' );
		$this->assertHasCategory( $parser, 'zh-Hant-N', '' );
	}

	public function testRenderPlain() {
		$parser = $this->getParser( Title::newFromText( 'User:User-1' ) );
		$wikiText = Babel::Render( $parser, 'plain=1', 'en' );
		$this->assertSame(
			'<div class="mw-babel-box mw-babel-box-N" dir="ltr">'
			. "\n"
			. '{|'
			. "\n"
			. '! dir="ltr" | [[(babel-portal: en)|en]]<span class="mw-babel-box-level-N">-N</span>'
			. "\n"
			. '| dir="ltr" lang="en" | This user has a [[:Category:en-N|native]] understanding of '
			. '[[:Category:en|English]].'
			. "\n|}\n"
			. '</div>',
			$wikiText
		);

		$this->assertHasCategory( $parser, 'en', 'N' );
		$this->assertHasCategory( $parser, 'en-N', '' );
	}

	public function testRenderRedLink() {
		$parser = $this->getParser( Title::newFromText( 'User:User-1' ) );
		$wikiText = Babel::Render( $parser, 'redLink' );
		$this->assertBabelBoxCount( 0, $wikiText );
		$this->assertStringContainsString(
			'<div class="mw-babel-notabox" dir="ltr">[[(babel-template: redLink)]]</div>',
			$wikiText
		);
	}

	public function testRenderInvalidTitle() {
		$parser = $this->getParser( Title::newFromText( 'User:User-1' ) );
		$wikiText = Babel::Render( $parser, '<invalidTitle>' );
		$this->assertBabelBoxCount( 0, $wikiText );
		$this->assertStringContainsString(
			'<div class="mw-babel-notabox" dir="ltr">(babel-template: <invalidTitle>)</div>',
			$wikiText
		);
	}

	public function testRenderNoSkillNoCategory() {
		$parser = $this->getParser( Title::newFromText( 'User:User-1' ) );
		$wikiText = Babel::Render( $parser, 'en-0' );
		$this->assertNotHasCategory( $parser, 'en' );
	}

	/**
	 * Data provider to run a test with both db enabled and disabled
	 */
	public static function provideSettings() {
		return [
			'lang info from db' => [ [ 'wgBabelUseDatabase' => true ] ],
			'lang info from categories' => [ [ 'wgBabelUseDatabase' => false ] ],
		];
	}

	/**
	 * @dataProvider provideSettings
	 */
	public function testGetUserLanguages( array $settings ) {
		$this->setMwGlobals( $settings );
		$user = User::newFromName( 'User-1' );
		$this->assertArrayEquals( [
			'de',
			'en',
			'es',
			'simple',
			'zh-Hant',
		], Babel::getUserLanguages( $user ) );

		// Filter based on level
		$this->assertArrayEquals( [
			'de',
			'zh-Hant',
			'es',
		], Babel::getUserLanguages( $user, '2' ) );

		$this->assertArrayEquals( [
			'de',
			'zh-Hant',
		], Babel::getUserLanguages( $user, '3' ) );

		// Non-numerical level
		$this->assertArrayEquals( [
			'de',
		], Babel::getUserLanguages( $user, 'N' ) );
	}

	/**
	 * @dataProvider provideSettings
	 */
	public function testGetUserLanguageInfo( array $settings ) {
		$this->setMwGlobals( $settings );
		$user = User::newFromName( 'User-1' );
		$languages = Babel::getUserLanguageInfo( $user );
		$this->assertArrayEquals( [
			'de' => 'N',
			'en' => '1',
			'es' => '2',
			'simple' => '1',
			'zh-Hant' => '3',
		], $languages, false, true );
	}
}
