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

  function testNoTypeIsSelected()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('0', $this->values['wpType']);
  }

  /*function testDescriptionIsValidated()
  {
    $request = new Http_Request();

    $_POST['desc'] = 'description';

    $presenter = new ChildWp_Presenter($request);

    $this->assertFalse($request->get('desc'));

    $presenter->validate();

    $this->assertTrue($request->get('desc'));
  }*/
}

?>
