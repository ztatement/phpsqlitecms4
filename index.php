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
 * @version 4.5.0.2025.01.14 $Id: index.php 1 2025-01-14 01:11:09Z ztatement $
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

// if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') )
/*
 * if (substr_count (filter_input (INPUT_SERVER, 'HTTP_ACCEPT_ENCODING', FILTER_SANITIZE_STRING), 'gzip') )
 * {
 * ob_start("ob_gzhandler");
 * } else {
 * ob_start();
 * }
 */
// Startet das Output Buffering, um die Ausgabe zu puffern
function startOutputBuffering( ): void
{
  // Ruft den Accept-Encoding Header ab
  $acceptEncoding = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING');

  // Bereinigen der Eingabe, um HTML-Sonderzeichen zu konvertieren
  if ($acceptEncoding !== null)
  {
    $acceptEncoding = htmlspecialchars($acceptEncoding, ENT_QUOTES, 'UTF-8');
  }

  // Überprüfen, ob zlib.output_compression bereits aktiviert ist
  # if ($acceptEncoding && strpos($acceptEncoding, 'gzip') !== false)
  if (ini_get('zlib.output_compression'))
  {
    ob_start(); // Nur ob_start ohne Handler, falls zlib aktiv ist
  }
  elseif ($acceptEncoding && strpos($acceptEncoding, 'gzip') !== false)
  {
    ob_start("ob_gzhandler"); // Aktiviert gzip Komprimierung, wenn akzeptiert
  }
  else
  {
    ob_start(); // Standard Output Buffering ohne Komprimierung
  }
}
/*
 * //Warning: ob_start(): Output handler 'ob_gzhandler' conflicts with 'zlib output compression
 * Änderungen:
 * Überprüfung von `zlib.output_compression`:
 * Der Code prüft, ob die zlib-Komprimierung bereits aktiviert ist.
 * Wenn ja, wird einfach `ob_start()` ohne den Gzip-Handler aufgerufen, um den Konflikt zu vermeiden.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

// Aufruf der Funktion zum Starten des Output Buffers
startOutputBuffering();

require_once './cms/includes/classes/Minify.php'; // Minify-Klassen laden

session_start(); // Startet die Session

define('CACHE_DIR', 'cms/cache/'); // Definiert das Cache-Verzeichnis

// get query string passed by mod_rewrite:
# if (isset($_GET['qs']))
# {
// if(get_magic_quotes_gpc()) $_GET['qs'] = stripslashes($_GET['qs']);
# $_GET['qs'] = stripslashes($_GET['qs']);
// Holt die URL-Parameter und prüft sie
# $qs = $_GET['qs'];
// Nutzt null coalescing operator für kürzere Zuweisung
$qs = $_GET['qs'] ?? '';
# }
# else
# {
# $qs = '';
# }

// Prüft, ob eine zwischengespeicherte Seite existiert
if (empty($_POST) && file_exists('./' . CACHE_DIR . 'settings.php'))
{
  include './' . CACHE_DIR . 'settings.php';
  $settings = get_settings();

  if (empty($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    # if ($qs == '')
    # {
    # $cache_file = rawurlencode(strtolower($settings['index_page'])) . '.cache';
    $cache_file = empty($qs) ? rawurlencode(strtolower($settings['index_page'])) . '.cache' : rawurlencode(strtolower($qs)) . '.cache';
    # }
    # else
    # {
    # $cache_file = rawurlencode(strtolower($qs)) . '.cache';
    # }
    if (file_exists('./' . CACHE_DIR . $cache_file))
    {
      include './' . CACHE_DIR . $cache_file;
      exit(); // Wenn die gecachte Seite vorhanden ist, beenden
    }
  }
}

define('IN_INDEX', true); // Definiert IN_INDEX als Konstantenwert

try
{
  // Lädt benötigte Konfigurations- und Funktionsdateien
  # throw new Exception('Error message...');
  # require('./cms/config/db_settings.conf.php');
  require './cms/includes/functions.inc.php';

  // load replacement functions for the multibyte string functions
  // if they are not available:
  # if(!defined('MB_CASE_LOWER')) require('./cms/includes/functions.mb_replacements.inc.php');
  require './cms/includes/classes/Database.php';

  $database = new Database();

  $settings = get_settings(); // Holt die Einstellungen aus der Datenbank

  // Zugriffsberechtigungsprüfung für nicht registrierte Benutzer
  if ($settings['check_access_permission'] == 1 && !isset($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    if (is_access_denied())
    {
      raise_error('403'); // Falls Zugriff verweigert wird, eine Fehlermeldung werfen
    }
  }

  // Setzt die Zeitzone, falls in Datenbank definiert
  if ($settings['time_zone'])
  {
    date_default_timezone_set($settings['time_zone']);
  }

  // Definiere URL- und Pfad-Konstanten nur für das Frondend
  // Kann wegen Backend nicht ausgelagert werden da es sonst zu Fehlern kommt.
  define('BASE_URL', get_base_url());
  define('STATIC_URL', BASE_URL . 'static/'); // Definiert den statischen URL-Pfad z.B. static.subdomain.com
  define('BASE_PATH', get_base_path()); // Definiert den Basis-Pfad
                                        // Alle anderen Konfigurationsdateien werden eingebunden.
  require BASE_PATH . 'cms/config/definitions.conf.php';

  require_once BASE_PATH . 'cms/includes/classes/PlatzhalterBild.php'; // Einbinden der PlatzhalterBild-Klasse
  $platzhalter = new PlatzhalterBild(); // Erstellen einer Instanz der Platzhalterbild-Klasse
  $GLOBALS['platzhalter'] = $platzhalter; // Die Instanz wird in den globalen Variablen gespeichert

  if ($settings['content_functions'] == 1)
  {
    require BASE_PATH . 'cms/includes/functions.content.inc.php'; // Lädt Content-spezifische Funktionen
  }

  // Template-Klasse einbinden
  require BASE_PATH . 'cms/includes/classes/Template.php';
  $template = new Template();
  # $template->set_settings($settings);

  if ($settings['caching'])
  {
    $cache = new Cache(BASE_PATH . CACHE_DIR, $settings); // Instanz der Cache-Klasse
    if (!empty($_POST) || isset($_SESSION[$settings['session_prefix'] . 'user_id']))
    {
      $cache->doCaching = false; // Caching deaktivieren, wenn POST-Daten existieren oder der User eingeloggt ist
    }
  }

  // Überprüft, ob der Benutzer eingeloggt ist und weist die entsprechenden Variablen zu
  if (isset($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    $template->assign('admin', true);
    $template->assign('user_id', $_SESSION[$settings['session_prefix'] . 'user_id']);
    $template->assign('user_type', $_SESSION[$settings['session_prefix'] . 'user_type']);
  }
  else
  {
    $template->assign('admin', false);
  }

  // Setze die Template-Daten
  $template->assign('settings', $settings);
  $template->assign('BASE_URL', BASE_URL);

  // Bestimme die Seite
  $qsp = explode(',', $qs);
  if ($qsp[0] == '')
  {
    define('PAGE', strtolower($settings['index_page']));
  }
  else
  {
    define('PAGE', strtolower($qsp[0]));
  }

  // Komma getrennte Parameter anhängen an $_GET ($_GET['get_1'], $_GET['get_2'] etc.)
  if (isset($qsp[1]))
  {
    $items = count($qsp);
    for ($i = 1; $i < $items; ++$i)
    {
      $_GET['get_' . $i] = $qsp[$i];
    }
  }

  # if(isset($_GET['get_1']) && $_GET['get_1']==IMAGE_IDENTIFIER && isset($_GET['get_2']))
  # {
  # // photo:
  # include(BASE_PATH.'cms/includes/photo.inc.php');
  # }
  # else
  # {

  // Lädt den Inhalt
  include BASE_PATH . 'cms/includes/content.inc.php';
  # }

  // Falls der Benutzer eingeloggt ist, Sprachdateien für den Admin-Bereich laden
  if (isset($_SESSION[$settings['session_prefix'] . 'user_id']))
  {
    $localization->add_language_file(BASE_PATH . 'cms/lang/' . $settings['admin_language'] . '.admin.lang.php');
  }

  // Template anzeigen
  # function displayTemplate( $template, $template_file, $content_type, $cache = null )
  function displayTemplate( Template $template, string $template_file, string $content_type, ?Cache $cache = null ): void
  {

    // Überprüfen, ob die Template-Datei gesetzt ist
    if (isset($template_file))
    {

      // Zuweisungen an das Template
      $template->assign('lang', Localization::$lang);
      $template->assign('content_type', $content_type);
      $template->assign('charset', Localization::$lang['charset']);

      // Header setzen
      header('Content-Type: ' . $content_type . ';charset=' . Localization::$lang['charset']);

      // Template anzeigen
      $template->display(BASE_PATH . 'cms/templates/' . $template_file);

      // Cache-Datei erstellen, falls Cache-Objekt übergeben wurde
      # if (isset($cache)){
      # if ($cache->cacheId && $cache->doCaching){
      if ($cache !== null && $cache->cacheId && $cache->doCaching)
      {
        $cache_content = $cache->createCacheContent($template->fetch(BASE_PATH . 'cms/templates/' . $template_file), $content_type, CHARSET);
        $cache->createChacheFile($cache_content);
      }
      # }
    }
  }

  // Template anzeigen
  # displayTemplate($template, $template_file, $content_type, $cache);
  displayTemplate($template, $template_file, $content_type);
} // end try

catch (Exception $exception)
{

  include './cms/includes/exception.inc.php'; // Fehlerbehandlung, wenn etwas schief geht
}
/*
 * Änderungen:
 * Die Verwendung von `FILTER_SANITIZE_STRING` entfernt,
 * da sie in PHP8 nicht mehr empfohlen wird.
 * Verwendung von `htmlspecialchars()`**: Diese Funktion konvertiert spezielle HTML-Zeichen
 * in ihre entsprechenden HTML-Entitäten, was hilft, XSS-Angriffe zu verhindern,
 * wenn die Eingabe in HTML ausgegeben wird.
 * Überprüfung, ob `$acceptEncoding` nicht `null` ist,
 * bevor wir `htmlspecialchars()` aufrufen.
 * Verwendung des null coalescing-Operators (??), um sicherzustellen, 
 * dass keine undefinierten Variablen verwendet werden.
 * startOutputBuffering() und displayTemplate() haben jetzt void als Rückgabewert deklariert.
 * Der Parameter $cache hat jetzt den Typ ?Cache anstelle von Cache = null. 
 * Das ? bedeutet, dass der Parameter entweder ein Cache-Objekt oder null sein kann.
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-14 $Date$ $LastChangedDate: 2025-01-14 18:01:24 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * ---------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$  - Description
 * 2025-01-14 : 4.5.0.2025.01.14    : ztatement        - @fix: void und Caching-Mechanismus angepasst
 * 2024-12-30 : 4.5.0.2024.12.30    : ztatement        - @new: Platzhalter Bild.
 * 2024-12-04 : 4.1.4.2024.12.04    : ztatement        - ad function displayTemplate
 * 2024-12-03 : 4.0.3.2024.12.04    : ztatement        - Modifid get_magic_quotes_gpc()
 * 2016-07-18 : 4.0.0 - Erste Veröffentlichung des neuen 4.x Stamm
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
