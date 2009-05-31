<?php

/**
 * List comprehensions for PHP (php-lc)
 * @version 0.13
 * @author Vlad Andersen <vlad.andersen@gmail.com>
 * @link http://code.google.com/p/php-lc/
 * @license GPL
 * 
 * With php-lc you can easily manipulate your PHP arrays in style of Python list comprehensions.
 * 
 * Python syntax: [i*2 for i in Data if i > 5]
 * php-lc syntax: lc ('$i*2 for $i in $Data if $i > 5', compact ('Data'))
 * 
 * See what you can do with php-lc on: http://code.google.com/p/php-lc/
 */

if (!function_exists('lc')) {
	/**
	 * lc() - a shortcut for executing a list comprehension
	 *
	 * == Basic syntax ==
	 * 
	 * The syntax for php-lc expressions is the following:
	 * 
	 *    <return> for [<key> => ]<element> in <Data> [if <condition>]
	 * 
	 * <return> could be any expression that is using <element>, <key> (if provided, discussed below) 
	 * or any of the passed variables. If no <key> is provided, php-lc will return an array with consecutive
	 * numeric indexes (a list).
	 *
	 * == Python-style formatting
	 *
	 * You can easily format string in Python style by prefixing the <return> with a percent sign (%).
	 *
	 *    %<a href="/data/%s/">%s</a> % strtolower ($value), $value for $value in $Data
	 *
	 * == Returning hashes ==
	 * 
	 * You can also use special syntax of <return>:
	 * 
	 *    {substr ($value, 0, 1) => $value} for $value in $Data
	 * 
	 * Using {$x => $y} as <return> will return an array with keyed indexes (a hash).
	 *
	 * Note: you can use a shortcut of the hash return syntax (without the curly braces) if you do not have any
	 *       array declarations in your code.
	 *
	 * E.g., you can use:
	 *
	 *    substr ($value, 0, 1) => $value for $value in $Data
	 *
	 * with the same result as above. However, if you have any array declarations in your return code (or even
	 * the string 'array') please use the full syntax:
	 *
	 *    { $value => array ($value) } for $value in Data
	 *
	 * Also please note that you can use only scalar values as keys if you choose to return hashes.
   *
   * You can also user the following code:
   *
   *    substr ($value, 0, 1) => [$value] for $value in $Data
   *
   * To create hashes with arrays (lists) of elements (e.g., this code will group values according to its first
   * letter).
	 * 
	 * <key> and <element> should be regular PHP variables that you'll be using in <return> and <condition>.
	 * When cycling through <Data>, each time <key> will be assigned to the current key value, <element> will be
	 * assigned to the current element. <key> is optional.
	 * 
	 * == Other variables ==
	 *
	 * <Data> is the name of variable passed to the list comprehension (discussed below).
	 * 
	 * <condition> could be any expression that is using <element>, <key> or any of the passed variables.
	 * Should return a boolean value. <condition> is optional.
	 * 
	 * == Passing variables to php-lc ==
	 * 
	 * The second argument of the function is a hash of variables that are passed into the list comprehension:
	 * 
	 *    array ('Data' => $Data)
	 *    array ('Req' => $_REQUEST, 'Get' => $_GET, 'Post' => $_POST, 'serverArgCount' => count($_SERVER))
	 *
	 * The variables are known inside the list comprehension by the keys you've given them in the Data array.
	 * 
	 * A shorthand for writing:
	 * 
	 *    array ('Data' => $Data, 'foo' => $foo)
	 * 
	 * is:
	 * 
	 *    compact ('Data', 'foo') 
	 *
	 * @param string $expression List comprehension expression
	 * @param array $Data List comprehension variables
	 * @return array
	 */
  function lc($expression, $Data = array()) {
    return ListComprehension::execute($expression, $Data);
  }
}

class ListComprehension {
	private $expression = '';
	private $Variables = array();
	
	const GLOBAL_ID = '__listcomprehension';

	/**
	 * See description of the lc function above.
	 *
   * @param string $expression List comprehension expression
   * @param array $Data List comprehension variables
   * @return array
 	 */
	public static function execute ($expression, $Data = array()) {
		$ListComprehension = new self ($expression, $Data);
    return $ListComprehension->evaluate();
	}
	
	private function __construct ($expression, $Data = array()) {
		$this->expression = $expression;
		$this->Variables = $Data;
	}
	
	private function evaluate() {
		$Object = array(); $IteratorMatches = array(); $ReturnMatches = array();
		if (!preg_match('#^\s*(.+?)\s+for\s+(.+?)\s+in\s+([^\[\]]+?)(\s+if\s+(.+?))?\s*$#ims', $this->expression, $Object)) return false;
    // print_r ($Object);
		
		$LCObject = new ListComprehensionObject();
		$LCObject->Iterable = $this->Variables[ltrim($Object[3], '$')];
		$LCObject->condition = isset ($Object[5]) ? $Object[5] : true;
		$LCObject->Variables = $this->Variables;
		$LCObject->iteratorName = $Object[2];
		if (preg_match ('#(.+)=>(.+)#', $LCObject->iteratorName, $IteratorMatches)) {
			$LCObject->iteratorName = trim ($IteratorMatches[2]);
			$LCObject->iteratorKeyName = trim ($IteratorMatches[1]);
		}
		
		$LCObject->return = trim($Object[1]);
		if (preg_match ('#^%(.+) % (.+)$#', $LCObject->return, $Matches)) {
			$LCObject->return = "sprintf ('" . $Matches[1] . "', " . $Matches[2] . ')';
		}
		if (preg_match ('#=>#', $LCObject->return)) {
		  if (!preg_match ('#array#i', $LCObject->return)) {
		    $LCObject->return = sprintf ('{%s}', trim ($LCObject->return, '{}'));
		  }
		  if (preg_match ('#^{(.+?)=>(.+)}$#', $LCObject->return, $ReturnMatches)) {
			  $LCObject->return = trim ($ReturnMatches[2]);
			  $LCObject->returnKey = trim ($ReturnMatches[1]);
		  }
      if (preg_match ('#^\[.+\]$#', $LCObject->return)) {
        $LCObject->return = substr ($LCObject->return, 1, -1);
        $LCObject->optionReturnArrayInHash = true;
      }
		}
		
		return $LCObject->run();
	} 
	
}

/**
 * The class for running list comprehension objects. Do not call directly.
 */
class ListComprehensionObject {
	public $Iterable = array();
	public $condition = '';
	public $Variables = array();
	public $iteratorName;
	public $iteratorKeyName = '';
	public $return;
	public $returnKey = '';
  /**
   * @var bool True if syntax of $k => [$v] used.
   */
  public $optionReturnArrayInHash = false;
	
	private $currentIterator;
	
	public function run() {
		if (!is_array ($this->Iterable)) return array();
		$GLOBALS[ListComprehension::GLOBAL_ID] = $this->Variables;
		//print $filterExpression;
		if (!$this->iteratorKeyName) {
			$filterExpression = 'extract ($GLOBALS["'.ListComprehension::GLOBAL_ID.'"]); return (' . $this->condition . ');';
			$filterFunction = create_function($this->iteratorName, $filterExpression);
			$this->Iterable = array_filter($this->Iterable, $filterFunction);
		} else {
			$filterExpression = 'extract ($GLOBALS["'.ListComprehension::GLOBAL_ID.'"]); if (' . $this->condition . ') return array (' .
                          $this->iteratorKeyName . ' => ' . $this->iteratorName . ');';
			$filterFunction = create_function($this->iteratorKeyName . ',' . $this->iteratorName, $filterExpression);
			$this->Iterable = array_map($filterFunction, array_keys($this->Iterable), $this->Iterable);
			$this->Iterable = array_filter ($this->Iterable);
			$Data = array();
			foreach ($this->Iterable as $Arr)
		      $Data[key($Arr)] = current($Arr);
			$this->Iterable = $Data;
		}

    // String manupulation
    while (preg_match ('#(\$[A-z0-9_]+)\[([0-9]*):([0-9]*)\]#', $this->return, $Matches)) {
      $this->return = str_replace ($Matches[0], sprintf ('substr (%s, %s%s%s)', $Matches[1], ($Matches[2] ? $Matches[2] : '0') ,
          ($Matches[3] ? ', ' : ''), ($Matches[3] <= 0 ? $Matches[3] : $Matches[3] - $Matches[2]) ), $this->return);
    }


		if (!$this->returnKey) {
			$returnExpression = 'extract ($GLOBALS["'.ListComprehension::GLOBAL_ID.'"]); return (' . $this->return . ');';
		} else {
			$returnExpression = 'extract ($GLOBALS["'.ListComprehension::GLOBAL_ID.'"]); return array (' . $this->returnKey . ' => ' . $this->return . ');';
		}
			
		if (!$this->iteratorKeyName) {
		  $returnFunction = create_function($this->iteratorName, $returnExpression);
      if (!is_callable($returnFunction))
        die ("Failed to execute the following lc-expression: " . $returnExpression);
		  $this->Iterable = array_map($returnFunction, $this->Iterable);
		} else {
		  $returnFunction = create_function($this->iteratorKeyName . ',' . $this->iteratorName, $returnExpression);
		  $this->Iterable = array_map($returnFunction, array_keys($this->Iterable), $this->Iterable);
		}
		
		unset ($GLOBALS[ListComprehension::GLOBAL_ID]);
		
		if (!$this->returnKey)   
			return array_values($this->Iterable);
			
	  $Data = array();
		foreach ($this->Iterable as $Arr) {
      if (!$this->optionReturnArrayInHash) {
        $Data[key($Arr)] = current($Arr);
        continue;
      }
      // If option return array in hash.
      if (!isset ($Data[key($Arr)]))
        $Data[key($Arr)] = array();
      $Data[key($Arr)][] = current($Arr);
    }

    $this->Iterable = $Data;
	  return $this->Iterable;
	}
	
	
}
