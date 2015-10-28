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
class objectSetTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$Nestr = new Nestr(new objectSetTestData);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	/**
	 * @depends testCreate
	 */
	public function testSet() {
		$Nestr = new Nestr([]);
		// Setting empty array
		$this->assertEquals([],        $Nestr->_set("foo")->_get("foo"));
		// Setting value
		$this->assertEquals("baz",     $Nestr->_set("bar", "baz")->_get("bar"));
	}

	/**
	 * @depends testCreate
	 */
	public function testSetNested() {
		$Nestr = new Nestr([]);
		// Setting empty array
		$key = "newkey";
		$this->assertEquals([],        $Nestr->_set($key)->_get($key));
		// Valid
		$this->assertEquals("three",   $Nestr->_set($key, "three")->_get($key));
	}

	/**
	 * @depends testCreate
	 */
	public function testSetMagic() {
		$Nestr = new Nestr([]);
		// Valid, 1 level)
		$Nestr->foo = "bar";
		$this->assertEquals("bar",     $Nestr->foo());
	}

	/**
	 * @depends testCreate
	 */
	public function testSetNestedMagic() {
		$Nestr = new Nestr([]);
		// Invalid, 1 level
		$Nestr->one = ["two" => "four"];
		$this->assertEquals("four",    $Nestr->one->two());
	}

}

class objectSetTestData {
	public $foo = "bar";
	public $one = [
		"two" => "three"
	];
}