<?php

/**
 * Klasse zur Berechtigungsprüfung und zum Schutz vor unerwünschten Zugriffen.
 *
 * Diese Klasse enthält Funktionen zur Überprüfung von Berechtigungen für die Bearbeitung
 * von Inhalten, zur Erkennung von blockierten IPs und User-Agents sowie zur Identifizierung
 * von unzulässigen Wörtern in Texten.
 *
 * @author Thomas Boettcher
 * @copyleft (c) 2025 ztatement
 * @version 4.6.0.2025.02.17
 * @file $Id: cms/includes/classes/Permission.php 1 Mon Feb 17 2025 12:21:22 GMT+0100Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 */

class Permissions
{

/**
  * Überprüft, ob die aktuelle Session gültig ist.
  *
  * Diese Funktion prüft, ob eine Benutzer-ID in der Session gesetzt ist
  * und ob der Benutzertyp gültig ist. Sie berücksichtigt auch die
  * Einstellung für die Zugriffsberechtigungsprüfung.
  *
  * @param array $settings  Die Anwendungseinstellungen.
  * @return bool  True, wenn die Session gültig ist, sonst false.
  */
  public static function isValidSession(array $settings): bool
  {
    $sessionPrefix = $settings['session_prefix'];

    // Prüfen, ob die Zugriffsberechtigungsprüfung aktiviert ist
    if ($settings['check_access_permission'] == 1)
    {
      // Prüfen, ob eine Benutzer-ID in der Session gesetzt ist
      if (!isset($_SESSION[$sessionPrefix . 'user_id']))
      {
        return false;
      }
    }

    // Prüfen, ob ein gültiger Benutzertyp gesetzt ist
    // user_type 1=admin, 0=user, 9=readonly
    if (!isset($_SESSION[$sessionPrefix . 'user_type']) || 
        !in_array($_SESSION[$sessionPrefix . 'user_type'], ['1', '0', '9']))
    {
      return false;
    }
    // Wenn alle Prüfungen bestanden wurden, ist die Session gültig
    return true;

  }


/**
  * Überprüft, ob der Benutzer zur Bearbeitung der Seite berechtigt ist.
  *
  * Die Funktion prüft, ob der aktuelle Benutzer die Berechtigung hat, die Seite zu bearbeiten.
  * Es werden verschiedene Kriterien berücksichtigt:
  * - Ist der Benutzer der Autor der Seite?
  * - Hat der Benutzer allgemeine Bearbeitungsrechte?
  * - Ist der Benutzer in der Liste der autorisierten Benutzer enthalten?
  *
  * @param int $author  Die Benutzer-ID des Autors der Seite.
  * @param int $editor  Die Benutzer-ID des Benutzers, der die Seite bearbeiten möchte.
  * @param int $edit_permission_general  Ein Flag, das angibt, ob allgemeine Bearbeitungsrechte bestehen.
  * @param string|null $edit_permission  Eine kommagetrennte Liste von Benutzer-IDs, die zur Bearbeitung berechtigt sind.
  *
  * @return bool  True, wenn der Benutzer zur Bearbeitung berechtigt ist, andernfalls false.
  */
  public static function is_authorized_to_edit(int $author, int $editor, int $edit_permission_general, ?string $edit_permission): bool
  {
    // Wenn keine spezifischen Berechtigungen festgelegt sind
    if (empty($edit_permission))
    {
      // Berechtigung erteilen, wenn der Autor mit dem Editor übereinstimmt oder allgemeine Bearbeitungsrechte bestehen
      return $author === $editor || $edit_permission_general === 1;
    }

    // Wenn spezifische Berechtigungen festgelegt sind
    // Autorisierte Benutzer aus der kommagetrennten Liste erstellen
    $cleared_authorized_users = array_map('intval', explode(',', $edit_permission));

    // Berechtigung erteilen, wenn der Autor mit dem Editor übereinstimmt, allgemeine Bearbeitungsrechte bestehen
    // oder der Editor in der Liste der autorisierten Benutzer enthalten ist
    return $author === $editor || $edit_permission_general === 1 || in_array($editor, $cleared_authorized_users, true);

  }


/**
  * Überprüft, ob der Zugriff verweigert werden soll (IP- und User-Agent-Sperren).
  *
  * Diese Funktion prüft, ob die IP-Adresse oder der User-Agent des aktuellen Benutzers
  * auf einer der Blacklists stehen. Wenn ja, wird der Zugriff verweigert.
  *
  * @return bool  True, wenn der Zugriff verweigert werden soll, andernfalls false.
  */
  public static function is_access_denied(): bool
  {
    // Abrufen der Blacklists aus der Datenbank
    $stmt = Database::$content->query("
      SELECT
        name, list
      FROM " . Database::$db_settings['banlists_table'] . "
      WHERE name='ips'
      OR name='user_agents'
    ");

    $ips = null;
    $user_agents = null;

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      if ($data['name'] === 'ips')
      {
        $ips = $data['list'];
      }

      if ($data['name'] === 'user_agents')
      {
        $user_agents = $data['list'];
      }
    }

    // Überprüfen, ob die IP-Adresse gesperrt ist
    if (isset($ips) && trim($ips) !== '')
    {
      $banned_ips = explode("\n", $ips);

      if (self::is_ip_banned($_SERVER['REMOTE_ADDR'], $banned_ips))
      {
        return true;
      }
    }

    // Überprüfen, ob der User-Agent gesperrt ist
    if (isset($user_agents) && trim($user_agents) !== '')
    {
      $banned_user_agents = explode("\n", $user_agents);

      if (self::is_user_agent_banned($_SERVER['HTTP_USER_AGENT'], $banned_user_agents))
      {
        return true;
      }
    }
    return false;

  }


/**
  * Überprüft, ob die IP-Adresse des Benutzers gesperrt ist.
  *
  * Die Funktion durchläuft die Liste der gesperrten IP-Adressen und prüft, ob die
  * IP-Adresse des Benutzers mit einer der gesperrten IP-Adressen übereinstimmt.
  * Es werden sowohl exakte Übereinstimmungen als auch IP-Bereiche (mit *) und CIDR-Notation unterstützt.
  *
  * @param string $ip  Die IP-Adresse des Benutzers.
  * @param array $banned_ips  Ein Array mit den gesperrten IP-Adressen.
  *
  * @return bool  True, wenn die IP-Adresse gesperrt ist, andernfalls false.
  */
  public static function is_ip_banned(string $ip, array $banned_ips): bool
  {
    foreach ($banned_ips as $banned_ip)
    {
      $banned_ip = trim($banned_ip);

      if (strpos($banned_ip, '*') !== false)
      {
        // IP-Bereich mit Sternchen
        $ip_range = substr($banned_ip, 0, strpos($banned_ip, '*'));

        if (strpos($ip, $ip_range) === 0)
        {
          return true;
        }
      }
      elseif (strpos($banned_ip, '/') !== false && preg_match("/(([0-9]{1,3}\.){3}[0-9]{1,3})\/([0-9]{1,2})/", $banned_ip, $regs))
      {
        // CIDR-Notation
        // Convert IP into bit pattern:
        $n_user_leiste = '00000000000000000000000000000000'; // 32 bits
        $n_user_ip = explode('.', trim($ip));

        for ($i = 0; $i <= 3; $i++) // go through every byte
        {
          for ($n_j = 0; $n_j < 8; $n_j++) // ... check every bit
          {
            if ($n_user_ip[$i] >= pow(2, 7 - $n_j)) // set to 1 if necessary
            {
              $n_user_ip[$i] -= pow(2, 7 - $n_j);
              $n_user_leiste[$n_j + $i * 8] = '1';
            }
          }
        }

        // analyze prefix length:
        $n_byte_array = explode('.', trim($regs[1])); // IP -> 4 Byte
        $n_cidr_bereich = (int) $regs[3]; // prefix length

        // bit pattern:
        $n_bitleiste = '00000000000000000000000000000000';

        for ($i = 0; $i <= 3; $i++) // go through every byte
        {
          if ($n_byte_array[$i] > 255) // invalid
          {
            $n_cidr_bereich = 0;
            break;
          }

          for ($n_j = 0; $n_j < 8; $n_j++) // ... check every bit
          {
            if ($n_byte_array[$i] >= pow(2, 7 - $n_j)) // set to 1 if necessary
            {
              $n_byte_array[$i] -= pow(2, 7 - $n_j);
              $n_bitleiste[$n_j + $i * 8] = '1';
            }
          }
        }

        // check if bit patterns match on the first n characters:
        if (strncmp($n_bitleiste, $n_user_leiste, $n_cidr_bereich) === 0 && $n_cidr_bereich > 0)
        {
          return true;
        }
      }
      else
      {
        // simple comparison:
        if ($ip === $banned_ip)
        {
          return true;
        }
      }
    }
    return false;

  }


/**
  * Überprüft, ob der User-Agent des Benutzers gesperrt ist.
  *
  * Die Funktion durchläuft die Liste der gesperrten User-Agents und prüft, ob der
  * User-Agent des Benutzers mit einem der gesperrten User-Agents übereinstimmt.
  *
  * @param string $user_agent  Der User-Agent des Benutzers.
  * @param array $banned_user_agents  Ein Array mit den gesperrten User-Agents.
  *
  * @return bool  True, wenn der User-Agent gesperrt ist, andernfalls false.
  */
  public static function is_user_agent_banned(string $user_agent, array $banned_user_agents): bool
  {
    foreach ($banned_user_agents as $banned_user_agent)
    {
      if (strpos($user_agent, $banned_user_agent) !== false)
      {
        return true;
      }
    }
    return false;

  }


/**
  * Sucht nach unzulässigen Wörtern in einem String.
  *
  * Die Funktion durchsucht einen String nach unzulässigen Wörtern, die in der
  * Datenbank gespeichert sind.
  *
  * @param string $string  Der zu durchsuchende String.
  *
  * @return array|false  Ein Array mit den gefundenen unzulässigen Wörtern oder false, wenn keine gefunden wurden.
  */
  public static function get_not_accepted_words(string $string): array|false
  {
    // Abrufen der Liste der unzulässigen Wörter aus der Datenbank
    $stmt = Database::$content->query("
      SELECT
        list
      FROM " . Database::$db_settings['banlists_table'] . "
      WHERE name = 'words'
      LIMIT 1
    ");

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($data['list']) && trim($data['list']) !== '')
    {
      $not_accepted_words = explode("\n", $data['list']);
      $found_not_accepted_words = [];

      foreach ($not_accepted_words as $not_accepted_word)
      {
        if ($not_accepted_word !== '' && mb_strpos($string, mb_strtolower($not_accepted_word, CHARSET), 0, CHARSET) !== false)
        {
          $found_not_accepted_words[] = $not_accepted_word;
        }
      }

      if (! empty($found_not_accepted_words))
      {
        return $found_not_accepted_words;
      }
    }
    return false;

  }


/**
  * Überprüft einen String auf zu lange Wörter.
  *
  * Die Funktion überprüft, ob ein String Wörter enthält, die länger sind als die
  * maximal zulässige Wortlänge.
  *
  * @param string $text  Der zu überprüfende Text.
  * @param int $word_maxlength  Die maximal zulässige Wortlänge.
  *
  * @return array|false  Ein Array mit den zu langen Wörtern oder false, wenn keine gefunden wurden.
  */
  public static function too_long_words(string $text, int $word_maxlength): array|false
  {
    // Normalisieren der Zeilenumbrüche
    $text = preg_replace("/\015\012|\015|\012/", "\n", $text);
    $text = str_replace("\n", ' ', $text);

    $words = explode(' ', $text);
    $too_long_words = [];

    foreach ($words as $word)
    {
      $length = mb_strlen(trim($word), CHARSET);

      if ($length > $word_maxlength)
      {
        $too_long_words[] = $word;
      }
    }
    return ! empty($too_long_words) ? $too_long_words : false;

  }

}


/**
  * Änderung:
  * 
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-17 $
  * @Date $LastChangedDate: Mon Feb 17 2025 12:21:22 GMT+0100 $
  * @editor: $LastChangedBy: ztatement $
  * ----------------
  * @see change.log
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-17 : 4.5.0.2025.02.17    : ztatement        - @new neu angelegte Klasse Permission
  *                                                       Funktionen aus functions.inc.php (deleted) eingefügt und Überarbeitet.
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
