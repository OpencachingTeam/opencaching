<?php
function __autoload($class_name)
{
  global $opt;

  $class_name = str_replace('_', '/', $class_name);

  require_once('classes/' . $class_name . '.php');
}

set_include_path($opt['rootpath'] . '../lib/');

Test_UnitTests::init();

?>
