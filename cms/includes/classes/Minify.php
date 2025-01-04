<?php

/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 *         @copyleft 2016 ztatement
 * @version 4.4.1.2024.12.19 $Id: cms/includes/Minify.php 1 2016-07-18 16:41:09Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS-German
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
  protected bool $compress_css = true;

  protected bool $compress_js = true;

  protected bool $info_comment = false;

  protected bool $remove_comments = true;

  protected string $html = '';

  public function __construct( string $html )
  {
    if (!empty($html))
    {
      $this->parseHTML($html);
    }
  }

  public function __toString( )
  {
    return $this->html;
  }

  protected function bottomComment( $raw, $compressed )
  {
    $raw = strlen($raw);
    $compressed = strlen($compressed);
    $savings = ($raw - $compressed) / $raw * 100;
    $savings = round($savings, 2);
    return '<!-- HTML Minify | https://www.demo-seite.com/ | Gr&#246;&#223;e reduziert um ' . $savings . '% | Von ' . $raw . ' Bytes, auf ' . $compressed . ' Bytes -->';
  }

  protected function minifyHTML( $html )
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
      if (is_null($tag))
      {
        if (!empty($token['script']))
        {
          $strip = $this->compress_js;
        }
        else 
          if (!empty($token['style']))
          {
            $strip = $this->compress_css;
          }
          else 
            if ($content == '<!--html-compression no compression-->')
            {
              $overriding = !$overriding;
              continue;
            }
            else 
              if ($this->remove_comments)
              {
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
          $raw_tag = $tag;
        }
        else 
          if ($tag === '/pre' || $tag === '/textarea')
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
              $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);
              $content = str_replace(' />', '/>', $content);
            }
          }
      }
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

  public function parseHTML( $html )
  {
    $this->html = $this->minifyHTML($html);
    if ($this->info_comment)
    {
      $this->html .= "\n" . $this->bottomComment($html, $this->html);
    }
  }

  protected function removeWhiteSpace( $str )
  {
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
    return preg_replace('/\s+/', ' ', $str);
  }
}

function minify_finish( $html )
{
  return new Minify($html);
}

function minify_start( )
{
  ob_start('minify_finish');
}
ob_start('minify_finish'); //*ende html comression*/

 /*
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2024-12-19 $Date$ $LastChangedDate: 2024-12-19 15:20:18 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$ $Revision$ - Description
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
