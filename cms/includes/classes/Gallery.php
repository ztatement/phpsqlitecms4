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
 * @version 4.5.0.2025.01.17
 * @file $Id: cms/includes/classes/Gallery.php 1 2025-01-17 07:01:38Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *         
 */
class Gallery {

  // Öffentliche Eigenschaften, die die Galerie-Daten repräsentieren
  public array $photos = [];

  // Liste der Fotos in der Galerie
  public int $number_of_photos = 0;

  // Anzahl der Fotos
  public int $photos_per_row = 4;

  // Standardanzahl der Fotos pro Reihe

  // Private Eigenschaft für Lokalisierung
  private Localization $_localization;

  /**
   * Konstruktor der Klasse
   *
   * @param string $gallery
   *        Der Name der Galerie
   * @param int $commentable
   *        Gibt an, ob die Galerie kommentierbar ist (1 = ja, 0 = nein)
   */
  # public function __construct( $gallery, $commentable = 0 )
  public function __construct( string $gallery, int $commentable = 0 )
  {
    // Lokalisierungsinstanz initialisieren
    $this->_localization = Localization::getInstance();

    // SQL-Abfrage für die Fotos der Galerie vorbereiten
    $dbr = Database::$content->prepare('
        SELECT
          id, photo_thumbnail, photo_normal, title, subtitle, description, photos_per_row
        FROM ' . Database::$db_settings['photo_table'] . '
        WHERE gallery = :gallery
        ORDER BY sequence ASC
    ');
    $dbr->bindValue(':gallery', $gallery, PDO::PARAM_STR);
    $dbr->execute();

    $i = 0; // Zähler für Fotos

    # while ($photo_data = $dbr->fetch())
    while ($photo_data = $dbr->fetch(PDO::FETCH_ASSOC))
    {
      // Kommentardaten laden, falls die Galerie kommentierbar ist
      if ($commentable === 1)
      {
        $count_result = Database::$entries->prepare('
            SELECT COUNT(*) AS comments
            FROM ' . Database::$db_settings['comment_table'] . '
            WHERE comment_id = :id
            AND type = 1
        ');
        $count_result->bindValue(':id', $photo_data['id'], PDO::PARAM_INT);
        $count_result->execute();

        # $count_data = $count_result->fetch();
        $count_data = $count_result->fetch(PDO::FETCH_ASSOC);
        # $this->photos[$i]['comments'] = $count_data['comments'];
        $this->photos[$i]['comments'] = $count_data['comments'] ?? 0; // Fallback, falls kein Ergebnis

        // Lokalisierungsvarianten für Kommentaranzahlen setzen
        $this->_localization->bindId('number_of_comments', $photo_data['id']);
        # switch ($count_data['comments']){
        # case 0: $this->_localization->selectBoundVariant('number_of_comments', $photo_data['id'], 0); break;
        # case 1: $this->_localization->selectBoundVariant('number_of_comments', $photo_data['id'], 1); break;
        # default:
        # $this->_localization->selectBoundVariant('number_of_comments', $photo_data['id'], 2);
        # $this->_localization->replacePlaceholderBound('comments', $count_data['comments'], 'number_of_comments', $photo_data['id']);
        # }
        $comment_count = $count_data['comments'] ?? 0;
        $this->_localization->selectBoundVariant('number_of_comments', $photo_data['id'], min($comment_count, 2) // Wähle Variante (0, 1, 2+)
        );

        if ($comment_count > 1)
        {
          $this->_localization->replacePlaceholderBound('comments', $comment_count, 'number_of_comments', $photo_data['id']);
        }
      }
      // Foto-Daten speichern, mit HTML-Sicherheitsmaßnahmen
      # $this->photos[$i]['id'] = $photo_data['id'];
      # $this->photos[$i]['photo_thumbnail'] = $photo_data['photo_thumbnail'];
      # $this->photos[$i]['photo_normal'] = $photo_data['photo_normal'];
      # $this->photos[$i]['title'] = htmlspecialchars($photo_data['title']);
      # $this->photos[$i]['subtitle'] = htmlspecialchars($photo_data['subtitle']);
      # $this->photos[$i]['description'] = htmlspecialchars($photo_data['description']);
      $this->photos[$i] = [
        'id' => $photo_data['id'],
        'photo_thumbnail' => $photo_data['photo_thumbnail'],
        'photo_normal' => $photo_data['photo_normal'],
        'title' => Helpers::decodeAndEscapeHtml($photo_data['title']),
        'subtitle' => Helpers::decodeAndEscapeHtml($photo_data['subtitle']),
        'description' => Helpers::decodeAndEscapeHtml($photo_data['description'])
      ];


      // Bildabmessungen überprüfen und hinzufügen
      /*
       * @ignore
       * Prüfe, ob die Datei existiert und lesbar ist:
       * file_exists($thumbnail_path) && is_readable($thumbnail_path) überprüft,
       * ob der Bildpfad korrekt existiert und zugänglich ist.
       * Nur wenn die Datei existiert und gültig ist, wird getimagesize() aufgerufen.
       * Falls nicht, werden sicherheitshalber width und height auf 0 gesetzt.
       * Diese Vorsichtsmaßnahmen vermeiden, dass bei nicht existierenden oder
       * ungültigen Bilddateien ein Fehler oder eine Warnung ausgegeben wird.
       */
      $thumbnail_path = MEDIA_DIR . $photo_data['photo_thumbnail'];
      if (file_exists($thumbnail_path) && is_readable($thumbnail_path))
      {
        $thumbnail_info = getimagesize($thumbnail_path);
        if ($thumbnail_info)
        {
          $this->photos[$i]['width'] = $thumbnail_info[0];
          $this->photos[$i]['height'] = $thumbnail_info[1];
        } else
        {
          $this->photos[$i]['width'] = 0;
          $this->photos[$i]['height'] = 0;
        }
      } else
      {
        $this->photos[$i]['width'] = 0;
        $this->photos[$i]['height'] = 0;
      }
      # $thumbnail_info = getimagesize(MEDIA_DIR . $photo_data['photo_thumbnail']);
      # $this->photos[$i]['width'] = $thumbnail_info[0];
      # $this->photos[$i]['height'] = $thumbnail_info[1];

      // Anzahl der Fotos pro Reihe setzen (falls angegeben)
      # $this->photos_per_row = $photo_data['photos_per_row'];
      $this->photos_per_row = $photo_data['photos_per_row'] ?: $this->photos_per_row;
      $i++;
    }
    // Gesamte Anzahl der Fotos speichern
    $this->number_of_photos = $i;
  }
}

/**
 * Änderung:
 * Für Klassenvariablen (public array $photos) und Methodenparameter (string $gallery) Typdeklarationen hinzugefügt.
 * htmlspecialchars wird konsequent mit ENT_QUOTES und UTF-8 verwendet, um potenzielle Sicherheitslücken zu vermeiden.
 * Verwendet jetzt PDO::FETCH_ASSOC, um assoziative Arrays zurückzugeben, was die Lesbarkeit erhöht.
 * Fallback-Mechanismen (?? 0) wurden hinzugefügt, um auf mögliche null-Werte zu reagieren.
 * Bildprüfung: file_exists und is_readable wurden ergänzt, um sicherzustellen, dass Dateien existieren und zugänglich sind.
 * Performance: min($comment_count, 2) vereinfacht die Auswahl der Lokalisierungsvariante.
 * PHP 8.4+/9 Kompatibilität: Verwendet jetzt strikte Typdeklarationen und moderne Kontrollstrukturen.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-17 $Date$ $LastChangedDate: 2025-01-17 07:01:38 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-20 : 4.5.0.2025.01.20 - update: decodeAndEscapeHtml-Funktion verwendet
 * 2025-01-17 : 4.5.0.2025.01.16 - update: PHP 8.4+/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
