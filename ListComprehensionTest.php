<?php

require_once 'ListComprehension.php';

$errors = 0;

/* Test 1 */

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

/* Test 2 */

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

/* Test 3 */

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

/* Test 4 */

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


if (!$errors) {
	echo "---\nAll tests passed, 0 errors.\n\n";
} else {
	echo "---\nOops! $errors test(s) failed.\n\n";
}