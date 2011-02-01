<?php

require_once('simpletest/autorun.php');

Mock::generate('Language_Translator');
Mock::generate('ChildWp_Handler');

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
  function testSetZeroCoordinate()
  {
    $template = new MockTemplate();
    $presenter = new ChildWp_Presenter();

    $presenter->prepare($template);

    $this->assertEqual(0, $template->get('wpLat'));
    $this->assertEqual(0, $template->get('wpLon'));
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

    $request->set('wp_type', 1);
    $request->set('coordNS', 'N');
    $request->set('coordLat', '10');
    $request->set('coordLatMin', '15');
    $request->set('coordEW', 'E');
    $request->set('coordLon', '20');
    $request->set('coordLonMin', '30');
    $request->set('desc', 'my waypoint');
    $childWpHandler->expectOnce('add', array(1, 10.25, 20.5, 'my waypoint'));

    $presenter = new ChildWp_Presenter($request);

    $presenter->addWaypoint($childWpHandler);
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
