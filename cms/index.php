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
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft () 2016 ztatement
 * @version 4.5.0.2025.01.15 $Id: index.php 1 2025-01-15 09:24:07Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS
 *         
 *         
 * @author $Author$
 * @coyleft $Copyright$
 * @version $Rev$: $Revision$ $Id: $
 *          Last changed: $Date$: $LastChangedDate$
 * @name $globalVariableName
 * @package name
 * @subpackage name
 * @since version
 * @todo Description
 *      
 * @license The MIT License (MIT)
 * @see /LICENSE
 * @see https://opensource.org/licenses/MIT Hiermit wird unentgeltlich jeder Person, die eine Kopie der Software und der zugehörigen
 *      Dokumentationen (die "Software") erhält, die Erlaubnis erteilt, sie uneingeschränkt zu nutzen,
 *      inklusive und ohne Ausnahme mit dem Recht, sie zu verwenden, zu kopieren, zu verändern,
 *      zusammenzufügen, zu veröffentlichen, zu verbreiten, zu unterlizenzieren und/oder zu verkaufen,
 *      und Personen, denen diese Software überlassen wird, diese Rechte zu verschaffen,
 *      unter den folgenden Bedingungen:
 *     
 *      Der obige Urheberrechtsvermerk und dieser Erlaubnisvermerk sind in allen Kopien
 *      oder Teilkopien der Software beizulegen.
 *     
 *      DIE SOFTWARE WIRD OHNE JEDE AUSDRÜCKLICHE ODER IMPLIZIERTE GARANTIE BEREITGESTELLT,
 *      EINSCHLIEẞLICH DER GARANTIE ZUR BENUTZUNG FÜR DEN VORGESEHENEN ODER EINEM BESTIMMTEN
 *      ZWECK SOWIE JEGLICHER RECHTSVERLETZUNG, JEDOCH NICHT DARAUF BESCHRÄNKT.
 *      IN KEINEM FALL SIND DIE AUTOREN ODER COPYRIGHTINHABER FÜR JEGLICHEN SCHADEN
 *      ODER SONSTIGE ANSPRÜCHE HAFTBAR ZU MACHEN, OB INFOLGE DER ERFÜLLUNG EINES VERTRAGES,
 *      EINES DELIKTES ODER ANDERS IM ZUSAMMENHANG MIT DER SOFTWARE
 *      ODER SONSTIGER VERWENDUNG DER SOFTWARE ENTSTANDEN.
 *      ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
session_start();

try
{
  define('IN_INDEX', TRUE);

  // @todo Vermeidung von mehrfachen require-Aufrufen, eine zentrale Datei die
  // alle Funktionen und Klassen lädt, was die Wartbarkeit des Codes verbessert.

  // include('./config/db_settings.conf.php');
  require ('./includes/functions.inc.php');
  require ('./includes/functions.admin.inc.php');

  // Lade die Funktionen für Multibyte-Strings, falls sie nicht verfügbar sind
  # if (!defined('MB_CASE_LOWER'))
  if (!function_exists('mb_strlen')) // überprüft die Verfügbarkeit der mb_*-Funktionen
  {
    require ('./includes/functions.mb_replacements.inc.php');
  }

  require ('./includes/classes/Database.php');
  $database = new Database(Database::ADMIN);

  $settings = get_settings();

  // Zugriffsberechtigungsprüfung für nicht registrierte Benutzer:
  if ($settings['check_access_permission'] == 1 && !isset($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    if (is_access_denied())
    {
      raise_error('403');
    }
  }

  // Konstante für verschiedene Verzeichnisse und URLs - Backend:
  define('ADMIN_DIR', 'cms/');
  define('CACHE_DIR', 'cms/cache/');
  define('BASE_URL', get_base_url('cms/'));
  define('STATIC_URL', BASE_URL . 'static/');
  define('BASE_PATH', get_base_path('cms/'));
  // Alle anderen Konstanten werden includiert.
  require (BASE_PATH . ADMIN_DIR . 'config/definitions.conf.php');

  if ($settings['caching'])
  {
    $cache = new Cache(BASE_PATH . CACHE_DIR, $settings);
    # if (empty($settings['admin_auto_clear_cache']))
    # $cache->autoClear = false;
    $cache->autoClear = $settings['admin_auto_clear_cache'] !== false;
  }

  if (isset($cache) && isset($_GET['clear_cache']) && isset($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    $cache->clear();
    header('Location: ' . BASE_URL);
    exit();
  }

  // Setze die Zeitzone:
  if ($settings['time_zone'])
  {
    date_default_timezone_set($settings['time_zone']);
  }

  # require('./lang/'.$settings['admin_language_file']);
  $localization = new Localization(BASE_PATH . 'cms/lang/' . $settings['admin_language'] . '.admin.lang.php');
  define('CHARSET', Localization::$lang['charset']);

  // Template setzen
  require ('./includes/classes/Template.php');
  $template = new Template();
  $template->assign('settings', $settings);
  # $template->set_settings($settings);

  // Setze die lokale Sprache:
  setlocale(LC_ALL, Localization::$lang['locale']);
  $template->assign('lang', Localization::$lang); // Übergibt die Sprachdaten an das Template

  // @todo Für das Backend verfügbar machen, funktioniert so nicht in Subtemplates.
  // Erstelle eine Instanz der Klasse PlatzhalterBild und speichere sie in $GLOBALS
  # require_once './includes/classes/PlatzhalterBild.php'; // Einbinden der PlatzhalterBild-Klasse
  # $platzhalter = new PlatzhalterBild(); // Instanz erstellen
  # $GLOBALS['platzhalter'] = $platzhalter; // In $GLOBALS speichern

  # $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'admin_index';
  $mode = $_REQUEST['mode'] ?? 'admin_index';
  $template->assign('mode', $mode);
  $template->assign('lang', Localization::$lang); // Sicherstellen, dass 'lang' auch hier gesetzt ist

  // Gehe zum Login, wenn der Benutzer nicht eingeloggt ist:
  if (empty($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    $mode = 'login';
    $template->assign('admin', false);
  } else
  {
    $template->assign('admin', true);
    $template->assign('user_id', $_SESSION[$settings['session_prefix'] . 'user_id']);
    $template->assign('user_type', $_SESSION[$settings['session_prefix'] . 'user_type']);
  }

  // Inklusive der entsprechenden Datei basierend auf dem Modus:
  switch ($mode)
  {
    case 'login':
    case 'logout':
      include ('./includes/login.inc.php');
      break;
    case 'dashboard':
      include ('./includes/dashboard.inc.php');
      break;
    case 'edit':
      include ('./includes/edit.inc.php');
      break;
    case 'pages':
      include ('./includes/pages.inc.php');
      break;
    case 'galleries':
      include ('./includes/galleries.inc.php');
      break;
    case 'gcb':
      include ('./includes/gcb.inc.php');
      break;
    case 'notes':
      include ('./includes/notes.inc.php');
      break;
    case 'comments':
      include ('./includes/comments.inc.php');
      break;
    case 'filemanager':
      include ('./includes/filemanager.inc.php');
      break;
    case 'spam_protection':
      include ('./includes/spam_protection.inc.php');
      break;
    case 'users':
      include ('./includes/users.inc.php');
      break;
    case 'settings':
      include ('./includes/settings.inc.php');
      break;
    case 'menus':
      include ('./includes/menus.inc.php');
      break;
    case 'image':
      include ('./includes/insert_image.inc.php');
      break;
    case 'modal':
      include ('./includes/modal.inc.php');
      break;
    case 'thumbnail':
      include ('./includes/insert_thumbnail.inc.php');
      break;
    case 'ajaxprocess':
      include ('./includes/ajaxprocess.inc.php');
      break;
    case 'tinymceimage':
      include ('./includes/tinymceimage.inc.php');
      break;
    default:
      include ('./includes/admin_index.inc.php');
  }

  // Zuordnung von Mode und Sprache an das Template:
  # $template->assign('mode', $mode);
  # $template->assign('lang', Localization::$lang);
  # #$template->set_lang($lang);

  # header('Content-Type: text/html; charset=' . Localization::$lang['charset']);
  # if (empty($template_file))
  # {
  # $template_file = 'main' . TPX;
  # }
  # $template->display(BASE_PATH . 'cms/templates/admin/' . $template_file);
  $template->display(BASE_PATH . 'cms/templates/admin/' . ($template_file ?? 'main.template.php'));
} // Ende try

catch (Exception $exception)
{
  error_log('Error: ' . $exception->getMessage());
  include ('./includes/exception.inc.php');
}
/**
 * Was wurde geändert?
 * Einbinden der Klasse PlatzhalterBild und Erstellen der Instanz: 
 * Direkt nach der session_start() und vor der Verarbeitung des restlichen Codes
 * (einschließlich Template-Inhalte) wird die Klasse PlatzhalterBild mit require_once eingebunden.
 * Speichern der Instanz in $GLOBALS: Die Instanz der Klasse PlatzhalterBild wird
 * mit $GLOBALS['platzhalter'] = $platzhalter; in die globale Variable gespeichert.
 * Dadurch kann in jedem Template oder anderen inkludierten PHP-Dateien auf diese Instanz zugegriffen werden.
 * Folgend muss nur src=" <?= $GLOBALS['platzhalter']->getBildTag('500x300') ?> " in ein img Tag eingefügt werden.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-15 $Date$ $LastChangedDate: 2025-01-15 09:24:07 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$  - Description
 * 2025-01-15 : 4.5.0.2025.01.15    : ztatement        - @fix ??
 * 2025-01-08 : 4.5.0.2025.01.08    : ztatement        - Codekorrekturen, Konfiguration und Logik
 *                                                       besser strukturiert
 * 2024-12-30 : 4.5.0.2024.12.30    : ztatement        - @new: Platzhalter Bild.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
