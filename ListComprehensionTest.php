<?php

require_once 'ListComprehension.php';

$errors = 0;

/* Test 1. Basic example with numbers */

$Foo = array (1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
$Test = lc ('$i*2 for $i in $Foo if $i > 5', compact ('Foo'));
$Expected = array (12, 14, 16, 18, 20);
echo $Test == $Expected ? "Passed test 1\n" : "Failed test 1\n";
if ($Test != $Expected) {
	echo "\n--Expected:";
	print_r ($Expected);
	echo "\n--Result:";
	print_r ($Test);
	$errors++;
}

/* Test 2. Basic example with strings */

$Foo = array ('Alabama', 'California', 'Texas', 'New York');
$Test = lc ('strtoupper($state) for $state in $Foo if strlen ($state) > 5 and strlen ($state) < 9', array ('Foo' => $Foo));
$Expected = array ('ALABAMA', 'NEW YORK');
echo $Test == $Expected ? "Passed test 2\n" : "Failed test 2\n";
if ($Test != $Expected) {
  echo "\n--Expected:";
  print_r ($Expected);
  echo "\n--Result:";
  print_r ($Test);
  $errors++;
}

/* Test 3. Support for hash returns (with "{}") */

$Foo = array ('AL' => 'Alabama', 'CA' => 'California', 'TE' => 'Texas', 'NY' => 'New York');
$Test = lc ('{strtoupper($state) => strtolower ($code)} for $code => $state in $Foo if strlen ($state) > 5 and strlen ($state) < 9', array ('Foo' => $Foo));
$Expected = array ('ALABAMA' => 'al', 'NEW YORK' => 'ny');
echo $Test == $Expected ? "Passed test 3\n" : "Failed test 3\n";
if ($Test != $Expected) {
  echo "\n--Expected:";
  print_r ($Expected);
  echo "\n--Result:";
  print_r ($Test);
  $errors++;
}

/* Test 4. Support for hash shortcuts (without "{}") */

$Foo = array (
  array ('name' => 'David Beckham', 'number' => 7),
  array ('name' => 'Ronaldinho', 'number' => 10),
  array ('name' => 'David Villa', 'number' => 9)
);
$Test = lc ('$player["number"] => preg_replace (\'#^[A-z]+\s#i\', "", $player["name"]) for $player in $Foo', array ('Foo' => $Foo));
$Expected = array (7 => "Beckham", 9 => "Villa", 10 => "Ronaldinho");
echo $Test == $Expected ? "Passed test 4\n" : "Failed test 4\n";
if ($Test != $Expected) {
  echo "\n--Expected:";
  print_r ($Expected);
  echo "\n--Result:";
  print_r ($Test);
  $errors++;
}

/* Test 5. Support for %-shortcut. */

$Foo = array (
  array ('title' => 'Brad Pitt', 'id' => 1),
  array ('title' => 'Al Pacino', 'id' => 2),
  array ('title' => 'Keanu Reeves', 'id' => 3)
);
$Test = lc ('%<a href="/%s">%s</a> % $actor["id"], $actor["title"] for $actor in $Foo', array ('Foo' => $Foo));
$Expected = array ('<a href="/1">Brad Pitt</a>', '<a href="/2">Al Pacino</a>', '<a href="/3">Keanu Reeves</a>');
echo $Test == $Expected ? "Passed test 5\n" : "Failed test 5\n";
if ($Test != $Expected) {
  echo "\n--Expected:";
  print_r ($Expected);
  echo "\n--Result:";
  print_r ($Test);
  $errors++;
}


$Foo = array (
  array ('name' => 'Beckham', 'number' => 7),
  array ('name' => 'Ronaldinho', 'number' => 10),
  array ('name' => 'Arshavin', 'number' => 10),
  array ('name' => 'Raul', 'number' => 7),
  array ('name' => 'Villa', 'number' => 9),
);
$Test = lc ('$player["number"] => [$player["name"]] for $player in $Foo', array ('Foo' => $Foo));
$Expected = array (7 => array ("Beckham", "Raul"), 10 => array ("Ronaldinho", "Arshavin"), 9 => array ("Villa"));
echo $Test == $Expected ? "Passed test 5\n" : "Failed test 5\n";
if ($Test != $Expected) {
  echo "\n--Expected:";
  print_r ($Expected);
  echo "\n--Result:";
  print_r ($Test);
  $errors++;
}

if (!$errors) {
	echo "---\nAll tests passed, 0 errors.\n\n";
} else {
	echo "---\nOops! $errors test(s) failed.\n\n";
}
