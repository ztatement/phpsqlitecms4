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
 * @copyleft () 2025 ztatement
 * @version 4.5.0.2025.01.07 $Id: cms/includes/exception.inc.php 1 2025-01-07 22:33:21Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *         
 */
if (!defined('IN_INDEX'))
{
  exit();
}

$debug_mode = true; // In der Produktion wird dieser Wert auf >false< gesetzt.

// Legen wir die Log-Datei fest
$log_file = BASE_PATH . '.log/_errors.log'; // Pfad zur Log-Datei

$website_title = isset($settings['website_title']) ? $settings['website_title'] : 'phpSQLiteCMS';
$lang = isset($localization) ? Localization::$lang['lang'] : 'de';
$charset = isset($localization) && isset(Localization::$lang['charset']) ? Localization::$lang['charset'] : 'utf-8';
$exception_title = isset($localization) && isset(Localization::$lang['exception_title']) ? Localization::$lang['exception_title'] : 'Error';
$exception_message = isset($localization) && isset(Localization::$lang['exception_message']) ? Localization::$lang['exception_message'] : 'Beim Verarbeiten dieser Anweisung ist ein Fehler aufgetreten.';

# header($_SERVER['SERVER_PROTOCOL'] . " 503 Service Unavailable");
$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
header($protocol . " 503 Service Unavailable");
header("Status: 503 Service Unavailable");
header('Content-Type: text/html; charset=' . $charset);

// Wenn wir im Debug-Modus sind, loggen wir die Fehlermeldung
if ($debug_mode && isset($exception))
{
  // Logge den Fehler in der Log-Datei
  $error_message = "Exception on " . date('d.m.Y H:i:s') . "\n";
  $error_message .= "Message: " . $exception->getMessage() . "\n";
  $error_message .= "Code: " . $exception->getCode() . "\n";
  $error_message .= "File: " . $exception->getFile() . "\n";
  $error_message .= "Line: " . $exception->getLine() . "\n";
  $error_message .= "Trace: " . $exception->getTraceAsString() . "\n\n";

  // Schreibe die Fehlerdetails in die Log-Datei
  error_log($error_message, 3, $log_file);
}

// lade das Template für die Fehlerseite
include (BASE_PATH . 'cms/templates/' . 'exception.template.php');

/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-07 $Date$ $LastChangedDate: 2025-01-07 22:33:21 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-07 : 4.5.0.2025.01.07 - auslagern der Fehlerseite "exception.template".
 *                                 Für die Fehlerausgabe $log_file hinzugefügt
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
