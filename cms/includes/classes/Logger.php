<?php
/**
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  *
  * @version 4.5.0.2025.02.05
  * @file $Id: cms/includes/classes/Logger.php 1 Wed, 05 Feb 2025 15:09:48 +0100Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  *
  * --------
  *
  * Funktionsklasse zum Fehler loggen basierend auf dem Level in den Einstellungen
  * Hilfsfunktionen für die ExceptionHandler-Klasse
  */

namespace cms\includes\classes;

class Logger {

  // Log-Level definieren
  const LEVEL_DEBUG = 'DEBUG';

  const LEVEL_INFO = 'INFO';

  const LEVEL_WARNING = 'WARNING';

  const LEVEL_ERROR = 'ERROR';

  const LEVEL_CRITICAL = 'CRITICAL';

/**
  * Gibt das zu verwendende Log-Level basierend auf den Einstellungen zurück.
  *
  * @param array $settings Die Einstellungen mit dem log_level.
  * @return string Das Log-Level.
  */
  public static function getLogLevel (array $settings): string
  {
    // Standardwert auf 'ERROR', falls kein gültiger Wert gesetzt ist.
    $logLevel = isset($settings['log_level']) ? strtoupper($settings['log_level']) : self::LEVEL_ERROR;

    // Überprüfen, ob der angegebene Wert ein gültiges Log-Level ist
    if (! in_array($logLevel, [self::LEVEL_DEBUG,self::LEVEL_INFO,self::LEVEL_WARNING,self::LEVEL_ERROR,self::LEVEL_CRITICAL]))
    {
      // Wenn das Log-Level ungültig oder nicht gesetzt ist, logge eine Warnung
      $invalidLogLevelMessage = "Kein gültiges Log-Level gesetzt, Standardwert 'ERROR' wird verwendet.";
      self::logMessage(self::LEVEL_WARNING, $invalidLogLevelMessage, $settings['log_file']);

      // Setze den Standardwert 'ERROR'
      $logLevel = self::LEVEL_ERROR;
    }

    return $logLevel;
  }


/**
  * Loggt eine Nachricht basierend auf dem Log-Level.
  *
  * @param string $level Das Log-Level.
  * @param string $message Die zu loggende Nachricht.
  * @param string $logFile Der Pfad zur Log-Datei.
  */
  public static function logMessage (string $level, string $message, string $logFile): void
  {
    $logMessage = "[" . strtoupper($level) . "] " . date('Y-m-d H:i:s') . "\n";
    $logMessage .= $message . "\n";
    $logMessage .= "----------------------------------\n";

    // Logge den Fehler entsprechend dem Log-Level
    error_log($logMessage, 3, $logFile);
  }


/**
  * Hilfsfunktion zum Loggen basierend auf dem Level in den Einstellungen.
  *
  * @param array $settings Die Einstellungen mit log_level.
  * @param string $message Die zu loggende Nachricht.
  * @param string $logFile Der Pfad zur Log-Datei.
  */
  public static function log (array $settings, string $message, string $logFile): void
  {
    $level = self::getLogLevel($settings); // Hole das aktuelle Log-Level

    // Nur loggen, wenn der gewählte Level den aktuellen Log-Level überschreitet
    $levelsHierarchy = [self::LEVEL_DEBUG => 1,self::LEVEL_INFO => 2,self::LEVEL_WARNING => 3,self::LEVEL_ERROR => 4,self::LEVEL_CRITICAL => 5];

    // Falls der aktuelle Level das gewählte Level überschreitet, logge die Nachricht
    if ($levelsHierarchy[$level] <= $levelsHierarchy[self::LEVEL_ERROR])
    {
      self::logMessage($level, $message, $logFile);
    }
  }


}


/**
  * Änderung:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-05 $ 
  * @date $LastChangedDate: Wed, 05 Feb 2025 15:09:48 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-05 : 4.5.0.2025.02.05    : ztatement        - neu angelegt
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
