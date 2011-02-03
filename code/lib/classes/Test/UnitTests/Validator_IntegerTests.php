<?php
require_once('simpletest/autorun.php');

class Validator_IntegerTests extends UnitTestCase
{
  function testIntegersAreValidByDefault()
  {
    $validator = new Validator_Integer();

    $this->assertTrue($validator->isValid('0'));
    $this->assertTrue($validator->isValid('00'));
    $this->assertTrue($validator->isValid('1'));
    $this->assertTrue($validator->isValid('10'));
    $this->assertTrue($validator->isValid('12345678'));
    $this->assertTrue($validator->isValid('-1234'));
  }

  function testNonIntegersAreNotValidByDefault()
  {
    $validator = new Validator_Integer();

    $this->assertFalse($validator->isValid(''));
    $this->assertFalse($validator->isValid('-'));
    $this->assertFalse($validator->isValid('a'));
    $this->assertFalse($validator->isValid('1.2'));
    $this->assertFalse($validator->isValid('123_45678'));
    $this->assertFalse($validator->isValid('--123'));
    $this->assertFalse($validator->isValid('-123,4'));
  }

  function testIntegersWithinMinMaxAreValid()
  {
    $validator = new Validator_Integer(10, 20);

    $this->assertTrue($validator->isValid('10'));
    $this->assertTrue($validator->isValid('20'));
    $this->assertTrue($validator->isValid('15'));
  }

  function testValidateWithMinAsZero()
  {
    $validator = new Validator_Integer(0, 10);

    $this->assertFalse($validator->isValid('-1'));
    $this->assertTrue($validator->isValid('0'));
    $this->assertTrue($validator->isValid('1'));
  }

  function testValidateWithMaxAsZero()
  {
    $validator = new Validator_Integer(-10, 0);

    $this->assertTrue($validator->isValid('-1'));
    $this->assertTrue($validator->isValid('0'));
    $this->assertFalse($validator->isValid('1'));
  }

  function testIntegerSameAsEqualMinMaxIsValid()
  {
    $validator = new Validator_Integer(15, 15);

    $this->assertTrue($validator->isValid('15'));
  }

  function testIntegersOutsideMinMaxAreNotValid()
  {
    $validator = new Validator_Integer(10, 20);

    $this->assertFalse($validator->isValid('9'));
    $this->assertFalse($validator->isValid('21'));
    $this->assertFalse($validator->isValid('0'));
  }

  function testThrowsExceptionIfMaxIsLessThanMin()
  {
    $this->expectException(new InvalidArgumentException());

    $validator = new Validator_Integer(20, 10);
  }

  function testValidateLength()
  {
    $validator = new Validator_Integer(false, false, '{1,2}');

    $this->assertTrue($validator->isValid('1'));
    $this->assertTrue($validator->isValid('12'));
    $this->assertFalse($validator->isValid('123'));
  }
}
?>