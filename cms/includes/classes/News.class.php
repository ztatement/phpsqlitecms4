<?php

class News
{

  # var $news = false;
  public int $total_pages;

  // Gesamtanzahl der Seiten für die Paginierung.
  public int $current_page = 1;

  // Aktuelle Seite, die angezeigt wird.
  public int $news_per_page;

  // Anzahl der Nachrichten, die pro Seite angezeigt werden sollen.
  public string $category = '';

  // Kategorie der anzuzeigenden Nachrichten.
  public string $category_urlencoded = '';

  // URL-kodierte Form der Kategorie.
  public bool $wfw = false;

  private int $id;

  // ID der Seite, von der die Nachrichten geladen werden.

  # private $pdo;
  # private $db_settings;
  private int $current_time;

  // Aktuelle Zeit.
  private Localization $_localization;

  public function __construct( int $id, int $news_per_page )
  {
    $this->id = $id;
    $this->news_per_page = $news_per_page;
    $this->current_time = time();
    $this->_localization = Localization::getInstance();

    # $category_identifier_length = strlen(CATEGORY_IDENTIFIER);
    # if (isset($_GET['get_1']) && substr($_GET['get_1'], 0, $category_identifier_length) == CATEGORY_IDENTIFIER)
    # {
    # $this->category = str_replace(AMPERSAND_REPLACEMENT, '&', substr($_GET['get_1'], $category_identifier_length));
    # $this->category_urlencoded = str_replace('%26', AMPERSAND_REPLACEMENT, urlencode($this->category));
    # }
    #
    # if (isset($_GET['get_2']))
    # $this->current_page = intval($_GET['get_2']);
    # else
    # $this->current_page = 1;
    # if ($this->current_page == 0)
    # $this->current_page = 1;
    $this->parseQueryParams();
  }

  private function parseQueryParams( ): void
  {
    $category_identifier_length = strlen(CATEGORY_IDENTIFIER);

    if (isset($_GET['get_1']) && str_starts_with($_GET['get_1'], CATEGORY_IDENTIFIER))
    {
      $this->category = str_replace(AMPERSAND_REPLACEMENT, '&', substr($_GET['get_1'], $category_identifier_length));
      $this->category_urlencoded = str_replace('%26', AMPERSAND_REPLACEMENT, urlencode($this->category));
    }

    $this->current_page = isset($_GET['get_2']) ? max(1, intval($_GET['get_2'])) : 1;
  }

  /*
   * Diese Methode ruft Nachrichten aus der Datenbank ab, basierend auf der aktuellen Seite, der Anzahl der Nachrichten pro Seite und der Kategorie (falls angegeben).
   * Führt eine SQL-Abfrage aus, um die Anzahl der Nachrichten zu zählen und eine zweite Abfrage, um die tatsächlichen Nachrichten abzurufen.
   * Die Nachrichten werden in einem Array gespeichert und zurückgegeben.
   */
  # public function get_news()
  public function get_news( ): array|false
  {
    /*
     * if ($this->category)
     * {
     * $dbr = Database::$content->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND category=:category AND time<=:time AND status!=0");
     * $dbr->bindParam(':category', $this->category, PDO::PARAM_STR);
     * }
     * else
     * {
     * $dbr = Database::$content->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND status!=0");
     * }
     * $dbr->bindParam(':include_page', $this->id, PDO::PARAM_INT);
     * $dbr->bindParam(':time', $this->current_time, PDO::PARAM_INT);
     * $dbr->execute();
     * $news_count = $dbr->fetchColumn();
     * if ($this->category && $news_count == 0)
     * {
     * header('Location: ' . BASE_URL . PAGE);
     * exit();
     * }
     * $this->total_pages = ceil($news_count / $this->news_per_page);
     * if ($this->current_page > $this->total_pages)
     * $this->current_page = $this->total_pages;
     * $this->_localization->replacePlaceholder('current_page', $this->current_page, 'pagination');
     * $this->_localization->replacePlaceholder('total_pages', $this->total_pages, 'pagination');
     * if ($this->category)
     * {
     * $dbr = Database::$content->prepare("SELECT id, page, title, page_title, category, type, time, teaser_headline, teaser, teaser_img, link_name, headline, content FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND category=:category AND status!=0 ORDER BY time DESC LIMIT " . (($this->current_page - 1) * $this->news_per_page) . ", " . $this->news_per_page);
     * $dbr->bindParam(':category', $this->category, PDO::PARAM_STR);
     * }
     * else
     * {
     * $dbr = Database::$content->prepare("SELECT id, page, title, page_title, category, type, time, teaser_headline, teaser, teaser_img, link_name, headline, content FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND status!=0 ORDER BY time DESC LIMIT " . (($this->current_page - 1) * $this->news_per_page) . ", " . $this->news_per_page);
     * }
     * $dbr->bindParam(':include_page', $this->id, PDO::PARAM_INT);
     * $dbr->bindParam(':time', $this->current_time, PDO::PARAM_INT);
     * $dbr->execute();
     * $i = 0;
     * while ($news_data = $dbr->fetch())
     * {
     * if ($news_data['type'] == 'commentable_page')
     * {
     * $dbr2 = Database::$entries->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['comment_table'] . " WHERE type=0 AND comment_id=:comment_id");
     * $dbr2->bindParam(':comment_id', $news_data['id'], PDO::PARAM_INT);
     * $dbr2->execute();
     * # $comment_count = $dbr2->fetchColumn();
     * $news[$i]['comments'] = $dbr2->fetchColumn();
     * # $this->lang_replacements[$news_data['id']]['comments'] = $news[$i]['comments'];
     * $this->_localization->bindId('number_of_comments', $news_data['id']);
     * switch ($news[$i]['comments'])
     * {
     * case 0:
     * $this->_localization->selectBoundVariant('number_of_comments', $news_data['id'], 0);
     * break;
     * case 1:
     * $this->_localization->selectBoundVariant('number_of_comments', $news_data['id'], 1);
     * break;
     * default:
     * $this->_localization->selectBoundVariant('number_of_comments', $news_data['id'], 2);
     * $this->_localization->replacePlaceholderBound('comments', $news[$i]['comments'], 'number_of_comments', $news_data['id']);
     * }
     * }
     * $news[$i]['id'] = $news_data['id'];
     * $news[$i]['category'] = $news_data['category'];
     * $news[$i]['category_urlencoded'] = str_replace('%26', AMPERSAND_REPLACEMENT, urlencode($news_data['category'] ?? ''));
     * $news[$i]['title'] = $news_data['title'];
     * if ($news_data['teaser_headline'] != '')
     * {
     * $news[$i]['teaser_headline'] = $news_data['teaser_headline'];
     * }
     * elseif ($news_data['headline'] != '')
     * {
     * $news[$i]['teaser_headline'] = $news_data['headline'];
     * }
     * elseif ($news_data['title'] != '')
     * {
     * $news[$i]['teaser_headline'] = $news_data['title'];
     * }
     * elseif ($news_data['page_title'] != '')
     * {
     * $news[$i]['teaser_headline'] = $news_data['page_title'];
     * }
     * else
     * {
     * $news[$i]['teaser_headline'] = $news_data['page'];
     * }
     * if ($news_data['teaser'] != '')
     * {
     * $news[$i]['teaser'] = $news_data['teaser'];
     * $news[$i]['more'] = true;
     * }
     * else
     * {
     * # if($news_data['content_formatting']==1)
     * # {
     * # $news[$i]['teaser'] = auto_html($news_data['content']);
     * # }
     * # else
     * # {
     * $news[$i]['teaser'] = $news_data['content'];
     * # }
     * $news[$i]['teaser'] = parse_special_tags($news[$i]['teaser'], $news_data['page']);
     * $news[$i]['more'] = false;
     * }
     * # $this -> news[$i]['teaser'] = stripslashes($teaser);
     * $news[$i]['page'] = $news_data['page'];
     * $news[$i]['timestamp'] = $news_data['time'];
     * # $news[$i]['time'] = $news_data['time'];
     * # $news[$i]['formated_time'] = format_time(TIME_FORMAT_FULL, $news_data['time']);
     * # $this->lang_replacements[$news_data['id']]['time'] = $news_data['time'];
     * $this->_localization->bindReplacePlaceholder($news_data['id'], 'time', $news_data['time'], 'news_time', Localization::FORMAT_TIME);
     * # $loc->bind_id('news_time', $key);
     * # $loc->replace_placeholder_bound('time', $val['time'], 'news_time', $key, Localization::FORMAT_TIME);
     * if (trim($news_data['teaser_img'] != ''))
     * {
     * $news[$i]['teaser_img'] = $news_data['teaser_img'];
     * $teaser_img_info = getimagesize(BASE_PATH . MEDIA_DIR . $news_data['teaser_img']);
     * $news[$i]['teaser_img_width'] = $teaser_img_info[0];
     * $news[$i]['teaser_img_height'] = $teaser_img_info[1];
     * }
     * $news[$i]['link_name'] = stripslashes($news_data['link_name']);
     * $i++;
     * }
     * if (isset($news))
     * return $news;
     * return false;
     */
    $news_count = $this->fetchNewsCount();
    if ($news_count === 0 && $this->category)
    {
      header('Location: ' . BASE_URL . PAGE);
      exit();
    }

    $this->total_pages = ceil($news_count / $this->news_per_page);
    $this->current_page = min($this->current_page, $this->total_pages);

    $this->_localization->replacePlaceholder('current_page', $this->current_page, 'pagination');
    $this->_localization->replacePlaceholder('total_pages', $this->total_pages, 'pagination');

    return $this->fetchNews();
  }

  private function fetchNewsCount( ): int
  {
    $query = $this->category ? "SELECT COUNT(*) FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND category=:category AND time<=:time AND status!=0" : "SELECT COUNT(*) FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND status!=0";

    $dbr = Database::$content->prepare($query);
    $dbr->bindParam(':include_page', $this->id, PDO::PARAM_INT);
    $dbr->bindParam(':time', $this->current_time, PDO::PARAM_INT);

    if ($this->category)
    {
      $dbr->bindParam(':category', $this->category, PDO::PARAM_STR);
    }

    $dbr->execute();
    return ( int )$dbr->fetchColumn();
  }

  private function fetchNews( ): array|false
  {
    $query = $this->category ? "SELECT id, page, title, page_title, category, type, time, teaser_headline, teaser, teaser_img, link_name, headline, content FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND category=:category AND status!=0 ORDER BY time DESC LIMIT " . (($this->current_page - 1) * $this->news_per_page) . ", " . $this->news_per_page : "SELECT id, page, title, page_title, category, type, time, teaser_headline, teaser, teaser_img, link_name, headline, content FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND status!=0 ORDER BY time DESC LIMIT " . (($this->current_page - 1) * $this->news_per_page) . ", " . $this->news_per_page;

    $dbr = Database::$content->prepare($query);
    $dbr->bindParam(':include_page', $this->id, PDO::PARAM_INT);
    $dbr->bindParam(':time', $this->current_time, PDO::PARAM_INT);

    if ($this->category)
    {
      $dbr->bindParam(':category', $this->category, PDO::PARAM_STR);
    }

    $dbr->execute();
    $news = [];
    while ($news_data = $dbr->fetch())
    {
      $news[] = $this->processNewsData($news_data);
    }

    return $news ?: false;
  }

  private function processNewsData( array $news_data ): array
  {
    $teaser_img_info = null;
    if (isset($news_data['teaser_img']) && $news_data['teaser_img'])
    {
      $teaser_img_info = getimagesize(BASE_PATH . MEDIA_DIR . $news_data['teaser_img']);
    }

    return [
        'id' => $news_data['id'],
        'category' => $news_data['category'],
        // Nullprüfung sicherstellen oder ?? '' verwenden
        // Hier wird geprüft, ob $news_data['category'] null ist. Wenn ja, wird ein leerer String '' verwendet.
        'category_urlencoded' => str_replace('%26', AMPERSAND_REPLACEMENT, urlencode($news_data['category'] ?? '')),
        'title' => $news_data['title'],
        'teaser_headline' => $news_data['teaser_headline'] ?: $news_data['headline'] ?: $news_data['title'] ?: $news_data['page'],
        'teaser' => parse_special_tags($news_data['teaser'], $news_data['page']),
        'more' => $news_data['teaser'] !== '',
        'teaser_img' => $teaser_img_info ? $news_data['teaser_img'] : null,
        'teaser_img_width' => $teaser_img_info[0] ?? null,
        'teaser_img_height' => $teaser_img_info[1] ?? null,
        'page' => $news_data['page'],
        'timestamp' => $news_data['time'],
        'link_name' => stripslashes($news_data['link_name'] ?? '')
    ];
  }

  /*
   * Diese Methode generiert einen RSS-Feed mit einer maximalen Anzahl von Elementen.
   * Sie führt eine SQL-Abfrage durch, um die neuesten Nachrichten abzurufen und formatiert diese für den RSS-Feed.
   * Je nach Parameter fullfeed wird entweder der vollständige Inhalt oder nur eine Zusammenfassung (Teaser) zurückgegeben.
   * public function get_feed( $rss_maximum_items = 20, $fullfeed = false )
   * {
   * $dbr = Database::$content->prepare("SELECT id, page, type, category, title, teaser, teaser_img, headline, content, time, last_modified FROM " . Database::$db_settings['pages_table'] . " WHERE include_page=:include_page AND time<=:time AND status!=0 ORDER BY time DESC LIMIT " . $rss_maximum_items);
   * $dbr->bindParam(':include_page', $this->id, PDO::PARAM_INT);
   * $dbr->bindParam(':time', $this->current_time, PDO::PARAM_INT);
   * $dbr->execute();
   * $i = 0;
   * while ($rss_data = $dbr->fetch())
   * {
   * $rss_items[$i]['category'] = htmlspecialchars($rss_data['category']);
   * $rss_items[$i]['title'] = htmlspecialchars($rss_data['title']);
   * if ($rss_data['headline'] && $fullfeed || empty($rss_data['teaser_headline']))
   * $rss_items[$i]['title'] = htmlspecialchars($rss_data['headline']);
   * elseif ($rss_data['teaser_headline'])
   * $rss_items[$i]['title'] = htmlspecialchars($rss_data['teaser_headline']);
   * else
   * $rss_items[$i]['title'] = htmlspecialchars($rss_data['title']);
   * if ($fullfeed || $rss_data['teaser'] == '')
   * {
   * $rss_items[$i]['content'] = $rss_data['content'];
   * $rss_items[$i]['content'] = parse_special_tags($rss_items[$i]['content'], $parent_page = $rss_data['page'], $rss = true);
   * }
   * else
   * {
   * $rss_items[$i]['content'] = auto_html($rss_data['teaser']);
   * $rss_items[$i]['content'] = $rss_data['teaser'];
   * }
   * if (!$fullfeed && $rss_data['teaser_img'])
   * {
   * $rss_items[$i]['teaser_img'] = $rss_data['teaser_img'];
   * $teaser_img_info = getimagesize(BASE_PATH . MEDIA_DIR . $rss_data['teaser_img']);
   * $rss_items[$i]['teaser_img_width'] = $teaser_img_info[0];
   * $rss_items[$i]['teaser_img_height'] = $teaser_img_info[1];
   * }
   * $rss_items[$i]['link'] = BASE_URL . $rss_data['page'];
   * $rss_items[$i]['pubdate'] = gmdate('r', $rss_data['time']);
   * if ($rss_data['type'] == 'commentable_page')
   * {
   * $this->wfw = true;
   * $rss_items[$i]['commentrss'] = BASE_URL . $rss_data['page'] . ',commentrss';
   * }
   * $i++;
   * }
   * if (isset($rss_items))
   * return $rss_items;
   * return false;
   * }
   * }
   */
  public function get_feed( int $rss_maximum_items = 20, bool $fullfeed = false ): array|false
  {
    // Datenbankabfrage vorbereiten
    $query = "SELECT id, page, type, category, title, teaser, teaser_img, headline, content, time, last_modified
              FROM " . Database::$db_settings['pages_table'] . "
              WHERE include_page=:include_page AND time<=:time AND status!=0
              ORDER BY time DESC
              LIMIT :rss_limit";

    $dbr = Database::$content->prepare($query);
    $dbr->bindParam(':include_page', $this->id, PDO::PARAM_INT);
    $dbr->bindParam(':time', $this->current_time, PDO::PARAM_INT);
    $dbr->bindParam(':rss_limit', $rss_maximum_items, PDO::PARAM_INT);
    $dbr->execute();

    $rss_items = [];
    while ($rss_data = $dbr->fetch(PDO::FETCH_ASSOC))
    {
      $rss_items[] = $this->processFeedItem($rss_data, $fullfeed);
    }

    return $rss_items ?: false;
  }

  // Hinzugefügte Hilfsmethode: processFeedItem
  // Ich habe die Logik zur Verarbeitung eines einzelnen RSS-Elements in eine eigene Methode ausgelagert. Dies macht den Hauptcode klarer und einfacher zu warten.
  private function processFeedItem( array $rss_data, bool $fullfeed ): array
  {
    $rss_item = [
        'category' => htmlspecialchars($rss_data['category'] ?? ''),
        'title' => htmlspecialchars($rss_data['teaser_headline'] ?? ($rss_data['headline'] && $fullfeed) ?? $rss_data['title']),
        'link' => BASE_URL . $rss_data['page'],
        'pubdate' => gmdate('r', $rss_data['time'])
    ];

    if ($fullfeed || empty($rss_data['teaser_headline']))
    {
      $rss_item['content'] = parse_special_tags($rss_data['content'], $rss_data['page'], true);
    }
    else
    {
      $rss_item['content'] = auto_html($rss_data['teaser'] ?? '');
    }

    if (!$fullfeed && !empty($rss_data['teaser_img']))
    {
      $rss_item['teaser_img'] = $rss_data['teaser_img'];
      $teaser_img_info = @getimagesize(BASE_PATH . MEDIA_DIR . $rss_data['teaser_img']);

      if ($teaser_img_info)
      {
        $rss_item['teaser_img_width'] = $teaser_img_info[0];
        $rss_item['teaser_img_height'] = $teaser_img_info[1];
      }
      else
      {
        $rss_item['teaser_img_width'] = null;
        $rss_item['teaser_img_height'] = null;
      }
    }

    if ($rss_data['type'] === 'commentable_page')
    {
      $this->wfw = true;
      $rss_item['commentrss'] = BASE_URL . $rss_data['page'] . ',commentrss';
    }

    return $rss_item;
  }
}
/*
    Was wurde geändert?
    Typisierung und Rückgabetypen:
    
    PHP 8 unterstützt Typen und Rückgabewerte. Die Rückgabetypen wurden entsprechend hinzugefügt (array|false).
    Code-Sicherheit & Validierung:
    
    Überprüfung auf existierende Indizes und ihre Inhalte (isset wurde vermieden, da ?? und Standardwerte verwendet werden).
    Vermeidung von Fehlern bei Bildgrößenabfragen mittels @getimagesize.
    Saubere Trennung der Logik:
    
    Die Methode processFeedItem kapselt die Datenverarbeitung für ein einzelnes RSS-Element ab.
    Fehlervermeidung bei Bildinformationen:
    
    Mit @getimagesize wird vermieden, dass Warnungen auftreten, wenn ein Bild nicht existiert oder kein gültiges Bild ist.
    Verbesserte Lesbarkeit und Wartbarkeit:
    
    Durch die Trennung der Verarbeitung in eine eigene Methode ist der Hauptblock sauber und leichter nachvollziehbar.
*/
/*
 * 
Typisierung hinzugefügt: Für Properties und Methodenparameter, um Code klar und sicher zu machen.
parseQueryParams extrahiert: Logik wurde modularisiert und vereinfacht.
fetchNewsCount und fetchNews separiert: Vermeidung von zu großen Methoden.
processNewsData: Einzelner Schritt zur Aufbereitung eines Datensatzes, was die Lesbarkeit verbessert.
Verwendung von Null-Sicherheits-Checks: Durch PHP 8 Features und präzisere Prüfungen.
Klare Methode für die Datenverarbeitung und Datenabfrage.
*/
