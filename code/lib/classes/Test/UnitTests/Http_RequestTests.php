<?php

require_once('simpletest/autorun.php');

class Http_RequestTestsSimpleValidator
{
  public $retval;
  public $validateCalled = false;
  public $validateValue;

  public function __construct($retval)
  {
    $this->retval = $retval;
  }

  public function isValid($value)
  {
    $this->validateCalled = true;
    $this->validateValue = $value;

    return $this->retval;
  }
}

class Http_RequestTests extends UnitTestCase
{
  function tearDown()
  {
    unset($_POST);
    unset($_GET);
  }

  function testGetReturnsEmptyDefaultValue()
  {
    $request = new Http_Request();

    $this->assertEqual('', $request->get('dummy'));
  }

  function testGetReturnsDefaultValue()
  {
    $request = new Http_Request();

    $this->assertEqual('1', $request->get('dummy', '1'));
    $this->assertEqual(2, $request->get('dummy', 2));
  }

  function testGetReturnsSetValue()
  {
    $request = new Http_Request();

    $request->set('key1', '2');
    $this->assertEqual('2', $request->get('key1'));

    $request->set('key1', 3);
    $this->assertEqual(3, $request->get('key1'));
  }

  function testGetReturnsDefaultValueIfNotPostIsValidated()
  {
    $_POST['key5'] = '7';

    $request = new Http_Request();

    $this->assertEqual('5', $request->get('key4', '5'));
  }

  function testRequestIsCreatedFromPost()
  {
    $_POST['key2'] = '4';

    $request = new Http_Request();

    $this->assertEqual('4', $request->getForValidation('key2'));
  }

  function testRequestCopiesPost()
  {
    $_POST['key4'] = '6';

    $request = new Http_Request();
    unset($_POST);

    $this->assertEqual('6', $request->getForValidation('key4'));
  }

  function testRequestIsCreatedFromGet()
  {
    $_GET['key3'] = '5';

    $request = new Http_Request();

    $this->assertEqual('5', $request->getForValidation('key3'));
  }

  function testRequestCopiesGet()
  {
    $_GET['key4'] = '7';

    $request = new Http_Request();
    unset($_GET);

    $this->assertEqual('7', $request->getForValidation('key4'));
  }

  function testGetReturnsValidatedPostValue()
  {
    $_POST['key6'] = '8';

    $request = new Http_Request();

    $request->validate('key6', new Http_RequestTestsSimpleValidator(true));

    $this->assertEqual('8', $request->get('key6', '5'));
  }

  function testGetReturnsDefaultIfInvalidPostValue()
  {
    $_POST['key7'] = '8';

    $request = new Http_Request();

    $request->validate('key7', new Http_RequestTestsSimpleValidator(false));

    $this->assertEqual('5', $request->get('key7', '5'));
  }

  function testValidatesDefaultValue()
  {
    $request = new Http_Request();
    $validator = new Http_RequestTestsSimpleValidator(true);

    $request->validate('key8', $validator);

    $this->assertTrue($validator->validateCalled);
    $this->assertEqual(false, $validator->validateValue);
  }
  
  function testValidateReturnsResultFromValidator()
  {
    $request = new Http_Request();

    $this->assertTrue($request->validate('key7', new Http_RequestTestsSimpleValidator(true)));
    $this->assertFalse($request->validate('key7', new Http_RequestTestsSimpleValidator(false)));
  }
}

?>
