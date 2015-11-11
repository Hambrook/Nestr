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
class serializeTest extends PHPUnit_Framework_TestCase {

	public function testSerialize() {
		$data = [
			"foo" => "bar",
			"one" => "two"
		];
		$Nestr = new Nestr();
		// Valid
		$Nestr->_data($data);
		// First level
		$tmp = unserialize(serialize($Nestr))->_data();
		// Nestred
		$this->assertEquals($data, $tmp);
	}

	public function testSerializeNested() {
		$data = [
			"foo" => "bar",
			"one" => [
				"two" => "three"
			]
		];
		$Nestr = new Nestr();
		// Valid
		$Nestr->_data($data);
		// First level
		$tmp = unserialize(serialize($Nestr))->_data();
		// Nestred
		$this->assertEquals($data, $tmp);
	}

}