<?php

require_once('simpletest/autorun.php');

class Coordinate_CoordinateTests extends UnitTestCase
{
  function testCreateCoordinate()
  {
    $coordinate = new Coordinate_Coordinate(10.25, 20.5);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(10.25, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(20.5, $coordinate->longitude(), 1e-6);
  }

  function testCreateWithRandArgs1()
  {
    $coordinate = new Coordinate_Coordinate(90, 180);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(90, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(180, $coordinate->longitude(), 1e-6);
  }

  function testCreateWithRandArgs2()
  {
    $coordinate = new Coordinate_Coordinate(-90, -180);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(-90, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(-180, $coordinate->longitude(), 1e-6);
  }

  function testCreateWithRandArgs3()
  {
    $coordinate = new Coordinate_Coordinate(89.999999, 179.999999);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(90, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(180, $coordinate->longitude(), 1e-6);
  }

  function testCreateWithTooLargeLatitudeThrowsException()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = new Coordinate_Coordinate(90.000001, 2);
  }

  function testCreateWithTooLargeLatitudeThrowsException2()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = new Coordinate_Coordinate(-90.000001, 2);
  }

  function testCreateWithTooLargeLongitudeThrowsException()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = new Coordinate_Coordinate(1, 180.000001);
  }

  function testCreateWithTooLargeLongitudeThrowsException2()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = new Coordinate_Coordinate(1, -180.000001);
  }

  function testLatDeg()
  {
    $coordinate = new Coordinate_Coordinate(10.25, 2);

    $this->assertTrue($coordinate->latHem());
    $this->assertEqual(10, $coordinate->latDeg());
  }

  function testNegativeLatDeg()
  {
    $coordinate = new Coordinate_Coordinate(-10.25, 2);

    $this->assertFalse($coordinate->latHem());
    $this->assertEqual(10, $coordinate->latDeg());
  }

  function testLatDegIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(15.999991, 2);

    $this->assertEqual(15, $coordinate->latDeg());
  }

  function testLatDegIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(15.999992, 2);

    $this->assertEqual(16, $coordinate->latDeg());
  }

  function testNegativeLatDegIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(-15.999991, 2);

    $this->assertEqual(15, $coordinate->latDeg());
  }

  function testNegativeLatDegIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(-15.999992, 2);

    $this->assertEqual(16, $coordinate->latDeg());
  }

  function testLonDeg()
  {
    $coordinate = new Coordinate_Coordinate(1, 20.30);

    $this->assertTrue($coordinate->lonHem());
    $this->assertEqual(20, $coordinate->lonDeg());
  }

  function testNegativeLonDeg()
  {
    $coordinate = new Coordinate_Coordinate(1, -20.30);

    $this->assertFalse($coordinate->lonHem());
    $this->assertEqual(20, $coordinate->lonDeg());
  }

  function testLonDegIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(1, 21.999991);

    $this->assertEqual(21, $coordinate->lonDeg());
  }

  function testLonDegIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(1, 21.999992);

    $this->assertEqual(22, $coordinate->lonDeg());
  }

  function testNegativeLonDegIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(1, -21.999991);

    $this->assertEqual(21, $coordinate->lonDeg());
  }

  function testNegativeLonDegIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(1, -21.999992);

    $this->assertEqual(22, $coordinate->lonDeg());
  }

  function testLatMin()
  {
    $coordinate = new Coordinate_Coordinate(10.25, 2);

    $this->assertWithinMargin(15, $coordinate->latMin(), 1e-6);
  }

  function testNegativeLatMin()
  {
    $coordinate = new Coordinate_Coordinate(-10.25, 2);

    $this->assertWithinMargin(15, $coordinate->latMin(), 1e-6);
  }

  function testLatMinIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(15.999991, 2);

    $this->assertWithinMargin(59.999, $coordinate->latMin(), 5e-4);
  }

  function testLatMinIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(15.999992, 2);

    $this->assertWithinMargin(0, $coordinate->latMin(), 5e-4);
  }

  function testNegativeLatMinIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(-15.999991, 2);

    $this->assertWithinMargin(59.999, $coordinate->latMin(), 5e-4);
  }

  function testNegativeLatMinIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(-15.999992, 2);

    $this->assertWithinMargin(0, $coordinate->latMin(), 5e-4);
  }

  function testLonMin()
  {
    $coordinate = new Coordinate_Coordinate(1, 20.5);

    $this->assertWithinMargin(30, $coordinate->lonMin(), 1e-6);
  }

  function testNegativeLonMin()
  {
    $coordinate = new Coordinate_Coordinate(1, -20.25);

    $this->assertWithinMargin(15, $coordinate->lonMin(), 1e-6);
  }

  function testLonMinIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(1, 21.999991);

    $this->assertWithinMargin(59.999, $coordinate->lonMin(), 5e-4);
  }

  function testLonMinIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(1, 21.999992);

    $this->assertWithinMargin(0, $coordinate->lonMin(), 5e-4);
  }

  function testNegativeLonMinIsRounded()
  {
    $coordinate = new Coordinate_Coordinate(1, -21.999991);

    $this->assertWithinMargin(59.999, $coordinate->lonMin(), 5e-4);
  }

  function testNegativeLonMinIsRounded2()
  {
    $coordinate = new Coordinate_Coordinate(1, -21.999992);

    $this->assertWithinMargin(0, $coordinate->lonMin(), 5e-4);
  }

  function testCreateFromHemDegMin()
  {
    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, 20, 30);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(10.25, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(20.5, $coordinate->longitude(), 1e-6);
  }

  function testCreateFromNegativeDegMin()
  {
    $coordinate = Coordinate_Coordinate::fromHemDegMin(false, 10, 15, false, 20, 30);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(-10.25, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(-20.5, $coordinate->longitude(), 1e-6);
  }

  function testCreateFromRandDegMin()
  {
    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 90, 0, true, 180, 0);

    $this->assertNotNull($coordinate);
    $this->assertWithinMargin(90, $coordinate->latitude(), 1e-6);
    $this->assertWithinMargin(180, $coordinate->longitude(), 1e-6);
  }

  function testFromDegMinThrowsExceptionIfNotIntegerLatDeg()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10.1, 15, true, 20, 30);
  }

  function testFromDegMinThrowsExceptionIfNotIntegerLonDeg()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, 20.2, 30);
  }

  function testFromDegMinThrowsExceptionIfNegativeLatDeg()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, -10, 15, true, 20, 30);
  }

  function testFromDegMinThrowsExceptionIfNegativeLonDeg()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, -20, 30);
  }

  function testFromDegMinThrowsExceptionIfTooLargeLatDeg()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 91, 15, true, 20, 30);
  }

  function testFromDegMinThrowsExceptionIfTooLargeLonDeg()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, 181, 30);
  }

  function testFromDegMinThrowsExceptionIfTooLargeLatMin()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 60, true, 20, 30);
  }

  function testFromDegMinThrowsExceptionIfTooLargeLonMin()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, 20, 60);
  }

  function testFromDegMinThrowsExceptionIfNegativeLatMin()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, -0.001, true, 20, 30);
  }

  function testFromDegMinThrowsExceptionIfNegativeLonMin()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, 20, -0.001);
  }
  
  function testFromDegMinThrowsExceptionIfTooLargeLatitude()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 90, 0.001, true, 20, 30);
  }

  function testFromDegMinThrowsExceptionIfTooLargeLongitude()
  {
    $this->expectException(new InvalidArgumentException());

    $coordinate = Coordinate_Coordinate::fromHemDegMin(true, 10, 15, true, 180, 0.001);
  }

  function testEqauls()
  {
    $coordinate1 = new Coordinate_Coordinate(1, 2);
    $coordinate2 = new Coordinate_Coordinate(1, 2);
    $coordinate3 = new Coordinate_Coordinate(2, 1);

    $this->assertEqual($coordinate1, $coordinate2);
    $this->assertNotEqual($coordinate1, $coordinate3);
  }

  function testEqauls2()
  {
    $coordinate1 = new Coordinate_Coordinate(0.1, 2);
    $coordinate2 = new Coordinate_Coordinate(-0.1, 2);

    $this->assertNotEqual($coordinate1, $coordinate2);
    $this->assertEqual($coordinate1, Coordinate_Coordinate::fromHemDegMin($coordinate1->latHem(), $coordinate1->latDeg(), $coordinate1->latMin(), $coordinate1->lonHem(), $coordinate1->lonDeg(), $coordinate1->lonMin()));
    $this->assertEqual($coordinate2, Coordinate_Coordinate::fromHemDegMin($coordinate2->latHem(), $coordinate2->latDeg(), $coordinate2->latMin(), $coordinate2->lonHem(), $coordinate2->lonDeg(), $coordinate2->lonMin()));
  }
}

?>
