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
class arrayHelpersTest extends PHPUnit\Framework\TestCase {

	private $data = [
		"foo" => "bar",
		"one" => [
			"two" => "three"
		]
	];

	public function testCreateAppend() {
		$Nestr = new Nestr($this->data);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	public function testCreateCount() {
		$Nestr = new Nestr($this->data);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	public function testCreateMerge() {
		$Nestr = new Nestr($this->data);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	/**
	 * @depends testCreateAppend
	 */
	public function testAppend($Nestr) {
		$Nestr->one->_append("four");
		$Nestr->one->_append("five");
		// Default
		$this->assertEquals("four",    $Nestr->one->_0());
		$this->assertEquals("five",    $Nestr->one->_1());
	}

	/**
	 * @depends testCreateCount
	 */
	public function testCount($Nestr) {
		// No default
		$this->assertEquals(2,         $Nestr->_count());
		// Default
		$this->assertEquals(2,         $Nestr->_count(false, 5));
		// Nestred
		$this->assertEquals(1,         $Nestr->one->_count());
		// Updated nestred count
		$Nestr->one->four = "five";
		$this->assertEquals(2,         $Nestr->one->_count());
	}

	/**
	 * @depends testCreateMerge
	 */
	public function testMerge($Nestr) {
		$Nestr->one->_merge([
			"four" => "five"
		]);

		// Existing value
		$this->assertEquals("three",   $Nestr->one->two());
		// New value
		$this->assertEquals("five",    $Nestr->one->four());
		// Count
		$this->assertEquals(2,         $Nestr->one->_count());
		// Old top level value
		$this->assertEquals("bar",     $Nestr->foo());
	}

}