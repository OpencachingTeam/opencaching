<?php

  require('./lib2/web.inc.php');

  $tpl->name = 'childwp';
  $tpl->menuitem = MNU_CACHES_WAYPOINT;

  $login->verify();

  if ($login->userid == 0)
    $tpl->redirect_login();

  $isSubmit = isset($_POST['submitform']);
  $redirect = isset($_POST['back']);

  $request = new Http_Request();
  $presenter = new ChildWp_Presenter($request);
  $cacheManager = new Cache_Manager();
  $handler = new ChildWp_Handler();

  $presenter->init($tpl, $cacheManager, $handler);
  $presenter->setTypes(array(new ChildWp_Type(1, 'Parking'), new ChildWp_Type(2, 'Reference point')));

  if ($isSubmit && $presenter->validate())
  {
    $presenter->doSubmit();
    $redirect = true;
  }

  if ($redirect)
    $tpl->redirect('editcache.php?cacheid=' . $presenter->getCacheId());

  $presenter->prepare($tpl);

  $tpl->display();

?>