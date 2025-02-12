<?php

/**
  * Smilies class
  *
  * @author Mark Alexander Hoschek <alex at mylittlehomepage dot net>
  * @copyright 2010 Mark Alexander Hoschek
  *
  * @version last 3.2015.04.02.18.42
  * @original-file cms/config/smilies.conf.php (deleted)
  * @package phpSQLiteCMS
  *
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * 
  * @version 4.5.0.2025.02.12
  * @file $Id: cms/includes/classes/Smilies.php 1 Wed, 12 Feb 2025 15:32:09 +0100Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  */

// Namespace beibehalten, falls gewünscht
#namespace cms\includes\classes;

#use Exception;

class Smilies
{

  // 'readonly' sorgt dafür, dass diese Werte nach der Initialisierung nicht mehr geändert werden können.
  public readonly array $smilies;

/**
  * Konstruktor, um die Smileys beim Erstellen der Klasse zu laden.
  *
  * Initialisiert das Array der Smileys und behandelt potenzielle Fehler
  * beim Laden der Daten.
  *
  * @throws Exception  Wenn die Smileys nicht geladen werden können.
  */
  public function __construct ()
  {
    // Initialisiere das Array mit Smileys
    try
    {
      $this->smilies = self::loadSmilies();
    }
    catch (Exception $e)
    {
      // Fehlerbehandlung: Falls beim Laden der Smileys ein Fehler auftritt
      $exceptionHandler = new Exception(
        true);
      $exceptionHandler->handle($e);
    }
  }


/**
  * Lädt die Smiley-Daten.
  *
  * Diese Methode definiert die Smileys als Array von Paaren [Smiley-Zeichen => Bilddatei].
  *
  * @return array Das Array von Smileys mit deren Bilddateien.
  * @throws Exception Wenn ein Fehler auftritt, z.B. ein Problem mit der Datenquelle.
  */
  private static function loadSmilies (): array
  {
    return [
      [':-)',   'smile.png'],         // Smiley
      [';-)',   'wink.png'],          // Zwinkern
      [':-P',   'tongue.png'],        // Zunge raus
      [':-D',   'big_smile.png'],     // Großes Lächeln
      [':-|',   'neutral.png'],       // Neutral
      [':-(',   'sad.png'],           // Traurig
      [':)',    'smile.png'],         // Einfaches Lächeln
      [':D',    'big_smile.png'],     // Einfaches großes Lächeln
      [':P',    'tongue.png'],        // Einfach Zunge raus
      [':O',    'surprised.png'],     // Überrascht
      ['B)',    'cool.png'],          // Cool (mit Sonnenbrille)
      [":'(",   'crying.png'],        // Weinen
      [':3',    'cat_face.png'],      // Katze (süßes Lächeln)
      ['XD',    'laughing.png'],      // Hart lachen
      [':|',    'neutral.png'],       // Neutral
      ['O:)',   'angel.png'],         // Engel (unschuldig)
      ['>:)',   'evil.png'],          // Teufel
      [':-/',   'skeptical.png'],     // Skeptisch
      ['8)',    'cool.png'],          // Cool (mit Brille)
      [':-*',   'kiss.png'],          // Kuss
      ['<3',    'heart.png'],         // Herz (Liebe)
      [':$',    'embarrassed.png'],   // Verlegen
      [':v',    'peace.png'],         // Frieden
      [':c)',   'glasses.png'],       // Smiley mit Brille (hipster)
      [':-]',   'geek.png'],          // Nerd (mit eckigen Klammern)
    ];
  }


/**
  * Ersetzt die Smiley-Zeichen im gegebenen String durch die entsprechenden Bild-HTML-Tags.
  *
  * @param string $string Der Eingabestring, der die Smileys enthält.
  * @return string Der String mit ersetzten Smileys.
  */
  public static function smilies (string $string): string
  {
    $smilies = self::loadSmilies();

    foreach ($smilies as $smiley)
    {
      $string = str_replace($smiley[0], '<img src="' . BASE_URL . SMILIES_DIR . $smiley[1] . '" alt="' . $smiley[0] . '" />', $string);
    }
    return $string;
  }


/**
  * Gibt alle Smileys aus.
  *
  * Diese Methode durchläuft das interne Array der Smileys (`$smilies`) und gibt für jedes 
  * Smiley ein HTML-Bild-Tag aus. Die Methode verwendet die `makeSmileyImageTag`-Funktion,
  * um das HTML-Tag für jedes Smiley zu erstellen und gibt es anschließend direkt aus.
  * 
  * Diese Methode eignet sich gut, um alle verfügbaren Smileys auf einer Webseite anzuzeigen,
  * beispielsweise in einem Formular zur Eingabe von Beiträgen oder Kommentaren.
  * 
  * @return void
  */
  public function displaySmilies (): void
  {
    foreach ($this->smilies as $smiley)
    {
      echo $this->makeSmileyImageTag($smiley[0], $smiley[1]);
    }
  }


/**
  * Gibt das HTML-Bild für einen Smiley zurück.
  *
  * @param string $smiley Das Smiley-Zeichen.
  * @param string $image Das Bild, das zum Smiley gehört.
  * @return string Das HTML-Tag für das Bild.
  */
  private function makeSmileyImageTag (string $smiley, string $image): string
  {
    return '<img src="' . BASE_PATH . SMILIES_DIR . $image . '" alt="' . Helpers::escapeHtml($smiley) . '" />';
  }

}


/**
  * Änderungen:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-12 $
  * @date $LastChangedDate: Wed, 12 Feb 2025 15:32:09 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-12 : 4.5.0.2025.02.12    : ztatement        - added: Smilies Klasse neu angelegt (zuvor smilies.conf)
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
