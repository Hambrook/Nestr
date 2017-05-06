# Nestr

[![Build Status](https://travis-ci.org/Hambrook/Nestr.svg?branch=master)](https://travis-ci.org/Hambrook/Nestr)

Nestr is a PHP class that lets you easily get and set values in nested arrays and objects without worrying about errors or missing data. You don't have to check if keys, properties or functions exist. It's all designed to fail gracefully.

Need a value from an array that is returned by a function that requires parameters on an object which is stored in an array? Nestr will get it for you, or return a default value if the one you want isn't there. Easy.

You can get and set nested values. You can iterate over them with `foreach`. The only limitation is that you can't _set_ the value returned by a function because, well, it's obvious. Unless that returned data was an object.

Nestr is the successor to [Nest](https://github.com/Hambrook/Nest).

#### Why do this...
```php
// need to get $array["one"]["two"]
if (array_key_exists("one", $array) && array_key_exists("two", $array["one"])) {
	$value = $array["one"]["two"];
}
```

#### When you could do this?
```php
// need to get $array["one"]["two"]
$value = $array->one->two();
```
You don't have to worry about any key checks, or checking if things are set... Just fetch the value. You can specify a default value (`null` by default) to use in case the one you want isn't there. Focus on building great apps instead of validating data.

Note the parentheses at the end, this triggers a fetch of the raw value (with an optional parameter for default) instead of the wrapper object.

## Example
```php
$Nestr = new \Hambrook\Nestr\Nestr(
	[
		"foo" => "bar",
		"one" => [
			"two" => "three"
		]
	]
);
```

#### Get a top-level value
```php
$Nestr->foo(); // note the parentheses at the end
// "bar"
```

#### We're going two levels in this time
```php
$Nestr->one->two();
// "three"
```

#### What if we try to get something that isn't there? Does it error?
```php
$Nestr->nope->two();
// returns `null`, not an error
```

#### Or we can specify our own default in case of error
```php
$value = $Nestr->nope->two("safe");
// returns "safe", not an error
```

#### Need to set a value? No problem
```php
$Nestr->one->four = "five";
// sets value to "five" (recursively creating levels as needed)
```

## Who is it for?
Nestr is for working with arrays and objects were you aren't always sure of the data. It works great with CLI scripts. But it can be used anywhere.

## Where are the exceptions?
Nestr doesn't throw any exceptions, that's the rule. Nestr was designed to fail gracefully with default values instead of using exceptions.

## What about the performance hit?
Although Nestr can be used anywhere, it was built primarily for CLI apps where milliseconds don't matter. I've kept speed in mind but it's not a primary concern. At some point I will add benchmarks and timing and see how much I can shave off the execution time.

## Testing
Install PHPUnit 6+ globally, then run it on the `tests/` directory.

## Feedback
Tell me if you loved it. Tell me if you hated it. Tell me if you used it and thought "meh". I'm keen to hear your feedback.

## Contributing
Feel free to fork this project and submit pull requests, or even just request features via the issue tracker. Please be descriptive with pull requests and match the existing code style.

## Roadmap
* Add any other standard documentation that should be included
* Maybe add a parameter to get() that allows specifying a validator callback
* _If you have an idea, [let me know](https://github.com/Hambrook/Nestr/issues)._

## License
Copyright &copy; 2015 Rick Hambrook

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
