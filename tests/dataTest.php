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
class dataTest extends PHPUnit_Framework_TestCase {

	private $data = [
		"foo" => "bar",
		"one" => [
			"two" => "three"
		]
	];

	public function testSet() {
		$Nestr = new Nestr();
		// Valid
		$Nestr->_data($this->data);
		// First level
		$this->assertEquals("bar",     $Nestr->foo());
		// Nestred
		$this->assertEquals("three",   $Nestr->one->two());

		$Nestr = new Nestr();
		// Valid
		$Nestr->_data(["bar" => "baz"]);
		// First level
		$this->assertEquals("baz",     $Nestr->bar());
		// Invalid, first level
		$this->assertEquals(null,      $Nestr->foo());
		// Invalid, nestred
		$this->assertEquals(null,      $Nestr->one->two());
	}

	public function testTo() {
		$Nestr = new Nestr();
		// Valid
		$Nestr->_data($this->data);
		$this->assertEquals($this->data, $Nestr->_data());
	}

}