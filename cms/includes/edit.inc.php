<?php
/**
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Mark Hoschek < mail at mark-hoschek dot de >
  * @copyright (c) 2014 Mark Hoschek
  *
  * @version last 3.2015.04.02.18.42
  * @link http://phpsqlitecms.net/
  * @package phpSQLiteCMS
  *
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  *
  * @version 4.5.0.2025.02.16
  * @file $Id: cms/includes/edit.inc.php 1 Sun Dec 16 2012 02:45:46 GMT+0100Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  *
  * ----------------
  *
  * Dieses Skript dient der Bearbeitung von Seiteninhalten.
  * Es berücksichtigt Benutzerberechtigungen und ermöglicht es, Seiteninhalte
  * entweder anzulegen oder zu bearbeiten.
  */

// Sicherheitsabfrage: Direktzugriff verhindern
if (! defined('IN_INDEX'))
{
  exit('Direkter Zugriff nicht erlaubt.');
}

// Prüfen, ob der Benutzer eingeloggt ist
if (isset($_SESSION[$settings['session_prefix'] . 'user_id']))
{

  // Die aktuelle Aktion festlegen, Standard: 'main'
  $action = $_REQUEST['action'] ?? 'main';

  // Seitentypen aus der Konfiguration laden
  $page_types = Config::get_page_types();

  // Benutzer laden
  $user_result = Database::$userdata->query("
    SELECT
      id, name
    FROM " . Database::$db_settings['userdata_table'] . "
    ORDER BY id ASC
  ");

  // Benutzerliste erstellen
  $users = [];
  while ($data = $user_result->fetch())
  {
    $users[(int) $data['id']] = Helpers::escapeHtml($data['name']);
  }

  // Initialisierung der Edit-Klasse
  $edit = new Edit($template, $settings, $users, $page_types);

  // Verarbeitung der Bearbeitungslogik
  $edit->process($action);
}


/*
 * @todo Fehlerbehandlung mit try-catch und Fehlerbehandlung mit ExceptionHandler::error() oder std. Exceptions mit $e->getMessage()
 */

/**
 * Änderung:
 * Der gesamte Code, der zuvor in edit.inc.php war (einschließlich der WYSIWYG-Verwaltung,
 * des Abrufens der Seitendaten, der Formularverarbeitung und der Switch-Anweisung), wurde entfernt.
 * Die neue Edit-Klasse wird mit den erforderlichen Parametern (Template, Einstellungen, Benutzer, Seitentypen) initialisiert.
 * Die process-Methode der Edit-Klasse wird aufgerufen, um die gesamte Bearbeitungslogik zu verarbeiten.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-02-16 $
 * @Date $LastChangedDate: Sun, 16 Feb 2025 00:07:58 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * ----------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$  - Description
 * 2025-02-16 : 4.5.0.2025.02.16    : ztatement        - update: Code komplett überarbeitet und entferten Code in der Edit Klasse wiederverwendet.
 * 2025-01-20 : 4.5.0.2025.01.20    : ztatement        - update: PHP 8.4+/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
