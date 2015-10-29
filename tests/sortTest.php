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
class sortTest extends PHPUnit_Framework_TestCase {

	private $data = [
		"foo" => "bar",
		"one" => [
			"z" => "zz",
			 3  => 33,
			"a" => "aa",
			 1  => 11,
			"B" => "BB",
			 2  => 22
		]
	];

	public function testAsort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		asort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("a")->one());
	}

	public function testARsort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		arsort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("ar")->_get("one"));
	}

	public function testKsort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		ksort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("k")->_get("one"));
	}

	public function testKRsort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		krsort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("kr")->_get("one"));
	}

	public function testNatSort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		natsort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("nat")->_get("one"));
	}

	public function testNatCaseSort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		natcasesort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("natcase")->_get("one"));
	}

	/*
	public function testUASort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		uasort($data);
		$this->assertEquals($data,     $Nestr->sort("one", "ua")->get("one"));
	}

	public function testUKSort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		uksort($data);
		$this->assertEquals($data,     $Nestr->sort("one", "uk")->get("one"));
	}
	*/

	public function testFullNamesort() {
		$Nestr = new Nestr($this->data);
		$data = $this->data["one"];
		asort($data);
		$this->assertEquals($data,     $Nestr->one->_sort("asort")->_get("one"));
	}

}