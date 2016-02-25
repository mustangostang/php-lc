## Python-style string formatting ##

We can also use a special syntax for formatting strings in style of Python by prefixing the whole expression with a percent sign:

```
$Foo = array (
  array ('title' => 'Brad Pitt', 'id' => 1),
  array ('title' => 'Al Pacino', 'id' => 2),
  array ('title' => 'Keanu Reeves', 'id' => 3)
);
print_r ( 
  lc ('%<a href="/%s">%s</a> % $actor["id"], $actor["title"] for $actor in $Foo', array ('Foo' => $Foo))
);
--
Array
(
    [0] => <a href="/1">Brad Pitt</a>
    [1] => <a href="/2">Al Pacino</a>
    [2] => <a href="/3">Keanu Reeves</a>
)
```