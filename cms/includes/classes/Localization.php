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
 * @version 4.5.0.2025.01.08 
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
}

/**
 * Änderungen:
 * Exception anstelle von die: Es wird eine Ausnahme geworfen, wenn kein Sprachfile angegeben ist.
 * require_once statt require: Verhindert mehrfaches Einbinden der gleichen Datei.
 * Jede Methode wurde kommentiert, um die Funktionalität zu erklären.
 * Verwendung von [$this, '_callbackFormatTimeWrapper']: Um sicherzustellen, dass der Funktionsaufruf korrekt funktioniert.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-08 $Date$
 * @date $LastChangedDate: 2025-01-28 14:14:01 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$  - Description
 * 2025-01-28 : 4.5.0.2025.01.28    : ztatement        - 
 * 2025-01-08 : 4.5.0.2025.01.08    : ztatement        - update: PHP 8.x/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
