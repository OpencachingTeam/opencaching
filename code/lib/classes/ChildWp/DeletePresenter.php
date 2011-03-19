<?php

class ChildWp_DeletePresenter extends ChildWp_Presenter
{
  public function __construct($request = false, $translator = false)
  {
    parent::__construct($request, $translator);
  }

  protected function getTitle()
  {
    return 'Delete waypoint';
  }

  protected function getSubmitButton()
  {
    return 'Delete';
  }

  protected function onDoSubmit($coordinate, $description)
  {
    $this->childWpHandler->delete($this->childId);
  }

  protected function onPrepare($template)
  {
    $template->assign(parent::tpl_disabled, true);
    $template->assign(parent::tpl_delete_id, $this->childId);
  }

  public function validate()
  {
    return true;
  }
}

?>
