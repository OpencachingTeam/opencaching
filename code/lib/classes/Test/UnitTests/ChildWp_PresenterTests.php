<?php

require_once('simpletest/autorun.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib2/error.inc.php';

Mock::generate('ChildWp_Handler');
Mock::generate('Cache_Manager');

class ChildWp_PresenterTests extends UnitTestCase
{
  private $errorCode;
  private $values;
  private $request;
  private $translator;

  public function assign($tpl_var, $value)
  {
    $this->values[$tpl_var] = $value;
  }

  public function error($errorCode)
  {
    $this->errorCode = $errorCode;
  }

  function setUp()
  {
    $this->errorCode = 0;
    $this->values = array();
    $this->request = new Http_Request();
    $this->translator = new Test_Translator();
  }

  private function createPresenter()
  {
    return new ChildWp_Presenter($this->request, $this->translator);
  }

  function testSetZeroCoordinate()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('N', $this->values[Coordinate_Presenter::lat_hem]);
    $this->assertEqual(0, $this->values[Coordinate_Presenter::lat_deg]);
    $this->assertEqual(0, $this->values[Coordinate_Presenter::lat_min]);
    $this->assertEqual('E', $this->values[Coordinate_Presenter::lon_hem]);
    $this->assertEqual(0, $this->values[Coordinate_Presenter::lon_deg]);
    $this->assertEqual(0, $this->values[Coordinate_Presenter::lon_min]);
  }

  function testSetEmptyDescription()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('', $this->values['wpDesc']);
  }

  function testPageTitleIsTranslated()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Add waypoint tr', $this->values['pagetitle']);
  }

  function testChildWpIsAdded()
  {
    $this->request->set('cacheid', 2);
    $this->request->set('wp_type', 1);
    $this->request->set(Coordinate_Presenter::lat_hem, 'N');
    $this->request->set(Coordinate_Presenter::lat_deg, '10');
    $this->request->set(Coordinate_Presenter::lat_min, '15');
    $this->request->set(Coordinate_Presenter::lon_hem, 'E');
    $this->request->set(Coordinate_Presenter::lon_deg, '20');
    $this->request->set(Coordinate_Presenter::lon_min, '30');
    $this->request->set('desc', 'my waypoint');

    $childWpHandler = new MockChildWp_Handler();
    $childWpHandler->expectOnce('add', array(2, 1, 10.25, 20.5, 'my waypoint'));

    $presenter = $this->createPresenter();

    $presenter->addWaypoint($childWpHandler);
  }

  function testSetsErrorIfNoCacheId()
  {
    $cacheManager = new MockCache_Manager();
    $presenter = $this->createPresenter();

    $cacheManager->setReturnValue('exists', false);

    $presenter->init($this, $cacheManager);

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfCacheDoesNotExist()
  {
    $cacheManager = new MockCache_Manager();

    $this->request->setForValidation('cacheid', '234');
    $cacheManager->setReturnValue('exists', false);
    $cacheManager->expectOnce('exists', array('234'));

    $presenter = $this->createPresenter();

    $presenter->init($this, $cacheManager);

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfUserMayNotModifyCache()
  {
    $cacheManager = new MockCache_Manager();

    $this->request->setForValidation('cacheid', '345');
    $cacheManager->setReturnValue('exists', true);
    $cacheManager->expectOnce('exists', array('345'));
    $cacheManager->setReturnValue('userMayModify', false);
    $cacheManager->expectOnce('userMayModify', array('345'));

    $presenter = $this->createPresenter();

    $presenter->init($this, $cacheManager);

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testDoesNotSetErrorIfCacheExists()
  {
    $cacheManager = new MockCache_Manager();

    $this->request->setForValidation('cacheid', '345');
    $cacheManager->setReturnValue('exists', true);
    $cacheManager->expectOnce('exists', array('345'));
    $cacheManager->setReturnValue('userMayModify', true);
    $cacheManager->expectOnce('userMayModify', array('345'));

    $presenter = $this->createPresenter();

    $presenter->init($this, $cacheManager);

    $this->assertEqual(0, $this->errorCode);
  }

  function testSetWaypointTypeIds()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(2, 'Type 2'));
    $presenter = $this->createPresenter();

    $presenter->setTypes($waypointTypes);

    $presenter->prepare($this);

    $this->assertTrue(in_array(1, $this->values['wpTypeIds']));
    $this->assertTrue(in_array(2, $this->values['wpTypeIds']));
  }

  function testSetWaypointTypeNames()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(2, 'Type 2'));
    $presenter = $this->createPresenter();

    $presenter->setTypes($waypointTypes);

    $presenter->prepare($this);

    $this->assertTrue(in_array('Type 1 tr', $this->values['wpTypeNames']));
    $this->assertTrue(in_array('Type 2 tr', $this->values['wpTypeNames']));
  }

  function testNoErrorDuringPrepare()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(2, 'Type 2'));

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $presenter->prepare($this);

    $this->assertFalse(isset($this->values['wpTypeError']));
  }

  function testNoTypeIsSelected()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('0', $this->values['wpType']);
  }

  function testDescriptionIsValidated()
  {
    $this->request->setForValidation('desc', 'description');

    $presenter = $this->createPresenter();

    $this->assertFalse($this->request->get('desc'));

    $presenter->validate();

    $this->assertEqual('description', $this->request->get('desc'));
  }

  function testCoordinateIsValidated()
  {
    $this->request->setForValidation(Coordinate_Presenter::lat_hem, 'N');
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, '10');
    $this->request->setForValidation(Coordinate_Presenter::lat_min, '15');
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, 'E');
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, '20');
    $this->request->setForValidation(Coordinate_Presenter::lon_min, '30');

    $presenter = $this->createPresenter();

    $presenter->validate();

    $this->assertEqual('N', $this->request->get(Coordinate_Presenter::lat_hem));
    $this->assertEqual('10', $this->request->get(Coordinate_Presenter::lat_deg));
    $this->assertEqual('15', $this->request->get(Coordinate_Presenter::lat_min));
    $this->assertEqual('E', $this->request->get(Coordinate_Presenter::lon_hem));
    $this->assertEqual('20', $this->request->get(Coordinate_Presenter::lon_deg));
    $this->assertEqual('30', $this->request->get(Coordinate_Presenter::lon_min));
  }

  function testValidateInvalidCoordinateReturnsError()
  {
    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testSetsErrorIfInvalidCoordinate()
  {
    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('Invalid coordinate tr', $this->values[Coordinate_Presenter::coord_error]);
  }

  function testWaypointTypeIsValidated()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(2, 'Type 2'));

    $this->request->setForValidation('wp_type', '1');

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $presenter->validate();

    $this->assertEqual('1', $this->request->get('wp_type'));
  }

  function testSetsErrorIfTypeNotChoosen()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(2, 'Type 2'));

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('Select waypoint type tr', $this->values['wpTypeError']);
  }

  function testSetsErrorIfTypeIsInvalid()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(3, 'Type 3'));

    $this->request->setForValidation('wp_type', '2');

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('Select waypoint type tr', $this->values['wpTypeError']);
  }

  function testValidateReturnsTrueIfValid()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(3, 'Type 3'));

    $this->request->setForValidation(Coordinate_Presenter::lat_hem, 'N');
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, '10');
    $this->request->setForValidation(Coordinate_Presenter::lat_min, '15');
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, 'E');
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, '20');
    $this->request->setForValidation(Coordinate_Presenter::lon_min, '30');
    $this->request->setForValidation('wp_type', '3');

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $this->assertTrue($presenter->validate());
  }

  function testValidateReturnsFalseIfTypeIsInvalid()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(3, 'Type 3'));

    $this->request->setForValidation(Coordinate_Presenter::lat_hem, 'N');
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, '10');
    $this->request->setForValidation(Coordinate_Presenter::lat_min, '15');
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, 'E');
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, '20');
    $this->request->setForValidation(Coordinate_Presenter::lon_min, '30');
    $this->request->setForValidation('wp_type', '2');

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $this->assertFalse($presenter->validate());
  }

  function testValidCoordinateIsShownAfterValidate()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(3, 'Type 3'));

    $this->request->setForValidation(Coordinate_Presenter::lat_hem, 'N');
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, '10');
    $this->request->setForValidation(Coordinate_Presenter::lat_min, '15');
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, 'E');
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, '20');
    $this->request->setForValidation(Coordinate_Presenter::lon_min, '30');
    $this->request->setForValidation('wp_type', '2');

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('N', $this->values[Coordinate_Presenter::lat_hem]);
    $this->assertEqual(10, $this->values[Coordinate_Presenter::lat_deg]);
    $this->assertEqual(15, $this->values[Coordinate_Presenter::lat_min]);
    $this->assertEqual('E', $this->values[Coordinate_Presenter::lon_hem]);
    $this->assertEqual(20, $this->values[Coordinate_Presenter::lon_deg]);
    $this->assertEqual(30, $this->values[Coordinate_Presenter::lon_min]);
  }

  function testValidDescriptionIsShownAfterValidate()
  {
    $this->request->setForValidation('desc', 'description');

    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('description', $this->values['wpDesc']);
  }

  function testValidTypeIsShownAfterValidate()
  {
    $waypointTypes = array(new ChildWp_Type(1, 'Type 1'), new ChildWp_Type(3, 'Type 3'));

    $this->request->setForValidation('wp_type', '3');

    $presenter = $this->createPresenter();
    $presenter->setTypes($waypointTypes);

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('3', $this->values['wpType']);
  }

  function testHtmlIsEscapedBeforeAdded()
  {
    $this->request->set('cacheid', 2);
    $this->request->set('wp_type', 1);
    $this->request->set(Coordinate_Presenter::lat_hem, 'N');
    $this->request->set(Coordinate_Presenter::lat_deg, '10');
    $this->request->set(Coordinate_Presenter::lat_min, '15');
    $this->request->set(Coordinate_Presenter::lon_hem, 'E');
    $this->request->set(Coordinate_Presenter::lon_deg, '20');
    $this->request->set(Coordinate_Presenter::lon_min, '30');
    $this->request->set('desc', 'my & < waypoint');

    $childWpHandler = new MockChildWp_Handler();
    $childWpHandler->expectOnce('add', array(2, 1, 10.25, 20.5, 'my &amp; &lt; waypoint'));

    $presenter = $this->createPresenter();

    $presenter->addWaypoint($childWpHandler);
  }
}

?>
