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
  * @version 4.5.0.2025.01.21
  * @file $Id: cms/config/definitions.php 1 2016-07-18 22:51:12Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  *
  */

/**
  * Konfigurationskonstanten für JavaScript- und CSS-Abhängigkeiten.
  *
  * Diese Datei definiert verschiedene Konstanten, die in der gesamten Anwendung verwendet werden,
  * um externe JavaScript-Bibliotheken, CSS-Stylesheets und
  * andere medienbezogene Einstellungen einzubinden. Diese Konstanten stellen sicher, dass die richtigen
  * Versionen und Pfade im gesamten Projekt konsistent verwendet werden.
  *
  * Constants:
  * - JQUERY: URL für die jQuery-Bibliothek.
  * - JQUERY_UI: URL für die jQuery-UI-Bibliothek.
  * - JQUERY_UI_CSS: URL für jQuery-UI-CSS.
  * - JQUERY_UI_HANDLER: URL für benutzerdefiniertes jQuery-UI-Handler-Skript.
  * - BOOTSTRAP: URL für Bootstrap-JavaScript-Bibliothek.
  * - BOOTSTRAP_CSS: URL für Bootstrap-CSS-Stylesheet.
  * - WYSIWYG_EDITOR: URL für das WYSIWYG-Editor-Skript (TinyMCE).
  * - WYSIWYG_EDITOR_INIT: URL für das Initialisierungsskript für den WYSIWYG-Editor.
  * - VALID_URL_CHARACTERS: Regulärer Ausdruck zum Validieren von URL-Zeichen.
  * - MEDIA_DIR: Verzeichnispfad für Mediendateien.
  * - IMAGE_IDENTIFIER: Kennung für Bilder.
  * - CATEGORY_IDENTIFIER: Kennungspräfix für Kategorien.
  * - AMPERSAND_REPLACEMENT: Ersetzungszeichenfolge für Et-Zeichen.
  * - SMILIES_DIR: Verzeichnispfad für Smilies.
  */

/**
  * protocol
  * ex: http, https,...
  * 
  * Wer CloudFlare nutzt, kann das ganz einfach umbiegen:
  * Ich sage bewusst "umbiegen", weil hier eine technische Voraussetzung vorgegaukelt wird.
  * Es handelt sich nämlich wie gesagt in keiner Weise um eine sichere Verbindung.
  */
  // if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']) {
  // $_SERVER['HTTPS'] = 1;
  // }
  define('PROTOCOL', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || 
                     isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http');

  define('HTP', PROTOCOL . ':');

  define('TPX', '.template.php'); // Endung für die Templates

  define('VALID_URL_CHARACTERS', '/^[a-zA-Z0-9._\-\/]+$/');
  //Sprachdateien
  define('LANGUAGE', 'static/languages/');

  define('MEDIA_DIR',  STATIC_URL . 'media/');
  define('IMAGES_DIR', STATIC_URL . 'images/');
  define('THEME_DIR',  STATIC_URL . 'theme/' . $settings['theme'] . '/');
  define('THEME_FONT', STATIC_URL . 'theme/fonts/' . $settings['font']);

  define('TEMPLATES_PATH', 'cms/templates/'); // Backend-Templates
  define('THEME_TEMPLATES', THEME_DIR . 'templates/'); // Benutzerdefinierte Themen-Templates

  define('IMAGE_IDENTIFIER',        'photo');
  define('CATEGORY_IDENTIFIER',     'category:');
  define('AMPERSAND_REPLACEMENT',   ':AMP:');
  define('SMILIES_DIR', MEDIA_DIR . 'smilies/');

/**
  * Jquery
  */
  define('JQUERY', HTP . '//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.slim.min.js');
  # define('JQUERY', HTP . 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js');
  # define('JQUERY_LOCAL', STATIC_URL . 'js/jquery.min.js');
  define('JQUERY_UI', HTP . '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js');
  define('JQUERY_UI_CSS', HTP . '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/base/jquery-ui.min.css');
  define('JQUERY_UI_HANDLER', STATIC_URL . 'js/jquery_ui_handler.js');

/**
  * Bootstrap
  */
  define('BOOTSTRAP_CSS',   HTP . '//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css');
  # define('BOOTSTRAP_SCSS_LOCAL', BASE_URL . 'assets/scss/bootstrap.scss');
  # define('BOOTSTRAP_CSS_LOCAL', STATIC_URL . 'stylesheets/bootstrap.min.css');
  // <!-- Optional theme -->
  # define('BOOTSTRAP_THEME_CSS', HTP . '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css');
  define('BOOTSTRAP_ICONS', HTP . '//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');

  define('BOOTSTRAP', HTP . '//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js');
  # define('BOOTSTRAP_LOCAL', STATIC_URL . 'javascripts/bootstrap.min.js');

/**
  * Weitere Annehmlichkeiten ;)
  */
  define('ANIMATE', HTP . '//cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
  define('TETHER',  HTP . '//cdnjs.cloudflare.com/ajax/libs/tether/1.4.7/js/tether.min.js');
  define('HOLDER',  HTP . '//cdnjs.cloudflare.com/ajax/libs/holder/2.9.8/holder.min.js');

/**
  * Schriftdateien
  */
  define('FONTAWESOME',  HTP . '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css');
  define('ZICON', STATIC_URL . 'fonts/z-Icon/z-icon-style.css');

/**
  * Editor
  */
  define('WYSIWYG_EDITOR', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/tinymce.min.js');
  define('WYSIWYG_EDITOR_INIT', 'assets/js/wysiwyg_init.js');

/**
  * Änderung:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-01-21 $Date$ 
  * @date $LastChangedDate: 2025-01-21 15:59:31 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$ : $Revision$ - Description
  * 2025-01-18 : 4.5.0.2025.01.21 - add: THEME_TEMPLATES und LANGUAGE
  * 2024-12-08 : 4.2.5.2024.12.08 - update: Bootstrap 5, JQuery 
  * 2016-07-18 : 4.0.0 - Erste Veröffentlichung des neuen 4.x Stamm
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
