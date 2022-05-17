<?php

namespace Babel\Tests\Unit;

use Babel;
use BabelStatic;
use Parser;

/**
 * @covers BabelStatic
 *
 * @group Babel
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
class BabelStaticTest extends \MediaWikiUnitTestCase {

	public function testOnParserFirstCallInit() {
		$parser = $this->getMockBuilder( Parser::class )
			->disableOriginalConstructor()
			->getMock();
		$parser->expects( $this->once() )
			->method( 'setFunctionHook' )
			->with( 'babel', [ Babel::class, 'Render' ] )
			->will( $this->returnValue( true ) );

		BabelStatic::onParserFirstCallInit( $parser );
	}

}
