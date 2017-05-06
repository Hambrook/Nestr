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
class issetTest extends PHPUnit\Framework\TestCase {

	private $data = [
		"foo" => "bar",
		"one" => [
			"two" => "three"
		]
	];

	public function testHas() {
		$Nestr = new Nestr($this->data);
		// First level
		$this->assertEquals(true,      $Nestr->_has("foo"));
		// Nestred
		$this->assertEquals(true,      $Nestr->one->_has("two"));

		// Invalid, first level
		$this->assertEquals(false,     $Nestr->_has("BAD"));
		// Invalid, nestred
		$this->assertEquals(false,     $Nestr->one->_has("BAD"));
		// Invalid, nestred
		$this->assertEquals(false,     $Nestr->bad->_has("two"));
	}

	public function testIsset() {
		$Nestr = new Nestr($this->data);
		// First level
		$this->assertEquals(true,      isset($Nestr->foo));
		// Nestred
		$this->assertEquals(true,      isset($Nestr->one->two));

		// Invalid, first level
		$this->assertEquals(false,     isset($Nestr->BAD));
		// Invalid, nestred
		$this->assertEquals(false,     isset($Nestr->one->BAD));
		// Invalid, nestred
		$this->assertEquals(false,     isset($Nestr->BAD->TWO));
	}

}