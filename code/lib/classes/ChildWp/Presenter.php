<?php

class ChildWp_Presenter
{
  private $request;
  private $translator;

  public function __construct($request = false, $translator = false)
  {
    $this->request = $this->initRequest($request);
    $this->translator = $this->initTranslator($translator);
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
    $childWpHandler->add($this->getType(), $this->getLat(), $this->getLon(), $this->getDesc());
  }

  private function getType()
  {
    return $this->request->get('wp_type');
  }

  private function getLat()
  {
    return 10.25;
  }

  private function getLon()
  {
    return 20.5;
  }

  private function getDesc()
  {
    return $this->request->get('desc');
  }

  public function prepare($template)
  {
    $template->assign('pagetitle', $this->translator->Translate('Add waypoint'));
    $template->assign('wpLat', 0);
    $template->assign('wpLon', 0);
    $template->assign('wpDesc', 0);
  }
}

?>
