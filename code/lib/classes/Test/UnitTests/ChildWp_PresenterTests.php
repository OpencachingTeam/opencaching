<?php

require_once('simpletest/autorun.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib2/error.inc.php';

Mock::generate('Language_Translator');
Mock::generate('ChildWp_Handler');
Mock::generate('Cache_Manager');

class MockTemplate
{
  private $values = array();

  public function assign($tpl_var, $value)
  {
    $this->values[$tpl_var] = $value;
  }

  public function get($tpl_var)
  {
    return $this->values[$tpl_var];
  }
}

class ChildWp_PresenterTests extends UnitTestCase
{
  private $errorCode;

  public function error($errorCode)
  {
    $this->errorCode = $errorCode;
  }

  function setUp()
  {
    $this->errorCode = 0;
  }

  function testSetZeroCoordinate()
  {
    $template = new MockTemplate();
    $presenter = new ChildWp_Presenter();

    $presenter->prepare($template);

    $this->assertEqual('N', $template->get(Coordinate_Presenter::lat_hem));
    $this->assertEqual(0, $template->get(Coordinate_Presenter::lat_deg));
    $this->assertEqual(0, $template->get(Coordinate_Presenter::lat_min));
    $this->assertEqual('E', $template->get(Coordinate_Presenter::lon_hem));
    $this->assertEqual(0, $template->get(Coordinate_Presenter::lon_deg));
    $this->assertEqual(0, $template->get(Coordinate_Presenter::lon_min));
  }

  function testSetEmptyDescription()
  {
    $template = new MockTemplate();
    $presenter = new ChildWp_Presenter();

    $presenter->prepare($template);

    $this->assertEqual('', $template->get('wpDesc'));
  }

  function testPageTitleIsTranslated()
  {
    $template = new MockTemplate();
    $translator = new MockLanguage_Translator();
    $translator->setReturnValue('translate', 'Add new waypoint');
    $translator->expectOnce('translate', array('Add waypoint'));

    $presenter = new ChildWp_Presenter(null, $translator);

    $presenter->prepare($template);

    $this->assertEqual('Add new waypoint', $template->get('pagetitle'));
  }

  function testChildWpIsAdded()
  {
    $request = new Http_Request();
    $childWpHandler = new MockChildWp_Handler();

    $request->set('cacheid', 2);
    $request->set('wp_type', 1);
    $request->set(Coordinate_Presenter::lat_hem, 'N');
    $request->set(Coordinate_Presenter::lat_deg, '10');
    $request->set(Coordinate_Presenter::lat_min, '15');
    $request->set(Coordinate_Presenter::lon_hem, 'E');
    $request->set(Coordinate_Presenter::lon_deg, '20');
    $request->set(Coordinate_Presenter::lon_min, '30');
    $request->set('desc', 'my waypoint');
    $childWpHandler->expectOnce('add', array(2, 1, 10.25, 20.5, 'my waypoint'));

    $presenter = new ChildWp_Presenter($request);

    $presenter->addWaypoint($childWpHandler);
  }

  function testSetsErrorIfNoCacheId()
  {
    $cacheManager = new MockCache_Manager();
    $presenter = new ChildWp_Presenter();

    $cacheManager->setReturnValue('exists', false);

    $presenter->init($this, $cacheManager);

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfCacheDoesNotExist()
  {
    $cacheManager = new MockCache_Manager();
    $_GET['cacheid'] = '234';

    $cacheManager->setReturnValue('exists', false);
    $cacheManager->expectOnce('exists', array('234'));

    $presenter = new ChildWp_Presenter();

    $presenter->init($this, $cacheManager);

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testSetsErrorIfUserMayNotModifyCache()
  {
    $cacheManager = new MockCache_Manager();
    $_GET['cacheid'] = '345';

    $cacheManager->setReturnValue('exists', true);
    $cacheManager->expectOnce('exists', array('345'));
    $cacheManager->setReturnValue('userMayModify', false);
    $cacheManager->expectOnce('userMayModify', array('345'));

    $presenter = new ChildWp_Presenter();

    $presenter->init($this, $cacheManager);

    $this->assertEqual(ERROR_CACHE_NOT_EXISTS, $this->errorCode);
  }

  function testDoesNotSetErrorIfCacheExists()
  {
    $cacheManager = new MockCache_Manager();
    $_GET['cacheid'] = '345';

    $cacheManager->setReturnValue('exists', true);
    $cacheManager->expectOnce('exists', array('345'));
    $cacheManager->setReturnValue('userMayModify', true);
    $cacheManager->expectOnce('userMayModify', array('345'));

    $presenter = new ChildWp_Presenter();

    $presenter->init($this, $cacheManager);

    $this->assertEqual(0, $this->errorCode);
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
