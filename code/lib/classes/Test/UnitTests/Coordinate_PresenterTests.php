<?php

require_once('simpletest/autorun.php');

Mock::generate('Language_Translator');

class Coordinate_PresenterTests extends UnitTestCase
{
  private $tpl_values;

  function setUp()
  {
    $this->tpl_values = array();
  }

  private function createValidRequest($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $request = new Http_Request();

    $request->set(Coordinate_Presenter::lat_hem, $lat_hem);
    $request->set(Coordinate_Presenter::lat_deg, $lat_deg);
    $request->set(Coordinate_Presenter::lat_min, $lat_min);
    $request->set(Coordinate_Presenter::lon_hem, $lon_hem);
    $request->set(Coordinate_Presenter::lon_deg, $lon_deg);
    $request->set(Coordinate_Presenter::lon_min, $lon_min);

    return $request;
  }

  private function createRawRequest($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $_POST[Coordinate_Presenter::lat_hem] = $lat_hem;
    $_POST[Coordinate_Presenter::lat_deg] = $lat_deg;
    $_POST[Coordinate_Presenter::lat_min] = $lat_min;
    $_POST[Coordinate_Presenter::lon_hem] = $lon_hem;
    $_POST[Coordinate_Presenter::lon_deg] = $lon_deg;
    $_POST[Coordinate_Presenter::lon_min] = $lon_min;

    return new Http_Request();
  }

  public function assign($tpl_var, $value)
  {
    $this->tpl_values[$tpl_var] = $value;
  }

  public function setError($error)
  {
    $this->tpl_values['coord_error'] = $error;
  }

  function testLatLonCanBeSet()
  {
    $presenter = new Coordinate_Presenter();

    $presenter->init(1, 2);

    $this->assertEqual(new Coordinate_Coordinate(1, 2), $presenter->getCoordinate());
  }

  function testLatLonAreReadFromRequest()
  {
    $request = $this->createValidRequest('N', 2, 33, 'E', 3, 45);
    $presenter = new Coordinate_Presenter($request);

    $this->assertEqual(new Coordinate_Coordinate(2.55, 3.75), $presenter->getCoordinate());
  }

  function testLatLonAreReadFromRequestSouth()
  {
    $request = $this->createValidRequest('S', 2, 33, 'E', 3, 45);
    $presenter = new Coordinate_Presenter($request);

    $this->assertEqual(new Coordinate_Coordinate(-2.55, 3.75), $presenter->getCoordinate());
  }

  function testLatLonAreReadFromRequestWest()
  {
    $request = $this->createValidRequest('N', 2, 33, 'W', 3, 45);
    $presenter = new Coordinate_Presenter($request);

    $this->assertEqual(new Coordinate_Coordinate(2.55, -3.75), $presenter->getCoordinate());
  }

  function testLatLonAreSetByRequestNotInit()
  {
    $request = $this->createValidRequest('N', 2, 33, 'E', 3, 45);
    $presenter = new Coordinate_Presenter($request);

    $presenter->init(1, 2);

    $this->assertEqual(new Coordinate_Coordinate(2.55, 3.75), $presenter->getCoordinate());
  }

  function testPresenterPreparesView()
  {
    $presenter = new Coordinate_Presenter();
    $presenter->init(1.33, 123.3456);

    $presenter->prepare($this);

    $this->assertIdentical('N', $this->tpl_values[Coordinate_Presenter::lat_hem]);
    $this->assertIdentical('01', $this->tpl_values[Coordinate_Presenter::lat_deg]);
    $this->assertIdentical('19.800', $this->tpl_values[Coordinate_Presenter::lat_min]);
    $this->assertIdentical('E', $this->tpl_values[Coordinate_Presenter::lon_hem]);
    $this->assertIdentical('123', $this->tpl_values[Coordinate_Presenter::lon_deg]);
    $this->assertIdentical('20.736', $this->tpl_values[Coordinate_Presenter::lon_min]);
  }

  function testPresenterPreparesView2()
  {
    $presenter = new Coordinate_Presenter();
    $presenter->init(1.33, 12.3456);

    $presenter->prepare($this);

    $this->assertIdentical('N', $this->tpl_values[Coordinate_Presenter::lat_hem]);
    $this->assertIdentical('01', $this->tpl_values[Coordinate_Presenter::lat_deg]);
    $this->assertIdentical('19.800', $this->tpl_values[Coordinate_Presenter::lat_min]);
    $this->assertIdentical('E', $this->tpl_values[Coordinate_Presenter::lon_hem]);
    $this->assertIdentical('012', $this->tpl_values[Coordinate_Presenter::lon_deg]);
    $this->assertIdentical('20.736', $this->tpl_values[Coordinate_Presenter::lon_min]);
  }

  function testPresenterPreparesViewSouth()
  {
    $presenter = new Coordinate_Presenter();
    $presenter->init(-1.33, 123.3456);

    $presenter->prepare($this);

    $this->assertIdentical('S', $this->tpl_values[Coordinate_Presenter::lat_hem]);
    $this->assertIdentical('01', $this->tpl_values[Coordinate_Presenter::lat_deg]);
    $this->assertIdentical('19.800', $this->tpl_values[Coordinate_Presenter::lat_min]);
    $this->assertIdentical('E', $this->tpl_values[Coordinate_Presenter::lon_hem]);
    $this->assertIdentical('123', $this->tpl_values[Coordinate_Presenter::lon_deg]);
    $this->assertIdentical('20.736', $this->tpl_values[Coordinate_Presenter::lon_min]);
  }

  function testPresenterPreparesViewWest()
  {
    $presenter = new Coordinate_Presenter();
    $presenter->init(1.33, -12.3456);

    $presenter->prepare($this);

    $this->assertIdentical('N', $this->tpl_values[Coordinate_Presenter::lat_hem]);
    $this->assertIdentical('01', $this->tpl_values[Coordinate_Presenter::lat_deg]);
    $this->assertIdentical('19.800', $this->tpl_values[Coordinate_Presenter::lat_min]);
    $this->assertIdentical('W', $this->tpl_values[Coordinate_Presenter::lon_hem]);
    $this->assertIdentical('012', $this->tpl_values[Coordinate_Presenter::lon_deg]);
    $this->assertIdentical('20.736', $this->tpl_values[Coordinate_Presenter::lon_min]);
  }

  function testPresenterPreparesViewFromRequest()
  {
    $request = $this->createValidRequest('N', '12', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $presenter->prepare($this);

    $this->assertIdentical('N', $this->tpl_values[Coordinate_Presenter::lat_hem]);
    $this->assertIdentical('12', $this->tpl_values[Coordinate_Presenter::lat_deg]);
    $this->assertIdentical('33.456', $this->tpl_values[Coordinate_Presenter::lat_min]);
    $this->assertIdentical('E', $this->tpl_values[Coordinate_Presenter::lon_hem]);
    $this->assertIdentical('031', $this->tpl_values[Coordinate_Presenter::lon_deg]);
    $this->assertIdentical('45.123', $this->tpl_values[Coordinate_Presenter::lon_min]);
  }

  function testPresenterValidatesRawRequest()
  {
    $request = $this->createRawRequest('N', '12', '00.000', 'E', '031', '59.999');
    $presenter = new Coordinate_Presenter($request);

    $presenter->validate();

    $this->assertIdentical('N', $request->get(Coordinate_Presenter::lat_hem));
    $this->assertIdentical('12', $request->get(Coordinate_Presenter::lat_deg));
    $this->assertIdentical('00.000', $request->get(Coordinate_Presenter::lat_min));
    $this->assertIdentical('E', $request->get(Coordinate_Presenter::lon_hem));
    $this->assertIdentical('031', $request->get(Coordinate_Presenter::lon_deg));
    $this->assertIdentical('59.999', $request->get(Coordinate_Presenter::lon_min));
  }

  function testPresenterReturnsResultOfValidation()
  {
    $request = $this->createRawRequest('N', '12', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertTrue($presenter->validate());
  }

  function testSouthLatHemIsValid()
  {
    $request = $this->createRawRequest('S', '12', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertTrue($presenter->validate());
  }

  function testEmptyLatHemIsNotValid()
  {
    $request = $this->createRawRequest('', '12', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testEastLatHemIsNotValid()
  {
    $request = $this->createRawRequest('E', '12', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testNorthSouthLatHemIsNotValid()
  {
    $request = $this->createRawRequest('NS', '12', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testWestLonHemIsValid()
  {
    $request = $this->createRawRequest('N', '12', '33.456', 'W', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertTrue($presenter->validate());
  }

  function testEmptyLonHemIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '33.456', '', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testSouthLonHemIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '33.456', 'S', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testEastWestLonHemIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '33.456', 'EW', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testNegativeLatDegIsNotValid()
  {
    $request = $this->createRawRequest('N', '-1', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lat_deg));
  }

  function testTooLargeLatDegIsNotValid()
  {
    $request = $this->createRawRequest('N', '91', '33.456', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lat_deg));
  }

  function testNegativeLonDegIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '33.456', 'E', '-001', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lon_deg));
  }

  function testTooLargeLonDegIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '33.456', 'E', '181', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lon_deg));
  }

  function testNegativeLatMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '-33.4', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lat_min));
  }

  function testTooLargeLatMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '60', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lat_min));
  }

  function testTooLongIntLatMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '023', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lat_min));
  }

  function testTooLongDecLatMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '13.4567', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lat_min));
  }

  function testNegativeLonMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '33.4', 'E', '031', '-4.13');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lon_min));
  }

  function testTooLargeLonMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '1', 'E', '031', '60');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lon_min));
  }

  function testTooLongIntLonMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '03', 'E', '031', '023');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lon_min));
  }

  function testTooLongDecLonMinIsNotValid()
  {
    $request = $this->createRawRequest('N', '12', '13.467', 'E', '031', '45.1234');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertFalse($request->get(Coordinate_Presenter::lon_min));
  }

  function testAllAreValidated()
  {
    $request = $this->createRawRequest('N', '12', '-33.4', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $this->assertIdentical('12', $request->get(Coordinate_Presenter::lat_deg));
    $this->assertFalse($request->get(Coordinate_Presenter::lat_min));
    $this->assertIdentical('031', $request->get(Coordinate_Presenter::lon_deg));
    $this->assertIdentical('45.123', $request->get(Coordinate_Presenter::lon_min));
  }

  function testPrepareDoesNotSetErrorIfValid()
  {
    $request = $this->createRawRequest('N', '12', '33.4', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertTrue($presenter->validate());
    $presenter->prepare($this);

    $this->assertFalse(isset($this->tpl_values['coord_error']));
  }

  function testPrepareDoesNotSetErrorIfNotValidated()
  {
    $presenter = new Coordinate_Presenter();

    $presenter->prepare($this);

    $this->assertFalse(isset($this->tpl_values['coord_error']));
  }

  function testPrepareSetsErrorIfNotValid()
  {
    $request = $this->createRawRequest('N', '12', '-33.4', 'E', '031', '45.123');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
    $presenter->prepare($this);

    $this->assertTrue(isset($this->tpl_values['coord_error']));
  }

  function testZeroCoordinateIsNotValid()
  {
    $request = $this->createRawRequest('N', '0', '0', 'E', '0', '0');
    $presenter = new Coordinate_Presenter($request);

    $this->assertFalse($presenter->validate());
  }

  function testErrorMessageIsTranslated()
  {
    $translator = new MockLanguage_Translator();
    $translator->setReturnValue('translate', 'Coordinate Error');
    $translator->expectOnce('translate', array('bad_coordinates'));

    $request = $this->createRawRequest('N', '0', '0', 'E', '0', '0');
    $presenter = new Coordinate_Presenter($request, $translator);

    $this->assertFalse($presenter->validate());
    $presenter->prepare($this);

    $this->assertEqual('Coordinate Error', $this->tpl_values['coord_error']);
  }
}

?>
