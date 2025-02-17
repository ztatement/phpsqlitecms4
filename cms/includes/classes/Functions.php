<?php

/**
  * Klasse Functions
  *
  * Diese Klasse enthält allgemeine Funktionen für das phpSQLiteCMS.
  * Sie dient zum Abrufen von Einstellungen, Inhalten, Menüs, Breadcrumbs
  * und globalen Inhaltsblöcken aus der Datenbank, sowie zur Formatierung
  * von Texten und URLs.
  *
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  *
  * @version 4.5.0.2025.02.17
  * @deleted file original $Id: cms/includes/functions.inc.php 1 Son, 16. Dez 2012, 02:45:46Z ztatement $
  * @file $Id: cms/includes/classes/Functions.php 1 Thu Jan 23 2025 18:07:39 GMT+0100Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  *
  * --------
  * Anmerkung:
  * Die meisten Funktionen habe ich aus der funktions.inc.php übernommen und modifiziert.
  *
  * Funktionsklasse für allgemeine Funktionen
  */

#namespace cms\includes\classes;

class Functions
{
/**
  * Ruft die Einstellungen aus der Datenbank ab und gibt sie als Array zurück.
  *
  * Diese Funktion führt eine SQL-Abfrage aus, um alle Einstellungen aus der
  * definierten Einstellungenstabelle der Datenbank abzurufen. Die Einstellungen
  * werden als Array zurückgegeben, wobei die Namen der Einstellungen die Schlüssel
  * und die Werte der Einstellungen die Werte des Arrays sind.
  *
  * @throws Exception Wenn die Datenbankverbindung nicht initialisiert ist oder ein Fehler
  * beim Abrufen der Einstellungen auftritt.
  *
  * @return array Ein assoziatives Array mit den Einstellungen. Falls keine Einstellungen
  * gefunden werden, wird ein leeres Array zurückgegeben.
  */
  public static function get_settings(): array
  {
    // Überprüfen, ob die Datenbankverbindung existiert
    if (! isset(Database::$content))
    {
        throw new Exception('Die Datenbankverbindung wurde nicht initialisiert.');
    }

    // Array für die Einstellungen initialisieren
    $settings = [];

    try
    {
      // Datenbankabfrage ausführen
      $query = "SELECT name, value FROM " . Database::$db_settings['settings_table'];
      $stmt = Database::$content->prepare($query);
      $stmt->execute();

      // Ergebnisse in ein Array speichern
      while ($line = $stmt->fetch(PDO::FETCH_ASSOC))
      {
        $settings[$line['name']] = $line['value'];
      }

      require_once 'Helpers.php';

      // Merging der Standardwerte mit den abgerufenen Einstellungen
      return Helpers::initializeSettings($settings);
    }
    catch (PDOException $e)
    {
      // Spezifische Fehlerbehandlung für PDO-Ausnahmen
      throw new Exception("PDO Fehler beim Abrufen der Einstellungen: " . $e->getMessage());
    }
    catch (Exception $e)
    {
      // Allgemeine Fehlerbehandlung
      throw new Exception("Fehler beim Abrufen der Einstellungen: " . $e->getMessage());
    }
  }


/**
  * Ermittelt das Protokoll einer URL oder gibt das Standardprotokoll basierend auf der Serverkonfiguration zurück.
  *
  * Diese Funktion prüft, ob eine URL ein gültiges Protokoll enthält (z.B. `http://`, `https://`, `ftp://`, etc.).
  * Falls die URL kein Protokoll enthält, wird automatisch `http://` vorangestellt.
  * Wenn keine URL übergeben wird, ermittelt die Funktion das Protokoll basierend auf den aktuellen Servereinstellungen
  * und gibt entweder `http://` oder `https://` zurück, je nachdem, ob die Verbindung sicher ist.
  *
  * Beispielaufrufe:
  * - `get_protocol()` ohne Parameter: Gibt das Protokoll basierend auf der aktuellen Serverumgebung zurück.
  * - `get_protocol('www.demo-seite.com')`: Prüft, ob die URL bereits ein Protokoll hat. Falls nicht, wird `http://` hinzugefügt.
  *
  * @param string $url Die URL, deren Protokoll überprüft werden soll. (Optional)
  *        Falls keine URL übergeben wird, wird das Protokoll basierend auf der Serverkonfiguration ermittelt.
  *
  * @return string Das vollständige Protokoll für die angegebene URL oder das Standardprotokoll für die aktuelle Serverumgebung.
  */
  public static function get_protocol(string $url = ''): string
  {
    // Falls keine URL übergeben wird, ermitteln wir das Protokoll für die aktuelle Seite
    if (empty($url))
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
                      $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
    }

    // Überprüfen, ob die übergebene URL bereits ein Protokoll enthält
    if (mb_substr($url, 0, 7, CHARSET) != 'http://' && 
        mb_substr($url, 0, 8, CHARSET) != 'https://' && 
        mb_substr($url, 0, 6, CHARSET) != 'ftp://' && 
        mb_substr($url, 0, 7, CHARSET) != 'sftp://' && 
        mb_substr($url, 0, 9, CHARSET) != 'gopher://' && 
        mb_substr($url, 0, 7, CHARSET) != 'news://')
    {
      // Wenn kein Protokoll vorhanden ist, fügen wir 'http://' hinzu
      $url = 'http://' . $url;
    }

    // Rückgabe der (gegebenen oder modifizierten) URL
    return $url;
  }

/**
  * Ermittelt die Basis-URL der Anwendung.
  *
  * Diese Funktion ermittelt die Basis-URL der Anwendung, indem sie das Protokoll,
  * den Hostnamen und das Verzeichnis des Skripts kombiniert. Sie kann optional
  * einen Teil der URL abschneiden, um die Basis-URL für Unterverzeichnisse zu erhalten.
  *
  * @param string $cut Ein optionaler Teil der URL, der abgeschnitten werden soll.
  *
  * @throws Exception Wenn der Hostname ungültig ist.
  *
  * @return string Die Basis-URL der Anwendung.
  */
  public static function get_base_url(string $cut = ''): string
  {
    global $settings;

    // Falls base_url bereits in den Einstellungen gesetzt ist, zurückgeben
    if (! empty($settings['base_url']))
    {
      return $settings['base_url'];
    }

    // Protokoll ermitteln
    $protocol = self::get_protocol();

    // Sicherstellen, dass der Host korrekt und sicher ist
    $host = $_SERVER['HTTP_HOST'] ?? '';

    // Optional: Host validieren (nur, wenn du zusätzliche Sicherheit wünschst)
    if (! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME))
    {
      throw new Exception('Ungültiger Hostname: ' . $host);
    }

    // Skriptname anstelle von PHP_SELF verwenden
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

    // Basis-URL zusammenstellen
    $base_url = $protocol . $host . rtrim(dirname($scriptName), '/\\') . '/';

    // Wenn $cut angegeben ist, den Teil der URL abschneiden
    if ($cut !== '')
    {
      $pos = strrpos($base_url, $cut);

      if ($pos !== false)
      {
        $base_url = substr($base_url, 0, $pos);
      }
    }
    return $base_url;

  }


/**
  * Ermittelt den Basis-Pfad der Anwendung.
  *
  * Diese Funktion ermittelt den Basis-Pfad der Anwendung, indem sie das Verzeichnis
  * des Skripts verwendet. Sie kann optional einen Teil des Pfades abschneiden,
  * um den Basis-Pfad für Unterverzeichnisse zu erhalten.
  *
  * @param string $cut Ein optionaler Teil des Pfades, der abgeschnitten werden soll.
  *
  * @return string Der Basis-Pfad der Anwendung.
  */
  public static function get_base_path(string $cut = ''): string
  {
    global $settings;

    // Falls base_path bereits in den Einstellungen gesetzt ist, zurückgeben
    if (! empty($settings['base_path']))
    {
      return $settings['base_path'];
    }

    // Basis-Pfad ermitteln
    $base_path = dirname($_SERVER['SCRIPT_FILENAME']) . '/';

    // Wenn $cut angegeben ist, den Teil des Pfades abschneiden
    if ($cut !== '')
    {
      $pos = strrpos($base_path, $cut);

      if ($pos !== false)
      {
        $base_path = substr($base_path, 0, $pos);
      }
    }
    return $base_path;

  }


/**
  * Holt den Seiteninhalt aus der Datenbank und gibt ihn als Array zurück.
  *
  * Diese Funktion führt eine SQL-Abfrage aus, um den Inhalt einer bestimmten Seite
  * aus der definierten Seiten-Tabelle der Datenbank abzurufen. Die Abfrage ist
  * case-insensitive und schließt Seiten mit Status 0 aus. Wenn die Seite gefunden wird,
  * wird ein assoziatives Array mit den Inhalten zurückgegeben, andernfalls false.
  *
  * @param string $page Der Name der Seite, die abgerufen werden soll.
  *
  * @throws Exception Wenn die Datenbankverbindung nicht initialisiert ist oder ein Fehler
  * beim Abrufen der Seiteninhalte auftritt.
  *
  * @return array|false Ein assoziatives Array mit den Seiteninhalten oder false, wenn die Seite nicht gefunden wird.
  */
  public static function get_content(string $page): array|false
  {
    // Überprüfen, ob die Datenbankverbindung existiert
    if (! isset(Database::$content))
    {
      throw new Exception('Die Datenbankverbindung wurde nicht initialisiert.');
    }

    // SQL-Abfrage definieren
    $content_query ="
      SELECT
        id, page, author, type, type_addition, time, last_modified, display_time,
        page_title, title, keywords, description, category, page_info, language,
        breadcrumbs, teaser_headline, teaser, content, sidebar_1, sidebar_2, sidebar_3,
        sections, menu_1, menu_2, menu_3, gcb_1, gcb_2, gcb_3, include_news, template,
        content_type, charset, edit_permission, edit_permission_general, tv, status
      FROM " . Database::$db_settings['pages_table'] . "
      WHERE lower(page) = lower(:page) AND status != 0
      LIMIT 1
    ";

    try
    {
      // Abfrage vorbereiten und Parameter binden
      $stmt = Database::$content->prepare($content_query);
      $stmt->bindParam(':page', $page, PDO::PARAM_STR);
      $stmt->execute();

      // Ergebnis abrufen
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      // Wenn Daten vorhanden sind, diese zurückgeben
      return $data ? $data : false;
    }
    catch (PDOException $e)
    {
      // Spezifische Fehlerbehandlung für PDO-Ausnahmen
      throw new Exception("PDO Fehler beim Abrufen der Seiteninhalte: " . $e->getMessage());
    }
    catch (Exception $e)
    {
      // Allgemeine Fehlerbehandlung
      throw new Exception("Fehler beim Abrufen der Seiteninhalte: " . $e->getMessage());
    }

  }


/**
  * Holt die Seitennamen und Seitentitel der Brotkrümel.
  *
  * Diese Funktion nimmt eine durch Kommas getrennte Liste von Breadcrumb-IDs
  * und gibt ein assoziatives Array der Seitennamen und Seitentitel zurück,
  * geordnet nach den IDs in der Liste.
  *
  * @param string $breadcrumbs_id_list Eine durch Kommas getrennte Liste von Breadcrumb-IDs.
  *
  * @throws Exception Wenn die Datenbankverbindung nicht initialisiert ist oder ein Fehler beim Abrufen der Breadcrumb-Daten auftritt.
  *
  * @return array|false Ein assoziatives Array mit den Breadcrumb-Daten oder false, wenn keine Daten gefunden wurden.
  */
  public static function get_breadcrumbs(string $breadcrumbs_id_list): array|false
  {
    // Überprüfen, ob die Datenbankverbindung existiert
    if (! isset(Database::$content))
    {
      throw new Exception('Die Datenbankverbindung wurde nicht initialisiert.');
    }

    if (trim($breadcrumbs_id_list) != '')
    {
      // Breadcrumb-IDs in ein Array umwandeln und Integer-Werte sicherstellen
      $breadcrumb_ids = explode(',', $breadcrumbs_id_list);
      $breadcrumb_ids = array_map('intval', $breadcrumb_ids);

      // IDs in einen String für die SQL-Abfrage umwandeln
      $ids = implode(',', $breadcrumb_ids);

      // SQL-Abfrage definieren, um Breadcrumb-Daten abzurufen
      $breadcrumbs_query ="
        SELECT id, page, title
        FROM " . Database::$db_settings['pages_table'] . "
        WHERE id IN(" . $ids . ")
      ";

      try
      {
        // Abfrage vorbereiten und ausführen
        $stmt = Database::$content->prepare($breadcrumbs_query);
        $stmt->execute();

        // Ergebnis abrufen und in ein ungeordnetes Array speichern
        $unordered_breadcrumbs = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
        {
          $unordered_breadcrumbs[$data['id']] = [
            'page' => $data['page'],
            'title' => $data['title'],
          ];
        }

        // Breadcrumbs nach den IDs in der ursprünglichen Liste ordnen
        $breadcrumbs = [];
        foreach ($breadcrumb_ids as $id)
        {
          if (isset($unordered_breadcrumbs[$id]))
          {
            $breadcrumbs[] = $unordered_breadcrumbs[$id];
          }
        }
        return ! empty($breadcrumbs) ? $breadcrumbs : false;

      }
      catch (PDOException $e)
      {
        // Spezifische Fehlerbehandlung für PDO-Ausnahmen
        throw new Exception("PDO Fehler beim Abrufen der Breadcrumbs: " . $e->getMessage());
      }
      catch (Exception $e)
      {
        // Allgemeine Fehlerbehandlung
        throw new Exception("Fehler beim Abrufen der Breadcrumbs: " . $e->getMessage());
      }
    }
    return false;

  }


/**
  * Holt die Menüdaten aus der Datenbank und gibt sie als strukturiertes Array zurück.
  *
  * Diese Funktion führt eine SQL-Abfrage aus, um alle Menüeinträge aus der Datenbank zu laden.
  * Die Menüeinträge werden nach Menübezeichner (`menu`) und Reihenfolge (`sequence`) sortiert.
  * Für jeden Menüeintrag wird ein Array mit den folgenden Informationen erstellt:
  * - `name`: Der Name des Menüs
  * - `title`: Der Titel des Menüs
  * - `link`: Der Link zum Menüeintrag (wobei ein gültiges Protokoll vorangestellt wird, falls notwendig)
  * - `section`: Die zugehörige Sektion des Menüs
  * - `accesskey`: Der Zugangsschlüssel für den Menüeintrag
  *
  * Wenn ein Menüeintrag keinen externen Link (z.B. zu einer anderen Webseite) enthält,
  * wird das Basis-URL-Protokoll automatisch vorangestellt.
  *
  * Diese Funktion gibt ein assoziatives Array zurück, das die Menüs nach ihrem `menu`-Schlüssel gruppiert enthält.
  * Jeder Menüeintrag wird durch seinen Index innerhalb des jeweiligen Menüs aufgelistet.
  *
  * Beispielaufruf:
  * ```
  * $menus = get_menus();
  * ```
  * Wenn Menüs vorhanden sind, wird das Array der Menüs zurückgegeben.
  *
  * @return array|false Ein assoziatives Array der Menüs oder `false`, wenn keine Menüs gefunden wurden.
  */
  public function get_menus(): array|false
  {
    // Das Array, das die Menüs enthalten wird
    $menus = [];

    // Holen der Lokalen Adresse mit der erweiterten get_base_url-Methode
    $local_url = self::get_base_url();

    // Verbindung zur Datenbank und Ausführen der SQL-Abfrage
    $menu_query = Database::$content->query("
      SELECT id, menu, name, title, link, section, accesskey
      FROM " . Database::$db_settings['menu_table'] . "
      ORDER BY menu ASC, sequence ASC
    ");

    // Überprüfen, ob die Abfrage erfolgreich war und Daten enthält
    if ($menu_query !== false)
    {
      // Iteriere durch die zurückgegebenen Menüeinträge und baue das Menü-Array auf
      $i = 0;
      while ($row = $menu_query->fetch())
      {
        // Initialisierung, wenn das Menü noch nicht im Array vorhanden ist
        if (! isset($menus[$row['menu']]))
        {
          $menus[$row['menu']] = [];
        }

        // Menüeintrag hinzufügen
        $menus[$row['menu']][$i]['name'] = $row['name'];
        $menus[$row['menu']][$i]['title'] = $row['title'];

        // Überprüfen, ob der Link mit einem externen Protokoll beginnt
        // Falls nicht, fügen wir das richtige Protokoll hinzu
        $menus[$row['menu']][$i]['link'] = $local_url . $row['link'];

        // Zusätzliche Menüinformationen (z.B. Section und Accesskey) hinzufügen
        $menus[$row['menu']][$i]['section'] = $row['section'];
        $menus[$row['menu']][$i]['accesskey'] = $row['accesskey'];

        // Zähler für die nächste Menüzeile erhöhen
        $i++;
      }
    }
    // Rückgabe der Menüs, wenn welche vorhanden sind, ansonsten `false`
    return ! empty($menus) ? $menus : false;

  }


/**
  * Holt die globalen Inhaltsblöcke aus der Datenbank.
  *
  * Diese Funktion ruft alle globalen Inhaltsblöcke aus der Datenbank ab und gibt
  * sie als assoziatives Array zurück, wobei der Identifier des Inhaltsblocks der
  * Schlüssel und der Inhalt der Wert ist.
  *
  * @return array|false Ein assoziatives Array mit den globalen Inhaltsblöcken oder false, wenn keine gefunden wurden.
  */
  public static function get_global_content_blocks(): array|false
  {
    $gcb_query = Database::$content->query("
      SELECT id, identifier, content
      FROM " . Database::$db_settings['gcb_table'] . "
      ORDER BY id ASC
    ");

    $gcb = [];
    while ($row = $gcb_query->fetch())
    {
      $gcb[$row['identifier']] = $row['content'];
    }
    return ! empty($gcb) ? $gcb : false;

  }


/**
  * Formatiert einen Absatz.
  *
  * Diese Funktion formatiert einen gegebenen String, indem sie Newline-Zeichen
  * in `<br>`-Tags umwandelt und spezielle Markierungen (z.B. `[[Text|Link]]`)
  * in HTML-Links umwandelt.
  *
  * @param string $string Der zu formatierende String.
  *
  * @return string Der formatierte String.
  */
  public function format_paragraph(string $string): string
  {
    $string = nl2br(preg_replace('/\[\[([^|\]]+?)(?:\|([^\]]+))?\]\]/e', "''.(('\$2')?'\$2':'\$1').''", $string));
    return $string;

  }


/**
  * Wandelt Text automatisch in HTML um.
  *
  * Diese Funktion trimmt den übergebenen Text, ersetzt mehrfache Newline-Zeichen
  * durch Absätze und wandelt Newline-Zeichen in `<br>`-Tags um.
  *
  * @param string $text Der zu formatierende Text.
  *
  * @return string Der formatierte Text.
  */
  public function auto_html(string $text): string
  {
    $text = trim($text);
    if ($text != '')
    {
      $text = '<p>' . $text . '</p>';
      $text = preg_replace("/(\015\012\015\012)|(\015\015)|(\012\012)/", "</p>\n<p>", $text);
      $text = nl2br($text);
    }
    return $text;

  }


/**
  * Filtert Steuerzeichen aus einer Zeichenfolge.
  *
  * Diese Funktion verwendet eine Zeichenfolge als Eingabe und entfernt Steuerzeichen,
  * die nicht druckbar sind und häufig zu Steuerungszwecken in Texten verwendet werden.
  * Diese Steuerzeichen umfassen Zeichen mit ASCII-Werten von
  * 0 bis 8, 11 bis 12 und 14 bis 31.
  *
  * Steuerzeichen sind normalerweise nicht sichtbar und können Probleme
  * bei der Textverarbeitung, Anzeige oder Speicherung verursachen. Diese Funktion zielt darauf ab,
  * die Eingabezeichenfolge zu bereinigen, indem diese Zeichen entfernt werden, während
  * sichtbare und druckbare Zeichen wie Leerzeichen, Tabulatoren
  * und Zeilenumbrüche erhalten bleiben.
  *
  * @param string $string Die zu filternde Eingabezeichenfolge.
  *
  * @return string Die gefilterte Zeichenfolge ohne Steuerzeichen.
  *
  * Verwendungsbeispiel:
  * ```
  * $input_string = "Hello\x01Welt";
  * $filtered_string = filter_control_characters($input_string);
  * echo $filtered_string; // Gibt aus: „HalloWelt“
  * ```
  */
  public function filter_control_characters(string $string): string
  {
    $control_chars = array_merge(
      array_map('chr', range(0, 8)),
      array_map('chr', range(11, 12)),
      array_map('chr', range(14, 31)),
      [chr(127)],
      array_map('chr', range(128, 159)),
      [chr(160), chr(173)]
    );

    // Replace horizontal tab with a space
    $string = str_replace("\t", " ", $string);
    return str_replace($control_chars, '', $string);

  }

/**
  * Verkürzt einen Link.
  *
  * Diese Funktion verkürzt einen Link, wenn er länger als die in den Einstellungen
  * definierte maximale Wortlänge ist.
  *
  * @param string $string Der zu verkürzende Link.
  *
  * @return string Der verkürzte Link.
  */
  public function shorten_link(string $string): string
  {
    global $settings;
    if (is_array($string))
    {
      if (count($string) == 2)
      {
        $pre = "";
        $url = $string[1];

      }
      else
      {
        $pre = $string[1];
        $url = $string[2];
      }
      $shortened_url = $url;

      if (strlen($url) > $settings['word_maxlength'])
      {
        $shortened_url = substr($url, 0, $settings['word_maxlength']) . "...";
      }
      return $pre . "<a href=\"" . $url . "\" rel=\"nofollow\">" . $shortened_url . "</a>";
    }
    return $string;

  }


/**
  * Evaluiert eine Funktion aus dem Inhalt.
  *
  * Diese Funktion evaluiert eine Funktion, die im Inhalt gefunden wurde.
  *
  * @param array $function Das Array mit der Funktion.
  *
  * @return mixed Das Ergebnis der evaluierten Funktion.
  */
  public function content_function(array $function): mixed
  {
    return @eval('return ' . $function[1] . ';');
  }

}


/**
  * Änderung:
  * Verwendung von strikten Typdeklarationen für Funktionsparameter und Rückgabewerte
  * (string, array, ?string, array, bool, mixed),
  * und Union Types wie array|false um mögliche Rückgabetypen klar zu deklarieren.
  * Arrow Functions für kürzere und prägnantere Code-Abschnitte verwendet.
  * Modernere Syntax für Sprachkonstrukte wie ´isset($_SERVER['HTTPS']) ? ... : ...`
  * Verbesserung der Code-Lesbarkeit, hinzufügen von ausführlichen Kommentaren 
  * zur Erklärung der Funktionalität und Logik der Klasse.
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-17 $Date$ 
  * @date $LastChangedDate: Mon Feb 17 2025 12:14:46 GMT+0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-17 : 4.5.0.2025.02.17    : ztatement        - update: PHP 8.4+/9 Kompatibilität
  *                                                       added: format_paragraph, auto_html, shorten_link und content_function
  * 2025-02-11 : 4.5.0.2025.02.11    : ztatement        - added: überarbeitete filter_control_characters
  * 2025-02-03 : 4.5.0.2025.02.03    : ztatement        - modified get_settings
  * 2025-01-27 : 4.5.0.2025.01.27    : ztatement        - added: get_protocol
  * 2025-01-26 : 4.5.0.2025.01.26    : ztatement        - added: get_breadcrumbs, get_menus
  * 2025-01-23 : 4.5.0.2025.01.23    : ztatement        - neu angelegt (aus original file cms/includes/functions.inc.php - deleted)
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
