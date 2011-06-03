<?php

global $opt;

require_once('simpletest/autorun.php');
require_once($opt['rootpath'] . 'lib2/error.inc.php');

Mock::generate('ChildWp_Handler');
Mock::generate('Cache_Manager');

class ChildWp_PresenterTests extends UnitTestCase
{
  private $errorCode;
  private $values;
  private $request;
  private $translator;
  private $cacheManager;
  private $childWpHandler;

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

    $this->cacheManager = new MockCache_Manager();
    $this->cacheManager->setReturnValue('exists', true, array('234'));
    $this->cacheManager->setReturnValue('exists', true, array('345'));
    $this->cacheManager->setReturnValue('exists', false);
    $this->cacheManager->setReturnValue('userMayModify', true, array('345'));
    $this->cacheManager->setReturnValue('userMayModify', false);

    $this->childWpHandler = new MockChildWp_Handler();

    $this->childWpHandler->setReturnValue('getChildWp', array('cacheid' => '345', 'type' => '3', 'latitude' => 20.5, 'longitude' => 30.75, 'description' => 'Start here.'), array('567'));
    $this->childWpHandler->setReturnValue('getChildWpIdAndNames', array(1 => 'Type 1 tr', 3 => 'Type 3 tr'));
  }

  private function createPresenter()
  {
    $controller = new ChildWp_Controller($this->request, $this->translator);

    $presenter = $controller->createPresenter($this, $this->cacheManager, $this->childWpHandler);

    return $presenter;
  }

  private function assertCoordinate($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $this->assertEqual($lat_hem, $this->values[Coordinate_Presenter::lat_hem]);
    $this->assertEqual($lat_deg, $this->values[Coordinate_Presenter::lat_deg]);
    $this->assertEqual($lat_min, $this->values[Coordinate_Presenter::lat_min]);
    $this->assertEqual($lon_hem, $this->values[Coordinate_Presenter::lon_hem]);
    $this->assertEqual($lon_deg, $this->values[Coordinate_Presenter::lon_deg]);
    $this->assertEqual($lon_min, $this->values[Coordinate_Presenter::lon_min]);
  }

  private function setCoordinate($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $this->request->set(Coordinate_Presenter::lat_hem, $lat_hem);
    $this->request->set(Coordinate_Presenter::lat_deg, $lat_deg);
    $this->request->set(Coordinate_Presenter::lat_min, $lat_min);
    $this->request->set(Coordinate_Presenter::lon_hem, $lon_hem);
    $this->request->set(Coordinate_Presenter::lon_deg, $lon_deg);
    $this->request->set(Coordinate_Presenter::lon_min, $lon_min);
  }

  private function setCoordinateForValidation($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $this->request->setForValidation(Coordinate_Presenter::lat_hem, $lat_hem);
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, $lat_deg);
    $this->request->setForValidation(Coordinate_Presenter::lat_min, $lat_min);
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, $lon_hem);
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, $lon_deg);
    $this->request->setForValidation(Coordinate_Presenter::lon_min, $lon_min);
  }

  private function setUpAddRequest($cacheId)
  {
    $this->request->setForValidation(ChildWp_Controller::req_cache_id, $cacheId);
  }

  private function setUpEditRequest($cacheId, $childId)
  {
    $this->request->setForValidation(ChildWp_Controller::req_cache_id, $cacheId);
    $this->request->setForValidation(ChildWp_Controller::req_child_id, $childId);
  }

  private function setUpDeleteRequest($cacheId, $childId)
  {
    $this->request->setForValidation(ChildWp_Controller::req_cache_id, $cacheId);
    $this->request->setForValidation(ChildWp_Controller::req_delete_id, $childId);
  }

  function testSetZeroCoordinate()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertCoordinate('N', 0, 0, 'E', 0, 0);
  }

  function testSetEmptyDescription()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('', $this->values[ChildWp_Presenter::tpl_wp_desc]);
  }

  function testPageTitleIsTranslated()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Add waypoint tr', $this->values[ChildWp_Presenter::tpl_page_title]);
  }

  function testChildWpIsAdded()
  {
    $this->setUpAddRequest('345');
    $this->request->set(ChildWp_Presenter::req_wp_type, 1);
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');
    $this->request->set(ChildWp_Presenter::req_wp_desc, 'my waypoint');

    $this->childWpHandler->expectOnce('add', array('345', 1, 10.25, 20.5, 'my waypoint'));

    $presenter = $this->createPresenter();

    $presenter->doSubmit();
  }

  function testSetsErrorIfNoCacheId()
  {
    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfCacheDoesNotExist()
  {
    $this->setUpAddRequest('123');

    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfUserMayNotModifyCache()
  {
    $this->setUpAddRequest('234');

    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testDoesNotSetErrorIfCacheExists()
  {
    $this->setUpAddRequest('345');

    $presenter = $this->createPresenter();

    $this->assertEqual(0, $this->errorCode);
  }

  function testSetWaypointTypeIds()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertTrue(in_array(1, $this->values[ChildWp_Presenter::tpl_wp_type_ids]));
    $this->assertTrue(in_array(3, $this->values[ChildWp_Presenter::tpl_wp_type_ids]));
  }

  function testSetWaypointTypeNames()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertTrue(in_array('Type 1 tr', $this->values[ChildWp_Presenter::tpl_wp_type_names]));
    $this->assertTrue(in_array('Type 3 tr', $this->values[ChildWp_Presenter::tpl_wp_type_names]));
  }

  function testNoErrorDuringPrepare()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertFalse(isset($this->values[ChildWp_Presenter::tpl_wp_type_error]));
  }

  function testNoTypeIsSelected()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('0', $this->values[ChildWp_Presenter::tpl_wp_type]);
  }

  function testDescriptionIsValidated()
  {
    $this->request->setForValidation(ChildWp_Presenter::req_wp_desc, 'description');

    $presenter = $this->createPresenter();

    $this->assertFalse($this->request->get(ChildWp_Presenter::req_wp_desc));

    $presenter->validate();

    $this->assertEqual('description', $this->request->get(ChildWp_Presenter::req_wp_desc));
  }

  function testCoordinateIsValidated()
  {
    $this->setCoordinateForValidation('N', '10', '15', 'E', '20', '30');

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
    $this->request->setForValidation(ChildWp_Presenter::req_wp_type, '1');

    $presenter = $this->createPresenter();

    $presenter->validate();

    $this->assertEqual('1', $this->request->get(ChildWp_Presenter::req_wp_type));
  }

  function testSetsErrorIfTypeNotChoosen()
  {
    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('Select waypoint type tr', $this->values[ChildWp_Presenter::tpl_wp_type_error]);
  }

  function testSetsErrorIfTypeIsInvalid()
  {
    $this->request->setForValidation(ChildWp_Presenter::req_wp_type, '2');

    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('Select waypoint type tr', $this->values[ChildWp_Presenter::tpl_wp_type_error]);
  }

  function testValidateReturnsTrueIfValid()
  {
    $this->setCoordinateForValidation('N', '10', '15', 'E', '20', '30');
    $this->request->setForValidation(ChildWp_Presenter::req_wp_type, '3');

    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());
  }

  function testValidateReturnsFalseIfTypeIsInvalid()
  {
    $this->setCoordinateForValidation('N', '10', '15', 'E', '20', '30');
    $this->request->setForValidation(ChildWp_Presenter::req_wp_type, '2');

    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());
  }

  function testValidCoordinateIsShownAfterValidate()
  {
    $this->setCoordinateForValidation('N', '10', '15', 'E', '20', '30');
    $this->request->setForValidation(ChildWp_Presenter::req_wp_type, '2');

    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertCoordinate('N', 10, 15, 'E', 20, 30);
  }

  function testValidDescriptionIsShownAfterValidate()
  {
    $this->request->setForValidation(ChildWp_Presenter::req_wp_desc, 'description');

    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('description', $this->values[ChildWp_Presenter::tpl_wp_desc]);
  }

  function testValidTypeIsShownAfterValidate()
  {
    $this->request->setForValidation(ChildWp_Presenter::req_wp_type, '3');

    $presenter = $this->createPresenter();

    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('3', $this->values[ChildWp_Presenter::tpl_wp_type]);
  }

  function testHtmlIsNotEscapedBeforeAdded()
  {
    $this->setUpAddRequest('345');
    $this->request->set(ChildWp_Presenter::req_wp_type, 1);
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');
    $this->request->set(ChildWp_Presenter::req_wp_desc, 'my & < waypoint');

    $this->childWpHandler->expectOnce('add', array('345', 1, 10.25, 20.5, 'my & < waypoint'));

    $presenter = $this->createPresenter();

    $presenter->doSubmit();
  }

  function testSetCacheId()
  {
    $this->setUpAddRequest('234');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('234', $this->values[ChildWp_Presenter::tpl_cache_id]);
  }

  function testSetsErrorIfWaypointDoesNotExist()
  {
    $this->setUpEditRequest('345', '456');

    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfWaypointDoesNotBelongToCache()
  {
    $this->setUpEditRequest('345', '566');

    $this->childWpHandler->setReturnValue('getChildWp', array('cacheid' => '234', 'type' => '3', 'latitude' => 20.5, 'longitude' => 30.75, 'description' => 'Start here.'), array('566'));

    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testNoErrorIfWaypointBelongsToCache()
  {
    $this->setUpEditRequest('345', '567');

    $presenter = $this->createPresenter();

    $this->assertEqual(0, $this->errorCode);
  }

  function testExistingWaypointIsShownAfterPrepare()
  {
    $this->setUpEditRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('3', $this->values[ChildWp_Presenter::tpl_wp_type]);
    $this->assertCoordinate('N', 20, 30, 'E', 30, 45);
    $this->assertEqual('Start here.', $this->values[ChildWp_Presenter::tpl_wp_desc]);
  }

  function testChildWpIsUpdated()
  {
    $this->setUpEditRequest('345', 567);
    $this->request->set(ChildWp_Presenter::req_wp_type, 1);
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');
    $this->request->set(ChildWp_Presenter::req_wp_desc, 'my waypoint');

    $this->childWpHandler->expectOnce('update', array(567, 1, 10.25, 20.5, 'my waypoint'));

    $presenter = $this->createPresenter();

    $presenter->doSubmit();
  }

  function testHtmlIsNotEscapedBeforeUpdate()
  {
    $this->setUpEditRequest('345', 567);
    $this->request->set(ChildWp_Presenter::req_wp_type, 1);
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');
    $this->request->set(ChildWp_Presenter::req_wp_desc, 'my & < waypoint');

    $this->childWpHandler->expectOnce('update', array(567, 1, 10.25, 20.5, 'my & < waypoint'));

    $presenter = $this->createPresenter();

    $presenter->doSubmit();
  }

  function testEscapedHtmlIsShownAsEscaped()
  {
    $this->setUpEditRequest('345', '568');

    $this->childWpHandler->setReturnValue('getChildWp', array('cacheid' => '345', 'type' => '3', 'latitude' => 20.5, 'longitude' => 30.75, 'description' => 'my &amp; &lt; waypoint'), array('568'));

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('my &amp; &lt; waypoint', $this->values[ChildWp_Presenter::tpl_wp_desc]);
  }

  function testPageTitleEditIsTranslated()
  {
    $this->setUpEditRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Edit waypoint tr', $this->values[ChildWp_Presenter::tpl_page_title]);
  }

  function testSetWaypointId()
  {
    $this->setUpEditRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('567', $this->values[ChildWp_Presenter::tpl_child_id]);
  }

  function testSubmitButtonAddIsTranslated()
  {
    $this->setUpAddRequest('345');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Add new tr', $this->values[ChildWp_Presenter::tpl_submit_button]);
  }

  function testSubmitButtonEditIsTranslated()
  {
    $this->setUpEditRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Save tr', $this->values[ChildWp_Presenter::tpl_submit_button]);
  }

  function testNotDisabledWhenAddWaypoint()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertFalse($this->values[ChildWp_Presenter::tpl_disabled]);
  }

  function testNotDisabledWhenEditWaypoint()
  {
    $this->setUpEditRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertFalse($this->values[ChildWp_Presenter::tpl_disabled]);
  }

  function testDisabledWhenDeleteWaypoint()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertTrue($this->values[ChildWp_Presenter::tpl_disabled]);
  }

  function testSubmitButtonDeleteIsTranslated()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Delete tr', $this->values[ChildWp_Presenter::tpl_submit_button]);
  }

  function testPageTitleDeleteIsTranslated()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('Delete waypoint tr', $this->values[ChildWp_Presenter::tpl_page_title]);
  }

  function testChildWpIsDeleted()
  {
    $this->setUpDeleteRequest('345', 567);
    $this->request->set(ChildWp_Presenter::req_wp_type, 1);
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');
    $this->request->set(ChildWp_Presenter::req_wp_desc, 'my waypoint');

    $this->childWpHandler->expectOnce('delete', array(567));

    $presenter = $this->createPresenter();

    $presenter->doSubmit();
  }

  function testSetsErrorIfDeletingWaypointDoesNotExist()
  {
    $this->setUpDeleteRequest('345', '456');

    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfDeletingWaypointDoesNotBelongToCache()
  {
    $this->setUpDeleteRequest('345', '566');

    $this->childWpHandler->setReturnValue('getChildWp', array('cacheid' => '234', 'type' => '3', 'latitude' => 20.5, 'longitude' => 30.75, 'description' => 'Start here.'), array('566'));

    $presenter = $this->createPresenter();

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testNoErrorIfDeletingWaypointBelongsToCache()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $this->assertEqual(0, $this->errorCode);
  }

  function testDeletingWaypointIsShownAfterPrepare()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('3', $this->values[ChildWp_Presenter::tpl_wp_type]);
    $this->assertCoordinate('N', 20, 30, 'E', 30, 45);
    $this->assertEqual('Start here.', $this->values[ChildWp_Presenter::tpl_wp_desc]);
  }

  function testSetDeletingWaypointId()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('567', $this->values[ChildWp_Presenter::tpl_delete_id]);
  }

  function testSetDeletingWaypointIdNotChildId()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertTrue(empty($this->values[ChildWp_Presenter::tpl_child_id]));
  }

  function testDeletingWaypointIsValid()
  {
    $this->setUpDeleteRequest('345', '567');

    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());
  }

  function testAddPresenterIsCreated()
  {
    $this->setUpAddRequest('345');

    $controller = new ChildWp_Controller($this->request, $this->translator);

    $presenter = $controller->createPresenter($this, $this->cacheManager, $this->childWpHandler);

    $this->assertIsA($presenter, 'ChildWp_AddPresenter');
  }

  function testEditPresenterIsCreated()
  {
    $this->setUpEditRequest('345', '567');

    $controller = new ChildWp_Controller($this->request, $this->translator);

    $presenter = $controller->createPresenter($this, $this->cacheManager, $this->childWpHandler);

    $this->assertIsA($presenter, 'ChildWp_EditPresenter');
  }

  function testDeletePresenterIsCreated()
  {
    $this->setUpDeleteRequest('345', '567');

    $controller = new ChildWp_Controller($this->request, $this->translator);

    $presenter = $controller->createPresenter($this, $this->cacheManager, $this->childWpHandler);

    $this->assertIsA($presenter, 'ChildWp_DeletePresenter');
  }
}

?>
