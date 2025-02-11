<?php

/**
  * Config
  *
  * @author Mark Hoschek < mail at mark-hoschek dot de >
  * @copyright (c) 2014 Mark Hoschek
  *
  * @version last 3.2015.04.02.18.42
  * @original-file $Id: cms/config/db_settings.conf.php
  * @original-file $Id: cms/config/page_types.conf.php
  * @package phpSQLiteCMS
  *
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * 
  * @version 4.5.0.2025.02.11
  * @file $Id: cms/includes/classes/Config.php 1 2025-02-11T20:45:49Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  */


class Config
{
/**
  * Diese Methode gibt die Datenbankeinstellungen zurück.
  *
  * Es werden zunächst die Standardwerte für die Datenbankeinstellungen definiert.
  * Anschließend werden diese Werte mit den übergebenen Einstellungen zusammengeführt,
  * sodass die übergebenen Werte die Standardwerte überschreiben, falls vorhanden.
  * Wenn kein oder ein ungültiger Datenbanktyp übergeben wird, wird 'sqlite' als Standardwert gesetzt.
  *
  * @param array $settings  Die spezifischen Einstellungen, die die Standardwerte überschreiben.
  * @return array  Die zusammengeführten Datenbankeinstellungen.
  */
  public static function get_db_settings($db_settings = [])
  {
    // Standardwerte für die Datenbankeinstellungen
    $default_settings = [
      'db_type'           => 'sqlite',
      'db_content_file'   => 'cms/data/content.sqlite',
      'settings_table'    => 'phpsqlitecms_settings',
      'pages_table'       => 'phpsqlitecms_pages',
      'menu_table'        => 'phpsqlitecms_menus',
      'gcb_table'         => 'phpsqlitecms_gcb',
      'news_table'        => 'phpsqlitecms_news',
      'notes_table'       => 'phpsqlitecms_notes',
      'photo_table'       => 'phpsqlitecms_photos',
      'banlists_table'    => 'phpsqlitecms_banlists',

      'db_entry_file'     => 'cms/data/entries.sqlite',
      'comment_table'     => 'phpsqlitecms_comments',
      'newsletter_table'  => 'phpsqlitecms_newsletter',

      'db_userdata_file'  => 'cms/data/userdata.sqlite',
      'userdata_table'    => 'phpsqlitecms_userdata',

      'db_account_file'   => 'cms/data/account.sqlite',
      'account_table'     => 'phpsqlitecms_account',
    ];

    // Unterstützte Datenbanktypen
    $supported_db_types = ['sqlite', 'postgresql', 'mysql'];

    // Überprüfen, ob der übergebene Datenbanktyp unterstützt wird
    if (isset($db_settings['db_type']) && in_array($db_settings['db_type'], $supported_db_types))
    {
      $db_type = $db_settings['db_type'];
    }
    else
    {
      $db_type = 'sqlite'; // Standardwert setzen
    }

    // Den ermittelten Datenbanktyp setzen
    $default_settings['db_type'] = $db_type;

    // Zusammenführen der spezifischen Einstellungen mit den Standardwerten
    return array_merge($default_settings, $db_settings);
  }


/**
  * Diese Methode gibt die Seitentypen-Einstellungen zurück.
  *
  * @return array Die Seitentypen-Einstellungen.
  */
  public static function get_page_types()
  {
    // Seitentypen-Einstellungen
    return [
      'default' => [
        'page_type_label' => 'page_type_default',
        'requires_parameter' => false
      ],
      'commentable_page' => [
        'page_type' => 'commentable_page.php',
        'page_type_label' => 'page_type_commentable',
        'requires_parameter' => false
      ],
      'overview' => [
        'page_type' => 'overview.php',
        'page_type_label' => 'page_type_overview',
        'requires_parameter' => false
      ],
      'blog-overview' => [
        'page_type' => 'blog-overview.php',
        'page_type_label' => 'page_type_overview',
        'requires_parameter' => false
      ],
      'news' => [
        'page_type' => 'news.php',
        'page_type_label' => 'page_type_news',
        'requires_parameter' => false
      ],
      'simple_news' => [
        'page_type' => 'simple_news.php',
        'page_type_label' => 'page_type_simple_news',
        'requires_parameter' => false
      ],
      'gallery' => [
        'page_type' => 'gallery.php',
        'page_type_label' => 'page_type_gallery',
        'requires_parameter' => true
      ],
      'formmailer' => [
        'page_type' => 'formmailer.php',
        'page_type_label' => 'page_type_formmailer',
        'requires_parameter' => true
      ],
      'redirect' => [
        'page_type' => 'redirect.php',
        'page_type_label' => 'page_type_redirect',
        'requires_parameter' => true
      ],
      'notes' => [
        'page_type' => 'notes.php',
        'page_type_label' => 'page_type_notes',
        'requires_parameter' => true
      ],
      'search' => [
        'page_type' => 'search.php',
        'page_type_label' => 'page_type_search',
        'requires_parameter' => false
      ],
      'lang_redirect' => [
        'page_type' => 'language_redirect.php',
        'page_type_label' => 'page_type_language_redirect',
        'requires_parameter' => false
      ],
      'rss' => [
        'page_type' => 'rss.php',
        'page_type_label' => 'page_type_rss_feed',
        'requires_parameter' => false
      ],
      'notes_rss' => [
        'page_type' => 'notes_rss.php',
        'page_type_label' => 'page_type_notes_rss_feed',
        'requires_parameter' => true
      ],
      'sitemap' => [
        'page_type' => 'sitemap.php',
        'page_type_label' => 'page_type_sitemap',
        'requires_parameter' => false
      ]
    ];
  }

}

/**
  *
  * // Beispielaufruf der Methode get_db_settings
  * $specific_settings = [
  *   'db_type' => 'mysql',
  *   'host' => 'localhost',
  *   'port' => 3306,
  *   'user' => 'root',
  *   'password' => '',
  *   'database' => 'phpsqlitecms'
  * ];
  *
  * $db_settings = Config::get_db_settings($specific_settings);
  * print_r($db_settings);
  *
  * // Beispielaufruf der Methode get_page_types
  * $page_types = Config::get_page_types();
  * print_r($page_types);
  * 
  */


/**
  * Änderungen:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-11 $
  * @date $LastChangedDate: Tue, 11 Feb 2025 20:45:49 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-11 : 4.5.0.2025.02.11    : ztatement        - added: neu angelegt (zuvor db_settings.conf und page_types.conf)
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
