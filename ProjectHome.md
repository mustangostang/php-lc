With php-lc you can easily manipulate your PHP arrays in style of Python list comprehensions.

  * Python syntax: `[i*2 for i in Data if i > 5]`
  * php-lc syntax: `lc ('$i*2 for $i in $Data if $i > 5', compact ('Data'))`

## php-lc examples ##

### Example 1. Experimenting with powers. ###

```
$Foo = array (1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
print_r (lc ('pow ($i, 2) for $i in $Foo if $i % 2', compact ('Foo')));
--
Array
(
    [0] => 1
    [1] => 9
    [2] => 25
    [3] => 49
    [4] => 81
)
```

### Example 2. Calculating years to pension for male employers. ###

```
$Foo = array (
  array ('name' => "Sam", 'age' => 53, 'sex' => 'm'),
  array ('name' => "Joe", 'age' => 51, 'sex' => 'm'),
  array ('name' => "Bill", 'age' => 58, 'sex' => 'm'),
  array ('name' => "Lisa", 'age' => 51, 'sex' => 'w'),
);
print_r (lc ('$Person["name"] => 60 - $Person["age"] for $Person in $Foo if $Person["sex"] == "m"', compact ('Foo')));
--
Array
(
    [Sam] => 7
    [Joe] => 9
    [Bill] => 2
)
```

Take a look at the [Examples](Examples.md) page for even more options.

## Installation ##

Simply include the `ListComprehension.php` from the installation package in your project and use the lc() function.