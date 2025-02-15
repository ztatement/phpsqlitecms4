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
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft (c) 2025 ztatement
 * 
 * @version 4.5.0.2025.01.22 $Id: cms/includes/classes/Database.php 1 2025-01-22 10:22:29Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 */
class Database {

  const ADMIN = 1;

  private static $_instance = null;

  public static $db_settings;

  public static $complete;

  public static $content;

  public static $entries;

  public static $userdata;

  public static $account;

  public function __construct ($mode = 0)
  {
    // Setzt die Instanz der Klasse auf das aktuelle Objekt
    self::$_instance = $this;

    // Die Konfigurationsdatei abhängig vom Modus laden
    if ($mode == 0)
    {
      require ('./cms/config/db_settings.conf.php');
    }
    else
    {
      require ('./config/db_settings.conf.php');
    }

    self::$db_settings = $db_settings;

    // Überprüft den Typ der Datenbank und stellt eine Verbindung her
    try
    {
      switch (self::$db_settings['type'])
      {
        case 'sqlite':
          // Wenn der Modus 0 (normal) ist, verwenden wir die Pfade aus dem aktuellen Verzeichnis
          if ($mode == 0)
          {
            self::$content = new PDO(
              'sqlite:' . self::$db_settings['db_content_file']);
            self::$entries = new PDO(
              'sqlite:' . self::$db_settings['db_entry_file']);
            self::$account = new PDO(
              'sqlite:' . self::$db_settings['db_account_file']);
            # self::$content->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # self::$entries->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # self::$account->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          }
          // Wenn der Modus 1 (remote) ist, verwenden wir die Pfade aus dem übergeordneten Verzeichnis
          if ($mode == 1)
          {
            self::$content = new PDO(
              'sqlite:../' . self::$db_settings['db_content_file']);
            self::$entries = new PDO(
              'sqlite:../' . self::$db_settings['db_entry_file']);
            self::$userdata = new PDO(
              'sqlite:../' . self::$db_settings['db_userdata_file']);
            self::$account = new PDO(
              'sqlite:../' . self::$db_settings['db_account_file']);
            # self::$content->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # self::$entries->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # self::$userdata->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # self::$account->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          }
          // Fehlerbehandlung aktivieren
          self::$content->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          self::$entries->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          self::$account->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          if (isset(self::$userdata))
          {
            self::$userdata->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          }
        break;

        case 'mysql':
          // Verbindung zu MySQL-Datenbank herstellen
          self::$complete = new PDO(
            'mysql:host=' . self::$db_settings['host'] . ';port=' . self::$db_settings['port'] . ';dbname=' . self::$db_settings['database'],
            self::$db_settings['user'],
            self::$db_settings['password']);
          self::$complete->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          self::$complete->query("set names utf8");
          // Wir setzen sowohl 'content' als auch 'entries' auf die Haupt-Datenbank
          self::$content = self::$complete;
          self::$entries = self::$complete;
          // Im Modus 1 auch die 'userdata' auf die Haupt-Datenbank setzen
          if ($mode == 1)
            # self::$userdata = &self::$complete; // Verweis-Operatoren (&) enfernt
            self::$userdata = self::$complete;
        break;

        case 'postgresql':
          self::$complete = new PDO(
            'pgsql:dbname=' . self::$db_settings['database'] . ';host=' . self::$db_settings['host'] . ';user=' . self::$db_settings['user'] . ';password=' . self::$db_settings['password']);
          self::$complete->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          // Wir setzen sowohl 'content' als auch 'entries' auf die Haupt-Datenbank
          # self::$complete->query("set names utf8");
          self::$content = self::$complete;
          self::$entries = self::$complete;

          // Im Modus 1 auch die 'userdata' auf die Haupt-Datenbank setzen
          if ($mode == 1)
            self::$userdata = self::$complete;
        break;

        default:
          // Fehlerbehandlung, falls der Datenbanktyp nicht unterstützt wird
          # echo "<p>Database type not supported.</p>";
          # exit();
          throw new \Exception(
            Helpers::escapeAndDecodeHtml($lang['db_type_not_supp']) . self::$db_settings['type']);
      }
    }
    catch (\PDOException $e)
    {
      // Fehlerbehandlung bei PDO-Verbindungsfehlern
      echo "<p>" . Helpers::escapeAndDecodeHtml($lang['error_database']) . $e->getMessage() . "</p>";
      exit();
    }
    catch (\Exception $e)
    {
      // Fehlerbehandlung für andere Ausnahmen
      echo "<p>" . Helpers::escapeAndDecodeHtml($lang['error']) . $e->getMessage() . "</p>";
      exit();
    }
  }

  // Singleton-Muster: Gibt die Instanz der Datenbank zurück
  # public static function getInstance()
  public static function getInstance (): Database
  {
    return self::$_instance;
  }

  /**
   * Kopiert die SQLite-Datenbank und fügt den Benutzernamen zum Dateinamen hinzu.
   *
   * @param string $username Der Benutzername, der an den Dateinamen angehängt werden soll.
   * @return string Der Pfad zur neuen, kopierten Datenbank.
   * @throws Exception Falls beim Kopieren ein Fehler auftritt.
   */
  public static function copyDatabase (string $username): string
  {
    // Sicherstellen, dass die Konfiguration geladen ist
    if (! self::$db_settings)
    {
      throw new Exception(
        "Datenbankkonfiguration ist nicht geladen.");
    }

    // Original-Datenbankpfad abrufen
    $originalDbPath = self::$db_settings['db_account_file']; // Beispiel: 'path/to/account.sqlite'

    // Überprüfen, ob die Original-Datenbank existiert und lesbar ist
    if (! file_exists($originalDbPath))
    {
      throw new Exception(
        "Die Original-Datenbank existiert nicht: $originalDbPath");
    }
    if (! is_readable($originalDbPath))
    {
      throw new Exception(
        "Die Original-Datenbank ist nicht lesbar: $originalDbPath");
    }

    // Sicherstellen, dass das Zielverzeichnis schreibbar ist
    $pathInfo = pathinfo($originalDbPath);
    $targetDir = $pathInfo['dirname'];
    if (! is_writable($targetDir))
    {
      throw new Exception(
        "Das Zielverzeichnis ist nicht schreibbar: $targetDir");
    }

    // Neuen Dateipfad mit Benutzername im Dateinamen erstellen
    $newDbPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '_' . $username . '.' . $pathInfo['extension'];

    // Versuchen, die Datei zu kopieren
    try
    {
      if (! copy($originalDbPath, $newDbPath))
      {
        throw new Exception(
          "Fehler beim Kopieren der Datenbank: $originalDbPath nach $newDbPath");
      }
    }
    catch (\Exception $e)
    {
      // Fehler beim Kopieren behandeln
      throw new Exception(
        "Fehler beim Kopieren der Datenbank: " . $e->getMessage());
    }

    // Rückgabe des Pfads zur neuen Datenbank
    return $newDbPath;
  }

  /**
   * Führt eine SQL-Abfrage aus und gibt das Ergebnis zurück.
   *
   * @param object $db Die Datenbankverbindung
   * @param string $query Die SQL-Abfrage
   * @return mixed Das Ergebnis der SQL-Abfrage oder false im Fehlerfall
   */
  public static function executeQuery ($db, $query)
  {
    try
    {
      // Wir verwenden vorbereitete Statements, um SQL-Injection zu verhindern.
      $stmt = $db->prepare($query); // Verwenden von prepared statements ist sicherer
      $stmt->execute(); // SQL ausführen

      // Wenn die Abfrage Daten zurückgibt (SELECT), gebe sie zurück
      if (strpos($query, 'SELECT') !== false)
      {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }

      return true; // Erfolgreiche Ausführung
    }
    catch (\PDOException $e)
    {
      // Exception Handling für PDO Fehler
      // PDOException wird ab PHP 8 für PDO Fehler geworfen
      error_log("Fehler bei der SQL-Abfrage: " . $e->getMessage());
      return false;
    }
  }
}

/**
 * Änderung:
 * PHP 8.x/9-Kompatibilität: Der &-Operator für die Referenzzuweisung
 * (z.B. self::$content = &self::$complete;) wurde entfernt.
 * In allen PDO-Verbindungen wurde der setAttribute(PDO::ATTR_ERRMODE,
 * PDO::ERRMODE_EXCEPTION)-Befehl hinzugefügt.
 * copyDatabase() Eine präzisere Fehlerbehandlung wurde hinzugefügt.
 * Erweiterte Fehlerbehandlung (PDOException)
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-22 $Date$ 
 * @date $LastChangedDate: 2025-01-22 10:22:29 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-22 : 4.5.0.2025.01.22 - added: function executeQuery
 * 2025-01-20 : 4.5.0.2025.01.20 - update: try-catch-Verwendung
 * 2025-01-15 : 4.5.0.2025.01.15 - update: PHP 8.x/9 Kompatibilität
 *                                 @fix Verweis-Operator (&) erstzt durch Standardzuweisung
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
  