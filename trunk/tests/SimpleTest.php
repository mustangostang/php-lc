<?php

require_once ("../ListComprehension.php");

class SimpleTest extends PHPUnit_Framework_TestCase {

    public function testSimpleArray() {
      $Foo = array (1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
      $Result = (lc ('$i*2 for $i in $Foo if $i > 5', compact ('Foo')));
      $Expected = array (12, 14, 16, 18, 20);
      $this->assertEquals ($Expected, $Result);
    }

    public function testStringOperations() {
      $Foo = array ('Alabama', 'California', 'Texas', 'New York');
      $Result = (lc ('strtoupper($state) for $state in $Foo if strlen ($state) > 5 and strlen ($state) < 9', array ('Foo' => $Foo)));
      $Expected = array ('ALABAMA', 'NEW YORK');
      $this->assertEquals ($Expected, $Result);
    }

    public function testStringHashes() {
      $Foo = array ('AL' => 'Alabama', 'CA' => 'California', 'TE' => 'Texas', 'NY' => 'New York');
      $Result = lc ('{strtoupper($state) => strtolower ($code)} for $code => $state in $Foo if strlen ($state) > 5 and strlen ($state) < 9', array ('Foo' => $Foo));
      $Expected = array ('ALABAMA' => 'al', 'NEW YORK' => 'ny');
      $this->assertEquals ($Expected, $Result);
    }

    public function testHashesAndRegexps() {
      $Foo = array (
        array ('name' => 'David Beckham', 'number' => 7),
        array ('name' => 'Ronaldinho', 'number' => 10),
        array ('name' => 'David Villa', 'number' => 9)
      );
      $Result = lc ('$player["number"] => preg_replace (\'#^[A-z]+\s#i\', "", $player["name"]) for $player in $Foo', array ('Foo' => $Foo));
      $Expected = array (7 => 'Beckham', 10 => 'Ronaldinho', 9 => 'Villa');
      $this->assertEquals ($Expected, $Result);
    }

    public function testSprintf() {
      $Foo = array (
        array ('title' => 'Brad Pitt', 'id' => "nm0000093"),
        array ('title' => 'Al Pacino', 'id' => "nm0000199"),
        array ('title' => 'Keanu Reeves', 'id' => "nm0000206")
      );
      $Result = lc ('%<a href="http://www.imdb.com/%s">%s</a> % $actor["id"], $actor["title"] for $actor in $Foo', array ('Foo' => $Foo));
      $Expected = array ('<a href="http://www.imdb.com/nm0000093">Brad Pitt</a>', '<a href="http://www.imdb.com/nm0000199">Al Pacino</a>',
                         '<a href="http://www.imdb.com/nm0000206">Keanu Reeves</a>');
      $this->assertEquals ($Expected, $Result);
    }

    public function testNestedArrays() {
      $Foo = array (
        array ('name' => 'Beckham', 'number' => 7),
        array ('name' => 'Ronaldinho', 'number' => 10),
        array ('name' => 'Arshavin', 'number' => 10),
        array ('name' => 'Raul', 'number' => 7),
        array ('name' => 'Villa', 'number' => 9)
      );
      $Result = lc ('$player["number"] => [$player["name"]] for $player in $Foo', array ('Foo' => $Foo));
      $Expected = array (7 => array ('Beckham', 'Raul'), 10 => array ('Ronaldinho', 'Arshavin'), 9 => array ('Villa'));
      $this->assertEquals ($Expected, $Result);
    }


    public function testStringBracketsInArgument() {
      $Data = array (
        '1 foo', '2bar', '3  baz'
      );
      $Result = lc ('trim($i[1:]) for $i in $Data', array ('Data' => $Data));
      $Expected = array ('foo', 'bar', 'baz');
      $this->assertEquals ($Expected, $Result);
    }

    public function testStringBracketsInArgument2() {
      $Data = array (
        'foo', 'bar', 'baz'
      );
      $Result = lc ('$i[1:2] for $i in $Data', array ('Data' => $Data));
      $Expected = array ('o', 'a', 'a');
      $this->assertEquals ($Expected, $Result);
    }

    public function testStringBracketsInArgument3() {
      $Data = array (
        'foo', 'bar', 'baz'
      );
      $Result = lc ('$i[:1] for $i in $Data', array ('Data' => $Data));
      $Expected = array ('f', 'b', 'b');
      $this->assertEquals ($Expected, $Result);
    }

}