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
 * @copyleft () 2025 ztatement
 * @version 4.5.0.2024.12.30 $Id: modal.inc.php 1 2025-01-04 15:37:07Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS
 *         
 */
if (!defined('IN_INDEX'))
{
  exit();
}

if (isset($_SESSION[$settings['session_prefix'] . 'user_id']) && isset($_REQUEST['action']))
{
  switch ($_REQUEST['action'])
  {
    case 'insert_gallery':
      // Abfrage der Galerien aus der Datenbank
      $dbr = Database::$content->query("SELECT DISTINCT gallery FROM " . Database::$db_settings['photo_table'] . " ORDER BY gallery ASC");
      $galleries = [];
      while ($data = $dbr->fetch())
      {
        // Hinzufügen der Galerie zur Liste
        $galleries[] = htmlspecialchars($data['gallery']);
      }
      // Überprüfen, ob das Array nicht leer ist
      # if (isset($galleries))
      if (!empty($galleries))
      {
        $template->assign('galleries', $galleries); // Übergabe der Galerien an das Template
      }

      $template_file = 'subtemplates/modal_insert_gallery' . TPX;
      break;

    case 'insert_thumbnail':
      // Abfrage der Thumbnails aus der Datenbank
      $dbr = Database::$content->query("SELECT id, title, gallery FROM " . Database::$db_settings['photo_table'] . " ORDER BY gallery ASC, sequence ASC");
      $thumbnails = []; // Initialisierung des Arrays
      $i = 0;
      while ($data = $dbr->fetch())
      {
        // Hinzufügen der Thumbnail-Daten zur Liste
        $thumbnails[$i]['id'] = $data['id'];
        $thumbnails[$i]['gallery'] = htmlspecialchars($data['gallery']);
        $thumbnails[$i]['title'] = htmlspecialchars($data['title']);
        ++$i;
      }
      // Überprüfen, ob das Array nicht leer ist
      # if (isset($thumbnails))
      if (!empty($thumbnails))
      {
        $template->assign('thumbnails', $thumbnails); // Übergabe der Thumbnails an das Template
      }

      $template_file = 'subtemplates/modal_insert_thumbnail' . TPX;
      break;

    # case 'insert_image':
    # $fp = opendir(BASE_PATH . MEDIA_DIR);
    # while ($file = readdir($fp))
    # {
    # if (preg_match('/\.jpg$/i', $file) || preg_match('/\.jpeg$/i', $file) || preg_match('/\.png$/i', $file))
    # {
    # $images[] = $file;
    # }
    # }
    # closedir($fp);
    #
    # if (isset($images))
    # {
    # natcasesort($images);
    # $template->assign('images', $images);
    # }
    # $template_file = 'subtemplates/modal_insert_image' . TPX;
    # break;

    case 'insert_image':
    case 'insert_raw_image':
      $images = []; // Initialisierung des Arrays für die Bilder
      $fp = opendir(BASE_PATH . MEDIA_DIR);
      if ($fp)
      {
        while ($file = readdir($fp))
        {
          // Prüfung auf unterstützte Bilddateiformate
          if (preg_match('/\.jpg$/i', $file) || preg_match('/\.jpeg$/i', $file) || preg_match('/\.png$/i', $file))
          {
            $images[] = $file; // Hinzufügen des Bildes zur Liste
          }
        }
        closedir($fp); // Schließen des Verzeichnisses
      }

      // Überprüfen, ob das Array nicht leer ist
      # if (isset($images))
      if (!empty($images))
      {
        natcasesort($images); // Sortieren der Bilder (nicht abhängig von Groß-/Kleinschreibung)
        $template->assign('images', $images); // Übergabe der Bilder an das Template
      }
      // Setzen des Template-Dateipfads basierend auf der Aktion
      # $template_file = 'subtemplates/modal_insert_raw_image' . TPX;
      $template_file = ($_REQUEST['action'] == 'insert_image') ? 'subtemplates/modal_insert_image' . TPX : 'subtemplates/modal_insert_raw_image' . TPX;
      break;
  }
}
/*
 * Änderungen:
 * Verwendung von !empty() statt isset() für Arrays: Anstelle von isset($galleries)
 * und isset($thumbnails), wird nun !empty($galleries) bzw. !empty($thumbnails) verwendet,
 * um zu prüfen, ob das Array tatsächlich Werte enthält.
 * Verschmelzen der beiden ähnlichen Fälle insert_image und insert_raw_image:
 * Da der Code für beide Fälle nahezu identisch ist, zusammengefasst und der
 * template_file-Wert anhand der Action gesetzt.
 * Sicherheitsvorkehrungen bei opendir(): Die Funktion opendir() kann fehlschlagen,
 * wenn das Verzeichnis nicht existiert oder nicht lesbar ist.
 * Überprüfung hinzugefügt, um sicherzustellen, dass der Handle ($fp) erfolgreich geöffnet wurde,
 * bevor auf das Verzeichnis zugegriffen wird.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-04 $Date$ $LastChangedDate: 2025-01-04 16:23:03 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * ---------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$   - Description
 * 2025-01-04 : 4.5.0.2024.12.30    : @ztatement        - @fix !empty() statt isset()
 *                                                        insert_image und insert_raw_image zusammengefasst.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
  