<?php

class ChildWp_EditPresenter extends ChildWp_Presenter
{
  public function __construct($request = false, $translator = false)
  {
    parent::__construct($request, $translator);
  }

  protected function getTitle()
  {
    return 'Edit waypoint';
  }

  protected function getSubmitButton()
  {
    return 'Save';
  }

  protected function onDoSubmit($coordinate, $description)
  {
    $this->childWpHandler->update($this->childId, $this->getType(), $coordinate->latitude(), $coordinate->longitude(), $description);
  }

  protected function onPrepare($template)
  {
    $template->assign(self::tpl_child_id, $this->childId);
  }
}

?>
