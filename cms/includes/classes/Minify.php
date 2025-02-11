<?php

/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft 2016 ztatement
 * @version 4.5.0.2025.02.11 $Id: cms/includes/classes/Minify.php 1 2016-07-18 16:41:09Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
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
 *      EINSCHLIE?LICH DER GARANTIE ZUR BENUTZUNG FÜR DEN VORGESEHENEN ODER EINEM BESTIMMTEN
 *      ZWECK SOWIE JEGLICHER RECHTSVERLETZUNG, JEDOCH NICHT DARAUF BESCHRÄNKT.
 *      IN KEINEM FALL SIND DIE AUTOREN ODER COPYRIGHTINHABER FÜR JEGLICHEN SCHADEN
 *      ODER SONSTIGE ANSPRÜCHE HAFTBAR ZU MACHEN, OB INFOLGE DER ERFÜLLUNG EINES VERTRAGES,
 *      EINES DELIKTES ODER ANDERS IM ZUSAMMENHANG MIT DER SOFTWARE
 *      ODER SONSTIGER VERWENDUNG DER SOFTWARE ENTSTANDEN.
 *      ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */


/*
 * html css js compression
 */
class Minify
{
  // Standardwerte für die Minifizierung
  protected bool $compress_css = true;  // Standard: CSS komprimieren
  protected bool $compress_js = true;  // Standard: JS komprimieren
  protected bool $info_comment = true;  // Standard: Keine Info-Kommentare hinzufügen (false) // true = Info-Kommentare hinzufügen
  protected bool $remove_comments = true;  // Standard: Kommentare entfernen
  protected string $html = '';  // HTML-String, der komprimiert wird


/**
  * Konstruktor, der das HTML entgegennimmt und die Minifizierung aufruft
  *
  * @param string $html  Der HTML-String, der komprimiert werden soll.
  */
  public function __construct( string $html )
  {
    if (!empty($html))
    {
      $this->parseHTML($html);
    }
  }


/**
  * Gibt das komprimierte HTML als String zurück
  *
  * @return string  Der komprimierte HTML-String.
  */
  public function __toString( ): string
  {
    return $this->html;
  }


/**
  * Fügt einen Kommentar am Ende hinzu, der die Größe der Reduzierung beschreibt
  *
  * @param string $raw  Der ursprüngliche HTML-String.
  * @param string $compressed  Der komprimierte HTML-String.
  * @return string  Der Kommentar, der die Reduzierung beschreibt.
  */
  protected function bottomComment( string $raw, string $compressed ): string
  {
    $raw_length = strlen($raw);
    $compressed_length = strlen($compressed);
    $savings = ($raw_length - $compressed_length) / $raw_length * 100;
    $savings = round($savings, 2);
    return '<!-- HTML Minify | https://www.demo-seite.com/ | Größe reduziert um ' . $savings . '% | Von ' . $raw_length . ' Bytes, auf ' . $compressed_length . ' Bytes -->';
  }


/**
  * Minifiziert HTML: Entfernt unnötige Leerzeichen, Kommentare und optimiert Tags
  *
  * @param string $html  Der HTML-String, der komprimiert werden soll.
  * @return string  Der komprimierte HTML-String.
  */
  protected function minifyHTML( string $html ): string
  {
    $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
    preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

    $overriding = false;
    $raw_tag = false;
    $html = '';

    foreach ($matches as $token)
    {
      $tag = $token['tag'] ?? null;
      $content = $token[0];

      // Tag-Handling
      if (is_null($tag))
      {
        if (!empty($token['script']))
        {
          $strip = $this->compress_js; // JS komprimieren
        }
        elseif (!empty($token['style']))
        {
          $strip = $this->compress_css; // CSS komprimieren
        }
        elseif ($content == '<!--html-compression no compression-->')
        {
          $overriding = !$overriding;
          continue;
        }
        elseif ($this->remove_comments)
        {
          // Kommentare entfernen (außer bestimmte Kommentare)
          if (!$overriding && $raw_tag !== 'textarea')
          {
            $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
          }
        }
      }
      else
      {
        if ($tag === 'pre' || $tag === 'textarea')
        {
          $raw_tag = $tag; // Raw Tags behandeln
        }
        elseif ($tag === '/pre' || $tag === '/textarea')
        {
          $raw_tag = false;
        }
        else
        {
          if ($raw_tag || $overriding)
          {
            $strip = false;
          }
          else
          {
            $strip = true;
            // Entfernt unnötige Leerzeichen in HTML-Attributen
            $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);
            $content = str_replace(' />', '/>', $content);
          }
        }
      }
      // Wenn das Tag komprimiert werden soll, Leerzeichen entfernen
      $strip = $strip ?? true;
      if ($strip)
      {
        $content = $this->removeWhiteSpace($content);
      }
      $html .= $content;
    }
    return $html;
  }


/**
  * Hauptfunktion zum Minifizieren des HTML-Codes
  *
  * @param string $html  Der HTML-String, der komprimiert werden soll.
  */
  public function parseHTML( string $html ): void
  {
    $this->html = $this->minifyHTML($html);
    if ($this->info_comment)
    {
      $this->html .= "\n" . $this->bottomComment($html, $this->html);
    }
  }


/**
  * Entfernt unnötige Leerzeichen aus einem String
  *
  * @param string $str  Der String, aus dem die Leerzeichen entfernt werden sollen.
  * @return string  Der String ohne unnötige Leerzeichen.
  */
  protected function removeWhiteSpace( string $str ): string
  {
    // Ersetzt alle Vorkommen von Tabulatoren, Zeilenumbrüchen und Wagenrückläufen mit einem einzelnen Leerzeichen
    $str = str_replace([
        "\t",
        "\n",
        "\r"
    ], ' ', $str);

    return preg_replace('/\s+/', ' ', $str); // Ersetzt alle aufeinanderfolgenden Leerzeichen durch ein einzelnes
  }

}


/**
  * Funktion, die ein Minify-Objekt erstellt und den HTML-String minifiziert
  *
  * @param string $html Der HTML-String, der komprimiert werden soll.
  * @return Minify Das Minify-Objekt, das den komprimierten HTML-String enthält.
  */
  function minify_finish( string $html ): Minify
  {
    return new Minify($html);
  }


/**
  * Funktion, die das Output-Buffering startet und minifiziert
  */
  function minify_start( ): void
  {
    ob_start('minify_finish');
  }
  // Starte das Output-Buffering und wendet die Minifizierung an
  ob_start('minify_finish'); //* Ende der HTML-Kompression */


/**
  * Änderungen
  * Änderungen: Typed Properties: Alle Eigenschaften
  * ($compress_css, $compress_js, $info_comment, $remove_comments, $html)
  * haben jetzt einen expliziten Typ (z. B. bool, string).
  * Die Methoden __toString(), minifyHTML(), parseHTML(), removeWhiteSpace() und 
  * bottomComment() haben jetzt die richtigen Typdeklarationen.
  * Null-Koaleszenz-Operator: Ich habe ?? verwendet, um Variablen auf null zu prüfen,
  * anstatt die veraltete isset() Methode zu verwenden.
  * 
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-11 $
  * @date $LastChangedDate: Tue, 11 Feb 2025 22:13:16 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$ $Revision$ - Description
  * 2025-02-11 : 4.5.0.2025.02.11 : ztatement - update: Code bereinigt und neu kommentiert.
  * 2025-01-14 : 4.5.0.2025.01.14 : ztatement - @fix: Typ bool, string; Verwendung von ??
  * 2024-12-04 : 4.0.4.2024.12.04 : ztatement - @fix: Minify-Klasse variablen (bool und string)
  * 2023-11-01 : 4.0.1.2023.12.04 : ztatement - update: Modifiziert für PHP8
  * 2016-07-18 : 4.0.0 - Erste Veröffentlichung
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
