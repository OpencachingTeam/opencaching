<?php

class ChildWp_Presenter
{
  private $request;
  private $translator;
  private $coordinate;

  public function __construct($request = false, $translator = false)
  {
    $this->request = $this->initRequest($request);
    $this->translator = $this->initTranslator($translator);
    $this->coordinate = new Coordinate_Presenter($this->request, $this->translator);
  }

  private function initRequest($request)
  {
    if ($request)
      return $request;

    return new Http_Request();
  }

  private function initTranslator($translator)
  {
    if ($translator)
      return $translator;

    return new Language_Translator();
  }

  public function addWaypoint($childWpHandler)
  {
    $coordinate = $this->coordinate->getCoordinate();

    $childWpHandler->add($this->getType(), $coordinate->latitude(), $coordinate->longitude(), $this->getDesc());
  }

  private function getType()
  {
    return $this->request->get('wp_type');
  }

  private function getDesc()
  {
    return $this->request->get('desc');
  }

  public function prepare($template)
  {
    $template->assign('pagetitle', $this->translator->Translate('Add waypoint'));
    $template->assign('wpDesc', 0);
    $this->coordinate->prepare($template);
  }
}

?>
