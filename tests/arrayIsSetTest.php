<?php

require_once(implode(DIRECTORY_SEPARATOR, [__DIR__, "..", "src", "Nestr.php"]));

use \Hambrook\Nestr\Nestr as Nestr;

/**
 * Tests for PHPUnit
 *
 * @author     Rick Hambrook <rick@rickhambrook.com>
 * @copyright  2015 Rick Hambrook
 * @license    https://www.gnu.org/licenses/gpl.txt  GNU General Public License v3
 */
class arrayIsSetTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$Nestr = new Nestr(
			[
				"foo" => "bar",
				"one" => [
					"two" => "three"
				]
			]
		);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	/**
	 * @depends testCreate
	 */
	public function testIsSet($Nestr) {
		// Is set
		$this->assertEquals(true,      $Nestr->_has("foo"));
		// Is not set
		$this->assertEquals(false,     $Nestr->_has("BAD"));
	}

	/**
	 * @depends testCreate
	 */
	public function testIsSetNestred($Nestr) {
		// Valid
		$this->assertEquals(true,      $Nestr->one->_has("two"));
		// Invalid second, no default
		$this->assertEquals(false,     $Nestr->one->_has("BAD"));
		// Invalid first, no default
		$this->assertEquals(false,     $Nestr->bad->_has("two"));
	}

	/**
	 * @depends testCreate
	 */
	public function testIsSetMagic($Nestr) {
		// Valid, 1 level)
		$this->assertEquals(true,      isset($Nestr->foo));
		// Invalid, 1 level, with default
		$this->assertEquals(false,     isset($Nestr->BAD));
	}

	/**
	 * @depends testCreate
	 */
	public function testIsSetNestredMagic($Nestr) {
		// Valid
		$this->assertEquals(true,      isset($Nestr->one->two));
		// Invalid first, no default
		$this->assertEquals(false,     isset($Nestr->BAD->two));
		// Invalid second, no default
		$this->assertEquals(false,     isset($Nestr->one->BAD));
	}

}