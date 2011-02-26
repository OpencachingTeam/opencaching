<?php

require_once('simpletest/autorun.php');

Mock::generate('CacheNote_Handler');

class CacheNote_PresenterTests extends UnitTestCase
{
  private $values;
  private $request;
  private $translator;
  private $cacheNoteHandler;

  public function assign($tpl_var, $value)
  {
    $this->values[$tpl_var] = $value;
  }

  function setUp()
  {
    $this->values = array();
    $this->request = new Http_Request();
    $this->translator = new Test_Translator();

    $this->cacheNoteHandler = new MockCacheNote_Handler();
    $this->cacheNoteHandler->setReturnValue('getCacheNote', array('id' => 321, 'note' => 'This is my note.', 'latitude' => 19.5, 'longitude' => 21.75), array(11, 123));
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

  private function setCoordinateForValidation($lat_hem, $lat_deg, $lat_min, $lon_hem, $lon_deg, $lon_min)
  {
    $this->request->setForValidation(Coordinate_Presenter::lat_hem, $lat_hem);
    $this->request->setForValidation(Coordinate_Presenter::lat_deg, $lat_deg);
    $this->request->setForValidation(Coordinate_Presenter::lat_min, $lat_min);
    $this->request->setForValidation(Coordinate_Presenter::lon_hem, $lon_hem);
    $this->request->setForValidation(Coordinate_Presenter::lon_deg, $lon_deg);
    $this->request->setForValidation(Coordinate_Presenter::lon_min, $lon_min);
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

  private function createPresenter()
  {
    $presenter = new CacheNote_Presenter($this->request, $this->translator);

    return $presenter;
  }

  function testEmptyNoteIsPresented()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertEqual('', $this->values[CacheNote_Presenter::tpl_note]);
  }

  function testEmptyCoordinateIsPresented()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertCoordinate('N', 0, 0, 'E', 0, 0);
  }

  function testInclCoordNotCheckedIfEmpty()
  {
    $presenter = $this->createPresenter();

    $presenter->prepare($this);

    $this->assertFalse($this->values[CacheNote_Presenter::tpl_incl_coord]);
  }

  function testPresentsUserCacheNote()
  {
    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 11, 123);
    $presenter->prepare($this);

    $this->assertEqual('This is my note.', $this->values[CacheNote_Presenter::tpl_note]);
    $this->assertCoordinate('N', 19, 30, 'E', 21, 45);
    $this->assertTrue($this->values[CacheNote_Presenter::tpl_incl_coord]);
  }

  function testSetsNoteId()
  {
    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 11, 123);
    $presenter->prepare($this);

    $this->assertEqual(321, $this->values[CacheNote_Presenter::tpl_note_id]);
  }

  function testSetsCacheId()
  {
    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 11, 123);
    $presenter->prepare($this);

    $this->assertEqual(123, $this->values[CacheNote_Presenter::tpl_cache_id]);
  }

  function testNoteIsValidated()
  {
    $this->request->setForValidation(CacheNote_Presenter::req_note, 'My note');

    $presenter = $this->createPresenter();

    $this->assertFalse($this->request->get(CacheNote_Presenter::req_note));

    $presenter->validate();

    $this->assertEqual('My note', $this->request->get(CacheNote_Presenter::req_note));
  }

  function testCoordinateIsValidated()
  {
    $this->request->setForValidation(CacheNote_Presenter::req_incl_coord, 'true');

    $this->setCoordinateForValidation('N', '18', '15', 'E', '23', '30');

    $presenter = $this->createPresenter();

    $presenter->validate();

    $this->assertEqual('N', $this->request->get(Coordinate_Presenter::lat_hem));
    $this->assertEqual('18', $this->request->get(Coordinate_Presenter::lat_deg));
    $this->assertEqual('15', $this->request->get(Coordinate_Presenter::lat_min));
    $this->assertEqual('E', $this->request->get(Coordinate_Presenter::lon_hem));
    $this->assertEqual('23', $this->request->get(Coordinate_Presenter::lon_deg));
    $this->assertEqual('30', $this->request->get(Coordinate_Presenter::lon_min));
  }

  function testSetsErrorIfInvalidCoordinate()
  {
    $this->request->setForValidation(CacheNote_Presenter::req_incl_coord, 'true');

    $presenter = $this->createPresenter();

    $this->assertFalse($presenter->validate());

    $presenter->prepare($this);

    $this->assertEqual('Invalid coordinate tr', $this->values[Coordinate_Presenter::coord_error]);
  }

  function testCoordinateNotValidatedIfNotIncluded()
  {
    $presenter = $this->createPresenter();

    $this->assertTrue($presenter->validate());

    $presenter->prepare($this);

    $this->assertTrue(empty($this->values[Coordinate_Presenter::coord_error]));
  }

  function testCacheNoteIsSaved()
  {
    $this->request->set(CacheNote_Presenter::req_note, 'my note');
    $this->request->set(CacheNote_Presenter::req_incl_coord, 'true');
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');

    $this->cacheNoteHandler->expectOnce('save', array(null, 12, 234, 'my note', 10.25, 20.5));

    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 12, 234);

    $presenter->doSubmit();
  }

  function testCoordinateIsNotSaved()
  {
    $this->request->set(CacheNote_Presenter::req_note, 'my note');
    $this->setCoordinate('N', '10', '15', 'E', '20', '30');

    $this->cacheNoteHandler->expectOnce('save', array(null, 12, 234, 'my note', 0, 0));

    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 12, 234);

    $presenter->doSubmit();
  }

  function testSubmittedCacheNoteIsPresented()
  {
    $this->request->setForValidation(CacheNote_Presenter::req_note, 'my note');
    $this->request->setForValidation(CacheNote_Presenter::req_incl_coord, 'true');
    $this->setCoordinateForValidation('N', '10', 'ff', 'E', '20', '30');

    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 11, 123);
    $presenter->validate();
    $presenter->prepare($this);

    $this->assertEqual('my note', $this->values[CacheNote_Presenter::tpl_note]);
  }

  function testSubmittedCacheNoteIsUpdated()
  {
    $this->request->setForValidation(CacheNote_Presenter::req_note, 'my note');
    $this->request->setForValidation(CacheNote_Presenter::req_incl_coord, 'true');
    $this->setCoordinateForValidation('N', '10', '15', 'E', '20', '30');

    $this->cacheNoteHandler->expectOnce('save', array(321, 11, 123, 'my note', 10.25, 20.5));

    $presenter = $this->createPresenter();

    $presenter->init($this->cacheNoteHandler, 11, 123);
    $presenter->validate();

    $presenter->doSubmit();
  }
}

?>
