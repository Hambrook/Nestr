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
class unsetTest extends PHPUnit_Framework_TestCase {

	private $data = [
		"foo" => "bar",
		"one" => [
			"two" => "three"
		]
	];

	public function testDelete() {
		$Nestr = new Nestr();
		$Nestr->_data($this->data);

		// First level
		$this->assertEquals(false,     $Nestr->_unset("foo")->_has("foo"));
		// Nestred
		$this->assertEquals(false,     $Nestr->one->_unset("two")->_has("two"));
		// Make sure it only deleted the final level
		$this->assertEquals(true,      $Nestr->_has("one"));

		$Nestr = new Nestr();
		$Nestr->_data($this->data);

		// Invalid, nestred
		$this->assertEquals(false,     $Nestr->BAD->_unset("two")->_has("two"));
		// Invalid, nestred
		$this->assertEquals(false,     $Nestr->one->_unset("BAD")->_has("BAD"));
		// Make sure it only deleted the final level
		$this->assertEquals(true,      $Nestr->_has("one"));
		// Invalid, first level
		$this->assertEquals(false,     $Nestr->_unset("BAD")->_has("BAD"));
	}

	public function testUnset() {
		$Nestr = new Nestr();
		$Nestr->_data($this->data);

		// First level
		unset($Nestr->foo);
		$this->assertEquals(false,     $Nestr->_has("foo"));
		// Nestred
		unset($Nestr->one->two);
		$this->assertEquals(false,     $Nestr->one->_has("two"));
		// Make sure it only deleted the final level
		$this->assertEquals(true,      $Nestr->_has("one"));

		$Nestr = new Nestr();
		$Nestr->_data($this->data);

		// Invalid, nestred
		unset($Nestr->BAD->two);
		$this->assertEquals(false,     $Nestr->BAD->_has("two"));
		// Invalid, nestred
		unset($Nestr->one->BAD);
		$this->assertEquals(false,     $Nestr->one->_has("BAD"));
		// Make sure it only deleted the final level
		$this->assertEquals(true,      $Nestr->_has("one"));
		// Invalid, first level
		unset($Nestr->BAD);
		$this->assertEquals(false,     $Nestr->_has("BAD"));
	}

}