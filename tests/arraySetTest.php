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
class arraySetTest extends PHPUnit_Framework_TestCase {

	public function testSet() {
		$Nestr = new Nestr([]);
		// Setting empty array
		$this->assertEquals([],        $Nestr->_set("foo")->_get("foo"));
		// Setting value
		$this->assertEquals("baz",     $Nestr->_set("bar", "baz")->_get("bar"));
	}

	public function testSetMagic() {
		$Nestr = new Nestr([]);
		// Valid, 1 level)
		$Nestr->foo = "bar";
		$this->assertEquals("bar",     $Nestr->_get("foo"));
	}

	public function testSetNestedMagic() {
		$this->markTestIncomplete("This test has not been implemented yet.");

		$Nestr = new Nestr([]);
		// Invalid, 1 level
		$Nestr->one->two = "four";
		$this->assertEquals("four",    $Nestr->one->_get("two"));
	}

}