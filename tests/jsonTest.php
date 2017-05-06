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
class jsonTest extends PHPUnit\Framework\TestCase {

	public function testCreate() {
		$Nestr = new Nestr([]);
		$this->assertInstanceOf("\Hambrook\Nestr\Nestr", $Nestr);
		return $Nestr;
	}
	/**
	 * @depends testCreate
	 */
	public function testLoad($Nestr) {
		// Valid
		$json = '{"foo":"bar","one":{"two":"three"}}';
		$Nestr->_loadJSON($json);
		// Valid
		$this->assertEquals("bar",     $Nestr->foo());
		// Valid, with default
		$this->assertEquals("three",   $Nestr->one->two());
	}

	/**
	 * @depends testCreate
	 */
	public function testTo($Nestr) {
		$Nestr->foo = "newfoo";
		$Nestr->one->two = "four";
		$json = $Nestr->_toJSON(false);
		$this->assertEquals('{"foo":"newfoo","one":{"two":"four"}}', $json);
	}

}