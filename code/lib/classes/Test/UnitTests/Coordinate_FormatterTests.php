<?php

require_once('simpletest/autorun.php');

class Coordinate_FormatterTests extends UnitTestCase
{
  function testFormatNorthHemisphere()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertEqual('N', $formatter->formatLatHem(new Coordinate_Coordinate(1, 2)));
  }

  function testFormatSouthHemisphere()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertEqual('S', $formatter->formatLatHem(new Coordinate_Coordinate(-1, 2)));
  }

  function testFormatEastHemisphere()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertEqual('E', $formatter->formatLonHem(new Coordinate_Coordinate(1, 2)));
  }

  function testFormatWestHemisphere()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertEqual('W', $formatter->formatLonHem(new Coordinate_Coordinate(1, -2)));
  }

  function testFormatLatitudeDegree()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertIdentical('00', $formatter->formatLatDeg(new Coordinate_Coordinate(0, 2)));
    $this->assertIdentical('01', $formatter->formatLatDeg(new Coordinate_Coordinate(1, 2)));
    $this->assertIdentical('10', $formatter->formatLatDeg(new Coordinate_Coordinate(10, 2)));
    $this->assertIdentical('00', $formatter->formatLatDeg(new Coordinate_Coordinate(-0.1, 2)));
    $this->assertIdentical('01', $formatter->formatLatDeg(new Coordinate_Coordinate(-1, 2)));
    $this->assertIdentical('10', $formatter->formatLatDeg(new Coordinate_Coordinate(-10, 2)));
  }

  function testFormatLongitudeDegree()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertIdentical('000', $formatter->formatLonDeg(new Coordinate_Coordinate(2, 0)));
    $this->assertIdentical('001', $formatter->formatLonDeg(new Coordinate_Coordinate(2, 1)));
    $this->assertIdentical('010', $formatter->formatLonDeg(new Coordinate_Coordinate(2, 10)));
    $this->assertIdentical('100', $formatter->formatLonDeg(new Coordinate_Coordinate(2, 100)));
    $this->assertIdentical('000', $formatter->formatLonDeg(new Coordinate_Coordinate(2, -0.1)));
    $this->assertIdentical('001', $formatter->formatLonDeg(new Coordinate_Coordinate(2, -1)));
    $this->assertIdentical('010', $formatter->formatLonDeg(new Coordinate_Coordinate(2, -10)));
    $this->assertIdentical('100', $formatter->formatLonDeg(new Coordinate_Coordinate(2, -100)));
  }

  function testFormatLatitudeMinute()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertIdentical('00.000', $formatter->formatLatMin(Coordinate_Coordinate::fromHemDegMin(true, 1, 0, true, 0, 0)));
    $this->assertIdentical('00.001', $formatter->formatLatMin(Coordinate_Coordinate::fromHemDegMin(true, 1, 0.001, true, 0, 0)));
    $this->assertIdentical('01.000', $formatter->formatLatMin(Coordinate_Coordinate::fromHemDegMin(true, 1, 1, true, 0, 0)));
    $this->assertIdentical('10.000', $formatter->formatLatMin(Coordinate_Coordinate::fromHemDegMin(true, 1, 10, true, 0, 0)));
    $this->assertIdentical('59.999', $formatter->formatLatMin(Coordinate_Coordinate::fromHemDegMin(true, 1, 59.999, true, 0, 0)));
  }

  function testFormatLongitudeMinute()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertIdentical('00.000', $formatter->formatLonMin(Coordinate_Coordinate::fromHemDegMin(true, 0, 0, true, 1, 0)));
    $this->assertIdentical('00.001', $formatter->formatLonMin(Coordinate_Coordinate::fromHemDegMin(true, 0, 0, true, 1, 0.001)));
    $this->assertIdentical('01.000', $formatter->formatLonMin(Coordinate_Coordinate::fromHemDegMin(true, 0, 0, true, 1, 1)));
    $this->assertIdentical('10.000', $formatter->formatLonMin(Coordinate_Coordinate::fromHemDegMin(true, 0, 0, true, 1, 10)));
    $this->assertIdentical('59.999', $formatter->formatLonMin(Coordinate_Coordinate::fromHemDegMin(true, 0, 0, true, 1, 59.999)));
  }

  function testFormatHtml()
  {
    $formatter = new Coordinate_Formatter();

    $this->assertIdentical('N 10&deg; 20.000\' E 030&deg; 40.000\'', $formatter->formatHtml(Coordinate_Coordinate::fromHemDegMin(true, 10, 20, true, 30, 40)));
    $this->assertIdentical('S 15&deg; 25.000\' W 035&deg; 45.000\'', $formatter->formatHtml(Coordinate_Coordinate::fromHemDegMin(false, 15, 25, false, 35, 45)));
  }
}

?>
