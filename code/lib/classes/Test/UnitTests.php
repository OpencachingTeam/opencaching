<?php

require_once('simpletest/autorun.php');

class Test_UnitTests extends TestSuite
{
  function __construct()
  {
    parent::__construct();
    $this->collect(dirname(__FILE__) . '/UnitTests', new SimplePatternCollector('/Tests.php/'));
  }

  static public function init()
  {
  }
}

?>
