<?php
/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft (c) 2025 ztatement
 * @version 4.5.0.2025.01.20
 * @file $Id: cms/includes/edit.inc.php 1 2025-01-20 08:10:07Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *
 */
class Helpers
{

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
   * @param array $settings
   * @return array
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
      'link_name' => Localization::$lang['teaser_default_linkname'],
      'template' => $settings['default_template'],
      'menu_1' => $settings['default_menu'],
      'edit_permission_general' => 0,
      'status' => 2
      // ... weitere Standardwerte analog ...
    ];
  }

  /**
   * Eine Funktion zur Ausgabe von sauberem HTML
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
 * 2025-01-20 : 4.5.0.2025.01.20 - added: decodeHtml und escapeAndDecodeHtml
 * 2025-01-20 : 4.5.0.2025.01.20 - added: Neue Helpers Klasse
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
