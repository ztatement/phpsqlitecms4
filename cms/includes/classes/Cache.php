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
  * @modified
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * 
  * @version 4.5.0.2025.01.26 
  * @file $Id: cms/includes/classes/Cache.php 1 2025-01-26 15:57:10Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  */

/**
  * Class Cache
  *
  * Diese Klasse implementiert die Caching-Funktionalität für die Anwendung.
  */
class Cache {

/**
  * Erlaubt das Caching
  * @var bool
  */
  public $doCaching = true;

/**
  * Erlaubt das automatische Löschen des Caches bei Änderungen über das Admin-Panel
  * @var bool
  */
  public $autoClear = true;

/**
  * Cache-ID
  * @var string|bool
  */
  public $cacheId = false;

/**
  * Speicherort der Cache-Dateien
  * @var string
  */
  private $_cacheDir;

/**
  * Caching-Einstellungen
  * @var array
  */
  private $_settings;

/**
  * Initialisierung des Cache-Objekts
  *
  * @param string $cacheDir  Speicherort der Cache-Dateien
  * @param array $settings  Caching-Einstellungen
  */
  public function __construct ($cacheDir, $settings)
  {
    $this->_cacheDir = $cacheDir;
    $this->_settings = $settings;
  }

/**
  * Erstellt den Cache-Inhalt mit den gegebenen Parametern
  *
  * @param string $content  Der zu cache-nde Inhalt
  * @param string $content_type  Der Inhaltstyp (z.B. text/html)
  * @param string $charset  Die Zeichencodierung des Inhalts
  * @param array $settings  Die Einstellungen, welche auch die Zeitzone beinhalten sollten
  * @return string  Der erstellte Cache-Inhalt
  */
  public function createCacheContent ($content, $content_type, $charset, $settings)
  {
    // Aktuellen Unix-Timestamp abrufen
    $timestamp = time();

    // Datum und Zeit mit der formatTimestamp-Funktion formatieren
    $lastModified = Helpers::formatTimestamp($timestamp, $settings, 'RFC 2822');

    $cacheContent = '
    <?php
      // Header mit dem formatierten Datum und der formatierten Zeit setzen
      header(\'Last-Modified: ' . $lastModified . '\');
      header(\'Cache-Control: public\');
      if (isset($_SERVER[\'HTTP_IF_MODIFIED_SINCE\']) && ' . $timestamp . ' <= strtotime($_SERVER[\'HTTP_IF_MODIFIED_SINCE\']))
      {
        header(\'HTTP/1.1 304 Not Modified\');
        exit;
      } else {
    ';
    if (function_exists('gzencode'))
    {
      $cacheContent .= '
      if (isset($_SERVER[\'HTTP_ACCEPT_ENCODING\']) && strpos($_SERVER[\'HTTP_ACCEPT_ENCODING\'], \'gzip\') !== false)
      {
        header(\'Content-Encoding: gzip\');
        header(\'Content-Type: ' . $content_type . '; charset=' . $charset . '\'); ?' . '>' . gzencode($content, 9) . '<?php
      } else {
        header(\'Content-Type: ' . $content_type . '; charset=' . $charset . '\'); ?' . '>' . $content . '<?php
      }';
    }
    else
    {
      $cacheContent .= '
      header(\'Content-Type: ' . $content_type . '; charset=' . $charset . '\'); ?' . '>' . $content . '<?php
    ';
    }
    $cacheContent .= '
    }
    ?>
    ';

    return $cacheContent;
  }

/**
  * Speichert die Cache-Datei
  *
  * @param string $content  Der Inhalt der Cache-Datei
  */
  public function createCacheFile ($content)
  {
    if ($this->cacheId && $this->doCaching)
    {
      $cacheFile = $this->_cacheDir . rawurlencode(strtolower($this->cacheId)) . '.cache';
      if (! file_exists($cacheFile))
      {
        $content = str_replace('<?xml', '<?php echo \'<?xml\'; ?>', $content);
        $fp = @fopen($cacheFile, 'w');
        @flock($fp, LOCK_EX);
        @fwrite($fp, $content);
        @flock($fp, LOCK_UN);
        @fclose($fp);
      }
    }

    if (! file_exists($this->_cacheDir . 'settings.php'))
    {
      $this->_createCacheSettingsFile();
    }
  }

 /**
  * Speichert die Cache-Einstellungsdatei
  */
  private function _createCacheSettingsFile ()
  {
    $content = "<?php\n";
    $content .= '$settings[\'session_prefix\'] = \'' . $this->_settings['session_prefix'] . '\';' . "\n";
    $content .= '$settings[\'index_page\'] = \'' . $this->_settings['index_page'] . '\';' . "\n";
    $content .= '?' . '>';

    $fp = @fopen($this->_cacheDir . 'settings.php', 'w');
    @flock($fp, LOCK_EX);
    @fwrite($fp, $content);
    @flock($fp, LOCK_UN);
    @fclose($fp);
  }

/**
  * Löscht alle Cache-Dateien und die Einstellungsdatei. Wenn $page gesetzt ist, wird nur die Cache-Datei dieser Seite gelöscht.
  *
  * @param string|bool $page  Der Name der Seite, deren Cache gelöscht werden soll. Wenn nicht gesetzt, werden alle Cache-Dateien gelöscht.
  */
  public function clear ($page = false)
  {
    if (! $page)
    {
      // Lösche alle Cache-Dateien (settings.php und *.cache)
      $cacheFiles = glob($this->_cacheDir . '{settings.php,*.cache}', GLOB_BRACE);
      if ($cacheFiles)
      {
        foreach ($cacheFiles as $cacheFile)
        {
          @unlink($cacheFile);
        }
      }
    }
    else
    {
      // Lösche Cache-Dateien einer spezifischen Seite
      $page = rawurlencode(strtolower($page));
      $cacheFiles = glob($this->_cacheDir . '{' . $page . '.cache,' . $page . '%2C*.cache}', GLOB_BRACE);
      if ($cacheFiles)
      {
        foreach ($cacheFiles as $cacheFile)
        {
          @unlink($cacheFile);
        }
      }
    }
  }

/**
  * Löscht die Foto-Cache-Datei
  *
  * @param int $id Die ID des Fotos, dessen Cache gelöscht werden soll
  */
  public function clearPhoto ($id)
  {
    $cacheFiles = glob($this->_cacheDir . '{*%2C' . IMAGE_IDENTIFIER . '%2C' . $id . '.cache,*%2C' . IMAGE_IDENTIFIER . '%2C' . $id . '%2C*.cache}', GLOB_BRACE);
    if ($cacheFiles)
    {
      foreach ($cacheFiles as $cacheFile)
      {
        @unlink($cacheFile);
      }
    }
  }

/**
  * Löscht den Cache von Übersichtsseiten
  *
  * @param string $page Der Name der Seite, deren verwandter Cache gelöscht werden soll
  */
  public function clearRelated ($page)
  {
    $stmt = Database::$content->prepare("SELECT include_page FROM " . Database::$db_settings['pages_table'] . " WHERE lower(page)=lower(:page) LIMIT 1");
    $stmt->bindParam(':page', $page, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($data['include_page']))
    {
      $stmt2 = Database::$content->prepare("SELECT page, type FROM " . Database::$db_settings['pages_table'] . " WHERE id=:id LIMIT 1");
      $stmt2->bindParam(':id', $data['include_page'], PDO::PARAM_INT);
      $stmt2->execute();
      $data2 = $stmt2->fetch(PDO::FETCH_ASSOC);
      if (isset($data2['page']))
      {
        $this->clear($data2['page']);
      }
    }
  }
}

/**
  * Änderung:
  * Verwendung von PDO::FETCH_ASSOC, so wie von LOCK_EX und LOCK_UN anstelle von numerischen Werten.
  * Datenbankabfragen verwenden nun prepare, bindParam und execute.
  * Generelle Verbesserungen und Konsistenz in der Fehlerbehandlung.
  * Neu: formatTimestamp: Diese Funktion wird aufgerufen, um das Datum und die Zeit 
  * im gewünschten Format zu generieren. Das Ergebnis wird im Header eingefügt.
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-01-26 $Date$ 
  * @date $LastChangedDate: 2025-01-26 15:57:10 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$ : $Revision$ - Description
  * 2025-01-26 : 4.5.0.2025.01.26 - update: PHP 8.x/9 Kompatibilität
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
