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
class arrayMathTest extends PHPUnit\Framework\TestCase {
	private $data = [
		"zero" => 0,
		"notzero" => 5,
		"one" => [
			"two" => 3
		]
	];

	public function testCreatePlus() {
		$Nestr = new Nestr($this->data);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	public function testCreateMinus() {
		$Nestr = new Nestr($this->data);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}

	/**
	 * @depends testCreatePlus
	 */
	public function testPlus($Nestr) {
		// Default
		$this->assertEquals(1,         $Nestr->_plus("zero")->zero());
		// Default again
		$this->assertEquals(2,         $Nestr->_plus("zero")->zero());
		// Plus 2
		$this->assertEquals(4,         $Nestr->_plus("zero", 2)->zero());
		// Default
		$this->assertEquals(6,         $Nestr->_plus("notzero")->notzero());
		// Default again
		$this->assertEquals(7,         $Nestr->_plus("notzero")->notzero());
		// Plus 2
		$this->assertEquals(9,         $Nestr->_plus("notzero", 2)->notzero());
	}

	/**
	 * @depends testCreatePlus
	 */
	public function testPlusEmpty($Nestr) {
		// Default
		$this->assertEquals(1,         $Nestr->_plus("empty")->empty());
		// Default again
		$this->assertEquals(2,         $Nestr->_plus("empty")->empty());
		// Plus 2
		$this->assertEquals(4,         $Nestr->_plus("empty", 2)->empty());
		// Default
		$this->assertEquals(3,         $Nestr->_plus("empty2", 1, 2)->empty2());
		// Default again
		$this->assertEquals(4,         $Nestr->_plus("empty2")->empty2());
		// Plus 2
		$this->assertEquals(6,         $Nestr->_plus("empty2", 2)->empty2());
	}

	/**
	 * @depends testCreatePlus
	 */
	public function testPlusNested($Nestr) {
		// Valid
		$this->assertEquals(4,         $Nestr->one->_plus("two")->two());
		// Valid, with default
		$this->assertEquals(5,         $Nestr->one->_plus("two")->two());
		// Valid, with default
		$this->assertEquals(7,         $Nestr->one->_plus("two", 2)->two());
		// Invalid first, no default
		$this->assertEquals(1,         $Nestr->BAD->_plus("two")->two());
		// Invalid second, no default
		$this->assertEquals(1,         $Nestr->one->_plus("BAD")->BAD());
	}

	/**
	 * @depends testCreateMinus
	 */
	public function testMinus($Nestr) {
		// Default
		$this->assertEquals(-1,        $Nestr->_minus("zero")->zero());
		// Default again
		$this->assertEquals(-2,        $Nestr->_minus("zero")->zero());
		// Minus 2
		$this->assertEquals(-4,        $Nestr->_minus("zero", 2)->zero());
		// Default
		$this->assertEquals(4,         $Nestr->_minus("notzero")->notzero());
		// Default again
		$this->assertEquals(3,         $Nestr->_minus("notzero")->notzero());
		// Plus 2
		$this->assertEquals(1,         $Nestr->_minus("notzero", 2)->notzero());
	}

	/**
	 * @depends testCreateMinus
	 */
	public function testMinusEmpty($Nestr) {
		// Default
		$this->assertEquals(-1,        $Nestr->_minus("empty")->empty());
		// Default again
		$this->assertEquals(-2,        $Nestr->_minus("empty")->empty());
		// Minus 2
		$this->assertEquals(-4,        $Nestr->_minus("empty", 2)->empty());
	}

	/**
	 * @depends testCreateMinus
	 */
	public function testMinusNested($Nestr) {
		// Valid
		$this->assertEquals(2,         $Nestr->one->_minus("two")->two());
		// Valid, with default
		$this->assertEquals(1,         $Nestr->one->_minus("two")->two());
		// Valid, with default
		$this->assertEquals(-1,        $Nestr->one->_minus("two", 2)->two());
		// Invalid first, no default
		$this->assertEquals(-1,        $Nestr->BAD->_minus("two")->two());
		// Invalid second, no default
		$this->assertEquals(-1,        $Nestr->one->_minus("BAD")->BAD());
	}

}