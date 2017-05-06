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
class arrayGetTest extends PHPUnit\Framework\TestCase {

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
	public function testGet($Nestr) {
		// Valid
		$this->assertEquals("bar",     $Nestr->_get("foo", null, true));
		// Valid, with default
		$this->assertEquals("bar",     $Nestr->_get("foo", "DEFAULT", true));
		// Invalid, no default
		$this->assertEquals(null,      $Nestr->_get("BAD", null, true));
		// Invalid, with default
		$this->assertEquals("DEFAULT", $Nestr->_get("BAD", "DEFAULT", true));
	}

	/**
	 * @depends testCreate
	 */
	public function testGetAsArray($Nestr) {
		// Valid
		$this->assertEquals("bar",     $Nestr["foo"]());
		// Valid, with default
		$this->assertEquals("bar",     $Nestr["foo"]("DEFAULT"));
		// Invalid, no default
		$this->assertEquals(null,      $Nestr["BAD"]());
		// Invalid, with default
		$this->assertEquals("DEFAULT", $Nestr["BAD"]("DEFAULT"));
	}

	/**
	 * @depends testCreate
	 */
	public function testGetAsObject($Nestr) {
		// Valid
		$this->assertEquals("bar",     $Nestr->foo());
		// Valid, with default
		$this->assertEquals("bar",     $Nestr->foo("DEFAULT"));
		// Invalid, no default
		$this->assertEquals(null,      $Nestr->BAD());
		// Invalid, with default
		$this->assertEquals("DEFAULT", $Nestr->BAD("DEFAULT"));
	}

	/**
	 * @depends testCreate
	 */
	public function testGetNestedAsArray($Nestr) {
		// Valid
		$this->assertEquals("three",   $Nestr["one"]["two"]());
		// Valid, with default
		$this->assertEquals("three",   $Nestr["one"]["two"]("DEFAULT"));
		// Invalid first, no default
		$this->assertEquals(null,      $Nestr["BAD"]["two"]());
		// Invalid second, no default
		$this->assertEquals(null,      $Nestr["one"]["BAD"]());
		// Invalid first, with default
		$this->assertEquals("DEFAULT", $Nestr["BAD"]["two"]("DEFAULT"));
		// Invalid second, with default
		$this->assertEquals("DEFAULT", $Nestr["one"]["BAD"]("DEFAULT"));
	}

	/**
	 * @depends testCreate
	 */
	public function testGetNestedAsObject($Nestr) {
		// Valid
		$this->assertEquals("three",   $Nestr->one->two());
		// Valid, with default
		$this->assertEquals("three",   $Nestr->one->two("DEFAULT"));
		// Invalid first, no default
		$this->assertEquals(null,      $Nestr->BAD->two());
		// Invalid second, no default
		$this->assertEquals(null,      $Nestr->one->BAD());
		// Invalid first, with default
		$this->assertEquals("DEFAULT", $Nestr->BAD->two("DEFAULT"));
		// Invalid second, with default
		$this->assertEquals("DEFAULT", $Nestr->one->BAD("DEFAULT"));
	}

}