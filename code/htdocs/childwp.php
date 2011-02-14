<?php

  require('./lib2/web.inc.php');
  require_once('./config2/childwp.inc.php');

  global $childWpTypes;

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
  $presenter->setTypes($childWpTypes);

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