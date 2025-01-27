<?php
/**
  * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * @version 4.5.0.2025.01.26
  * @file $Id: cms/includes/classes/Functions.php 1 2025-01-23 18:07:39Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  *
  * --------
  * Anmerkung:
  * Die meisten Funktionen habe ich aus der funktions.inc.php übernommen und modifiziert.
  *
  * Funktionsklasse für allgemeine Funktionen
  */

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
  * @throws Exception  Wenn die Datenbankverbindung nicht initialisiert ist oder ein Fehler
  *     beim Abrufen der Einstellungen auftritt.
  * 
  * @return array  Ein assoziatives Array mit den Einstellungen. Falls keine Einstellungen
  *     gefunden werden, wird ein leeres Array zurückgegeben.
  */
  public static function get_settings(): array
  {
    // Überprüfen, ob die Datenbankverbindung existiert
    if (!isset(Database::$content)) {
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

      // Falls keine Ergebnisse, leeres Array zurückgeben
      if (!$settings)
      {
        return [];
      }
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
    return $settings;
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
  * @param string $url  Die URL, deren Protokoll überprüft werden soll. (Optional)
  *                     Falls keine URL übergeben wird, wird das Protokoll basierend auf der Serverkonfiguration ermittelt.
  * @return string  Das vollständige Protokoll für die angegebene URL oder das Standardprotokoll für die aktuelle Serverumgebung.
  */
  public static function get_protocol(string $url = ''): string
  {
    // Falls keine URL übergeben wird, ermitteln wir das Protokoll für die aktuelle Seite
    if (empty($url))
    {
      return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
          || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') 
          ? 'https://' 
          : 'http://';
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

  #function get_base_url(string $cut = false): string
  public static function get_base_url(string $cut = ''): string
  {
    global $settings;

    // Falls base_url bereits in den Einstellungen gesetzt ist, zurückgeben
    if (!empty($settings['base_url']))
    {
        return $settings['base_url'];
    }

    // Protokoll ermitteln
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') 
        ? 'https://' 
        : 'http://';

    // Sicherstellen, dass der Host korrekt und sicher ist
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

    // Optional: Host validieren (nur, wenn du zusätzliche Sicherheit wünschst)
    if (!filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME))
    {
        throw new Exception('Ungültiger Hostname: ' . $host);
    }

    // Skriptname anstelle von PHP_SELF verwenden
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';

    // Basis-URL zusammenstellen
    $base_url = $protocol . $host . rtrim(dirname($scriptName), '/\\') . '/';

    // Wenn $cut angegeben ist, den Teil der URL abschneiden
    if ($cut !== '') {
        $pos = strrpos($base_url, $cut);
        if ($pos !== false) {
            $base_url = substr($base_url, 0, $pos);
        }
    }

    return $base_url;
  }


  public static function get_base_path(string $cut = ''): string
  {
    global $settings;

    // Falls base_path bereits in den Einstellungen gesetzt ist, zurückgeben
    if (!empty($settings['base_path']))
    {
        return $settings['base_path'];
    }

    // Basis-Pfad ermitteln
    $base_path = dirname($_SERVER['SCRIPT_FILENAME']) . '/';

    // Wenn $cut angegeben ist, den Teil des Pfades abschneiden
    if ($cut !== '') {
        $pos = strrpos($base_path, $cut);
        if ($pos !== false) {
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
  *         beim Abrufen der Seiteninhalte auftritt.
  * 
  * @return array|false Ein assoziatives Array mit den Seiteninhalten oder false, wenn die Seite nicht gefunden wird.
  */
  public static function get_content($page)
  {
    // Überprüfen, ob die Datenbankverbindung existiert
    if (!isset(Database::$content))
    {
      throw new Exception('Die Datenbankverbindung wurde nicht initialisiert.');
    }

    // SQL-Abfrage definieren
    $content_query = "
      SELECT 
        id, page, author, type, type_addition, time, last_modified, display_time, page_title, title, 
        keywords, description, category, page_info, language, breadcrumbs, teaser_headline, teaser, 
        content, sidebar_1, sidebar_2, sidebar_3, sections, menu_1, menu_2, menu_3, gcb_1, gcb_2, gcb_3, 
        include_news, template, content_type, charset, edit_permission, edit_permission_general, tv, status 
      FROM " . Database::$db_settings['pages_table'] . " 
      WHERE lower(page)=lower(:page) 
      AND status!=0 
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
      if (isset($data['id']))
      {
        return $data;
      } 
      else
      {
        return false; // Falls keine Daten gefunden wurden
      }
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
  * @param string $breadcrumbs_id_list  Eine durch Kommas getrennte Liste von Breadcrumb-IDs.
  * 
  * @throws Exception  Wenn die Datenbankverbindung nicht initialisiert ist oder ein Fehler beim Abrufen der Breadcrumb-Daten auftritt.
  * 
  * @return array|false  Ein assoziatives Array mit den Breadcrumb-Daten oder false, wenn keine Daten gefunden wurden.
  */
  public static function get_breadcrumbs($breadcrumbs_id_list)
  {
        // Überprüfen, ob die Datenbankverbindung existiert
        if (!isset(Database::$content)) {
            throw new Exception('Die Datenbankverbindung wurde nicht initialisiert.');
        }

        if (trim($breadcrumbs_id_list) != '') {
            // Breadcrumb-IDs in ein Array umwandeln und Integer-Werte sicherstellen
            $breadcrumb_ids = explode(',', $breadcrumbs_id_list);
            $breadcrumb_ids = array_map('intval', $breadcrumb_ids);

            // IDs in einen String für die SQL-Abfrage umwandeln
            $ids = implode(',', $breadcrumb_ids);

            // SQL-Abfrage definieren, um Breadcrumb-Daten abzurufen
            $breadcrumbs_query = "
                SELECT 
                    id, page, title 
                FROM " . Database::$db_settings['pages_table'] . " 
                WHERE id IN(" . $ids . ")
            ";

            try {
                // Abfrage vorbereiten und ausführen
                $stmt = Database::$content->prepare($breadcrumbs_query);
                $stmt->execute();

                // Ergebnis abrufen und in ein ungeordnetes Array speichern
                $unordered_breadcrumbs = [];
                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $unordered_breadcrumbs[$data['id']] = [
                        'page' => $data['page'],
                        'title' => $data['title']
                    ];
                }

                // Breadcrumbs nach den IDs in der ursprünglichen Liste ordnen
                $breadcrumbs = [];
                foreach ($breadcrumb_ids as $id) {
                    if (isset($unordered_breadcrumbs[$id])) {
                        $breadcrumbs[] = $unordered_breadcrumbs[$id];
                    }
                }

                if (!empty($breadcrumbs)) {
                    return $breadcrumbs;
                }
            } catch (PDOException $e) {
                // Spezifische Fehlerbehandlung für PDO-Ausnahmen
                throw new Exception("PDO Fehler beim Abrufen der Breadcrumbs: " . $e->getMessage());
            } catch (Exception $e) {
                // Allgemeine Fehlerbehandlung
                throw new Exception("Fehler beim Abrufen der Breadcrumbs: " . $e->getMessage());
            }
        }
        return false;
  }

/**
  * Holt die Menüs aus der Datenbank.
  * 
  * @return array|false Gibt ein assoziatives Array der Menüs zurück oder false, wenn keine Menüs gefunden wurden.
  */
  public function get_menus()
  {
    // Das Array, das die Menüs enthalten wird
    $menus = [];

    // Holen des Protokolls mit der erweiterten get_protocol-Methode
    $funktions = new Functions();
    $protocol = $funktions::get_protocol();  // Hier holen wir das Protokoll, falls URL nicht angegeben ist

    // Verbindung zur Datenbank und Ausführen der SQL-Abfrage
    $menu_query = Database::$content->query("
      SELECT 
        id, menu, name, title, link, section, accesskey 
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
        if (!isset($menus[$row['menu']]))
        {
          $menus[$row['menu']] = [];
        }

        // Menüeintrag hinzufügen
        $menus[$row['menu']][$i]['name'] = $row['name'];
        $menus[$row['menu']][$i]['title'] = $row['title'];

        // Überprüfen, ob der Link mit einem externen Protokoll beginnt
        // Falls nicht, fügen wir das richtige Protokoll hinzu
        $menus[$row['menu']][$i]['link'] = $protocol . ($row['link']);

        // Zusätzliche Menüinformationen (z.B. Section und Accesskey) hinzufügen
        $menus[$row['menu']][$i]['section'] = $row['section'];
        $menus[$row['menu']][$i]['accesskey'] = $row['accesskey'];

        // Zähler für die nächste Menüzeile erhöhen
        $i++;
      }
    }

    // Rückgabe der Menüs, wenn welche vorhanden sind, ansonsten `false`
    if (!empty($menus))
    {
      return $menus;
    }

    return false;
  }

}


/**
  * Änderung:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-01-27 $Date$ 
  * @date $LastChangedDate: 2025-01-27 10:27:13 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-01-27 : 4.5.0.2025.01.27    : ztatement        - added: get_protocol
  * 2025-01-26 : 4.5.0.2025.01.26    : ztatement        - added: get_breadcrumbs, get_menus
  * 2025-01-23 : 4.5.0.2025.01.23    : ztatement        - neu angelegt
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
