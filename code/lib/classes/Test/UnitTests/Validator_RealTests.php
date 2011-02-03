<?php
require_once('simpletest/autorun.php');

class Validator_RealTests extends UnitTestCase
{
  function testIntegersAreValidByDefault()
  {
    $validator = new Validator_Real();

    $this->assertTrue($validator->isValid('0'));
    $this->assertTrue($validator->isValid('0'));
    $this->assertTrue($validator->isValid('1'));
    $this->assertTrue($validator->isValid('10'));
    $this->assertTrue($validator->isValid('12345678'));
    $this->assertTrue($validator->isValid('-1234'));
  }

  function testRealsAreValidByDefault()
  {
    $validator = new Validator_Real();

    $this->assertTrue($validator->isValid('0.1'));
    $this->assertTrue($validator->isValid('-1.2'));
    $this->assertTrue($validator->isValid('1,3'));
  }

  function testNonRealsAreNotValidByDefault()
  {
    $validator = new Validator_Real();

    $this->assertFalse($validator->isValid(''));
    $this->assertFalse($validator->isValid('-'));
    $this->assertFalse($validator->isValid('a'));
    $this->assertFalse($validator->isValid('1.'));
    $this->assertFalse($validator->isValid('1.2,1'));
    $this->assertFalse($validator->isValid('123_45678'));
    $this->assertFalse($validator->isValid('--123'));
    $this->assertFalse($validator->isValid('-123a4'));
  }

  function testRealsWithinMinMaxAreValid()
  {
    $validator = new Validator_Real(10.5, 20.5);

    $this->assertTrue($validator->isValid('10,5'));
    $this->assertTrue($validator->isValid('20.5'));
    $this->assertTrue($validator->isValid('15'));
  }

  function testValidateWithMinAsZero()
  {
    $validator = new Validator_Real(0, 10);

    $this->assertFalse($validator->isValid('-0.1'));
    $this->assertTrue($validator->isValid('0'));
  }

  function testValidateWithMaxAsZero()
  {
    $validator = new Validator_Real(-10, 0);

    $this->assertTrue($validator->isValid('0'));
    $this->assertFalse($validator->isValid('0,1'));
  }

  function testRealsOutsideMinMaxAreNotValid()
  {
    $validator = new Validator_Real(10.5, 20.5);

    $this->assertFalse($validator->isValid('10.4'));
    $this->assertFalse($validator->isValid('20,6'));
    $this->assertFalse($validator->isValid('0'));
  }

  function testThrowsExceptionIfMaxIsLessThanMin()
  {
    $this->expectException(new InvalidArgumentException());

    $validator = new Validator_Real(20, 10);
  }

  function testValidateLength()
  {
    $validator = new Validator_Real(false, false, '{1,2}', '{1,3}');

    $this->assertTrue($validator->isValid('1.2'));
    $this->assertTrue($validator->isValid('12.3'));
    $this->assertTrue($validator->isValid('1.234'));
    $this->assertTrue($validator->isValid('12.345'));
    $this->assertFalse($validator->isValid('123.4'));
    $this->assertFalse($validator->isValid('12,3456'));
    $this->assertTrue($validator->isValid('0'));
    $this->assertTrue($validator->isValid('20'));
  }
}
?>