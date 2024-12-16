<?php
/**
  * @file cms/definitions.php
  * @version 4.0.0 $Id: definitions.php 1 2016-07-18 22:51:12Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

/**
 * protocol
 * ex: http, https,...
 */
 
//if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']) {
//    $_SERVER['HTTPS'] = 1;
//}
//Wer CloudFlare nutzt, kann das ganz einfach umbiegen:
//Ich sage bewusst "umbiegen", weil hier eine technische Voraussetzung vorgegaukelt wird, die in Wahrheit nicht besteht. 
//Es handelt sich nämlich wie gesagt in keiner Weise um eine sichere Verbindung.
 
define('PROTOCOL', isset($_SERVER['HTTPS']) 
                  && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)
                  || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) 
                  && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http');


  
  define('HTP', PROTOCOL . ':');
  
  define ('TPX', '.phtml');
   
  
    
  define('JQUERY', HTP .'//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js');
  define('JQUERY_LOCAL', STATIC_URL.'js/jquery.min.js');
  define('JQUERY_UI', HTP .'//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');
  define('JQUERY_UI_CSS', HTP .'//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
  define('JQUERY_UI_HANDLER', STATIC_URL.'js/jquery_ui_handler.js');

  define('TETHER', HTP .'//cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js');
  define('HOLDER', HTP .'//cdnjs.cloudflare.com/ajax/libs/holder/2.9.4/holder.min.js');
  
  //define('BOOTSTRAP', HTP .'//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
  define('BOOTSTRAP', HTP .'//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js');
  define('BOOTSTRAP_LOCAL', STATIC_URL.'javascripts/bootstrap.min.js');
  
  //define('BOOTSTRAP_CSS', HTP .'//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
  define('BOOTSTRAP_CSS', HTP .'//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css');
  define('BOOTSTRAP_CSS_LOCAL', STATIC_URL.'stylesheets/bootstrap.min.css');
  //<!-- Optional theme -->
  define('BOOTSTRAP_THEME_CSS', HTP .'//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css');

  define('FONTAWESOME', HTP .'//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

  //define('WYSIWYG_EDITOR', HTP .'//tinymce.cachefly.net/4.2/tinymce.min.js');
  define('WYSIWYG_EDITOR', HTP .'//cdn.tinymce.com/4/tinymce.min.js');
  define('WYSIWYG_EDITOR_INIT', 'assets/js/wysiwyg_init.js');

  define('VALID_URL_CHARACTERS', '/^[a-zA-Z0-9._\-\/]+$/');
  define('MEDIA_DIR', STATIC_URL.'images/');
  define('IMAGE_IDENTIFIER', 'photo');
  define('CATEGORY_IDENTIFIER', 'category:');
  define('AMPERSAND_REPLACEMENT', ':AMP:');
  define('SMILIES_DIR', MEDIA_DIR.'smilies/');

 /*
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * last modified: 2016-07-18
  * -------------
  * changelog:
  *
  * 2016-07-18: 4.0.0 - Erste Veröffentlichung des neuen 4.x Stamm
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
