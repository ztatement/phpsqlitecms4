<?php
/**
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * @version 4.5.0.2025.01.29
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

#namespace cms\includes\classes;

#use DateTime;
#use DateTimeZone;
#use Exception;
#use Localization;

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
  * Initialisiert die Einstellungen mit Standardwerten und kombiniert diese mit den tatsächlichen, 
  * aus der Datenbank abgerufenen Einstellungen.
  * 
  * Diese Methode stellt sicher, dass alle erforderlichen Einstellungen vorhanden sind, indem sie
  * die abgerufenen Daten mit vordefinierten Standardwerten zusammenführt. Sollte ein bestimmter 
  * Einstellungseintrag in der Datenbank fehlen, wird der Standardwert verwendet. 
  * 
  * Die Funktion nutzt die `array_merge`-Methode, um die Standardwerte und die tatsächlichen 
  * Einstellungen zu kombinieren. Dabei werden die abgerufenen Werte aus der Datenbank die 
  * Standardwerte überschreiben, falls diese vorhanden sind.
  * 
  * @param array $settings  Die aus der Datenbank abgerufenen Einstellungen, die mit den Standardwerten kombiniert werden sollen.
  * 
  * @return array  Ein Array, das alle kombinierten Einstellungen enthält. 
  *                Dies umfasst sowohl die abgerufenen Einstellungen als auch die Standardwerte 
  *                für nicht vorhandene oder leere Einträge.
  * 
  * Beispiel:
  * ```php
  * $settings = Helpers::initializeSettings($databaseSettings);
  * // Gibt z.B. ['count_views' => 10, 'error_page' => '/error.html', 'content_auto_link' => false] zurück,
  * // falls $databaseSettings nur für 'count_views' und 'error_page' Werte enthält.
  * ```
  */
  public static function initializeSettings($settings)
  {
    // Standardwerte für die Einstellungen
    $definedSettings = [
      'index_page'                      => 'home',
      'error_page'                      => '404',
      'comment_order'                   => '0',
      'comments_per_page'               => '10',
      'default_page_language'           => 'english_en-US',
      'website_title'                   => 'phpSQLiteCMS',
      'website_footnote_1'              => '',
      'website_footnote_2'              => '',
      'news_per_page'                   => '10',
      'email'                           => '',
      'comment_notification'            => '0',
      'comment_maxlength'               => '1000',
      'word_maxlength'                  => '30',
      'name_maxlength'                  => '50',
      'email_hp_maxlength'              => '100',
      'default_image_class'             => 'img-fluid',
      'default_thumbnail_class'         => 'img-thumbnail',
      'default_gallery_image_class'     => 'thumbnail',
      'image_classes'                   => 'float-start, float-end, thumbnail, rounded, img-fluid, img-thumbnail',
      'photos_commentable'              => '0',
      'count_views'                     => '0',
      'default_template'                => 'default.template.php',
      'default_photo_template'          => 'photo.template.php',
      'default_slideshow_template'      => 'slideshow.template.php',
      'location_maxlength'              => '50',
      'admin_entries_per_page'          => '20',
      'resize'                          => '1024',
      'resize_xy'                       => 'x',
      'compression'                     => '80',
      'session_prefix'                  => 'zcms_',
      'default_menu'                    => 'main_menu',
      'smiley_directory'                => 'assets/media/smilies',
      'comment_smilies'                 => '1',
      'comment_auto_link'               => '1',
      'content_smilies'                 => '0',
      'content_auto_link'               => '0',
      'default_description'             => '',
      'default_keywords'                => '',
      'akismet_key'                     => '',
      'rss_maximum_items'               => '20',
      'search_results_per_page'         => '20',
      'counter_last_resetted'           => '1738548145', // sollte unbedingt vor Start der Webseite angepasst werden. Mon Feb 03 2025 03:02:25 GMT+0100
      'notes_per_page'                  => '10',
      'entries_show_email'              => '0',
      'mail_parameter'                  => '',
      'base_url'                        => '',
      'default_photos_per_row'          => '4',
      'version'                         => '4.5.0', // sollte unbedingt vor Start der Webseite angepasst werden.
      'caching'                         => '0',
      'breadcrumbs'                     => '5',
      'slideshow'                       => '0',
      'content_function'                => '0',
      'base_path'                       => '',
      'admin_language'                  => 'english_en-US',
      'time_zone'                       => 'UTC',
      'timezone'                        => 'UTC',
      'default_formatting'              => '',
      'akismet_entry_check'             => '0',
      'akismet_mail_check'              => '0',
      'prevent_repeated_posts_minutes'  => '2',
      'comment_remove_blank_lines'      => '1',
      'admin_auto_clear_cache'          => '1',
      'check_access_permission'         => '0',
      'global_content_blocks'           => '1',
      'readonly'                        => '9',
      'lightbox_enabled'                => '1',
      'pingbacks_enabled'               => '0',
      'wysiwyg_editor'                  => '0',
      'simple_news_per_page'            => '10',
      'email_text_maxlength'            => '10000',
      'email_subject_maxlength'         => '100',
      'author'                          => '',
      'thumbnail_resize_xy'             => 'x',
      'thumbnail_resize'                => '180',
      'thumbnail_compression'           => '65',
      'thumbnail_prefix'                => '',
      'thumbnail_postfix'               => '_thumbnail',
      'theme'                           => 'default',
      'font'                            => 'z-IconPro-Light', // default Schriftart im phpSQLiteCMS
      'log_file'                        => './.log/_errors.log',
      'log_level'                       => 'DEBUG',
      'debug_on_off'                    => '0',

      // Weitere Standardwerte können hier hinzugefügt werden
    ];

    // Überprüfen, ob ein Datenbankwert keinen Standardwert hat
  #  foreach ($settings as $key => $value)
  #  {
  #    if (!array_key_exists($key, $definedSettings))
  #    {
  #      Logger::log(LOG_SETTINGS, "Kein Standardwert für den Eintrag '{$key}' vorhanden.");
  #    }
  #  }

    // Merging der abgerufenen Einstellungen mit den Standardwerten
    // Die abgerufenen Werte überschreiben die Standardwerte, wenn sie vorhanden sind
    return array_merge($definedSettings, $settings);
  }


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
  public static function formatTimestamp(int $timestamp, array $settings, string $tz_format = 'RFC 2822') : string|false
  {
    // Zeitzonen-Einstellung aus den Benutzereinstellungen laden
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
    switch ($tz_format) {
        case 'RFC 2822':
            // Gibt das Datum im RFC 2822 Format zurück
            return $datetime->tz_format(DateTime::RFC2822);

        case 'ISO 8601':
            // Gibt das Datum im ISO 8601 (ATOM) Format zurück
            return $datetime->tz_format(DateTime::ATOM);

        case 'Klartext':
            // Gibt das Datum im Klartext-Format zurück (z. B. '01. Januar 2025, 15:30:00')
            return $datetime->tz_format('d. F Y, H:i:s');

        default:
            // Wenn ein ungültiges Format übergeben wird, gibt es false zurück
            return false;
    }
  }


/**
  * Hilfsfunktion:  Gibt das Zeitformat für eine angegebene Sprache zurück,
  * basierend auf einer vordefinierten Liste. Falls kein Format für die angegebene
  * Sprache gefunden wird, wird ein Fallback-Wert aus den globalen Sprach-Settings
  * ($lang['time_format']) verwendet. Falls auch dieser nicht gesetzt ist,
  * wird ein Standardzeitformat verwendet.
  * 
  * Die Funktion berücksichtigt eine Reihe von bekannten Sprachformaten für die Anzeige von Datums- und
  * Uhrzeitangaben, wie sie für Länder spezifisch üblich sind. Falls der Sprachcode nicht vorhanden ist,
  * wird eine Ausnahme ausgelöst und das Standardformat 'Y-m-d H:i' zurückgegeben.
  * 
  * Beispiel:
  *   get_time_format('de_DE'); // Gibt 'd.m.Y H:i' für Deutschland zurück
  *   get_time_format('en_US'); // Gibt 'm/d/Y h:i A' für die USA zurück
  *   get_time_format('zh_CN'); // Gibt 'Y-m-d H:i' für China zurück
  * 
  * @param string $lang  Der Sprachcode, für den das Zeitformat ermittelt werden soll (z.B. 'de_DE', 'en_US').
  *                      Falls kein Format für die angegebene Sprache vorhanden ist, wird ein Fallback
  *                      verwendet. Falls $lang nicht gesetzt ist, wird 'en_US' als Standard verwendet.
  * 
  * @return string  Das Zeitformat für die angegebene Sprache im kurzen Format (z.B. 'd.m.Y H:i').
  *                 Wenn kein Format gefunden wird, wird der Fallback aus $lang['time_format'] verwendet.
  *                 Falls dieser ebenfalls nicht gesetzt ist, wird 'Y-m-d H:i' als Standardformat zurückgegeben.
  * 
  * @throws Exception  Wenn für die angegebene Sprache weder ein bekanntes Format noch ein Fallback vorhanden ist.
  */
  public static function get_time_format($t_lang = 'en_US')
  {
    // Holen der Einstellungen für die Standardsprache der Seite
    $t_lang = Functions::get_settings('default_page_language');

    // Kurz-Format-Zeitangaben für verschiedene Sprachen
    $short_time_format = [
      'de_DE' => 'd.m.Y H:i',       // Deutschland
      'en_US' => 'm/d/Y h:i A',     // USA
      'fr_FR' => 'd/m/Y H:i',       // Frankreich
      'zh_CN' => 'Y-m-d H:i',       // China
      'ja_JP' => 'Y/m/d H:i',       // Japan
      'ru_RU' => 'd.m.Y H:i',       // Russland
      'es_ES' => 'd/m/Y H:i',       // Spanien
      'it_IT' => 'd/m/Y H:i',       // Italien
      'hi_IN' => 'd/m/Y H:i',       // Indien
      'pt_BR' => 'd/m/Y H:i',       // Brasilien
      'bg_BG' => 'd.m.Y H:i',       // Bulgarien
      'ko_KR' => 'Y.m.d H:i',       // Südkorea
      'th_TH' => 'd/m/Y H:i',       // Thailand
    ];

    // Langes-Format-Zeitangaben für verschiedene Sprachen
    $long_time_format = [
      'de_DE' => 'l, j. F Y H:i:s',    // Deutschland
      'en_US' => 'l, F j, Y h:i A',    // USA
      'fr_FR' => 'l d F Y H:i',        // Frankreich
      'zh_CN' => 'l, F j, Y, H:i',     // China
      'ja_JP' => 'Y年m月d日 H:i',       // Japan
      'ru_RU' => 'l, j F Y H:i',       // Russland
      'es_ES' => 'l, d F Y H:i',       // Spanien
      'it_IT' => 'l, d F Y H:i',       // Italien
      'hi_IN' => 'l, j F Y H:i',       // Indien
      'pt_BR' => 'l, j F Y H:i',       // Brasilien
      'bg_BG' => 'l, j F Y H:i',       // Bulgarien
      'ko_KR' => 'l, Y년 m월 d일 H:i',  // Südkorea
      'th_TH' => 'l, j F Y H:i',       // Thailand
    ];

    try
    {
      // Falls kein Format für die angegebene Sprache existiert, verwenden wir den Fallback
      if (!isset($short_time_format[$t_lang]) && !isset($long_time_format[$t_lang]))
      {
        // Falls $settings['default_page_language'] nicht gesetzt ist, werfen wir eine Ausnahme
        if (!isset($settings['default_page_language']))
        {
          throw new Exception("Kein Sprachformat für die angegebene Sprache oder \$settings['default_page_language'] gesetzt.");
        }
        return $lang['time_format'] . ' ' . $lang['time_format_full']; // Fallback aus Sprachdatei
      }

      // Rückgabe des Formats für die angegebene Sprache
      return $short_time_format[$t_lang] . ' ' . $long_time_format[$t_lang];
    }
    catch (Exception $e)
    {
      // Fehlerbehandlung: Ausgabe der Fehlermeldung (optional)
      error_log("Fehler in get_time_format: " . $e->getMessage());

      // Rückgabe eines Standardformats, falls ein Fehler auftritt
      return 'Y-m-d H:i'; // Standardwert
    }
  }


/**
  * Diese Methode gibt die entsprechende Zeitzone basierend auf der übergebenen Sprachkodierung zurück.
  *
  * Es wird zunächst die Standardzeitzone aus den Einstellungen abgerufen.
  * Anschließend wird überprüft, ob die übergebene Sprachkodierung (tz_lang)
  * in der Liste der vordefinierten Zeitzonen vorhanden ist.
  * Wenn die Sprachkodierung in der Liste gefunden wird, wird die entsprechende Zeitzone zurückgegeben.
  * Andernfalls wird die Standardzeitzone zurückgegeben.
  *
  * @param string $tz_lang  Die Sprachkodierung, für die die Zeitzone ermittelt 
                            werden soll (z.B. 'de_DE' für Deutschland).
  * @return string $timezone  Die entsprechende Zeitzone oder die Standardzeitzone,
                              wenn die Sprachkodierung nicht gefunden wird.
  */
  public static function get_time_zone($tz_lang)
  {
    // Holen der Einstellungen für die Standardzeitzone
    $settings = self::initializeSettings([]);
    $default_timezone = $settings['time_zone'];

    // Vordefinierte Zeitzonen für verschiedene Sprachkodierungen
    $selection_time_zone = [
      'de_DE' => 'Europe/Berlin',     // Deutschland
      'en_US' => 'America/New_York',  // USA
      'fr_FR' => 'Europe/Paris',      // Frankreich
      'zh_CN' => 'Asia/Shanghai',     // China
      'ja_JP' => 'Asia/Tokyo',        // Japan
      'ru_RU' => 'Europe/Moscow',     // Russland
      'es_ES' => 'Europe/Madrid',     // Spanien
      'it_IT' => 'Europe/Rome',       // Italien
      'hi_IN' => 'Asia/Kolkata',      // Indien
      'pt_BR' => 'America/Sao_Paulo', // Brasilien
      'bg_BG' => 'Europe/Sofia',      // Bulgarien
      'ko_KR' => 'Asia/Seoul',        // Südkorea
      'th_TH' => 'Asia/Bangkok'       // Thailand
    ];

    // Zeitzone setzen oder Standardzeitzone verwenden
    if (array_key_exists($tz_lang, $selection_time_zone))
    {
        $timezone = $selection_time_zone[$tz_lang];
    }
    else
    {
        $timezone = $default_timezone;
    }

    return $timezone;
  }


/**
  * Hilfsfunktion: Seitendaten bereinigen und vorbereiten
  *
  * Diese Funktion nimmt Rohdaten einer Seite sowie Benutzerdaten entgegen und bereinigt bzw. 
  * vorbereitet sie für die weitere Verarbeitung. Dabei werden verschiedene Datentypen konvertiert 
  * und HTML-Entities sicher kodiert.
  *
  * @param array $data Array mit den Rohdaten der Seite.
  * @param array $users Array mit den Benutzerdaten.
  * @return array Bereinigtes und vorbereitetes Array mit Seitendaten.
  */
  public static function prepare_page_data(array $data, array $users): array
  {
    // Bereinigtes Datenarray
    $cleaned_data = [];
    $cleaned_data['id'] = (int) $data['id'];
    $cleaned_data['page'] = self::escapeHtml($data['page'] ?? '');
    $cleaned_data['author'] = intval($data['author']);
    $cleaned_data['type'] = self::escapeHtml($data['type'] ?? '');
    $cleaned_data['type_addition'] = self::escapeHtml($data['type_addition'] ?? '');
    $cleaned_data['time'] = date("Y-m-d H:i:s", $data['time']);
    $cleaned_data['last_modified'] = date("Y-m-d H:i:s");
    $cleaned_data['display_time'] = intval($data['display_time']);
    $cleaned_data['title'] = self::escapeHtml($data['title'] ?? '');
    $cleaned_data['page_title'] = self::escapeHtml($data['page_title'] ?? '');
    $cleaned_data['description'] = self::escapeHtml($data['description'] ?? '');
    $cleaned_data['keywords'] = self::escapeHtml($data['keywords'] ?? '');
    $cleaned_data['category'] = self::escapeHtml($data['category'] ?? '');
    $cleaned_data['page_info'] = self::escapeHtml($data['page_info'] ?? '');
    $cleaned_data['breadcrumbs'] = explode(',', self::escapeHtml($data['breadcrumbs'] ?? ''));
    $cleaned_data['sections'] = str_replace(',', ', ', self::escapeHtml($data['sections'] ?? ''));
    $cleaned_data['include_page'] = intval($data['include_page']);
    $cleaned_data['include_order'] = intval($data['include_order']);
    $cleaned_data['include_rss'] = intval($data['include_rss']);
    $cleaned_data['include_sitemap'] = intval($data['include_sitemap']);
    $cleaned_data['include_news'] = intval($data['include_news']);
    $cleaned_data['link_name'] = self::escapeHtml($data['link_name'] ?? '');
    $cleaned_data['menu_1'] = self::escapeHtml($data['menu_1'] ?? '');
    $cleaned_data['menu_2'] = self::escapeHtml($data['menu_2'] ?? '');
    $cleaned_data['menu_3'] = self::escapeHtml($data['menu_3'] ?? '');
    $cleaned_data['gcb_1'] = self::escapeHtml($data['gcb_1'] ?? '');
    $cleaned_data['gcb_2'] = self::escapeHtml($data['gcb_2'] ?? '');
    $cleaned_data['gcb_3'] = self::escapeHtml($data['gcb_3'] ?? '');
    $cleaned_data['template'] = self::escapeHtml($data['template'] ?? '');
    $cleaned_data['language'] = self::escapeHtml($data['language'] ?? '');
    $cleaned_data['content_type'] = self::escapeHtml($data['content_type'] ?? '');
    $cleaned_data['charset'] = self::escapeHtml($data['charset'] ?? '');
    $cleaned_data['teaser_headline'] = self::escapeHtml($data['teaser_headline'] ?? '');
    $cleaned_data['teaser'] = self::escapeHtml($data['teaser'] ?? '');
    $cleaned_data['teaser_img'] = self::escapeHtml($data['teaser_img'] ?? '');
    $cleaned_data['sidebar_1'] = self::escapeHtml($data['sidebar_1'] ?? '');
    $cleaned_data['sidebar_2'] = self::escapeHtml($data['sidebar_2'] ?? '');
    $cleaned_data['sidebar_3'] = self::escapeHtml($data['sidebar_3'] ?? '');
    $cleaned_data['page_notes'] = self::escapeHtml($data['page_notes'] ?? '');
    $cleaned_data['edit_permission_general'] = intval($data['edit_permission_general']);
    $cleaned_data['tv'] = str_replace(',', ', ', self::escapeHtml($data['tv'] ?? ''));
    $cleaned_data['status'] = intval($data['status']);
    $cleaned_data['content'] = self::escapeHtml($data['content'] ?? '');
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
      'time'            => date("Y-m-d H:i:s"),
      'last_modified'   => date("Y-m-d H:i:s"),
      'display_time'    => 0,
      'include_page'    => 0,
      'include_order'   => 0,
      'include_rss'     => 0,
      'include_sitemap' => 0,
      'include_news'    => 0,
      'link_name'       => Localization::$lang['teaser_default_linkname'] ?? 'teaser_default_linkname',
      'template'        => $settings['default_template'] ?? 'default_template',
      'menu_1'          => $settings['default_menu'] ?? 'default_menu',
      'edit_permission_general' => 0,
      'status'          => 2
      // ... weitere Standardwerte analog ...
    ];
  }


/**
  * Hilfsfunktion: HTML-Entities sicher kodieren
  *
  * Diese Funktion kodiert spezielle Zeichen in einem String, um sicherzustellen,
  * dass sie korrekt und sicher in HTML dargestellt werden.
  *
  * @param string $string  Der zu kodierende String.
  * @return string  Der sicher kodierte String.
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


/**
  * Hilfsfunktion: Lädt Content-spezifische Funktionen
  * 
  * @return 
  */
  public static function content_spezifische_Funktion( $x, $y )
  {
    return $x + $y;
  }


  // ... weitere Funktionen ...
}


/**
  * Änderung:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-11 
  * @date $LastChangedDate: Wed, Tue, 11 Feb 2025 13:06:08 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-11 : 4.5.0.2025.02.11    : ztatement        - added: get_time_zone
  * 2025-02-03 : 4.5.0.2025.02.03    : ztatement        - added: initializeSettings
  * 2025-01-29 : 4.5.0.2025.01.29    : ztatement        - added: get_time_format
  * 2025-01-23 : 4.5.0.2025.01.23    : ztatement        - added: formatTimestamp kleine korrekturen
  * 2025-01-20 : 4.5.0.2025.01.20    : ztatement        - added: decodeHtml und escapeAndDecodeHtml
  *                                                       cleanText und cleanUrl
  * 2025-01-20 : 4.5.0.2025.01.20    : ztatement        - added: Neue Helpers Klasse
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
