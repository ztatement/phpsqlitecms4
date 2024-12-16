<?php
/**
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Mark Hoschek < mail at mark-hoschek dot de >
  * @copyright (c) 2014 Mark Hoschek
  * @version last 3.2015.04.02.18.42
  * @link http://phpsqlitecms.net/
  * @package phpSQLiteCMS
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]de>
  *         @copyleft 2016 ztatement
  * @version 4.0.0 $Id: cms/index.php 1 2016-07-18 16:38:01Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms.de/
  * @package phpSQLiteCMS
  * 
  * 
  * @author $Author$
  *         @coyleft $Copyright$
  * @version $Rev$: $Revision$ $Id: $
  * Last changed: $Date$: $LastChangedDate$
  * @name $globalVariableName
  * @package name
  * @subpackage name
  * @since version
  * @todo Description
  *
  * @license The MIT License (MIT)
  * @see /LICENSE
  * @see https://opensource.org/licenses/MIT
  *
  *   Hiermit wird unentgeltlich jeder Person, die eine Kopie der Software und der zugehörigen 
  *   Dokumentationen (die "Software") erhält, die Erlaubnis erteilt, sie uneingeschränkt zu nutzen, 
  *   inklusive und ohne Ausnahme mit dem Recht, sie zu verwenden, zu kopieren, zu verändern, 
  *   zusammenzufügen, zu veröffentlichen, zu verbreiten, zu unterlizenzieren und/oder zu verkaufen, 
  *   und Personen, denen diese Software überlassen wird, diese Rechte zu verschaffen, 
  *   unter den folgenden Bedingungen:
  * 
  *   Der obige Urheberrechtsvermerk und dieser Erlaubnisvermerk sind in allen Kopien 
  *   oder Teilkopien der Software beizulegen.
  * 
  *   DIE SOFTWARE WIRD OHNE JEDE AUSDRÜCKLICHE ODER IMPLIZIERTE GARANTIE BEREITGESTELLT, 
  *   EINSCHLIEẞLICH DER GARANTIE ZUR BENUTZUNG FÜR DEN VORGESEHENEN ODER EINEM BESTIMMTEN 
  *   ZWECK SOWIE JEGLICHER RECHTSVERLETZUNG, JEDOCH NICHT DARAUF BESCHRÄNKT. 
  *   IN KEINEM FALL SIND DIE AUTOREN ODER COPYRIGHTINHABER FÜR JEGLICHEN SCHADEN 
  *   ODER SONSTIGE ANSPRÜCHE HAFTBAR ZU MACHEN, OB INFOLGE DER ERFÜLLUNG EINES VERTRAGES, 
  *   EINES DELIKTES ODER ANDERS IM ZUSAMMENHANG MIT DER SOFTWARE 
  *   ODER SONSTIGER VERWENDUNG DER SOFTWARE ENTSTANDEN.
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
/*
  //if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') )
  if (substr_count (filter_input (INPUT_SERVER, 'HTTP_ACCEPT_ENCODING', FILTER_SANITIZE_STRING), 'gzip') )
  {
    ob_start("ob_gzhandler");
  }
  else
  {
    ob_start();
  }

  require_once ('./cms/includes/classes/Minify.class.php');
  */
session_start();

define('CACHE_DIR', 'cms/cache/');

// get query string passed by mod_rewrite:
if(isset($_GET['qs']))
 {
  //if(get_magic_quotes_gpc()) $_GET['qs'] = stripslashes($_GET['qs']);
  $_GET['qs'] = stripslashes($_GET['qs']);
  $qs = $_GET['qs'];
 }
else
 {
  $qs = '';
 }

// check if requested page is cached and if so displays it:
if(empty($_POST) && file_exists('./'.CACHE_DIR.'settings.php'))
 {
  include('./'.CACHE_DIR.'settings.php');
  $settings = get_settings();
  if(empty($_SESSION[$settings['session_prefix'].'user_id']))
   {
    if($qs=='') $cache_file = rawurlencode(strtolower($settings['index_page'])).'.cache';
    else $cache_file = rawurlencode(strtolower($qs)).'.cache';
    if(file_exists('./'.CACHE_DIR.$cache_file))
     {
      include('./'.CACHE_DIR.$cache_file);
      exit; // that's it if cached page is available.
     }
   }
 }

define('IN_INDEX', TRUE);

try
 {
  #throw new Exception('Error message...');
  #require('./cms/config/db_settings.conf.php');
  require('./cms/includes/functions.inc.php');

  // load replacement functions for the multibyte string functions
  // if they are not available:
  #if(!defined('MB_CASE_LOWER')) require('./cms/includes/functions.mb_replacements.inc.php');

  require('./cms/includes/classes/Database.class.php');
  $database = new Database();

  $settings = get_settings();

  // access permission check for not registered users:
  if($settings['check_access_permission']==1 && !isset($_SESSION[$settings['session_prefix'].'user_id']))
   {
    if(is_access_denied()) raise_error('403');
   }

  // set timezone:
  if($settings['time_zone']) date_default_timezone_set($settings['time_zone']);

  define('BASE_URL', get_base_url());
  define('STATIC_URL', BASE_URL.'assets/'); // static.subdomain.com 
  define('BASE_PATH', get_base_path());
  
  require(BASE_PATH.'cms/config/definitions.conf.php');
  
  if($settings['content_functions']==1) require(BASE_PATH.'cms/includes/functions.content.inc.php');

  require('./cms/includes/classes/Template.class.php');
  $template = new Template();
  #$template->set_settings($settings);

  if($settings['caching'])
   {
    $cache = new Cache(BASE_PATH.CACHE_DIR, $settings);
    if(!empty($_POST) || isset($_SESSION[$settings['session_prefix'].'user_id']))
     {
      $cache->doCaching = false;
     }
   }

  if(isset($_SESSION[$settings['session_prefix'].'user_id']))
   {
    $template->assign('admin', true);
    $template->assign('user_id', $_SESSION[$settings['session_prefix'].'user_id']);
    $template->assign('user_type', $_SESSION[$settings['session_prefix'].'user_type']);
    
   }
  else
   {
    $template->assign('admin', false);
   }

  $template->assign('settings', $settings);

  $template->assign('BASE_URL', BASE_URL);

  $qsp = explode(',',$qs);
  if($qsp[0] == '')
   {
    define('PAGE', strtolower($settings['index_page']));
   }
  else
   {
    define('PAGE',strtolower($qsp[0]));
   }

  // append comma separated parameters to $_GET ($_GET['get_1'], $_GET['get_2'] etc.):
  if(isset($qsp[1]))
   {
    $items = count($qsp);
    for($i=1;$i<$items;++$i)
     {
      $_GET['get_'.$i] = $qsp[$i];
     }
   }

  #if(isset($_GET['get_1']) && $_GET['get_1']==IMAGE_IDENTIFIER && isset($_GET['get_2']))
  # {
  #  // photo:
  #  include(BASE_PATH.'cms/includes/photo.inc.php');
  # }
  #else
  # {
    // regular content:
    include(BASE_PATH.'cms/includes/content.inc.php');
  # }

  if(isset($_SESSION[$settings['session_prefix'].'user_id'])) $localization->add_language_file(BASE_PATH.'cms/lang/'.$settings['admin_language'].'.admin.lang.php');
  
  // display template:
  if(isset($template_file))
   {
    $template->assign('lang', Localization::$lang);
    $template->assign('content_type', $content_type);
    $template->assign('charset', Localization::$lang['charset']);
    header('Content-Type: '.$content_type.'; charset='.Localization::$lang['charset']);
    $template->display(BASE_PATH.'cms/templates/'.$template_file);
    // create cache file:
    if(isset($cache))
     {
      if($cache->cacheId && $cache->doCaching)
       {
        $cache_content = $cache->createCacheContent($template->fetch(BASE_PATH.'cms/templates/'.$template_file), $content_type, CHARSET);
        $cache->createChacheFile($cache_content);
       }
     }
   }
 } // end try
catch(Exception $exception)
 {
  include('./cms/includes/exception.inc.php');
 }

 /*
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2016-07-18 $Date$ $LastChangedDate: 2016-12-05 18:45:23 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  *
  * $Date$ $Revision$ - Description
  * 2024-12-03: 4.0.0 - Modifid get_magic_quotes_gpc()
  * 2016-07-18: 4.0.0 - Erste Veröffentlichung des neuen 4.x Stamm
  * 2016-07-18: 4.0.0 - Erste Veröffentlichung des neuen 4.x Stamm
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
