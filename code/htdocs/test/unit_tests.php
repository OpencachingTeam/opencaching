<?php
 
function __autoload($class_name)
{
  $class_name = str_replace('_', '/', $class_name);
  require_once $_SERVER['DOCUMENT_ROOT'] . '/../lib/classes/' . $class_name . '.php';
}

set_include_path($_SERVER['DOCUMENT_ROOT'] . '/../lib/');

 Test_UnitTests::init();

?>
