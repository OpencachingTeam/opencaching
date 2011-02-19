<?php

class ChildWp_AddPresenter extends ChildWp_Presenter
{
  public function __construct($request = false, $translator = false)
  {
    parent::__construct($request, $translator);
  }

  protected function getTitle()
  {
    return 'Add waypoint';
  }

  protected function getSubmitButton()
  {
    return 'Add new';
  }

  protected function onDoSubmit($coordinate, $description)
  {
    $this->childWpHandler->add($this->cacheId, $this->getType(), $coordinate->latitude(), $coordinate->longitude(), $description);
  }
}

?>
