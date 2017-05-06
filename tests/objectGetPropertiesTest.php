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
class objectGetPropertiesTest extends PHPUnit\Framework\TestCase {

	public function testCreate() {
		$Nestr = new Nestr(new objectGetPropertiesTestData);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	public function testGet() {
		$Nestr = $this->testCreate();
		// Valid
		$this->assertEquals("bar",     $Nestr->_get("foo"));
		// Valid, with default
		$this->assertEquals("bar",     $Nestr->_get("foo", "DEFAULT"));
		// Invalid, no default
		$this->assertEquals(null,      $Nestr->_get("BAD"));
		// Invalid, with default
		$this->assertEquals("DEFAULT", $Nestr->_get("BAD", "DEFAULT"));
	}

	public function testGetNested() {
		$Nestr = $this->testCreate();
		// Valid
		$this->assertEquals("three",   $Nestr->one->_get("two"));
		// Valid, with default
		$this->assertEquals("three",   $Nestr->one->_get("two", "DEFAULT"));
		// Invalid first, no default
		$this->assertEquals(null,      $Nestr->BAD->_get("two"));
		// Invalid second, no default
		$this->assertEquals(null,      $Nestr->one->_get("BAD"));
		// Invalid first, with default
		$this->assertEquals("DEFAULT", $Nestr->BAD->_get("two", "DEFAULT"));
		// Invalid second, with default
		$this->assertEquals("DEFAULT", $Nestr->one->_get("BAD", "DEFAULT"));
	}

	public function testMagicGet() {
		$Nestr = $this->testCreate();
		// Valid, 1 level)
		$this->assertEquals("bar",     $Nestr->foo());
		// Invalid, 1 level
		$this->assertEquals("bar",     $Nestr->foo("DEFAULT"));
		// Invalid, 1 level, with default
		$this->assertEquals(null,      $Nestr->BAD());
		// Invalid, 1 level, with default
		$this->assertEquals("DEFAULT", $Nestr->BAD("DEFAULT"));
	}

	public function testMagicGetNested() {
		$Nestr = $this->testCreate();
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

class objectGetPropertiesTestData {
	public $foo = "bar";
	public $one = [
		"two" => "three"
	];
}