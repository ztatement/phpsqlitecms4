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
 * @version 4.5.0.2025.01.16 $Id: cms/config/page_types.conf.php 1 2025-01-16 19:46:19Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *         
 */
// Sicherstellen, dass $page_types als leeres Array initialisiert ist
$page_types = [];

$page_types['default'] = [
    'page_type_label' => 'page_type_default',
    'requires_parameter' => false
];

$page_types['commentable_page'] = [
    'page_type' => 'commentable_page.php',
    'page_type_label' => 'page_type_commentable',
    'requires_parameter' => false
];

$page_types['overview'] = [
    'page_type' => 'overview.php',
    'page_type_label' => 'page_type_overview',
    'requires_parameter' => false
];

$page_types['blog-overview'] = [
    'page_type' => 'blog-overview.php',
    'page_type_label' => 'page_type_overview',
    'requires_parameter' => false
];

$page_types['news'] = [
    'page_type' => 'news.php',
    'page_type_label' => 'page_type_news',
    'requires_parameter' => false
];

$page_types['simple_news'] = [
    'page_type' => 'simple_news.php',
    'page_type_label' => 'page_type_simple_news',
    'requires_parameter' => false
];

$page_types['gallery'] = [
    'page_type' => 'gallery.php',
    'page_type_label' => 'page_type_gallery',
    'requires_parameter' => true
];

$page_types['formmailer'] = [
    'page_type' => 'formmailer.php',
    'page_type_label' => 'page_type_formmailer',
    'requires_parameter' => true
];

$page_types['redirect'] = [
    'page_type' => 'redirect.php',
    'page_type_label' => 'page_type_redirect',
    'requires_parameter' => true
];

$page_types['notes'] = [
    'page_type' => 'notes.php',
    'page_type_label' => 'page_type_notes',
    'requires_parameter' => true
];

$page_types['search'] = [
    'page_type' => 'search.php',
    'page_type_label' => 'page_type_search',
    'requires_parameter' => false
];

$page_types['lang_redirect'] = [
    'page_type' => 'language_redirect.php',
    'page_type_label' => 'page_type_language_redirect',
    'requires_parameter' => false
];

$page_types['rss'] = [
    'page_type' => 'rss.php',
    'page_type_label' => 'page_type_rss_feed',
    'requires_parameter' => false
];

$page_types['notes_rss'] = [
    'page_type' => 'notes_rss.php',
    'page_type_label' => 'page_type_notes_rss_feed',
    'requires_parameter' => true
];

$page_types['sitemap'] = [
    'page_type' => 'sitemap.php',
    'page_type_label' => 'page_type_sitemap',
    'requires_parameter' => false
];
/**
 * Änderung:
 * PHP 8.x/9-Kompatibilität: Statt array() die neuere Kurzsyntax [] verwendet.
 * Sicherstellen, dass $page_types initialisiert ist.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-16 $Date$ $LastChangedDate: 2025-01-16 19:46:19 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-16 : 4.5.0.2025.01.16 - update: PHP 8.x/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
