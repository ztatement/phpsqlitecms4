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
 * @copyleft (c) 2025 ztatement
 * @version 4.5.0.2025.01.16 $Id: cms/includes/login.inc.php 1 2025-01-16 20:22:12Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *         
 */
// Verhindert direkten Zugriff auf die Datei
if (!defined('IN_INDEX'))
{
  exit();
}

// Prüfe, ob ein Benutzer eingeloggt ist und keine Aktion gesetzt wurde
# if (isset($_SESSION[$settings['session_prefix'] . 'user_id']) && empty($action))
if (!empty($_SESSION[$settings['session_prefix'] . 'user_id']) && empty($action))
{
  session_destroy();
  header("Location: ../");
  exit();
}

// Prüfe, ob kein Benutzer eingeloggt ist und ein Login-Versuch stattfindet
# elseif (empty($_SESSION[$settings['session_prefix'] . 'user_id']) && isset($_POST['username']) && isset($_POST['userpw']))
if (empty($_SESSION[$settings['session_prefix'] . 'user_id']) && !empty($_POST['username']) && !empty($_POST['userpw']))
{
  # $username = $_POST['username'];
  $username = trim($_POST['username']);
  # $userpw = $_POST['userpw'];
  $userpw = trim($_POST['userpw']);

  // Validierung: Benutzername und Passwort dürfen nicht leer sein
  # if (isset($_POST['username']) && trim($_POST['username']) != '' && isset($_POST['userpw']) && trim($_POST['userpw']) != '')
  if ($username !== '' && $userpw !== '')
  {
    // Vorbereiten und Ausführen der Datenbankabfrage
    $dbr = Database::$userdata->prepare('
      SELECT 
        id, name, pw, type, wysiwyg 
      FROM ' . Database::$db_settings['userdata_table'] . ' 
      WHERE lower(name) = lower(:name) 
      LIMIT 1
    ');
    # $dbr->bindValue(':name',mb_strtolower($_POST['username'],CHARSET), PDO::PARAM_STR);
    # $dbr->bindValue(':name', $_POST['username'], PDO::PARAM_STR);
    $dbr->bindValue(':name', $username, PDO::PARAM_STR);
    $dbr->execute();
    # $row = $dbr->fetch();
    $row = $dbr->fetch(PDO::FETCH_ASSOC);

    // Prüfen, ob ein Benutzer gefunden wurde
    # if (isset($row['id']))
    if ($row)
    {
      // Passwort validieren
      # if (is_pw_correct($_POST['userpw'], $row['pw']))
      if (is_pw_correct($userpw, $row['pw']))
      {
        // Benutzerdaten in der Session speichern
        $_SESSION[$settings['session_prefix'] . 'user_id'] = $row['id'];
        $_SESSION[$settings['session_prefix'] . 'user_name'] = $row['name'];
        $_SESSION[$settings['session_prefix'] . 'user_type'] = $row['type'];
        $_SESSION[$settings['session_prefix'] . 'wysiwyg'] = $row['wysiwyg'];

        // Letztes Login-Datum aktualisieren
        $dbr = Database::$userdata->prepare('
          UPDATE ' . Database::$db_settings['userdata_table'] . ' 
          SET last_login = :now 
          WHERE id = :id
        ');
        $dbr->bindValue(':now', time(), PDO::PARAM_INT);
        $dbr->bindValue(':id', $row['id'], PDO::PARAM_INT);
        $dbr->execute();

        // Weiterleitung nach erfolgreichem Login
        header('Location: ./');
        exit();
      }
      else
      {
        $login_failed = true;
      }
    }
    else
    {
      $login_failed = true;
    }
  }
  else
  {
    $login_failed = true;
  }

  // Bei fehlgeschlagenem Login-Versuch
  # if (isset($login_failed))
  if (isset($login_failed) && $login_failed)
  {
    header('Location: index.php?msg=login_failed');
    exit();
  }
}

// Standardaktion, wenn kein Benutzer eingeloggt ist
# elseif (empty($_SESSION[$settings['session_prefix'] . 'user_id']) && empty($action))
if (empty($_SESSION[$settings['session_prefix'] . 'user_id']) && empty($action))
{
  $action = "login";
}

/*
 # @todo Benutzerdatenbank anlegen wenn noch nicht vorhanden
 # try
 # {
 # $newDbPath = Database::copyDatabase('user_name');
 # echo "Datenbank erfolgreich kopiert: $newDbPath";
 # }
 # catch (Exception $e)
 # {
 # echo "Fehler: " . $e->getMessage();
 # }
 */

// Verarbeite die Aktion (z. B. Login-Seite anzeigen)
switch ($action)
{
  case 'login':
    $template->assign('subtitle', Localization::$lang['login']);
    $template->assign('subtemplate', 'login.inc' . TPX);
    break;
}
/**
 * Änderung:
 * PHP 8.x/9-Kompatibilität: @fix isset() wurde durch !empty() ersetzt
 * Verwendet trim() für Eingaben, um unnötige Leerzeichen zu entfernen.
 * Die Abfrage verwendet jetzt PDO::FETCH_ASSOC, um ein assoziatives Array zu erhalten.
 * Überprüft und initialisiert $username und $userpw, um sicherzustellen, dass sie nicht leer sind.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-16 $Date$ $LastChangedDate: 2025-01-16 20:22:12 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-16 : 4.5.0.2025.01.16 - update: PHP 8.x/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
