<?php

/**
 * Mail class to send e-mails
 *
 * @author Mark Alexander Hoschek <alex at mylittlehomepage dot net>
 * @copyright 2010 Mark Alexander Hoschek
 * 
 * @version last 3.2015.04.02.18.42
 * @package phpSQLiteCMS
 * 
 * @modified:
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft (c) 2025 ztatement
 * @version 4.5.0.2025.01.20
 * @file $Id: cms/includes/classes/Mail.php 1 2025-01-20 20:11:06Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 */

class Mail {

  // Konstanten für Header-Trennzeichen und Transfer-Codierung
  # const MAIL_HEADER_SEPARATOR = "\n"; // "\r\n" complies with RFC 2822 but might cause problems in some cases (see http://php.net/manual/en/function.mail.php)
  const MAIL_HEADER_SEPARATOR = "\r\n"; // RFC 2822-konformer Header-Separator
  const MAIL_HEADER_TRANSFER_ENCODING = 'Q'; // 'B' for Base64 or 'Q' for Quoted-Printable
  
  private $charset = 'utf-8'; // Standardzeichensatz für E-Mail

 /**
  * Konstruktor: Legt die standardmäßige interne Zeichenkodierung fest.
  */
  public function __construct ()
  {
    // Setzt die interne Zeichencodierung für mbstring
    mb_internal_encoding($this->charset);
  }

 /**
  * Legt den Zeichensatz der E-Mail fest.
  *
  * @param string $charset Für die Kodierung zu verwendender Zeichensatz.
  */
  public function set_charset ($charset)
  {
    // Überprüft, ob der Zeichensatz gültig ist
    if (! in_array(strtolower($charset), mb_list_encodings()))
    {
      throw new \InvalidArgumentException(
        "Ungültiger Zeichensatz: $charset"
      );
    }

    $this->charset = $charset;
    mb_internal_encoding($this->charset);
  }

 /**
  * Validiert das Format einer E-Mail-Adresse.
  *
  * @param string $email Die zu validierende E-Mail-Adresse.
  * @return bool Wahr, wenn gültig, andernfalls falsch.
  */
  public function is_valid_email ($email)
  {
    # if(preg_match("/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)){
    # return true;
    # }
    # return false;
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

 /**
  * Escapet und codiert den Anzeigenamen für den E-Mail-Header.
  *
  * @param string $display_name Der zu kodierende Anzeigename.
  * @return string Codierter Anzeigename.
  */
  public function escape_display_name ($display_name)
  {
    // Escapet Anführungszeichen und kodiert den Header
    try
    {
      $display_name = str_replace('"', '\\"', $display_name);

      # if(preg_match("/(\.|\;|\")/", $display_name)){
      # return '"'.mb_encode_mimeheader($display_name, $this->charset, self::MAIL_HEADER_TRANSFER_ENCODING, self::MAIL_HEADER_SEPARATOR).'"';
      # }else{
      return mb_encode_mimeheader($display_name, $this->charset, self::MAIL_HEADER_TRANSFER_ENCODING, self::MAIL_HEADER_SEPARATOR);
    }
    catch (\Exception $e)
    {
      // Fehlerbehandlung falls das Encoding fehlschlägt
      throw new \RuntimeException(
        "Fehler beim Kodieren des Anzeigenamens: " . $e->getMessage()
      );
    }
  }

 /**
  * Kombiniert einen Anzeigenamen und eine E-Mail-Adresse.
  *
  * @param string $display_name Der Anzeigename.
  * @param string $email Die E-Mail-Adresse.
  * @return string Formatierte E-Mail-Adresse mit Anzeigenamen.
  */
  public function make_address ($display_name, $email)
  {
    return $this->escape_display_name($display_name) . ' <' . $email . '>';
  }

 /**
  * Filtert und bereinigt E-Mail-Headerwerte, um eine Einschleusung zu verhindern.
  *
  * @param string $string Der zu bereinigende Header-Wert.
  * @return string Bereinigter Header-Wert.
  */
  private function mail_header_filter ($string)
  {
    # return preg_replace("/(\015\012|\015|\012|content-transfer-encoding:|mime-version:|content-type:|subject:|to:|cc:|bcc:|from:|reply-to:)/ims", '', $string);
    # return preg_replace("/(\015\012|\015|\012|to:|cc:|bcc:|from:|reply-to:)/ims", '', $string);
    # return preg_replace("/(\015\012|\015|\012)/", '', $string);
    // Entfernt unerwünschte Zeilenumbrüche
    return preg_replace("/(\r\n|\r|\n)/", '', $string);
  }

 /**
  * Kodiert einen String im Quoted-Printable-Format.
  * Original written by Andy Prevost http://phpmailer.sourceforge.net and distributed under the Lesser General Public License (LGPL) http://www.gnu.org/copyleft/lesser.html
  *
  * @param string $input Der zu kodierende Eingabestring.
  * @param int $line_max Maximale Zeilenlänge.
  * @param bool $space_conv Gibt an, ob Leerzeichen explizit konvertiert werden sollen.
  * @return string Kodierter String.
  */
  private function my_quoted_printable_encode ($input, $line_max = 76, $space_conv = false)
  {
    try
    {
      // Kodierung in Quoted-Printable
      # $hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
      $hex = array_merge(range('0', '9'), range('A', 'F'));
      $lines = preg_split('/(?:\r\n|\r|\n)/', $input);
      # $eol = "\n";
      $eol = self::MAIL_HEADER_SEPARATOR;
      $escape = '=';
      $output = '';

      # while(list(, $line) = each($lines))
      foreach ($lines as $line)
      {
        $newline = '';
        $linlen = strlen($line);

        for ($i = 0; $i < $linlen; $i ++)
        {
          # $c = substr($line, $i, 1);
          $c = $line[$i];
          $dec = ord($c);

          // Spezielle Zeichen kodieren
          # if(($i == 0) && ($dec == 46)) // convert first point in the line into =2E
          if ($i === 0 && $dec === 46) // Convert leading "." in a line
          {
            $c = '=2E';
          }

          # if($dec == 32){
          # if($i==($linlen-1)) // convert space at eol only
          if ($dec === 32 && ($i === $linlen - 1 || $space_conv)) // Convert spaces at EOL
          {
            $c = '=20';
          } # elseif($space_conv){
          # $c = '=20';
          # }}elseif(($dec == 61) || ($dec < 32) || ($dec > 126)) // always encode "\t", which is *not* required
          elseif ($dec === 61 || $dec < 32 || $dec > 126) // Encode non-ASCII or special chars
          {
            $h2 = floor($dec / 16);
            # $h1 = floor($dec%16);
            $h1 = $dec % 16;
            $c = $escape . $hex[$h2] . $hex[$h1];
          }

          // Zeilenlänge überprüfen
          if ((strlen($newline) + strlen($c)) >= $line_max) // CRLF is not counted
          {
            $output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
            $newline = '';
          }
          # if($dec == 46) // check if newline first character will be point or not {
          # $c = '=2E';
          # }}
          $newline .= $c;
        }
        $output .= $newline . $eol;
      }
      return $output;
    }
    catch (\Exception $e)
    {
      // Fehlerbehandlung bei der Kodierung
      throw new \RuntimeException(
        "Fehler bei der Kodierung des Textes: " . $e->getMessage()
      );
    }
  }

 /**
  * Sendet eine E-Mail mit der mail()-Funktion von PHP.
  *
  * @param string $to E-Mail-Adresse des Empfängers.
  * @param string $from E-Mail-Adresse des Absenders.
  * @param string $subject Betreff der E-Mail.
  * @param string $message E-Mail-Text.
  * @param string $additional_parameters Optionale zusätzliche Parameter für mail().
  * @return bool True, wenn die E-Mail erfolgreich gesendet wurde, andernfalls false.
  */
  public function send ($to, $from, $subject, $message, $additional_parameters = '')
  {
    try
    {
      // Filtern und Bereinigen von Eingaben
      $to = $this->mail_header_filter($to);
      $subject = mb_encode_mimeheader($this->mail_header_filter($subject), $this->charset, self::MAIL_HEADER_TRANSFER_ENCODING, self::MAIL_HEADER_SEPARATOR);
      $message = $this->my_quoted_printable_encode($message);

      // Kopfzeilen vorbereiten
      $headers = "From: " . $this->mail_header_filter($from) . self::MAIL_HEADER_SEPARATOR;
      $headers .= "MIME-Version: 1.0" . self::MAIL_HEADER_SEPARATOR;
      $headers .= "X-Sender-IP: " . $_SERVER["REMOTE_ADDR"] . self::MAIL_HEADER_SEPARATOR;
      # $headers .= "X-Mailer: " . BASE_URL . self::MAIL_HEADER_SEPARATOR;
      $headers .= "Content-Type: text/plain; charset=" . $this->charset . self::MAIL_HEADER_SEPARATOR;
      $headers .= "Content-Transfer-Encoding: quoted-printable";

      # if ($additional_parameters){
      # if (@mail($to, $subject, $message, $headers, $additional_parameters)){
      # return true;
      # }else{
      # return false;
      # }}else{
      # if (@mail($to, $subject, $message, $headers)){
      # return true;
      # }else{
      # return false;
      # }}
      // Sendet die E-Mail und gibt das Ergebnis zurück
      return mail($to, $subject, $message, $headers, $additional_parameters);
    }
    catch (\Exception $e)
    {
      // Fehlerbehandlung beim Senden der E-Mail
      throw new \RuntimeException(
        "Fehler beim Senden der E-Mail: " . $e->getMessage()
      );
    }
  }
}

/**
 * Änderung: update: PHP 8.4+/9 Kompatibilität
 * Die Validierung der E-Mail-Adresse nutzt nun filter_var() anstelle von preg_match()
 * Verbesserte Lesbarkeit und korrekte EOL-Verwendung
 * Nur Zeilenumbrüche werden entfernt, um Header-Injection zu verhindern
 * An mehreren Stellen im Code try-catch-Fehlerbehandlung eingeführt,
 * z. B. in der Methode escape_display_name(), my_quoted_printable_encode() und send().
 * In der Methode set_charset() wird jetzt geprüft, ob der übergebene Zeichensatz gültig ist, 
 * indem er mit der Funktion mb_list_encodings() verglichen wird. 
 * Falls der Zeichensatz ungültig ist, wird nun eine InvalidArgumentException geworfen.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-20 $Date$ $LastChangedDate: 2025-01-20 20:11:06 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-20 : 4.5.0.2025.01.20 - added: Fehlerbehandlung mit try-catch
 * 2025-01-17 : 4.5.0.2025.01.17 - @fix filter_var() anstelle von preg_match()
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
