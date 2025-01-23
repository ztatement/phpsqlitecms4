<?php
/**
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * @version 4.5.0.2025.01.23
  * @file $Id: cms/includes/classes/Helpers.php 1 2025-01-20 08:10:07Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  *
  * --------
  * Helferklasse für allgemeine Funktionen
  * Diese Datei enthält Funktionen zur Arbeit mit verschiedenen Operationen:
  * - Datenbankoperationen
  * - Stringmanipulationen
  * - Sicherheitshandhabung
  */

class Helpers
{
  #private int $timestamp;
  #private string $format;
  #private array $settings;
/**
  * Konstruktor der Klasse
  */
  #public function __construct () {}

/**
  * Hilfsfunktion, um einen Zeitstempel mit der gegebenen Zeitzone zu formatieren.
  *
  * @param int $timestamp  Der Unix-Zeitstempel
  * @param string $format  Das gewünschte Format (z.B. 'RFC 2822', 'ISO 8601')
  * @param array $settings  Die Einstellungen, welche auch die Zeitzone beinhalten sollten
  * @return string|false  Formatierter Zeitstempel oder false bei einem ungültigen Format
  * @throws Exception  Falls ein Fehler bei der Zeitzonenverarbeitung auftritt
  */
  #public static function formatTimestamp($timestamp, $format = 'RFC 2822', array $settings): string
  public static function formatTimestamp(int $timestamp, array $settings, string $format = 'RFC 2822') : string|false
  {
    // Zeitzonen-Einstellung aus den Benutzereinstellungen laden
    #$timezone = $settings['timezone'];
    if (!isset($settings['timezone']))
    {
      throw new Exception('Zeitzone ist nicht gesetzt in den Einstellungen.');
    }

    // Hier wird der Wert aus den globalen Einstellungen (z. B. 'UTC', 'UTC+1', etc.) geladen
    $timezone = $settings['timezone'];
    // Erzeuge ein DateTime-Objekt aus dem Unix-Timestamp
    $datetime = new DateTime('@' . $timestamp);

    // Setze die Zeitzone anhand der übergebenen Einstellungen
    // Dies ermöglicht es, den Zeitstempel in der entsprechenden Zeitzone anzuzeigen
    $datetime->setTimezone(new DateTimeZone($timezone)); 

    // Rückgabe des formatierten Zeitstempels, basierend auf dem gewünschten Format
    switch ($format) {
        case 'RFC 2822':
            // Gibt das Datum im RFC 2822 Format zurück
            return $datetime->format(DateTime::RFC2822);

        case 'ISO 8601':
            // Gibt das Datum im ISO 8601 (ATOM) Format zurück
            return $datetime->format(DateTime::ATOM);

        case 'Klartext':
            // Gibt das Datum im Klartext-Format zurück (z. B. '01. Januar 2025, 15:30:00')
            return $datetime->format('d. F Y, H:i:s');

        default:
            // Wenn ein ungültiges Format übergeben wird, gibt es false zurück
            return false;
    }
  }

/**
  * Hilfsfunktion: Seitendaten bereinigen und vorbereiten
  *
  * @param array $data
  * @param array $users
  * @return array
  */
  public static function prepare_page_data(array $data, array $users): array
  {
    $cleaned_data = [];
    $cleaned_data['id'] = (int) $data['id'];
    $cleaned_data['page'] = htmlspecialchars($data['page'] ?? '');
    $cleaned_data['author'] = intval($data['author']);
    $cleaned_data['type'] = htmlspecialchars($data['type'] ?? '');
    $cleaned_data['type_addition'] = htmlspecialchars($data['type_addition'] ?? '');
    $cleaned_data['time'] = date("Y-m-d H:i:s", $data['time']);
    $cleaned_data['last_modified'] = date("Y-m-d H:i:s");
    $cleaned_data['display_time'] = intval($data['display_time']);
    $cleaned_data['title'] = htmlspecialchars($data['title'] ?? '');
    $cleaned_data['page_title'] = htmlspecialchars($data['page_title'] ?? '');
    $cleaned_data['description'] = htmlspecialchars($data['description'] ?? '');
    $cleaned_data['keywords'] = htmlspecialchars($data['keywords'] ?? '');
    $cleaned_data['category'] = htmlspecialchars($data['category'] ?? '');
    $cleaned_data['page_info'] = htmlspecialchars($data['page_info'] ?? '');
    $cleaned_data['breadcrumbs'] = explode(',', htmlspecialchars($data['breadcrumbs'] ?? ''));
    $cleaned_data['sections'] = str_replace(',', ', ', htmlspecialchars($data['sections'] ?? ''));
    $cleaned_data['include_page'] = intval($data['include_page']);
    $cleaned_data['include_order'] = intval($data['include_order']);
    $cleaned_data['include_rss'] = intval($data['include_rss']);
    $cleaned_data['include_sitemap'] = intval($data['include_sitemap']);
    $cleaned_data['include_news'] = intval($data['include_news']);
    $cleaned_data['link_name'] = htmlspecialchars($data['link_name'] ?? '');
    $cleaned_data['menu_1'] = htmlspecialchars($data['menu_1'] ?? '');
    $cleaned_data['menu_2'] = htmlspecialchars($data['menu_2'] ?? '');
    $cleaned_data['menu_3'] = htmlspecialchars($data['menu_3'] ?? '');
    $cleaned_data['gcb_1'] = htmlspecialchars($data['gcb_1'] ?? '');
    $cleaned_data['gcb_2'] = htmlspecialchars($data['gcb_2'] ?? '');
    $cleaned_data['gcb_3'] = htmlspecialchars($data['gcb_3'] ?? '');
    $cleaned_data['template'] = htmlspecialchars($data['template'] ?? '');
    $cleaned_data['language'] = htmlspecialchars($data['language'] ?? '');
    $cleaned_data['content_type'] = htmlspecialchars($data['content_type'] ?? '');
    $cleaned_data['charset'] = htmlspecialchars($data['charset'] ?? '');
    $cleaned_data['teaser_headline'] = htmlspecialchars($data['teaser_headline'] ?? '');
    $cleaned_data['teaser'] = htmlspecialchars($data['teaser'] ?? '');
    $cleaned_data['teaser_img'] = htmlspecialchars($data['teaser_img'] ?? '');
    $cleaned_data['sidebar_1'] = htmlspecialchars($data['sidebar_1'] ?? '');
    $cleaned_data['sidebar_2'] = htmlspecialchars($data['sidebar_2'] ?? '');
    $cleaned_data['sidebar_3'] = htmlspecialchars($data['sidebar_3'] ?? '');
    $cleaned_data['page_notes'] = htmlspecialchars($data['page_notes'] ?? '');
    $cleaned_data['edit_permission_general'] = intval($data['edit_permission_general']);
    $cleaned_data['tv'] = str_replace(',', ', ', htmlspecialchars($data['tv'] ?? ''));
    $cleaned_data['status'] = intval($data['status']);
    $cleaned_data['content'] = htmlspecialchars($data['content'] ?? '');
    // ... weitere Bereinigung analog ...
    return $cleaned_data;
  }

/**
  * Hilfsfunktion: Standardwerte für neue Seite erstellen
  *
  * @param array $settings  Einstellungen, z.B. Standard-Template
  * @return array  Standardwert-Array für die Seite
  */
  public static function create_default_page_data(array $settings): array
  {
    // Standardwerte für eine neue Seite setzen
    return [
      'time' => date("Y-m-d H:i:s"),
      'last_modified' => date("Y-m-d H:i:s"),
      'display_time' => 0,
      'include_page' => 0,
      'include_order' => 0,
      'include_rss' => 0,
      'include_sitemap' => 0,
      'include_news' => 0,
      'link_name' => Localization::$lang['teaser_default_linkname'] ?? 'teaser_default_linkname',
      'template' => $settings['default_template'] ?? 'default_template',
      'menu_1' => $settings['default_menu'] ?? 'default_menu',
      'edit_permission_general' => 0,
      'status' => 2
      // ... weitere Standardwerte analog ...
    ];
  }

/**
  * Hilfsfunktion: zur Ausgabe von sauberem HTML
  * 
  * @param string $string
  * @return string
  */
  public static function escapeHtml(string $string): string
  {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }

/**
  * Hilfsfunktion: HTML-entkodieren
  * 
  * @param string $string
  * @return string
  */
  public static function decodeHtml(string $string): string
  {
    return html_entity_decode($string, ENT_QUOTES, 'UTF-8');
  }

/**
  * Hilfsfunktion: HTML-escapen und entkodieren
  * 
  * @param string $string
  * @return string
  */
  public static function escapeAndDecodeHtml(string $string): string
  {
    return htmlspecialchars(html_entity_decode($string, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
  }

/**
  * Hilfsfunktion: Bereinigt und formatiert einen Text, indem er HTML-Elemente entfernt und Zeilenumbrüche ersetzt.
  * 
  * @param string $text Der zu formatierende Text
  * @return string Der bereinigte Text
  */
  public static function cleanText($text)
  {
    // Entfernt alle HTML-Tags aus dem Text
    $cleanedText = strip_tags($text);

    // Ersetzt alle vorkommenden Zeilenumbrüche mit einem Leerzeichen
    $cleanedText = preg_replace('/\r\n|\r|\n/', ' ', $cleanedText);

    return trim($cleanedText);  // Entfernt führende und nachfolgende Leerzeichen
  }

/**
  * Hilfsfunktion: Bereinigt eine URL und stellt sicher, dass sie sicher verwendet werden kann.
  * 
  * @param string $url Die zu bereinigende URL
  * @return string Die bereinigte URL
  */
  public static function cleanUrl($url)
  {
    return filter_var($url, FILTER_SANITIZE_URL);
  }

  // ... weitere Funktionen ...
}

/**
  * Änderung:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-01-20 $Date$ $LastChangedDate: 2025-01-20 08:10:07 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$ : $Revision$ - Description
  * 2025-01-23 : 4.5.0.2025.01.23 - added: formatTimestamp kleine korrekturen
  * 2025-01-20 : 4.5.0.2025.01.20 - added: decodeHtml und escapeAndDecodeHtml
  *                                 cleanText und cleanUrl
  * 2025-01-20 : 4.5.0.2025.01.20 - added: Neue Helpers Klasse
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
