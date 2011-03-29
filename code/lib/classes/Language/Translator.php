<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib2/translate.class.php');

class Language_Translator
{
  public function translate($lang_string)
  {
    $translate = createTranslate(1);

    return $translate->t($lang_string, '', '', '');
  }
}

?>
