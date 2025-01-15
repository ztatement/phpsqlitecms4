<?php

/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft 2016 ztatement
 * @version 4.5.0.2025.01.14 $Id: cms/includes/classes/Minify.php 1 2016-07-18 16:41:09Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *         
 * @license The MIT License (MIT)
 * @see /LICENSE
 * @see https://opensource.org/licenses/MIT Hiermit wird unentgeltlich jeder Person, die eine Kopie der Software und der zugeh�rigen
 *      Dokumentationen (die "Software") erh�lt, die Erlaubnis erteilt, sie uneingeschr�nkt zu nutzen,
 *      inklusive und ohne Ausnahme mit dem Recht, sie zu verwenden, zu kopieren, zu ver�ndern,
 *      zusammenzuf�gen, zu ver�ffentlichen, zu verbreiten, zu unterlizenzieren und/oder zu verkaufen,
 *      und Personen, denen diese Software �berlassen wird, diese Rechte zu verschaffen,
 *      unter den folgenden Bedingungen:
 *     
 *      Der obige Urheberrechtsvermerk und dieser Erlaubnisvermerk sind in allen Kopien
 *      oder Teilkopien der Software beizulegen.
 *     
 *      DIE SOFTWARE WIRD OHNE JEDE AUSDR�CKLICHE ODER IMPLIZIERTE GARANTIE BEREITGESTELLT,
 *      EINSCHLIE?LICH DER GARANTIE ZUR BENUTZUNG F�R DEN VORGESEHENEN ODER EINEM BESTIMMTEN
 *      ZWECK SOWIE JEGLICHER RECHTSVERLETZUNG, JEDOCH NICHT DARAUF BESCHR�NKT.
 *      IN KEINEM FALL SIND DIE AUTOREN ODER COPYRIGHTINHABER F�R JEGLICHEN SCHADEN
 *      ODER SONSTIGE ANSPR�CHE HAFTBAR ZU MACHEN, OB INFOLGE DER ERF�LLUNG EINES VERTRAGES,
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
  /*
   * protected $compress_css = true;
   * protected $compress_js = true;
   * protected $info_comment = false;
   * protected $remove_comments = true;
   * protected $html;
   * public function __construct($html)
   * {
   * if (!empty ($html) )
   * {
   * $this->parseHTML($html);
   * }
   * }
   */
  // Standard: CSS komprimieren
  protected bool $compress_css = true;

  // Standard: JS komprimieren
  protected bool $compress_js = true;

  // Standard: Keine Info-Kommentare hinzufügen (false)
  protected bool $info_comment = true;

  // true = Info-Kommentare hinzufügen

  // Standard: Kommentare entfernen
  protected bool $remove_comments = true;

  // HTML-String, der komprimiert wird
  protected string $html = '';

  // Konstruktor, der das HTML entgegennimmt und die Minifizierung aufruft
  public function __construct( string $html )
  {
    if (!empty($html))
    {
      $this->parseHTML($html);
    }
  }

  // Die __toString-Methode sorgt dafür, dass das Objekt als String zurückgegeben wird
  # public function __toString( )
  public function __toString( ): string
  {
    return $this->html;
  }

  // Fügt einen Kommentar am Ende hinzu, der die Größe der Reduzierung beschreibt
  # protected function bottomComment( $raw, $compressed )
  protected function bottomComment( string $raw, string $compressed ): string
  {
    # $raw = strlen($raw);
    $raw_length = strlen($raw);
    # $compressed = strlen($compressed);
    $compressed_length = strlen($compressed);
    # $savings = ($raw - $compressed) / $raw * 100;
    $savings = ($raw_length - $compressed_length) / $raw_length * 100;
    $savings = round($savings, 2);
    # return '<!-- HTML Minify | https://www.demo-seite.com/ | Gr&#246;&#223;e reduziert um ' . $savings . '% | Von ' . $raw . ' Bytes, auf ' . $compressed . ' Bytes -->';
    return '<!-- HTML Minify | https://www.demo-seite.com/ | Größe reduziert um ' . $savings . '% | Von ' . $raw_length . ' Bytes, auf ' . $compressed_length . ' Bytes -->';
  }

  // Minifiziert HTML: Entfernt unnötige Leerzeichen, Kommentare und optimiert Tags
  # protected function minifyHTML( $html )
  protected function minifyHTML( string $html ): string
  {
    $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
    preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

    $overriding = false;
    $raw_tag = false;
    $html = '';

    foreach ($matches as $token)
    {
      # $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;
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
      # $strip = isset($strip) ? $strip : true;
      $strip = $strip ?? true;
      if ($strip)
      {
        $content = $this->removeWhiteSpace($content);
      }
      $html .= $content;
    }
    return $html;
  }

  // Hauptfunktion zum Minifizieren des HTML-Codes
  # public function parseHTML( $html )
  public function parseHTML( string $html ): void
  {
    $this->html = $this->minifyHTML($html);
    if ($this->info_comment)
    {
      $this->html .= "\n" . $this->bottomComment($html, $this->html);
    }
  }

  // Entfernt unnötige Leerzeichen aus einem String
  # protected function removeWhiteSpace( $str )
  protected function removeWhiteSpace( string $str ): string
  {
    // Ersetzt alle Vorkommen von Tabulatoren, Zeilenumbrüchen und Wagenrückläufen mit einem einzelnen Leerzeichen
    # $str = str_replace("\t", ' ', $str);
    # $str = str_replace("\n", '', $str);
    # $str = str_replace("\r", '', $str);
    $str = str_replace([
        "\t",
        "\n",
        "\r"
    ], ' ', $str);
    # while (stristr($str, ' '))
    # {
    # $str = str_replace(' ', ' ', $str);
    # }
    # return $str;
    return preg_replace('/\s+/', ' ', $str); // Ersetzt alle aufeinanderfolgenden Leerzeichen durch ein einzelnes
  }
}

// Funktion, die ein Minify-Objekt erstellt und den HTML-String minifiziert
# function minify_finish( $html )
function minify_finish( string $html ): Minify
{
  return new Minify($html);
}

// Funktion, die das Output-Buffering startet und minifiziert
# function minify_start( )
function minify_start( ): void
{
  ob_start('minify_finish');
}
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
  * @LastModified: 2025-01-14 $Date$ $LastChangedDate: 2025-01-14 18:01:24 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$ $Revision$ - Description
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
