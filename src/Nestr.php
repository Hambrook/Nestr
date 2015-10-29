<?php

namespace Hambrook\Nestr;

/*
	TODO

	Finish docblocks
	Organising of functions
	Functions to add
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
 * @version    1.0.0
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
		"parent"       => false,
		"isSet"        => false,
		"isCollection" => false,
		"isArray"      => false,
		"isNumeric"    => false,
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
	public function _data($data=[], $debug=false) {
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
		if ($value instanceof self && !$value->_isSet()) {
			return $this;
		}
		if (!$this->_isObject()) {
			if (!$this->_isArray()) {
				$this->_["data"] = [];
			}
			if (is_array($value)) {
				$this->_["data"][$key] = new self($value);
				$this->_["data"][$key]->_setParent($this);
			} else {
				$this->_["data"][$key] = $value;
			}
		} else {
			if (is_array($value)) {
				$this->_["data"]->$key = new self($value);
				$this->_["data"]->$key->_setParent($this);
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
		if (!$this->_isSet()) { return false; }
		if ($this->_isArray() && !isset($this->_["data"][$key])) { return false; }
		if ($this->_isObject() && isset($this->_["data"]->$key) && $this->_["data"]->$key instanceof self) {
			return $this->_["data"]->$key->_isSet();
		}
		return true;
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
	 * _ISNumeric
	 *
	 * Is the current dataset numeric?
	 *
	 * @return  bool  True if data is an object
	 */
	public function _isNumeric() {
		return $this->_["isNumeric"];
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
	 *  GENERIC HELPER FUNCTIONS                                             *
	 *************************************************************************/

	/**
	 * _KEYS
	 *
	 * Get a list of valid keys
	 *
	 * @return  array  Array of keys for the dataset
	 */
	public function _keys() {
		if ($this->_isArray()) {
			return array_keys($this->_["data"]);
		}
		if ($this->_isObject()) {
			return array_merge(
				get_object_vars($this->_["data"]),
				get_class_methods($this->_["data"])
			);
		}
		return [];
	}


	/*************************************************************************
	 *  NUMERIC HELPER FUNCTIONS                                             *
	 *************************************************************************/

	/**
	 * _PLUS
	 *
	 * Increment the numerical value at the specified path, by the specified amount
	 *
	 * @param   string  $key      The key for the value to increase
	 * @param   float   $value    The amount to increment by
	 * @param   float   $default  Default value to start with if existing value isn't numeric
	 *
	 * @return  $this             Return self, for chaining
	 */
	public function _plus($key, $value=1, $default=0) {
		if (!$this->_has($key)) {
			$this->_set($key, $default);
		}
		$tmp = $this->_get($key);
		if (!is_numeric($tmp)) {
			// if no manual default was set, then don't override an existing non-numeric value
			if (func_num_args() < 3) {
				return $this;
			}
			$tmp = $default;
		}
		$tmp = $tmp + $value;
		$this->_set($key, $tmp);

		return $this;
	}

	/**
	 * _MINUS
	 *
	 * Decrease the numerical value at the specified path, by the specified amount
	 *
	 * @param   string  $key      The key for the value to decrease
	 * @param   float   $value    The amount to decrease by
	 * @param   float   $default  Default value to start with if existing value isn't numeric
	 *
	 * @return  $this            Return self, for chaining
	 */
	public function _minus($key, $value=1, $default=0) {
		if (!$this->_has($key)) {
			$this->_set($key, $default);
		}
		$tmp = $this->_get($key);
		if (!is_numeric($tmp)) {
			// if no manual default was set, then don't override an existing non-numeric value
			if (func_num_args() < 3) {
				return $this;
			}
			$tmp = $default;
		}
		$tmp = $tmp - $value;
		$this->_set($key, $tmp);

		return $this;
	}


	/*************************************************************************
	 *  JSON HELPER FUNCTIONS                                                *
	 *************************************************************************/

	/**
	 * TOJSON
	 *
	 * Generate JSON and return it
	 *
	 * @param   bool    $pretty  Whether to pretty print or not
	 *
	 * @return  string           The generated JSON
	 */
	public function _toJSON($pretty=true) {
		return json_encode($this->_nestrToArray(), ($pretty) ? JSON_PRETTY_PRINT : 0);
	}

	/**
	 * LOADJSON
	 *
	 * Generate dataset from JSON
	 *
	 * @param   string  $json  The JSON string to decode and load
	 *
	 * @return  $this          This
	 */
	public function _loadJSON($json) {
		$this->_data(@json_decode($json, true));
		return $this;
	}


	/*************************************************************************
	 *  ARRAY HELPER FUNCTIONS                                               *
	 *************************************************************************/

	/**
	 * _APPEND
	 *
	 * Append data (arrays only at present)
	 *
	 * @param   mixed         $value  New new value to append
	 * @param   mixed         $force  Force the value to be an array, even if it's not
	 *
	 * @return  $this                 Return self, for chaining
	 */
	public function _append($value=null, $force=true) {
		if (!$this->_isArray()) {
			if (!$force) {
				return $this;
			}
			$this->_["data"] = [];
		}
		$this->_["data"][] = $value;

		return $this;
	}

	/**
	 * _COUNT
	 *
	 * Count the items at the path
	 *
	 * @param   int           $default  The amount to return if invalid
	 *
	 * @return  $this                   Return self, for chaining
	 */
	public function _count($default=0) {
		if ($this->_isArray()) {
			return count($this->_["data"]);
		}
		if ($this->_isObject()) {
			return count(
				array_merge(
					get_object_vars($this->_["data"]),
					get_class_methods($this->_["data"])
				)
			);
		}

		return count($tmp);
	}

	/**
	 * _MERGE
	 *
	 * Merge data (arrays only at present)
	 *
	 * @param   array         $value  New new array to merge in
	 * @param   bool          $force  Force the value to be an array, even if it's not
	 *
	 * @return  $this                 Return self, for chaining
	 */
	public function _merge($value=[], $force=true) {
		if (!is_array($value) || !count($value)) {
			return $this;
		}
		$tmp = $this->_["data"];
		if (!$this->_isArray()) {
			if (!$force) {
				return $this;
			}
			$tmp = [];
		}
		$tmp = array_merge($tmp, $value);
		return $this->_data($tmp);
	}

	/**
	 * _SORT
	 *
	 * Sort an array by sort method
	 *
	 * @param   string           $method           Optional sort method
	 * @param   callable|string  $flagsOrCallable  Optional flags or callback
	 *
	 * @return  $this                              Return self, for chaining
	 */
	public function _sort($method="", $flagsOrCallable=false) {
		$data = $this->_data();
		$tmp = &$data;

		if (!$this->_isArray()) {
			return $this;
		}

		if (!is_callable($method)) {
			$method = $method."sort";
		}
		if (!is_callable($method)) {
			return $this;
		}

		$params = [&$tmp];
		if ($flagsOrCallable) {
			$params[] = $flagsOrCallable;
		}
		call_user_func_array($method, $params);

		return $this->_data($tmp)->_getParent();
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
		// Are we getting a numeric key?
		if (strpos($key, "__") === 0) {
			$key = -1 * str_replace("_", "", $key);
		}
		if (strpos($key, "_") === 0) {
			$key = 1 * str_replace("_", "", $key);
		}

		if ($raw && !$this->_has($key)) {
			return $default;
		}

		if ($this->_isArray()) {
			if ($raw) {
				if (!isset($this->_["data"][$key])) { return $default; }
				if ($this->_["data"][$key] instanceof self) {
					return ($this->_["data"][$key]->_isSet()) ? $this->_["data"][$key]->_data() : $default;
				}
				return $this->_["data"][$key];
			}
			if (!isset($this->_["data"][$key])) {
				$this->_["data"][$key] = new self();
				$this->_["data"][$key]->_setParent($this);
			}
			if (!$this->_["data"][$key] instanceof self) {
				$this->_["data"][$key] = new self($this->_["data"][$key]);
				$this->_["data"][$key]->_setParent($this);
			}
			return $this->_["data"][$key];
		} else
		if ($this->_isObject()) {
			if ($raw) {
				if (!isset($this->_["data"]->$key)) { return $default; }
				if ($this->_["data"]->$key instanceof self) {
					return ($this->_["data"]->$key->_isSet()) ? $this->_["data"]->$key->_data() : $default;
				}
				return $this->_["data"]->$key;
			}
			if ($raw) {
				if (!isset($this->_["data"]->$key)) { return $default; }
				return ($this->_["data"]->$key instanceof self)
					? $this->_["data"]->$key->_["data"]
					: $this->_["data"]->$key;
			}
			if (!isset($this->_["data"]->$key)) {
				$this->_["data"]->$key = new self();
				$this->_["data"]->$key->_setParent($this);
				$this->_updateType();
				return $this->_["data"]->$key;
			}
			if ($this->_["data"]->$key instanceof self) {
				return $this->_["data"]->$key;
			}
			$this->_["data"]->$key = new self($this->_["data"]->$key);
			$this->_["data"]->$key->_setParent($this);
			return $this->_["data"]->$key;
		}
		if ($raw) {
			return ($this->_isSet()) ? $this->_["data"] : $default;
		}
		if ($this->_isSet()) {
			if ($raw) {
				return $this->_["data"];
			}
			$this->_["data"]->$key = new self($this->_["data"]);
			$this->_["data"]->$key->_setParent($this);
			return $this->_["data"]->$key;
		}
		return ($raw) ? $default : (new self())->_setParent($this);
	}

	/**
	 * _GETPARENT
	 *
	 * Get the parent object when nesting
	 *
	 * @internal
	 *
	 * @return  Nestr  Parent object
	 */
	public function _getParent() {
		return ($this->_hasParent()) ? $this->_["parent"] : $this;
	}

	/**
	 * _HASPARENT
	 *
	 * Check if we have a parent object
	 *
	 * @internal
	 *
	 * @return  bool  Whether or not we have a parent object
	 */
	public function _hasParent() {
		return ($this->_["parent"] instanceof self);
	}

	/**
	 * _SETPARENT
	 *
	 * Set the parent object when nesting
	 *
	 * @internal
	 * @param   Nestr  $Nestr  Nestr object to set a s parent
	 *
	 * @return  $this          $this
	 */
	public function _setParent($Nestr) {
		$this->_["parent"] = $Nestr;
		return $this;
	}

	/**
	 * _UPDATETYPE
	 *
	 * Update the data type values
	 *
	 * @internal
	 *
	 * @return  bool  True if data is an array or object
	 */
	private function _updateType() {
		$this->_["isArray"] = is_array($this->_["data"]);
		$this->_["isObject"] = is_object($this->_["data"]);
		$this->_["isNumeric"] = is_numeric($this->_["data"]);
		$this->_["isCollection"] = ($this->_["isArray"] || $this->_["isObject"]);
		return $this;
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
	 *
	 * @return  $this          This with data unserialized and set
	 */
	public function unserialize($data) {
		$this->_["data"] = unserialize($data);
		$this->_updateType();
		return $this;
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
		if (count($args)) {
			return $this->_getActual($key, true, $args[0]);
		}
		return $this->_getActual($key, true);
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

}