<?php

namespace Hambrook\Nestr;

/*
	TODO

	Finish docblocks
	Organising of functions
	Functions to add
		_sort
		_append
		_plus
		_minus
		_first
		_count
		_walk
		_filter
*/

/**
 * NESTR
 *
 * Easily get and set nested items within arrays and objects via chained calls without the hassle of validation.
 *
 * @package    Nestr
 *
 * @version    0.0.1
 *
 * @author     Rick Hambrook <rick@rickhambrook.com>
 * @copyright  2015 Rick Hambrook
 * @license    https://www.gnu.org/licenses/gpl.txt  GNU General Public License v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Nestr extends \ArrayObject {

	private $_ = [
		"data"         => null,
		"isSet"        => false,
		"isCollection" => false,
		"isArray"      => false,
		"isObject"     => false
	];

	/**
	 * __CONSTRUCT
	 *
	 * Get or set the entire dataset
	 *
	 * @param   mixed  $data  Data to store for management
	 */
	public function __construct($data=[]) {
		if (func_num_args()) {
			$this->_data($data);
		}
	}

	/**
	 * _DATA
	 *
	 * Get or set the entire dataset
	 *
	 * @param   mixed  $data  Key to find by
	 *
	 * @return  mixed         Raw value from $key
	 */
	public function _data($data=[]) {
		if (!func_num_args()) {
			return $this->_nestrToArray();
		}
		$this->_["isSet"] = true;
		if (is_array($data)) {
			$this->_["data"] = [];
			foreach ($data as $k => $v) {
				$this->_set($k, $v);
			}
		} else {
			$this->_["data"] = $data;
		}
		$this->_updateType();
		return $this;
	}

	/**
	 * _GET
	 *
	 * Get a value
	 *
	 * @param   string  $key      Key to find by
	 * @param   mixed   $default  Default value if not found
	 *
	 * @return  mixed             Raw value from $key
	 */
	public function _get($key, $default=null) {
		return $this->_getActual($key, true, $default);
	}

	/**
	 * _SET
	 *
	 * Set a value
	 *
	 * @param   string  $key    Key to set
	 * @param   string  $value  New value
	 *
	 * @return  $this           This
	 */
	public function _set($key, $value=[]) {
		$this->_["isSet"] = true;
		if (!$this->_isObject()) {
			if (!$this->_isArray()) {
				$this->_["data"] = [];
			}
			if (is_array($value)) {
				$this->_["data"][$key] = new self($value);
			} else {
				$this->_["data"][$key] = $value;
			}
		} else {
			if (is_array($value)) {
				$this->_["data"]->$key = new self($value);
			} else {
				$this->_["data"]->$key = $value;
			}
		}
		$this->_["isSet"] = true;
		$this->_updateType();
		return $this;
	}

	/**
	 * _HAS
	 *
	 * Effectively ISSET
	 *
	 * @param   string  $key  Key to check
	 *
	 * @return  bool          Whether or not the value is set
	 */
	public function _has($key) {
		// https://ilia.ws/archives/247-Performance-Analysis-of-isset-vs-array_key_exists.html
		return (
			$this->_isSet() &&
			(
				($this->_isArray() && isset($this->_["data"][$key])) ||
				($this->_isObject() && isset($this->_["data"]->$key))
			)
		);
	}

	/**
	 * _UNSET
	 *
	 * Effectively unset
	 *
	 * @param   string  $key  Key to unset
	 *
	 * @return  $this         This
	 */
	public function _unset($key) {
		if (!$this->_has($key)) { return $this; }
		if ($this->_isArray())  { unset($this->_["data"][$key]); return $this; }
		if ($this->_isObject()) { unset($this->_["data"]->$key); return $this; }
	}


	/*************************************************************************
	 *  IS FUNCTIONS                                                         *
	 *************************************************************************/

	/**
	 * _ISARRAY
	 *
	 * Is the current dataset an array?
	 *
	 * @return  bool  True if data is an array
	 */
	public function _isArray() {
		return $this->_["isArray"];
	}

	/**
	 * _ISCOLLECTION
	 *
	 * Is the current dataset an array or object?
	 *
	 * @return  bool          True if data is an array or object
	 */
	public function _isCollection() {
		return $this->_["isCollection"];
	}

	/**
	 * _ISOBJECT
	 *
	 * Is the current dataset an object?
	 *
	 * @return  bool  True if data is an object
	 */
	public function _isObject() {
		return $this->_["isObject"];
	}

	/**
	 * _ISSET
	 *
	 * Is there currently data set?
	 *
	 * @return  bool  True if data is set
	 */
	public function _isSet() {
		return $this->_["isSet"];
	}


	/*************************************************************************
	 *  EXPORT FUNCTIONS                                                     *
	 *************************************************************************/

	/**
	 * _NESTRTOARRAY
	 *
	 * Recursively convert Nestr objects to arrays, leave other objects alone
	 *
	 * @return  mixed  Data but with arrays as arrays instead of Nestr objects
	 */
	public function _nestrToArray() {
		if (!$this->_isArray()) {
			return $this->_["data"];
		}
		$data = [];
		foreach ($this->_["data"] as $k => $v) {
			$data[$k] = ($v instanceof self) ? $v->_nestrToArray() : $v;
		}
		return $data;
	}


	/*************************************************************************
	 *  INTERNAL FUNCTIONS                                                   *
	 *************************************************************************/

	/**
	 * _UPDATETYPE
	 *
	 * Update the data type values
	 *
	 * @internal
	 *
	 * @return    bool  True if data is an array or object
	 */
	private function _updateType() {
		$this->_["isArray"] = is_array($this->_["data"]);
		$this->_["isObject"] = is_object($this->_["data"]);
		$this->_["isCollection"] = ($this->_["isArray"] || $this->_["isObject"]);
		return $this;
	}

	/**
	 * _GETACTUAL
	 *
	 * Internal get function to manage raw/default complexities
	 *
	 * @internal
	 *
	 * @param   string  $key      Key to find value for
	 * @param   string  $raw      Do we want the raw value instead of Nestr object?
	 * @param   string  $default  Default if not set
	 *
	 * @return  $this|mixed       True if data is an array or object
	 */
	private function _getActual($key, $raw=false, $default=null) {
		if ($this->_isArray()) {
			if ($raw) {
				if (!isset($this->_["data"][$key])) { return $default; }
				return ($this->_["data"][$key] instanceof self)
					? $this->_["data"][$key]->_["data"]
					: $this->_["data"][$key];
			}
			if (!isset($this->_["data"][$key])) {
				return new self();
			}
			if ($this->_["data"][$key] instanceof self) {
				return $this->_["data"][$key];
			}
			return new self($this->_["data"][$key]);
		} else
		if ($this->_isObject()) {
			if ($raw) {
				if (!isset($this->_["data"]->$key)) { return $default; }
				return ($this->_["data"]->$key instanceof self)
					? $this->_["data"]->$key->_["data"]
					: $this->_["data"]->$key;
			}
			if (!isset($this->_["data"]->$key)) {
				$this->_["data"]->$key = new self();
				$this->_updateType();
				return $this->_["data"]->$key;
			}
			if ($this->_["data"]->$key instanceof self) {
				return $this->_["data"]->$key;
			}
			$this->_["data"]->$key = new self($this->_["data"]->$key);
			return $this->_["data"]->$key;
		}
		if ($raw) {
			return $default;
		}
		return new self();
	}


	/*************************************************************************
	 *  ARRAY/OFFSET FUNCTIONS                                               *
	 *************************************************************************/

	/**
	 * GETITERATOR
	 *
	 * Get an array iterator based on the data
	 *
	 * @return  ArrayIterator  Array iterator of the data
	 */
	public function getIterator() {
		return new \ArrayIterator($this->_["data"]);
	}

	/**
	 * OFFSETGET
	 *
	 * Get an the value at an array offset
	 *
	 * @param   string  $key  Key to get value for
	 *
	 * @return  mixed         Value from offset
	 */
	public function offsetGet($key) {
		return $this->_getActual($key);
	}

	/**
	 * OFFSETSET
	 *
	 * Set the value at an offset
	 *
	 * @param   string  $key    Key to set value for
	 * @param   string  $value  New value
	 *
	 * @return  $this           This
	 */
	public function offsetSet($key, $value) {
		return $this->set($key, $value);
	}

	/**
	 * OFFSETEXISTS
	 *
	 * Check if an offset is set
	 *
	 * @param   string  $key  Key to check
	 *
	 * @return  bool          True if offset exists
	 */
	public function offsetExists($key) {
		return $this->_has($key);
	}

	/**
	 * OFFSETUNSET
	 *
	 * Unset the value at an offset
	 *
	 * @param   string  $key  Key to unset
	 *
	 * @return  $this         This
	 */
	public function offsetUnset($key) {
		return $this->_unset($key);
	}


	/*************************************************************************
	 *  MAGIC FUNCTIONS                                                      *
	 *************************************************************************/

	/**
	 * __CALL
	 *
	 * Get the raw value, optionally with a default value
	 *
	 * @param   string  $key   Key to get the value for
	 * @param   array   $args  Array of extra parameters (currently only default value)
	 *
	 * @return  mixed          The raw value from the offset, or default if not set 
	 */
	public function __call($key, $args=[]) {
		switch(count($args)) {
			// Get raw, no default
			case 0:
				return $this->_getActual($key, true);

			// Get raw, with default
			default:
				return $this->_getActual($key, true, $args[0]);
		}
	}

	/**
	 * __GET
	 *
	 * Get the next object from the offset
	 *
	 * @param   string  $key   Key to get the value for
	 *
	 * @return  $this          Nestr object for this offset
	 */
	public function __get($key) {
		return call_user_func([$this, "_getActual"], $key);
	}

	/**
	 * __INVOKE
	 *
	 * Raw dataset or default
	 *
	 * @param   mixed  $default  Default value if no data present
	 *
	 * @return  mixed            Data set or default
	 */
	public function __invoke($default=null) {
		return ($this->_["isSet"]) ? $this->_nestrToArray() : $default;
	}

	/**
	 * __ISSET
	 *
	 * Magic isset method
	 *
	 * @param   string  $key  Key to check
	 *
	 * @return  bool          Whether or not the value is set
	 */
	public function __isset($key=false) {
		if (!func_num_args()) {
			return $this->_isSet();
		}
		return $this->_has($key);
	}

	public function __set($key, $value) {
		return call_user_func_array([$this, "_set"], func_get_args());
	}

	/**
	 * __UNSET
	 *
	 * Magic unset method
	 *
	 * @param   string  $key  Key to the value
	 *
	 * @return  $this         This
	 */
	public function __unset($key) {
		$this->_unset($key);
	}


	/*************************************************************************
	 *  SERIALIZE FUNCTIONS                                                  *
	 *************************************************************************/

	/**
	 * SERIALIZE
	 *
	 * Serialize data within the object
	 *
	 * @return  string  The serialized data ready for storage
	 */
	public function serialize() {
		return serialize($this->_nestrToArray());
	}

	/**
	 * UNSERIALIZE
	 *
	 * Restore serialized data
	 *
	 * @param   string  $data  Data to restore to the object
	 */
	public function unserialize($data) {
		$this->_["data"] = unserialize($data);
		$this->_updateType();
		return $this;
	}

}