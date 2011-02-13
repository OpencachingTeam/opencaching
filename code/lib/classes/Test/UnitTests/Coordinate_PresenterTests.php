<?php

require_once('simpletest/autorun.php');

class Coordinate_PresenterTests extends UnitTestCase
{
  private $tpl_values;
  private $request;
  private $translator;

  function setUp()
  {
    $this->tpl_values = array();
    $this->request = new Http_Request();
    $this->translator = new Test_Translator();
  }

  private function createPresenter()
  {
    $presenter = new Coordinate_Presenter($this->request, $this->translator);

    return $presenter;
  }

  private function setUpValidRequest($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $this->request->set(Coordinate_Presenter::lat_hem, $lat_hem);
    $this->request->set(Coordinate_Presenter::lat_deg, $lat_deg);
    $this->request->set(Coordinate_Presenter::lat_min, $lat_min);
    $this->request->set(Coordinate_Presenter::lon_hem, $lon_hem);
    $this->request->set(Coordinate_Presenter::lon_deg, $lon_deg);
    $this->request->set(Coordinate_Presenter::lon_min, $lon_min);
  }

  private function setUpRawRequest($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $this->request->setForValidation(Coordinate_Presenter::lat_hem, $lat_hem);
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, $lat_deg);
    $this->request->setForValidation(Coordinate_Presenter::lat_min, $lat_min);
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, $lon_hem);
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, $lon_deg);
    $this->request->setForValidation(Coordinate_Presenter::lon_min, $lon_min);
  }

  public function assign($tpl_var, $value)
  {
    $this->tpl_values[$tpl_var] = $value;
  }

  function testLatLonCanBeSet()
  {
    $presenter = $this->createPresenter();

    $presenter->init(1, 2);

    $this->assertEqual(new Coordinate_Coordinate(1, 2), $presenter->getCoordinate());
  }

  function testLatLonAreReadFromRequest()
  {
    $this->setUpValidRequest('N', 2, 33, 'E', 3, 45);
    $presenter = $this->createPresenter();

    $this->assertEqual(new Coordinate_Coordinate(2.55, 3.75), $presenter->getCoordinate());
  }

  function testLatLonAreReadFromRequestSouth()
  {
    $this->setUpValidRequest('S', 2, 33, 'E', 3, 45);
    $presenter = $this->createPresenter();

    $this->assertEqual(new Coordinate_Coordinate(-2.55, 3.75), $presenter->getCoordinate());
  }

  function testLatLonAreReadFromRequestWest()
  {
    $this->setUpValidRequest('N', 2, 33, 'W', 3, 45);
    $presenter = $this->createPresenter();

    $this->assertEqual(new Coordinate_Coordinate(2.55, -3.75), $presenter->getCoordinate());
  }

  function testLatLonAreSetByRequestNotInit()
  {
    $this->setUpValidRequest('N', 2, 33, 'E', 3, 45);
    $presenter = $this->createPresenter();

    $presenter->init(1, 2);

    $this->assertEqual(new Coordinate_Coordinate(2.55, 3.75), $presenter->getCoordinate());
  }

  function testPresenterPreparesView()
  {
    $presenter = $this->createPresenter();
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
    $presenter = $this->createPresenter();
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
    $presenter = $this->createPresenter();
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
    $presenter = $this->createPresenter();
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
    $this->setUpValidRequest('N', '12', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

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
    $this->setUpRawRequest('N', '12', '00.000', 'E', '031', '59.999');
    $presenter = $this->createPresenter();

    $presenter->validate();

    $this->assertIdentical('N', $this->request->get(Coordinate_Presenter::lat_hem));
    $this->assertIdentical('12', $this->request->get(Coordinate_Presenter::lat_deg));
    $this->assertIdentical('00.000', $this->request->get(Coordinate_Presenter::lat_min));
    $this->assertIdentical('E', $this->request->get(Coordinate_Presenter::lon_hem));
    $this->assertIdentical('031', $this->request->get(Coordinate_Presenter::lon_deg));
    $this->assertIdentical('59.999', $this->request->get(Coordinate_Presenter::lon_min));
  }

  function testPresenterReturnsResultOfValidation()
  {
    $this->setUpRawRequest('N', '12', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());
  }

  function testSouthLatHemIsValid()
  {
    $this->setUpRawRequest('S', '12', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());
  }

  function testEmptyLatHemIsNotValid()
  {
    $this->setUpRawRequest('', '12', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testEastLatHemIsNotValid()
  {
    $this->setUpRawRequest('E', '12', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testNorthSouthLatHemIsNotValid()
  {
    $this->setUpRawRequest('NS', '12', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testWestLonHemIsValid()
  {
    $this->setUpRawRequest('N', '12', '33.456', 'W', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());
  }

  function testEmptyLonHemIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '33.456', '', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testSouthLonHemIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '33.456', 'S', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testEastWestLonHemIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '33.456', 'EW', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testNegativeLatDegIsNotValid()
  {
    $this->setUpRawRequest('N', '-1', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_deg));
  }

  function testTooLargeLatDegIsNotValid()
  {
    $this->setUpRawRequest('N', '91', '33.456', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_deg));
  }

  function testNegativeLonDegIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '33.456', 'E', '-001', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lon_deg));
  }

  function testTooLargeLonDegIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '33.456', 'E', '181', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lon_deg));
  }

  function testNegativeLatMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '-33.4', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_min));
  }

  function testTooLargeLatMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '60', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_min));
  }

  function testTooLongIntLatMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '023', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_min));
  }

  function testTooLongDecLatMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '13.4567', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_min));
  }

  function testNegativeLonMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '33.4', 'E', '031', '-4.13');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lon_min));
  }

  function testTooLargeLonMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '1', 'E', '031', '60');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lon_min));
  }

  function testTooLongIntLonMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '03', 'E', '031', '023');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lon_min));
  }

  function testTooLongDecLonMinIsNotValid()
  {
    $this->setUpRawRequest('N', '12', '13.467', 'E', '031', '45.1234');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertFalse($this->request->get(Coordinate_Presenter::lon_min));
  }

  function testAllAreValidated()
  {
    $this->setUpRawRequest('N', '12', '-33.4', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $this->assertIdentical('12', $this->request->get(Coordinate_Presenter::lat_deg));
    $this->assertFalse($this->request->get(Coordinate_Presenter::lat_min));
    $this->assertIdentical('031', $this->request->get(Coordinate_Presenter::lon_deg));
    $this->assertIdentical('45.123', $this->request->get(Coordinate_Presenter::lon_min));
  }

  function testPrepareDoesNotSetErrorIfValid()
  {
    $this->setUpRawRequest('N', '12', '33.4', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());
    $presenter->prepare($this);

    $this->assertFalse(isset($this->tpl_values['coord_error']));
  }

  function testPrepareDoesNotSetErrorIfNotValidated()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertFalse(isset($this->tpl_values['coord_error']));
  }

  function testPrepareSetsErrorIfNotValid()
  {
    $this->setUpRawRequest('N', '12', '-33.4', 'E', '031', '45.123');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $presenter->prepare($this);

    $this->assertTrue(isset($this->tpl_values['coord_error']));
  }

  function testZeroCoordinateIsNotValid()
  {
    $this->setUpRawRequest('N', '0', '0', 'E', '0', '0');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testErrorMessageIsTranslated()
  {
    $this->setUpRawRequest('N', '0', '0', 'E', '0', '0');
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
    $presenter->prepare($this);

    $this->assertEqual('Invalid coordinate tr', $this->tpl_values['coord_error']);
  }
}

?>
