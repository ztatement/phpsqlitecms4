<?php

/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Mark Alexander Hoschek <alex at phpsqlitecms dot net>
 * @copyright 2006-2010 Mark Alexander Hoschek
 *
 * @version last 3.2015.04.02.18.42
 * @link http://phpsqlitecms.net/
 * @package phpSQLiteCMS
 * 
 *
 * @modified by
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft (c) 2025 ztatement
 *
 * @version 4.5.0.2025.01.28 
 * @file $Id: cms/includes/classes/Localization.php 1 2012-12-16 02:46:16Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 * 
 */
class Localization {

  const FORMAT_TIME = true;

  private static $_instance = null;

  public static $lang;

  private static $_lang;

  private $replacement;

  /**
   * Konstruktor der Klasse.
   * Lädt eine Sprachdatei, wenn sie angegeben wird.
   *
   * @param string $language_file  Pfad zur Sprachdatei.
   * @throws Exception  Wenn keine Sprachdatei angegeben ist.
   */
  public function __construct ($language_file)
  {
    // Instanz wird gespeichert
    self::$_instance = $this;

    // Überprüfen, ob eine Sprachdatei angegeben wurde
    if ($language_file)
    {
      // Sicherstellen, dass die Datei nur einmal geladen wird
      # require ($language_file);
      require_once ($language_file);
      self::$lang = $lang;
    }
    else
    {
      // Wenn keine Datei angegeben wird, wird eine Ausnahme geworfen
      # die('No language file specifed!');
      throw new Exception(
        'No language file specified!');
    }
  }

  /**
   * Gibt eine Liste der verfügbaren Sprachen zurück.
   *
   * Diese Funktion durchsucht den Ordner für Sprachdateien und gibt die verfügbaren
   * Sprachen in einem detaillierten Format zurück.
   *
   * @param bool $admin  Gibt an, ob die Sprachdateien für das Admin-Panel oder die öffentliche Seite geladen werden sollen.
   *
   * @return array|false  Ein Array der verfügbaren Sprachen im Detailformat oder `false`, wenn keine Sprachen gefunden wurden.
   */
  public function get_languages (bool $admin = false)
  {
    // Definiert das Dateischema, je nachdem ob für Admin oder öffentliche Seite
    $file_schema = $admin ? '.admin.lang.php' : '.lang.php';
    $length = 0 - strlen($file_schema);
    $languages = [];

    // Durchläuft alle Dateien im Sprachverzeichnis, die dem Schema entsprechen
    foreach (glob(BASE_PATH . 'cms/lang/*' . $file_schema) as $filename)
    {
      $languages[] = substr(basename($filename), 0, $length); // Dateiname ohne Erweiterung ".[admin|page].lang.php"
    }

    // Wenn Sprachen gefunden wurden, sortiere sie und gebe die Details zurück
    if (! empty($languages))
    {
      natcasesort($languages);
      $languages_detailed = [];
      foreach ($languages as $i => $language)
      {
        $languages_detailed[$i]['identifier'] = $language;
        $languages_detailed[$i]['name'] = $this->get_language_name($language); // Sprachname holen
      }
      return $languages_detailed;
    }

    return false; // Keine Sprachen gefunden
  }

  /**
   * Hilfsfunktion zur Formatierung eines Sprachstrings in ein lesbares Format.
   *
   * Diese Funktion formatiert einen Sprachcode im Format 'en_US' zu einem lesbaren
   * Namen, z.B. 'Englisch (US)' oder 'Deutsch' für den String 'de'.
   *
   * @param string $string Der Sprachcode im Format 'en_US' oder nur 'de'.
   *
   * @return string Der formatierte Sprachname.
   */
  public function get_language_name (string $string): string
  {
    if (empty($string))
    {
      return 'Unbekannte Sprache'; // Standardwert, falls der Eingabestring leer ist
    }

    $string_parts = explode('_', $string);

    if (isset($string_parts[1]))
    {
      $name = ucfirst($string_parts[0]) . ' (' . strtoupper($string_parts[1]) . ')';
    }
    else
    {
      $name = ucfirst($string_parts[0]);
    }

    return $name;
  }

  /**
   * Übersetzt einen gegebenen Schlüssel in der angegebenen Sprache.
   *
   * Diese Funktion prüft, ob ein Schlüssel für die angegebene Sprache vorhanden ist,
   * und gibt den entsprechenden übersetzten Text zurück. Falls der Schlüssel nicht
   * existiert, wird der Schlüssel selbst zurückgegeben.
   *
   * @param string $key Der Übersetzungsschlüssel.
   * @param string $language Die gewünschte Sprache.
   *
   * @return string Der übersetzte Text oder der Schlüssel, wenn keine Übersetzung gefunden wurde.
   */
  public function translate (string $key, string $language): string
  {
    global $lang; // Zugriff auf das $lang-Array

    // Prüfen, ob der Key für die angegebene Sprache existiert
    if (isset($lang[$language][$key]))
    {
      return htmlspecialchars($lang[$language][$key], ENT_QUOTES, 'UTF-8');
    }

    // Falls der Key nicht existiert, gib den Key selbst zurück
    return htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
  }

  /**
   * Fügt eine weitere Sprachdatei hinzu, indem der Inhalt in die bestehende Sprachdatei integriert wird.
   *
   * @param string $language_file  Pfad zur Sprachdatei.
   */
  public function add_language_file ($language_file)
  {
    # require($language_file);
    require_once ($language_file);
    self::$lang = array_merge(self::$lang, $lang);
  }

  # private function __clone() {}

  /**
   * Gibt die Instanz der Klasse zurück.
   *
   * @return Localization  Die aktuelle Instanz.
   */
  public static function getInstance ()
  {
    # static $instance = null;
    # if(isset($new_instance) && is_object($new_instance))
    # {
    # self::$instance = $new_instance;
    # }
    return self::$_instance;
  }

  # public static function getInstance($language_file=false)
  # {
  # if(self::$instance === NULL)
  # {
  # self::$instance = new self($language_file);
  # }
  # return self::$instance;
  # }

  /**
   * Weist einen Wert einer bestimmten Sprachvariablen zu.
   *
   * @param string $key  Der Schlüssel der Sprachvariable.
   * @param string $val  Der Wert der Sprachvariable.
   */
  public function assign ($key, $val)
  {
    self::$lang[$key] = $val;
  }

  /**
   * Ersetzt Platzhalter in einem Text mit einem Wert und optionalem Zeitformat.
   *
   * @param string $placeholder  Der Platzhalter, der ersetzt werden soll.
   * @param mixed $replacement  Der Ersatzwert.
   * @param string $index  Der Sprachschlüssel, in dem der Platzhalter ersetzt wird.
   * @param bool $format_time  Ob das Ersetzen Zeitformatierung erfordert.
   */
  public function replacePlaceholder ($placeholder, $replacement, $index, $format_time = false)
  {
    if ($format_time)
    {
      $this->replacement = $replacement;
      # self::$lang[$index] = preg_replace_callback('/\['.$placeholder.'\|(.*?)\]/', array(&$this, '_callbackFormatTimeWrapper'), self::$lang[$index]);
      self::$lang[$index] = preg_replace_callback('/\[' . $placeholder . '\|(.*?)\]/', [
        $this,'_callbackFormatTimeWrapper'
      ], self::$lang[$index]);
    }
    else
    {
      self::$lang[$index] = str_replace('[' . $placeholder . ']', $replacement, self::$lang[$index]);
    }
  }

  /**
   * Ersetzt Platzhalter innerhalb eines bestimmten Elements in einem Array von Sprachvariablen.
   *
   * @param string $placeholder  Der Platzhalter, der ersetzt werden soll.
   * @param mixed $replacement  Der Ersatzwert.
   * @param string $index  Der Sprachschlüssel, in dem der Platzhalter ersetzt wird.
   * @param string $id  Der Index innerhalb des Spracharrays.
   * @param bool $format_time  Ob das Ersetzen Zeitformatierung erfordert.
   */
  public function replacePlaceholderBound ($placeholder, $replacement, $index, $id, $format_time = false)
  {
    if ($format_time)
    {
      $this->replacement = $replacement;
      # self::$lang[$index][$id] = preg_replace_callback('/\['.$placeholder.'\|(.*?)\]/', array(&$this, '_callbackFormatTimeWrapper'), self::$lang[$index][$id]);
      self::$lang[$index][$id] = preg_replace_callback('/\[' . $placeholder . '\|(.*?)\]/', [
        $this,'_callbackFormatTimeWrapper'
      ], self::$lang[$index][$id]);
    }
    else
    {
      self::$lang[$index][$id] = str_replace('[' . $placeholder . ']', $replacement, self::$lang[$index][$id]);
    }
  }

  /**
   * Bindet eine Sprachvariable und überträgt sie in das Hauptarray.
   *
   * @param string $index  Der Index der Sprachvariable.
   * @param string $id  Der Index des Elements, das gebunden werden soll.
   */
  public function bindId ($index, $id)
  {
    if (empty(self::$_lang[$index]))
    {
      self::$_lang[$index] = self::$lang[$index];
      unset(self::$lang[$index]);
    }
    self::$lang[$index][$id] = self::$_lang[$index];
  }

  /**
   * Ersetzt Platzhalter innerhalb eines gebundenen Elements.
   *
   * @param string $id  Der Index des Elements, das ersetzt werden soll.
   * @param string $placeholder  Der Platzhalter, der ersetzt werden soll.
   * @param mixed $replacement  Der Ersatzwert.
   * @param string $index  Der Sprachindex.
   * @param bool $format_time  Ob das Ersetzen Zeitformatierung erfordert.
   */
  public function bindReplacePlaceholder ($id, $placeholder, $replacement, $index, $format_time = false)
  {
    $this->bindId($index, $id);
    $this->replacePlaceholderBound($placeholder, $replacement, $index, $id, $format_time);
  }

  /**
   * Wählt eine Variante der Sprachvariable aus.
   *
   * @param string $index  Der Sprachindex.
   * @param int $i  Der Index der Variante.
   */
  public function selectVariant ($index, $i)
  {
    self::$lang[$index] = self::$lang[$index][$i];
  }

  /**
   * Wählt eine Variante einer gebundenen Sprachvariable aus.
   *
   * @param string $index  Der Sprachindex.
   * @param string $id  Der Index des Elements.
   * @param int $i  Der Index der Variante.
   */
  public function selectBoundVariant ($index, $id, $i)
  {
    self::$lang[$index][$id] = self::$lang[$index][$id][$i];
  }

  /**
   * Ersetzt Links innerhalb eines Textes durch HTML-Anker.
   *
   * @param string $link  Der Link, der eingesetzt werden soll.
   * @param string $index  Der Sprachindex, in dem der Link ersetzt wird.
   */
  public function replaceLink ($link, $index)
  {
    self::$lang[$index] = str_replace('[[', '<a href="' . $link . '">', self::$lang[$index]);
    self::$lang[$index] = str_replace(']]', '</a>', self::$lang[$index]);
  }

  /**
   * Wrapper für die Zeitformatierung innerhalb eines Platzhalters.
   *
   * @param array $matches  Die übereinstimmenden Teile des regulären Ausdrucks.
   * @return string  Das formatierte Datum.
   */
  private function _callbackFormatTimeWrapper ($matches)
  {
    return $this->_callbackFormatTime($matches[1], $this->replacement);
  }

  /**
   * Formatiert das Datum anhand eines gegebenen Formats und Zeitstempels.
   *
   * @param string $format  Das Datumsformat.
   * @param int $timestamp  Der Zeitstempel.
   * @return string  Das formatierte Datum.
   */
  private function _callbackFormatTime ($tformat, $timestamp)
  {
    return date($tformat, $timestamp);
  }

  /**
   * Extrahiert den Sprachcode aus einem Dateinamen.
   *
   * Diese Hilfsfunktion extrahiert den Sprachcode aus einem Dateinamen im Format 'chinese_zh-CN'
   * und gibt den Code 'zh-CN' zurück.
   *
   * @param string $filename Der Dateiname, aus dem der Sprachcode extrahiert werden soll.
   *
   * @return string Der extrahierte Sprachcode.
   */
  public function extract_language_code_from_filename (string $filename): string
  {
    // Den Dateinamen an Unterstrichen trennen und den letzten Teil zurückgeben
    $parts = explode('_', $filename);
    return end($parts); // Gibt den Sprachcode zurück (z.B. 'zh-CN')
  }
}

/**
 * Änderungen:
 * Exception anstelle von die: Es wird eine Ausnahme geworfen, wenn kein Sprachfile angegeben ist.
 * require_once statt require: Verhindert mehrfaches Einbinden der gleichen Datei.
 * Jede Methode wurde kommentiert, um die Funktionalität zu erklären.
 * Verwendung von [$this, '_callbackFormatTimeWrapper']: Um sicherzustellen, dass der Funktionsaufruf korrekt funktioniert.
 * empty($etwas) statt isset($etwas) verwendet.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-28 $Date$
 * @date $LastChangedDate: 2025-01-28 16:51:27 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$  - Description
 * 2025-01-28 : 4.5.0.2025.01.28    : ztatement        - added: Erweitert um get_languages, _name, translate
 * 2025-01-08 : 4.5.0.2025.01.08    : ztatement        - update: PHP 8.x/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
