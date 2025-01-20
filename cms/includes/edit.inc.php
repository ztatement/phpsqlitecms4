<?php
/**
 * phpSQLiteCMS - ein einfaches und leichtes PHP Content-Management System auf Basis von PHP und SQLite
 *
 * @author Mark Hoschek < mail at mark-hoschek dot de >
 * @copyright (c) 2014 Mark Hoschek
 * @version last 3.2015.04.02.18.42
 * @link http://phpsqlitecms.net/
 * @package phpSQLiteCMS
 *
 * @author Thomas Boettcher <github[at]ztatement[dot]com>
 * @copyleft (c) 2025 ztatement
 * @version 4.5.0.2025.01.20
 * @file $Id: cms/includes/edit.inc.php 1 2025-01-20 07:13:10Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 *
 */
/*
 * edit.inc.php
 *
 * Dieses Skript dient der Bearbeitung von Seiteninhalten.
 * Es berücksichtigt Benutzerberechtigungen und ermöglicht es, Seiteninhalte
 * entweder anzulegen oder zu bearbeiten.
 */

// Sicherheitsabfrage: Direktzugriff verhindern
if (! defined('IN_INDEX')) {
  exit('Direkter Zugriff nicht erlaubt.');
}

// Prüfen, ob der Benutzer eingeloggt ist
if (isset($_SESSION[$settings['session_prefix'] . 'user_id'])) {
  // Die aktuelle Aktion festlegen, Standard: 'main'
  # $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'main';
  $action = $_REQUEST['action'] ?? 'main';

  // Überprüfen, ob der WYSIWYG-Editor aktiviert ist
  # if (isset($_SESSION[$settings['session_prefix'] . 'wysiwyg']) && $_SESSION[$settings['session_prefix'] . 'wysiwyg'] == 1)
  # $wysiwyg = true;
  $wysiwyg = isset($_SESSION[$settings['session_prefix'] . 'wysiwyg']) && $_SESSION[$settings['session_prefix'] . 'wysiwyg'] == 1;

  // WYSIWYG-Editor verwalten
  if (isset($_REQUEST['enable_wysiwyg']) || isset($_REQUEST['disable_wysiwyg'])) {
    $wysiwyg = isset($_REQUEST['enable_wysiwyg']) ? 1 : 0;

    $query = "UPDATE " . Database::$db_settings['userdata_table'] . "
              SET wysiwyg = :wysiwyg
              WHERE id = :id";

    $stmt = Database::$userdata->prepare($query);
    $stmt->bindParam(':wysiwyg', $wysiwyg, PDO::PARAM_INT);
    $stmt->bindParam(':id', $_SESSION[$settings['session_prefix'] . 'user_id'], PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION[$settings['session_prefix'] . 'wysiwyg'] = $wysiwyg;
  }

  // WYSIWYG-Status an das Template übergeben
  if (isset($wysiwyg)) {
    $template->assign('wysiwyg', true);
  }

  // Seitentypen aus der Konfiguration laden
  include (BASE_PATH . 'cms/config/page_types.conf.php');
  $template->assign('page_types', $page_types);

  // Benutzer laden
  $user_result = Database::$userdata->query("
      SELECT 
          id, name
      FROM " . Database::$db_settings['userdata_table'] . "
      ORDER BY id ASC
  ");

  # $i = 0;

  // Benutzerliste erstellen
  $users = [];
  while ($data = $user_result->fetch()) {
    # $users[$data['id']] = $data['name'];
    $users[(int) $data['id']] = htmlspecialchars($data['name']);
  }

  // Überprüfen, ob eine Seiten-ID zum bearbeiten übergeben wurde

  if (isset($_GET['id'])) {
    $query = "
      SELECT 
          id, page, author, type, type_addition, time, last_modified, display_time,
          title, page_title, description, keywords, category, page_info, breadcrumbs,
          sections, include_page, include_order, include_rss, include_sitemap, include_news,
          link_name, menu_1, menu_2, menu_3, gcb_1, gcb_2, gcb_3, template, language, content_type, charset,
          teaser_headline, teaser, teaser_img, content, sidebar_1, sidebar_2, sidebar_3, page_notes, 
          edit_permission, edit_permission_general, tv, status 
      FROM " . Database::$db_settings['pages_table'] . " 
      WHERE id = :id 
      LIMIT 1
    ";

    $stmt = Database::$content->prepare($query);
    $stmt->bindValue(':id', $_POST['id'] ?? '', PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetch();

    // Fehlerbehandlung für ungültige oder fehlende Seite
    # if (! isset($data['id'])) {
    if (! $data) {
      $action = 'page_doesnt_exist';
    }
    elseif (! is_authorized_to_edit(
      $_SESSION[$settings['session_prefix'] . 'user_id'],
      $_SESSION[$settings['session_prefix'] . 'user_type'],
      $data['author'],
      $data['edit_permission'],
      $data['edit_permission_general']
    )) {
      $action = 'no_authorization';
    }
    else {

      // Seite bearbeiten: Daten bereinigen und vorbereiten
      $page_data = prepare_page_data($data, $users);

      # $edit_permission_array = explode(',', $data['edit_permission']);
      # foreach ($edit_permission_array as $edit_permission) {
      # $edit_permission = intval(trim($edit_permission));
      # if (isset($users[$edit_permission])) {
      # $permitted_users[] = htmlspecialchars($users[$edit_permission] ?? '');
      # }
      # }
      # if (isset($permitted_users)) {
      # $page_data['edit_permission'] = implode(', ', $permitted_users);
      # } else {
      # $page_data['edit_permission'] = '';
      # }

      $send_pingbacks = 0;
      $action = 'main';
    }
  }
  else {
    $page_data = create_default_page_data($settings);
    $send_pingbacks = $settings['pingbacks_enabled'] ? 1 : 0;
  }
}

/**
 * Hilfsfunktion: Seitendaten bereinigen und vorbereiten
 * 
 * @param array $data
 * @param array $users
 * @return array
 */
function prepare_page_data (array $data, array $users): array
{
  $cleaned_data = [];
  $cleaned_data['id'] = (int) $data['id'];
  $cleaned_data['page'] = htmlspecialchars($data['page'] ?? '');
  $cleaned_data['author'] = intval($data['author']);
  $cleaned_data['type'] = htmlspecialchars($data['type'] ?? '');
  $cleaned_data['type_addition'] = htmlspecialchars($data['type_addition'] ?? '');
  $cleaned_data['time'] = date("Y-m-d H:i:s", $data['time']);
  $cleaned_data['last_modified'] = date("Y-m-d H:i:s");
  $cleaned_data['display_time'] = intval($data['display_time']);
  $cleaned_data['title'] = htmlspecialchars($data['title'] ?? '');
  $cleaned_data['page_title'] = htmlspecialchars($data['page_title'] ?? '');
  $cleaned_data['description'] = htmlspecialchars($data['description'] ?? '');
  $cleaned_data['keywords'] = htmlspecialchars($data['keywords'] ?? '');
  $cleaned_data['category'] = htmlspecialchars($data['category'] ?? '');
  $cleaned_data['page_info'] = htmlspecialchars($data['page_info'] ?? '');
  $cleaned_data['breadcrumbs'] = explode(',', htmlspecialchars($data['breadcrumbs'] ?? ''));
  $cleaned_data['sections'] = str_replace(',', ', ', htmlspecialchars($data['sections'] ?? ''));
  $cleaned_data['include_page'] = intval($data['include_page']);
  $cleaned_data['include_order'] = intval($data['include_order']);
  $cleaned_data['include_rss'] = intval($data['include_rss']);
  $cleaned_data['include_sitemap'] = intval($data['include_sitemap']);
  $cleaned_data['include_news'] = intval($data['include_news']);
  $cleaned_data['link_name'] = htmlspecialchars($data['link_name'] ?? '');
  $cleaned_data['menu_1'] = htmlspecialchars($data['menu_1'] ?? '');
  $cleaned_data['menu_2'] = htmlspecialchars($data['menu_2'] ?? '');
  $cleaned_data['menu_3'] = htmlspecialchars($data['menu_3'] ?? '');
  $cleaned_data['gcb_1'] = htmlspecialchars($data['gcb_1'] ?? '');
  $cleaned_data['gcb_2'] = htmlspecialchars($data['gcb_2'] ?? '');
  $cleaned_data['gcb_3'] = htmlspecialchars($data['gcb_3'] ?? '');
  $cleaned_data['template'] = htmlspecialchars($data['template'] ?? '');
  $cleaned_data['language'] = htmlspecialchars($data['language'] ?? '');
  $cleaned_data['content_type'] = htmlspecialchars($data['content_type'] ?? '');
  $cleaned_data['charset'] = htmlspecialchars($data['charset'] ?? '');
  $cleaned_data['teaser_headline'] = htmlspecialchars($data['teaser_headline'] ?? '');
  $cleaned_data['teaser'] = htmlspecialchars($data['teaser'] ?? '');
  $cleaned_data['teaser_img'] = htmlspecialchars($data['teaser_img'] ?? '');
  $cleaned_data['sidebar_1'] = htmlspecialchars($data['sidebar_1'] ?? '');
  $cleaned_data['sidebar_2'] = htmlspecialchars($data['sidebar_2'] ?? '');
  $cleaned_data['sidebar_3'] = htmlspecialchars($data['sidebar_3'] ?? '');
  $cleaned_data['page_notes'] = htmlspecialchars($data['page_notes']);
  $cleaned_data['edit_permission_general'] = intval($data['edit_permission_general']);
  $cleaned_data['tv'] = str_replace(',', ', ', htmlspecialchars($data['tv'] ?? ''));
  $cleaned_data['status'] = intval($data['status']);
  $cleaned_data['content'] = htmlspecialchars($data['content'] ?? '');
  // ... weitere Bereinigung analog ...
  return $cleaned_data;
}

/**
 *  Hilfsfunktion: Standardwerte für neue Seite erstellen
 *  
 * @param array $settings
 * @return array
 */
function create_default_page_data (array $settings): array
{
  // Standardwerte für eine neue Seite setzen
  return [
    'time' => date("Y-m-d H:i:s"),
    'last_modified' => date("Y-m-d H:i:s"),
    'display_time' => 0,
    'include_page' => 0,
    'include_order' => 0,
    'include_rss' => 0,
    'include_sitemap' => 0,
    'include_news' => 0,
    'link_name' => Localization::$lang['teaser_default_linkname'],
    'template' => $settings['default_template'],
    'menu_1' => $settings['default_menu'],
    'edit_permission_general' => 0,
    'status' => 2
    // ... weitere Standardwerte analog ...
  ];
}

// edit submitted
if (isset($_POST['content'])) {
  if (isset($_POST['id'])) {
    $dbr = Database::$content->prepare("
        SELECT 
          id, author, edit_permission, edit_permission_general 
        FROM " . Database::$db_settings['pages_table'] . " 
        WHERE id=:id 
        LIMIT 1
    ");
    $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $dbr->execute();
    $data = $dbr->fetch();

    if (! isset($data['id'])) {
      $errors[] = 'page_doesnt_exist';
    }
    elseif (! is_authorized_to_edit(
      $_SESSION[$settings['session_prefix'] . 'user_id'],
      $_SESSION[$settings['session_prefix'] . 'user_type'],
      $data['author'],
      $data['edit_permission'],
      $data['edit_permission_general']
    )) {
      $errors[] = 'no_authorization_edit';
    }
  }

  if (empty($errors)) {
    # $_POST['page'] = isset($_POST['page']) ? trim($_POST['page']) : '';
    # $_POST['title'] = isset($_POST['title']) ? trim($_POST['title']) : '';
    # $_POST['gcb_1'] = isset($_POST['gcb_1']) ? trim($_POST['gcb_1']) : '';
    # $_POST['gcb_2'] = isset($_POST['gcb_2']) ? trim($_POST['gcb_2']) : '';
    # $_POST['gcb_3'] = isset($_POST['gcb_3']) ? trim($_POST['gcb_3']) : '';
    $fields = [
      'page','title','gcb_1','gcb_2','gcb_3','content_type','charset'
    ];
    foreach ($fields as $field) {
      $_POST[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
    }
    # $_POST['include_page'] = isset($_POST['include_page']) ? intval($_POST['include_page']) : 0;
    # $_POST['include_rss'] = isset($_POST['include_rss']) ? intval($_POST['include_rss']) : 0;
    # $_POST['include_sitemap'] = isset($_POST['include_sitemap']) ? intval($_POST['include_sitemap']) : 0;
    # $_POST['include_news'] = isset($_POST['include_news']) ? intval($_POST['include_news']) : 0;
    # if (empty($_POST['rss']))
    # $_POST['rss'] = 0;
    # if (empty($_POST['sitemap']))
    # $_POST['sitemap'] = 0;
    # if (empty($_POST['content_type']))
    # $_POST['content_type'] = '';
    # if (empty($_POST['charset']))
    # $_POST['charset'] = '';
    # if (empty($_POST['edit_permission_general']))
    # $_POST['edit_permission_general'] = 0;
    # $_POST['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
    # $_POST['display_time'] = isset($_POST['display_time']) && $_POST['display_time'] == 1 ? 1 : 0;
    $flags = [
      'include_page','include_rss','include_sitemap','include_news','edit_permission_general','status','display_time'
    ];
    foreach ($flags as $flag) {
      $_POST[$flag] = isset($_POST[$flag]) ? intval($_POST[$flag]) : 0;
    }
    # if ($_POST['status'] > 2)
    # $_POST['status'] = 2;
    $_POST['status'] = min($_POST['status'], 2);
    # $send_pingbacks = isset($_POST['send_pingbacks']) && $_POST['send_pingbacks'] == 1 ? 1 : 0;
    $send_pingbacks = ! empty($_POST['send_pingbacks']) ? 1 : 0;

    // trim sections:
    # $sections_array = explode(',', $_POST['sections']);
    # foreach ($sections_array as $item) {
    # if (trim($item) != '') {
    # $cleared_sections_array[] = trim($item);
    # }
    # }
    # $_POST['sections'] = '';
    # if (isset($cleared_sections_array)) {
    # $cleared_sections_array_count = count($cleared_sections_array);
    # $i = 1;
    # foreach ($cleared_sections_array as $section) {
    # $_POST['sections'] .= $section;
    # if ($i < $cleared_sections_array_count)
    # $_POST['sections'] .= ',';
    # ++ $i;
    # }
    # }
    $_POST['sections'] = implode(",", array_filter(array_map('trim', explode(',', $_POST['sections'] ?? ''))));

    // trim custom values:
    # $tv_array = explode(',', $_POST['tv']);
    # foreach ($tv_array as $item) {
    # if (trim($item) != '') {
    # $cleared_tv_array[] = trim($item);
    # }
    # }
    # if (isset($cleared_tv_array))
    # $_POST['tv'] = implode(',', $cleared_tv_array);
    # else
    # $_POST['tv'] = '';
    $_POST['tv'] = implode(",", array_filter(array_map('trim', explode(',', $_POST['tv'] ?? ''))));

    // generate breadcrumb list
    if (isset($_POST['breadcrumbs']) && is_array($_POST['breadcrumbs'])) {
      # foreach ($_POST['breadcrumbs'] as $breadcrumb) {
      # if (! empty($breadcrumb))
      # $cleared_breadcrumbs[] = intval($breadcrumb);
      # }
      # if (isset($cleared_breadcrumbs)) {
      # $breadcrumb_list = implode(',', $cleared_breadcrumbs);
      # }
      # }
      # if (empty($breadcrumb_list)) {
      # $breadcrumb_list = '';
      # }
      $breadcrumb_list = implode(",", array_map('intval', array_filter($_POST['breadcrumbs'])));
    }
    else {
      $breadcrumb_list = '';
    }

    // generate edit permission list
    $edit_permission_list = '';
    # $users_array = explode(',', $_POST['edit_permission']);
    $users_array = explode(',', $_POST['edit_permission'] ?? '');
    $cleared_users_array = [];

    foreach ($users_array as $current_user) {
      $current_user = strtolower(trim($current_user));
      # if (trim($current_user) != '' && in_array(strtolower(trim($current_user)), $users)) {
      if ($current_user && in_array($current_user, $users)) {
        # $cleared_users_array[] = strtolower(trim($current_user));
        $cleared_users_array[] = $users[$current_user];
        # } else {
        # if (trim($current_user) != '') {
        # $invalid_username = true;
        # }

        # if (isset($cleared_users_array)) {
        # $cleared_users_array_count = count($cleared_users_array);
        # $users_trans = array_flip($users);
        # $i = 1;
        # foreach ($cleared_users_array as $current_user) {
        # $edit_permission_list .= $users_trans[$current_user];
        # if ($i < $cleared_users_array_count)
        # $edit_permission_list .= ',';
        # ++ $i;
        # }
        # }
        # if (isset($invalid_username)) {
      }
      elseif ($current_user) {
        $errors[] = 'invalid_edit_auth_list';
        break;
      }
    }

    if (! empty($cleared_users_array)) {
      $edit_permission_list = implode(',', $cleared_users_array);
    }

    # $page = trim($_POST['page']);
    $type_addition = trim($_POST['type_addition']);
    # if (empty($_POST['page']))
    # $errors[] = 'error_page_name_empty';
    # elseif (! preg_match(VALID_URL_CHARACTERS, $_POST['page']))
    # $errors[] = 'error_page_name_spec_chars';
    if (empty($_POST['page']) || ! preg_match(VALID_URL_CHARACTERS, $_POST['page'])) {
      $errors[] = 'error_page_name_empty_or_invalid';
    }

    # if(empty($_POST['title'])) $errors[] = 'error_no_title';

    if ($_POST['teaser_img'] && ! file_exists(MEDIA_DIR . $_POST['teaser_img'])) {
      $errors[] = 'err_teaser_img_doesnt_exist';
    }

    # if (empty($page_types[$_POST['type']]))
    if (empty($page_types[$_POST['type']])) {
      $errors[] = 'invalid_page_type';
      # if (isset($page_types[$_POST['type']]) && $page_types[$_POST['type']]['requires_parameter'] == true && trim($type_addition) == '')
    }
    elseif (! empty($page_types[$_POST['type']]['requires_parameter']) && ! $type_addition) {
      $errors[] = 'page_type_req_param';
    }

    # if (($time = strtotime($_POST['time'])) === false)
    # $errors[] = 'time_invalid';
    $time = strtotime($_POST['time']) ?: $errors[] = 'time_invalid';
    # if (($last_modified = strtotime($_POST['last_modified'])) === false)
    # $errors[] = 'last_modified_invalid';
    $last_modified = strtotime($_POST['last_modified']) ?: $errors[] = 'last_modified_invalid';
  }

  if (empty($errors)) {
    $dbr = Database::$content->prepare("
        SELECT 
          id, page 
        FROM " . Database::$db_settings['pages_table'] . " 
        WHERE lower(page)=:page 
        LIMIT 1
      ");
    $dbr->bindValue(':page', strtolower($_POST['page']), PDO::PARAM_STR);
    $dbr->execute();
    $data = $dbr->fetch();

    # if (isset($data['id'])) {
    # # if(isset($_POST['id']) && intval($_POST['id'])==intval($data['id']) && empty($_POST['edit_mode'])){
    # # // OK...
    # # }
    # if (! (isset($_POST['id']) && empty($_POST['edit_mode']) && intval($data['id']) == intval($_POST['id'])))
    if ($data['id'] && (! empty($_POST['id']) && empty($_POST['edit_mode']) && intval($data['id']) !== intval($_POST['id']))) {
      $errors[] = 'error_page_name_alr_exists';
    }
  }

  if (empty($errors)) {
    # if (isset($_POST['id']) && empty($_POST['edit_mode'])) {
    # $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['pages_table'] . " SET page=:page, type=:type, type_addition=:type_addition, time=:time, last_modified=:last_modified, display_time=:display_time, last_modified_by=:last_modified_by, title=:title, page_title=:page_title, description=:description, keywords=:keywords, category=:category, page_info=:page_info, breadcrumbs=:breadcrumbs, teaser_headline=:teaser_headline, teaser=:teaser, teaser_img=:teaser_img, content=:content, sidebar_1=:sidebar_1, sidebar_2=:sidebar_2, sidebar_3=:sidebar_3, sections=:sections, include_page=:include_page, include_order=:include_order, include_rss=:include_rss, include_sitemap=:include_sitemap, include_news=:include_news, link_name=:link_name, menu_1=:menu_1, menu_2=:menu_2, menu_3=:menu_3, gcb_1=:gcb_1, gcb_2=:gcb_2, gcb_3=:gcb_3, template=:template, language=:language, content_type=:content_type, charset=:charset, page_notes=:page_notes, edit_permission=:edit_permission, edit_permission_general=:edit_permission_general, tv=:tv, status=:status WHERE id=:id");
    # $dbr->bindParam(':page', $_POST['page'], PDO::PARAM_STR);
    # $dbr->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
    # $dbr->bindParam(':type_addition', $type_addition, PDO::PARAM_STR);
    # $dbr->bindParam(':time', $time, PDO::PARAM_INT);
    # $dbr->bindParam(':last_modified', $last_modified, PDO::PARAM_INT);
    # $dbr->bindParam(':display_time', $_POST['display_time'], PDO::PARAM_INT);
    # $dbr->bindParam(':last_modified_by', $_SESSION[$settings['session_prefix'] . 'user_id'], PDO::PARAM_INT);
    # $dbr->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
    # $dbr->bindParam(':page_title', $_POST['page_title'], PDO::PARAM_STR);
    # $dbr->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
    # $dbr->bindParam(':keywords', $_POST['keywords'], PDO::PARAM_STR);
    # $dbr->bindParam(':category', $_POST['category'], PDO::PARAM_STR);
    # $dbr->bindParam(':page_info', $_POST['page_info'], PDO::PARAM_STR);
    # $dbr->bindParam(':breadcrumbs', $breadcrumb_list, PDO::PARAM_STR);
    # $dbr->bindParam(':teaser_headline', $_POST['teaser_headline'], PDO::PARAM_STR);
    # $dbr->bindParam(':teaser', $_POST['teaser'], PDO::PARAM_STR);
    # $dbr->bindParam(':teaser_img', $_POST['teaser_img'], PDO::PARAM_STR);
    # $dbr->bindParam(':content', $_POST['content'], PDO::PARAM_STR);
    # $dbr->bindParam(':sidebar_1', $_POST['sidebar_1'], PDO::PARAM_STR);
    # $dbr->bindParam(':sidebar_2', $_POST['sidebar_2'], PDO::PARAM_STR);
    # $dbr->bindParam(':sidebar_3', $_POST['sidebar_3'], PDO::PARAM_STR);
    # $dbr->bindParam(':sections', $_POST['sections'], PDO::PARAM_STR);
    # $dbr->bindParam(':include_page', $_POST['include_page'], PDO::PARAM_INT);
    # $dbr->bindParam(':include_order', $_POST['include_order'], PDO::PARAM_INT);
    # $dbr->bindParam(':include_rss', $_POST['include_rss'], PDO::PARAM_INT);
    # $dbr->bindParam(':include_sitemap', $_POST['include_sitemap'], PDO::PARAM_INT);
    # $dbr->bindParam(':include_news', $_POST['include_news'], PDO::PARAM_INT);
    # $dbr->bindParam(':link_name', $_POST['link_name'], PDO::PARAM_STR);
    # $dbr->bindParam(':menu_1', $_POST['menu_1'], PDO::PARAM_STR);
    # $dbr->bindParam(':menu_2', $_POST['menu_2'], PDO::PARAM_STR);
    # $dbr->bindParam(':menu_3', $_POST['menu_3'], PDO::PARAM_STR);
    # $dbr->bindParam(':gcb_1', $_POST['gcb_1'], PDO::PARAM_STR);
    # $dbr->bindParam(':gcb_2', $_POST['gcb_2'], PDO::PARAM_STR);
    # $dbr->bindParam(':gcb_3', $_POST['gcb_3'], PDO::PARAM_STR);
    # $dbr->bindParam(':template', $_POST['template'], PDO::PARAM_STR);
    # $dbr->bindParam(':language', $_POST['language'], PDO::PARAM_STR);
    # $dbr->bindParam(':content_type', $_POST['content_type'], PDO::PARAM_STR);
    # $dbr->bindParam(':charset', $_POST['charset'], PDO::PARAM_STR);
    # $dbr->bindParam(':page_notes', $_POST['page_notes'], PDO::PARAM_STR);
    # $dbr->bindParam(':edit_permission', $edit_permission_list, PDO::PARAM_STR);
    # $dbr->bindParam(':edit_permission_general', $_POST['edit_permission_general'], PDO::PARAM_INT);
    # $dbr->bindParam(':tv', $_POST['tv'], PDO::PARAM_STR);
    # $dbr->bindParam(':status', $_POST['status'], PDO::PARAM_INT);
    # $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    # $dbr->execute();
    // print_r(Database::$content->errorInfo());
    $query = isset($_POST['id']) && empty($_POST['edit_mode']) ? "UPDATE" : "INSERT INTO";
    $query .= " " . Database::$db_settings['pages_table'] . " SET ";

    // Prepare fields for the query
    $fields = [
      'page','type','type_addition','time','last_modified','display_time','last_modified_by',
      'title','page_title','description','keywords','category','page_info','breadcrumbs',
      'teaser_headline','teaser','teaser_img','content',
      'sidebar_1','sidebar_2','sidebar_3','sections',
      'include_page','include_order','include_rss','include_sitemap','include_news',
      'link_name','menu_1','menu_2','menu_3','gcb_1','gcb_2','gcb_3',
      'template','language','content_type','charset','page_notes',
      'edit_permission','edit_permission_general','tv','status'
    ];

    $set_clauses = array_map(fn ($field) => "$field = :$field", $fields);
    $query .= implode(', ', $set_clauses);

    if ($query === 'UPDATE') {
      $query .= " WHERE id = :id";
    }

    $stmt = Database::$content->prepare($query);

    foreach ($fields as $field) {
      $stmt->bindValue(":$field", $_POST[$field] ?? '', PDO::PARAM_STR);
    }

    if ($query === 'UPDATE') {
      $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    }

    $stmt->execute();
  }
  # } else {

  // Prüft, ob das Formular übermittelt wurde und ein Bearbeitungsmodus aktiv ist
  if (isset($_POST['edit_mode'])) {
    $time = $last_modified;
  }

  try {
    $query = "
        INSERT INTO " . Database::$db_settings['pages_table'] . " (
            page, type, type_addition, time, last_modified, display_time, last_modified_by, title, page_title,
            description, keywords, category, page_info, breadcrumbs, teaser_headline, teaser, teaser_img,
            content, sidebar_1, sidebar_2, sidebar_3, sections, include_page, include_order, include_rss,
            include_sitemap, include_news, link_name, menu_1, menu_2, menu_3, gcb_1, gcb_2, gcb_3,
            template, language, content_type, charset, page_notes, edit_permission, edit_permission_general,
            tv, status, author 
        ) VALUES (
            :page, :type, :type_addition, :time, :last_modified, :display_time, :last_modified_by, :title, :page_title,
            :description, :keywords, :category, :page_info, :breadcrumbs, :teaser_headline, :teaser, :teaser_img,
            :content, :sidebar_1, :sidebar_2, :sidebar_3, :sections, :include_page, :include_order, :include_rss,
            :include_sitemap, :include_news, :link_name, :menu_1, :menu_2, :menu_3, :gcb_1, :gcb_2, :gcb_3,
            :template, :language, :content_type, :charset, :page_notes, :edit_permission, :edit_permission_general,
            :tv, :status, :author 
        )
    ";

    $stmt = Database::$content->prepare($query);

    $fields = [
      'page','type','type_addition','time','last_modified','display_time','last_modified_by',
      'title','page_title','description','keywords','category','page_info','breadcrumbs',
      'teaser_headline','teaser','teaser_img','content','sidebar_1','sidebar_2','sidebar_3','sections',
      'include_page','include_order','include_rss','include_sitemap','include_news',
      'link_name','menu_1','menu_2','menu_3','gcb_1','gcb_2','gcb_3',
      'template','language','content_type','charset','page_notes',
      'edit_permission','edit_permission_general','tv','status','author'
    ];

    foreach ($fields as $field) {
      $stmt->bindValue(":$field", $_POST[$field] ?? '', PDO::PARAM_STR);
    }

    $stmt->bindValue(':author', $_SESSION[$settings['session_prefix'] . 'user_id'], PDO::PARAM_INT);

    $stmt->execute();
  }
  catch (Exception $e) {
    // Fehlerbehandlung (falls erforderlich)
    echo "Fehler: " . $e->getMessage();
  }

  // Automatische Cache-Bereinigung, falls aktiviert
  if (isset($cache) && $cache->autoClear) {
    $cache->clear();
  }

  // Verarbeitung von Pingbacks, falls aktiviert
  if ($settings['pingbacks_enabled'] && $send_pingbacks) {
    $page_content = $_POST['content'];

    if ($settings['content_auto_link'] == 1) {
      $page_content = make_link($page_content);
    }

    $page_content = parse_special_tags($page_content);

    $pingback = new Pingback();
    $pingback->ping(BASE_URL . $_POST['page'], $page_content);
  }

  // Weiterleitung basierend auf dem Status
  # if (intval($_POST['status']) == 0) {
  if ((int) $_POST['status'] === 0) {
    header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=pages');
    exit();
  }
  else {
    // echo '<p>Should redirect to '.BASE_URL.$_POST['page'].'</p>';
    // header('Location: '.BASE_URL.$_POST['page']);
    header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=edit&id=' . $_POST['id']);
    // https://getbutterfly.com/demo/cms/index.php?mode=edit&id=53
    exit();
  }
  # } else {
  # $template->assign('errors', $errors);
  # if (isset($_POST['id']))
  # $page_data['id'] = intval($_POST['id']);
  # $page_data['edit_mode'] = isset($_POST['edit_mode']) ? intval($_POST['edit_mode']) : 0;
  # $page_data['page'] = isset($_POST['page']) ? htmlspecialchars($_POST['page'] ?? '') : '';
  # $page_data['category'] = isset($_POST['category']) ? htmlspecialchars($_POST['category'] ?? '') : '';
  # $page_data['page_info'] = isset($_POST['page_info']) ? htmlspecialchars($_POST['page_info'] ?? '') : '';
  # $page_data['page_title'] = isset($_POST['page_title']) ? htmlspecialchars($_POST['page_title'] ?? '') : '';
  # $page_data['description'] = isset($_POST['description']) ? htmlspecialchars($_POST['description'] ?? '') : '';
  # $page_data['keywords'] = isset($_POST['keywords']) ? htmlspecialchars($_POST['keywords'] ?? '') : '';
  # $page_data['title'] = isset($_POST['title']) ? htmlspecialchars($_POST['title'] ?? '') : '';
  # $page_data['teaser'] = isset($_POST['teaser']) ? htmlspecialchars($_POST['teaser'] ?? '') : '';
  # $page_data['teaser_headline'] = isset($_POST['teaser_headline']) ? htmlspecialchars($_POST['teaser_headline'] ?? '') : '';
  # $page_data['teaser_img'] = isset($_POST['teaser_img']) ? htmlspecialchars($_POST['teaser_img'] ?? '') : '';
  # $page_data['sidebar_1'] = isset($_POST['sidebar_1']) ? htmlspecialchars($_POST['sidebar_1'] ?? '') : '';
  # $page_data['sidebar_2'] = isset($_POST['sidebar_2']) ? htmlspecialchars($_POST['sidebar_2'] ?? '') : '';
  # $page_data['sidebar_3'] = isset($_POST['sidebar_3']) ? htmlspecialchars($_POST['sidebar_3'] ?? '') : '';
  # $page_data['type'] = isset($_POST['type']) ? htmlspecialchars($_POST['type'] ?? '') : '';
  # $page_data['type_addition'] = isset($_POST['type_addition']) ? htmlspecialchars($_POST['type_addition'] ?? '') : '';
  # $page_data['time'] = isset($_POST['time']) ? htmlspecialchars($_POST['time'] ?? '') : '';
  # $page_data['last_modified'] = isset($_POST['last_modified']) ? htmlspecialchars($_POST['last_modified'] ?? '') : '';
  # $page_data['display_time'] = isset($_POST['display_time']) && $_POST['display_time'] == 1 ? 1 : 0;
  # $page_data['include_page'] = isset($_POST['include_page']) ? intval($_POST['include_page']) : 0;
  # $page_data['include_order'] = isset($_POST['include_order']) ? intval($_POST['include_order']) : 0;
  # $page_data['include_rss'] = isset($_POST['include_rss']) ? intval($_POST['include_rss']) : 0;
  # $page_data['include_sitemap'] = isset($_POST['include_sitemap']) ? intval($_POST['include_sitemap']) : 0;
  # $page_data['include_news'] = isset($_POST['include_news']) ? intval($_POST['include_news']) : 0;
  # $page_data['link_name'] = isset($_POST['link_name']) ? htmlspecialchars($_POST['link_name'] ?? '') : Localization::$lang['teaser_default_linkname'];
  # $page_data['template'] = isset($_POST['template']) ? htmlspecialchars($_POST['template'] ?? '') : $settings['default_template'];
  # $page_data['language'] = isset($_POST['language']) ? htmlspecialchars($_POST['language'] ?? '') : '';
  # $page_data['content_type'] = isset($_POST['content_type']) ? htmlspecialchars($_POST['content_type'] ?? '') : '';
  # $page_data['charset'] = isset($_POST['charset']) ? htmlspecialchars($_POST['charset'] ?? '') : '';
  # $page_data['menu_1'] = isset($_POST['menu_1']) ? htmlspecialchars($_POST['menu_1'] ?? '') : $settings['default_menu'];
  # $page_data['menu_2'] = isset($_POST['menu_2']) ? htmlspecialchars($_POST['menu_2'] ?? '') : '';
  # $page_data['menu_3'] = isset($_POST['menu_3']) ? htmlspecialchars($_POST['menu_3'] ?? '') : '';
  # $page_data['gcb_1'] = isset($_POST['gcb_1']) ? htmlspecialchars($_POST['gcb_1'] ?? '') : '';
  # $page_data['gcb_2'] = isset($_POST['gcb_2']) ? htmlspecialchars($_POST['gcb_2'] ?? '') : '';
  # $page_data['gcb_3'] = isset($_POST['gcb_3']) ? htmlspecialchars($_POST['gcb_3'] ?? '') : '';
  # $page_data['page_notes'] = isset($_POST['page_notes']) ? htmlspecialchars($_POST['page_notes'] ?? '') : '';
  # $page_data['sections'] = isset($_POST['sections']) ? htmlspecialchars($_POST['sections'] ?? '') : '';
  # $page_data['tv'] = isset($_POST['tv']) ? htmlspecialchars($_POST['tv'] ?? '') : '';
  # $page_data['edit_permission_general'] = isset($_POST['edit_permission_general']) ? intval($_POST['edit_permission_general']) : 0;
  # $page_data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;

  # if (isset($_POST['breadcrumbs']) && is_array($_POST['breadcrumbs'])) {
  # foreach ($_POST['breadcrumbs'] as $breadcrumb) {
  # if (! empty($breadcrumb))
  # $page_data['breadcrumbs'][] = intval($breadcrumb);
  # }
  # }

  # $page_data['content'] = isset($_POST['content']) ? htmlspecialchars($_POST['content'] ?? '') : '';

  # $edit_mode = isset($_POST['edit_mode']) && $_POST['edit_mode'] == 1 ? 1 : 0;
  # }
}
// end if(isset($_POST['content']))

switch ($action)
{
  case 'main':
    // Abrufen der verfügbaren Seiten
    # $dbr = Database::$content->query("SELECT id, page, type FROM " . Database::$db_settings['pages_table'] . " ORDER BY page ASC");
    $dbr = Database::$content->query("
        SELECT
          id, page, type
        FROM " . Database::$db_settings['pages_table'] . "
        ORDER BY page ASC
      ");

    $pages = [];
    $simple_news_pages = [];
    $i = 0;
    $ii = 0;

    while ($pages_data = $dbr->fetch()) {
      $pages[$i] = [ # }
                      # $pages[$i]['id'] = $pages_data['id'];
                      # $pages[$i]['page'] = $pages_data['page'];
                      # $pages[$i]['type'] = $pages_data['type'];
        'id' => $pages_data['id'],'page' => $pages_data['page'],'type' => $pages_data['type']
      ];

      // Separates Array für News-Seiten erstellen
      # if ($pages_data['type'] == 'news' || $pages_data['type'] == 'simple_news') {
      if (in_array($pages_data['type'], [
        'news','simple_news'
      ], true)) {
        # $simple_news_pages[$ii]['id'] = $pages_data['id'];
        # $simple_news_pages[$ii]['page'] = $pages_data['page'];
        $simple_news_pages[$ii] = [
          'id' => $pages_data['id'],'page' => $pages_data['page']
        ];
        # ++ $ii;
        $ii ++;
      }
      # ++ $i;
      $i ++;
    }

    // Seiten dem Template zuweisen
    # if (isset($pages)) {
    if (! empty($pages)) {
      $template->assign('pages', $pages);
    }

    # if (isset($simple_news_pages)) {
    if (! empty($simple_news_pages)) {
      $template->assign('simple_news_pages', $simple_news_pages);
    }

    // Abrufen der verfügbaren Menüs
    $menu_result = Database::$content->query("
        SELECT DISTINCT 
          menu 
        FROM " . Database::$db_settings['menu_table'] . " 
        ORDER BY menu ASC
      ");

    $menus = [];
    while ($menu_data = $menu_result->fetch()) {
      $menus[] = $menu_data['menu'];
    }

    // Menüs dem Template zuweisen
    # if (isset($menus)) {
    if (! empty($menus)) {
      $template->assign('menus', $menus);
    }

    // Abrufen der globalen Inhaltsblöcke (Global Content Blocks, GCBs)
    $gcb_result = Database::$content->query("
        SELECT 
          id, identifier 
        FROM " . Database::$db_settings['gcb_table'] . " 
        ORDER BY id ASC
      ");

    # $i = 0;
    $gcbs = [];
    while ($gcb_data = $gcb_result->fetch()) {
      # $gcbs[$i]['id'] = $gcb_data['id'];
      # $gcbs[$i]['identifier'] = $gcb_data['identifier'];
      # $i ++;
      $gcbs[] = [
        'id' => $gcb_data['id'],'identifier' => $gcb_data['identifier']
      ];
    }

    // GCBs dem Template zuweisen
    # if (isset($gcbs)) {
    if (! empty($gcbs)) {
      $template->assign('gcbs', $gcbs);
    }

    // Abrufen der verfügbaren Templates
    # $handle = opendir(BASE_PATH . 'cms/templates/');
    $template_dir = BASE_PATH . 'cms/templates/';
    # while ($file = readdir($handle)) {
    # if (preg_match('/\.phtml$/i', $file)) {
    # $template_file_array[] = $file;
    # }
    # }
    # closedir($handle);
    # natcasesort($template_file_array);
    # $i = 0;
    # foreach ($template_file_array as $file) {
    # $template_files[$i] = $file;
    # # $template_files[$i]['name'] = htmlspecialchars($file);
    # $i ++;
    # }
    $template_files = array_filter(scandir($template_dir), fn ($file) => preg_match('/\.template.php$/i', $file));

    // Sortieren der Templates und Zuweisen an das Template
    # if (isset($template_files)) {
    if (! empty($template_files)) {
      natcasesort($template_files);
      # $template->assign('template_files', $template_files);
      $template->assign('template_files', array_values($template_files));
    }

    // Unterstützte Sprachen dem Template zuweisen
    $template->assign('page_languages', get_languages());

    // Bearbeitungsmodus überprüfen und zuweisen
    # if (empty($edit_mode)) {
    # $edit_mode = 0;
    $edit_mode = $edit_mode ?? 0;
    # }
    $template->assign('edit_mode', $edit_mode);

    // Seitendaten zuweisen (falls vorhanden)
    if (isset($page_data)) {
      $template->assign('page_data', $page_data);
      # $template->assign('send_pingbacks', $send_pingbacks);
      $template->assign('send_pingbacks', $send_pingbacks ?? false);
    }

    // Subtemplate für die Bearbeitungsseite festlegen
    $template->assign('subtemplate', 'edit.inc' . TPX);
  break;

  case 'page_doesnt_exist':
    // Fehlerfall: Seite existiert nicht
    $template->assign('invalid_request', 'page_doesnt_exist');
    $template->assign('subtemplate', 'edit.inc' . TPX);
  break;

  case 'no_authorization':
    // Fehlerfall: Keine Berechtigung zur Bearbeitung
    $template->assign('invalid_request', 'no_authorization_edit');
    $template->assign('subtemplate', 'edit.inc' . TPX);
  break;
}

/*
 * @todo Fehlerbehandlung mit try-catch und Ausgabe von Exceptions mit $e->getMessage()
 */
/**
 * Änderung:
 * 
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-20 $Date$ $LastChangedDate: 2025-01-20 07:13:10 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2025-01-20 : 4.5.0.2025.01.20 - update: PHP 8.4+/9 Kompatibilität
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
