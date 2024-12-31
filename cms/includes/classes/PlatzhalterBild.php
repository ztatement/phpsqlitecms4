<?php

/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Thomas Boettcher <github[at]ztatement[dot]de>
 * @copyleft () 2024 ztatement
 * @version 4.5.0.2024.12.30 $Id: cms/includes/classes/PlatzhalterBild.php 1 2024-12-30 15:58:38Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS
 *         
 *         
 * @author $Author$
 * @coyleft $Copyright$
 * @version $Rev$: $Revision$ $Id: $
 *          Last changed: $Date$: $LastChangedDate$
 * @name $globalVariableName
 * @package name
 * @subpackage name
 * @since version
 * @todo Description
 *      
 * @license The MIT License (MIT)
 * @see /LICENSE
 * @see https://opensource.org/licenses/MIT Hiermit wird unentgeltlich jeder Person, die eine Kopie der Software und der zugehörigen
 *      Dokumentationen (die "Software") erhält, die Erlaubnis erteilt, sie uneingeschränkt zu nutzen,
 *      inklusive und ohne Ausnahme mit dem Recht, sie zu verwenden, zu kopieren, zu verändern,
 *      zusammenzufügen, zu veröffentlichen, zu verbreiten, zu unterlizenzieren und/oder zu verkaufen,
 *      und Personen, denen diese Software überlassen wird, diese Rechte zu verschaffen,
 *      unter den folgenden Bedingungen:
 *     
 *      Der obige Urheberrechtsvermerk und dieser Erlaubnisvermerk sind in allen Kopien
 *      oder Teilkopien der Software beizulegen.
 *     
 *      DIE SOFTWARE WIRD OHNE JEDE AUSDRÜCKLICHE ODER IMPLIZIERTE GARANTIE BEREITGESTELLT,
 *      EINSCHLIEẞLICH DER GARANTIE ZUR BENUTZUNG FÜR DEN VORGESEHENEN ODER EINEM BESTIMMTEN
 *      ZWECK SOWIE JEGLICHER RECHTSVERLETZUNG, JEDOCH NICHT DARAUF BESCHRÄNKT.
 *      IN KEINEM FALL SIND DIE AUTOREN ODER COPYRIGHTINHABER FÜR JEGLICHEN SCHADEN
 *      ODER SONSTIGE ANSPRÜCHE HAFTBAR ZU MACHEN, OB INFOLGE DER ERFÜLLUNG EINES VERTRAGES,
 *      EINES DELIKTES ODER ANDERS IM ZUSAMMENHANG MIT DER SOFTWARE
 *      ODER SONSTIGER VERWENDUNG DER SOFTWARE ENTSTANDEN.
 *      ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class PlatzhalterBild
{

  private string $imageDir;

  // Konstruktor mit Typisierung
  public function __construct( string $imageDir = 'assets/images/' )
  {
    // Sicherstellen, dass das Verzeichnis existiert
    if (!is_dir($imageDir))
    {
      mkdir($imageDir, 0777, true); // Verzeichnis erstellen, falls es nicht existiert
    }
    $this->imageDir = rtrim($imageDir, '/') . '/';
  }

  // Typisierung für Rückgabewert (string)
  public function erstellePlatzhalterBild( string $dimension ): string
  {
    try
    {
      list ($width, $height) = explode('x', $dimension);

      // Erstelle ein leeres Bild mit den angegebenen Dimensionen
      $image = imagecreatetruecolor($width, $height);

      // Hintergrundfarbe (z.B. Grau)
      $bgColor = imagecolorallocate($image, 200, 200, 200);

      // Fülle das Bild mit der Hintergrundfarbe
      imagefill($image, 0, 0, $bgColor);

      // Textfarbe (schwarz)
      $textColor = imagecolorallocate($image, 100, 100, 100);

      // Text, der auf das Bild geschrieben wird
      $text = "{$width}x{$height}";

      // Schriftart-Datei (muss vorhanden sein)
      $fontPath = './static/fonts/z-Icon/z-IconPro-Light.ttf'; // Pfad zur Schriftart (ggf. ersetzen)

      // Berechne die Textposition, damit der Text in der Mitte erscheint
      $textBox = imagettfbbox(20, 0, $fontPath, $text);
      $textWidth = $textBox[2] - $textBox[0];
      $textHeight = $textBox[1] - $textBox[5];

      // Textposition festlegen
      $x = ($width - $textWidth) / 2;
      $y = ($height - $textHeight) / 2 + $textHeight;

      // Text auf das Bild schreiben
      imagettftext($image, 20, 0, intval($x), intval($y), $textColor, $fontPath, $text);

      // Bild-Pfad generieren (z.B. "images/platzhalter_150x80.jpg")
      $imagePath = $this->imageDir . 'platzhalter_' . $width . 'x' . $height . '.jpg';

      // Bild als JPG speichern
      # imagejpeg($image, $imagePath);
      if (!imagejpeg($image, $imagePath))
      {
        throw new Exception("Fehler beim Speichern des Bildes.");
      }

      // Rückgabe des Bildpfads
      return $imagePath;
    }
    catch (Exception $e)
    {
      // Fehlerbehandlung: Logge den Fehler oder gebe eine benutzerfreundliche Nachricht zurück
      error_log($e->getMessage());
      return 'Fehler beim Erstellen des Bildes.';
    }
    finally {

      // Speicher freigeben
      imagedestroy($image); // Sicherstellen, dass der Speicher immer freigegeben wird

      // Rückgabe des Bildpfads
      return $imagePath;
    }
  }

  // Typisierung für Rückgabewert (string)
  public function getBildTag( string $dimension ): string
  {
    $bildPfad = $this->erstellePlatzhalterBild($dimension);
    # return '<img src="' . $bildPfad . '" alt="Platzhalter ' . $dimension . '" />';
    return $bildPfad . '" alt="Platzhalter ' . $dimension;
  }
}

/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2024-12-30 $Date$ $LastChangedDate: 2024-12-30 15:58:38 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$   - Description
 * 2024-12-30 : 4.5.0.2024.12.30    : @ztatement        - @new: neue Klasse für Platzhalter Bilder erstellt.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
