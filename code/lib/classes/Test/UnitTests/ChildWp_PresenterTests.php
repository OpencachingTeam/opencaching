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

    $request->set('wp_type', 1);
    $request->set(Coordinate_Presenter::lat_hem, 'N');
    $request->set(Coordinate_Presenter::lat_deg, '10');
    $request->set(Coordinate_Presenter::lat_min, '15');
    $request->set(Coordinate_Presenter::lon_hem, 'E');
    $request->set(Coordinate_Presenter::lon_deg, '20');
    $request->set(Coordinate_Presenter::lon_min, '30');
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
