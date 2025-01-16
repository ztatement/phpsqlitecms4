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
 * @version 4.5.0.2025.01.16 $Id: cms/includes/page_types/rss.php 1 2025-01-16 18:56:23Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *         
 */
// Sicherstellen, dass das Skript nicht direkt aufgerufen wird
if (!defined('IN_INDEX'))
{
  exit('Direct access is not allowed.');
}

// Aktuelle Zeit abrufen
$current_time = time();

// Verbindung zur Datenbank und Abfrage vorbereiten
# $dbr = Database::$content->prepare("SELECT id, page, type, category, title, teaser, teaser_formatting, teaser_img, teaser_headline, content, content_formatting, time, last_modified FROM " . Database::$db_settings['pages_table'] . " WHERE include_rss=:include_rss AND time<=:time AND status!=0 ORDER BY time DESC LIMIT " . $settings['rss_maximum_items']);
$dbr = Database::$content->prepare("
    SELECT
        id, page, type, category, title, teaser, teaser_formatting, teaser_img,
        teaser_headline, content, content_formatting, time, last_modified
    FROM " . Database::$db_settings['pages_table'] . "
    WHERE include_rss = :include_rss
    AND time <= :time
    AND status != 0
    ORDER BY time DESC
    LIMIT :limit
");

// Werte binden und Abfrage ausführen
$dbr->bindParam(':include_rss', $page_id, PDO::PARAM_INT);
$dbr->bindParam(':time', $current_time, PDO::PARAM_INT);
$dbr->bindValue(':limit', $settings['rss_maximum_items'], PDO::PARAM_INT);
$dbr->execute();

// Prüfen, ob der Voll-Feed aktiviert ist
# if (isset($_GET['get_1']) && $_GET['get_1'] == 'fullfeed' && $settings['enable_fullfeeds'])
# $fullfeed = true;
# else
# $fullfeed = false;
// Sicherstellen, dass $fullfeed definiert ist, falls der GET-Parameter nicht gesetzt ist
$fullfeed = isset($_GET['get_1']) && $_GET['get_1'] === 'fullfeed' && $settings['enable_fullfeeds'];

// Initialisierung für RSS-Items
$rss_items = [];
$i = 0;

// Alle Daten aus der Abfrage durchgehen
# while ($rss_data = $dbr->fetch())
while ($rss_data = $dbr->fetch(PDO::FETCH_ASSOC))
{
  // Kategorie und Titel verarbeiten und escapen
  # $rss_items[$i]['category'] = htmlspecialchars($rss_data['category']);
  $rss_items[$i]['category'] = htmlspecialchars($rss_data['category'] ?? '');
  # $rss_items[$i]['title'] = htmlspecialchars($rss_data['title']);

  # if($rss_data['headline'] && $fullfeed || empty($rss_data['teaser_headline'])) $rss_items[$i]['title'] = htmlspecialchars($rss_data['headline']);
  # if ($rss_data['teaser_headline'])
  # $rss_items[$i]['title'] = htmlspecialchars($rss_data['teaser_headline']);
  # else
  # $rss_items[$i]['title'] = htmlspecialchars($rss_data['title']);
  $rss_items[$i]['title'] = htmlspecialchars($rss_data['teaser_headline'] ?? $rss_data['title'] ?? '');

  // Inhalt abhängig vom Modus verarbeiten (Voll-Feed oder Teaser)
  # if ($fullfeed || $rss_data['teaser'] == '')
  if ($fullfeed || empty($rss_data['teaser']))
  {
    # if ($rss_data['content_formatting'] == 1){
    # $rss_items[$i]['content'] = auto_html($rss_data['content']);
    # }else{
    # $rss_items[$i]['content'] = $rss_data['content'];
    # }
    # $rss_items[$i]['content'] = parse_special_tags($rss_items[$i]['content'], $parent_page = $rss_data['page'], $rss = true);
    # $rss_items[$i]['content'] = preg_replace_callback("#\[image:(.+?)\]#is", "create_image", $rss_items[$i]['content']);
    # $rss_items[$i]['content'] = preg_replace_callback("#\[thumbnail:(.+?)\]#is", "create_thumbnail_rss", $rss_items[$i]['content']);
    # $rss_items[$i]['content'] = preg_replace_callback("#\[gallery:(.+?)\]#is", "create_gallery_rss", $rss_items[$i]['content']);
    # $rss_items[$i]['content'] = preg_replace('/\[\[([^|\]]+?)(?:\|([^\]]+))?\]\]/e', "'<a href=\"\$1\">'.(('\$2')?'\$2':'\$1').'</a>'", $rss_items[$i]['content']);
    $rss_items[$i]['content'] = $rss_data['content_formatting'] == 1 ? auto_html($rss_data['content'] ?? '') : ($rss_data['content'] ?? '');

    $rss_items[$i]['content'] = parse_special_tags($rss_items[$i]['content'], $rss_data['page'], true);
  }
  else
  {
    # if ($rss_data['teaser_formatting'] == 1){
    # $rss_items[$i]['content'] = auto_html($rss_data['teaser']);
    # }else{
    # $rss_items[$i]['content'] = $rss_data['teaser'];
    # }
    $rss_items[$i]['content'] = $rss_data['teaser_formatting'] == 1 ? auto_html($rss_data['teaser'] ?? '') : ($rss_data['teaser'] ?? '');
  }

  // Bildinformationen hinzufügen, falls vorhanden und kein Voll-Feed
  # if (!$fullfeed && $rss_data['teaser_img'])
  if (!$fullfeed && !empty($rss_data['teaser_img']))
  {
    $rss_items[$i]['teaser_img'] = $rss_data['teaser_img'];
    # $teaser_img_info = getimagesize(BASE_PATH . MEDIA_DIR . $rss_data['teaser_img']);
    $teaser_img_path = BASE_PATH . MEDIA_DIR . $rss_data['teaser_img'];

    # $rss_items[$i]['teaser_img_width'] = $teaser_img_info[0];
    # $rss_items[$i]['teaser_img_height'] = $teaser_img_info[1];
    if (file_exists($teaser_img_path))
    {
      $teaser_img_info = getimagesize($teaser_img_path);
      $rss_items[$i]['teaser_img_width'] = $teaser_img_info[0];
      $rss_items[$i]['teaser_img_height'] = $teaser_img_info[1];
    }
  }

  // Link und Veröffentlichungsdatum hinzufügen
  $rss_items[$i]['link'] = BASE_URL . $rss_data['page'];
  $rss_items[$i]['pubdate'] = gmdate('r', $rss_data['time']);

  // Kommentarfunktionalität hinzufügen, falls zutreffend
  # $wfw = false;
  if ($rss_data['type'] === 'commentable_page')
  {
    # $wfw = true;
    $rss_items[$i]['commentrss'] = BASE_URL . $rss_data['page'] . ',commentrss';
  }
  $i++;
}

// RSS-Daten an das Template übergeben
# if (isset($wfw))
# $template->assign('wfw', $wfw);
if (isset($rss_items))
{
  $template->assign('rss_items', $rss_items);
  $template->assign('wfw', array_column($rss_items, 'commentrss') ? true : false);
}

// Content-Type und Template-Datei setzen
$content_type = 'text/xml';
$template_file = 'rss' . TPX;

// Caching konfigurieren, falls aktiviert
if (isset($cache))
{
  # if ($fullfeed){
  # $cache->cacheId = PAGE . ',full';
  $cache->cacheId = PAGE . ($fullfeed ? ',full' : '');
  # }else{
  # $cache->cacheId = PAGE;
  # }
}
/**
 * Änderung:
 * PHP 8.x/9-Kompatibilität: Die Bedingungen wurden vereinfacht, veraltete Syntax entfernt.
 * Verwendung von bindValue statt bindParam, da es sicherer ist und keine Variable referenziert werden muss.
 * fetch(PDO::FETCH_ASSOC): Datensätze als assoziative Arrays abrufen.
 * Null-Sicherheitsprüfung mit ??, Verhindert, dass null an htmlspecialchars() übergeben wird.
 * Ähnlich bei teaser_headline, title, teaser und content.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-16 $Date$ $LastChangedDate: 2025-01-16 18:56:23 +0100 $
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
