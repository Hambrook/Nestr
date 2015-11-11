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
class objectForeachTest extends PHPUnit_Framework_TestCase {

	public function testForeachIndexed() {
		$Nestr = new Nestr(new testObjectForeachData);
		$tmp = "";
		foreach ($Nestr as $t) {
			$tmp .= $t;
		}
		$this->assertEquals("abc",     $tmp);
	}

}

class testObjectForeachData {
	public $one = "a";
	public $two = "b";
	public $three = "c";
}