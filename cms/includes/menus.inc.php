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
 * @copyleft () 2024 ztatement
 * @version 4.4.0.2024.12.18 $Id: cms/includes/menus.inc.php 1 2024-12-18 14:40:23Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @github https://github.com/ztatement/phpsqlitecms4
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
if (!defined('IN_INDEX'))
  exit();

/*
 * if (isset($_SESSION[$settings['session_prefix'] . 'user_id']) && $_SESSION[$settings['session_prefix'] . 'user_type'] == 1)
 * {
 * if (isset($_GET['edit']))
 * {
 * $menu = $_GET['edit'];
 * $action = 'edit';
 * }
 */

/**
 * Bearbeiten eines bestimmten Menüs
 */
function editMenu( $menuName )
{
  global $template;

  $dbr = Database::$content->prepare("SELECT id, name, sequence, title, link, section, accesskey FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence ASC");
  $dbr->bindValue(':menu', trim($menuName), PDO::PARAM_STR);
  $dbr->execute();
  $items = $dbr->fetchAll(PDO::FETCH_ASSOC);

  $template->assign('items', array_map('htmlspecialchars', $items));
  $template->assign('menu', htmlspecialchars($menuName));
  $template->assign('subtitle', Localization::$lang['menus']);
  $template->assign('subtemplate', 'menus_edit.inc' . TPX);
}

/*
 * if (isset($_REQUEST['delete']))
 * {
 * if (isset($_REQUEST['confirmed']))
 * {
 * $dbr = Database::$content->prepare("DELETE FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu");
 * $dbr->bindParam(':menu', $_REQUEST['delete'], PDO::PARAM_STR);
 * $dbr->execute();
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus');
 * exit();
 * }
 * else
 * {
 * $template->assign('menu', htmlspecialchars($_REQUEST['delete']));
 * $action = 'delete';
 * }
 * }
 */

/**
 * Löschen eines Menüeintrags
 */
function deleteMenuItem( $itemId )
{
  global $template;

  Database::$content->beginTransaction();
  try
  {
    $dbr = Database::$content->prepare("DELETE FROM " . Database::$db_settings['menu_table'] . " WHERE id=:id");
    $dbr->bindValue(':id', $itemId, PDO::PARAM_INT);
    $dbr->execute();

    $dbr = Database::$content->prepare("SELECT id FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence ASC");
    $menuName = $dbr->fetchColumn();
    $dbr->execute();
    $ids = $dbr->fetchAll(PDO::FETCH_COLUMN);

    $sequence = 1;
    foreach ($ids as $id)
    {
      $update_dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['menu_table'] . " SET sequence=:sequence WHERE id=:id");
      $update_dbr->bindValue(':sequence', $sequence++, PDO::PARAM_INT);
      $update_dbr->bindValue(':id', $id, PDO::PARAM_INT);
      $update_dbr->execute();
    }

    Database::$content->commit();
    header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $menuName);
    exit();
  }
  catch (Exception $e)
  {
    Database::$content->rollBack();
    error_log($e->getMessage());
  }
}

/*
 * if (isset($_GET['set_default']))
 * {
 * $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['settings_table'] . " SET value=:value WHERE name='default_menu'");
 * $dbr->bindValue(':value', trim($_GET['set_default']), PDO::PARAM_STR);
 * $dbr->execute();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus');
 * exit();
 * }
 * if (isset($_POST['new_menu_name']))
 * {
 * $dbr = Database::$content->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['menu_table'] . " WHERE lower(menu)=:menu");
 * $dbr->bindValue(':menu', trim(strtolower($_POST['new_menu_name'])), PDO::PARAM_STR);
 * $dbr->execute();
 * if ($dbr->fetchColumn() > 0)
 * {
 * $errors[] = 'menu_already_exists';
 * $action = 'new';
 * }
 * elseif (!preg_match('/^[a-zA-Z0-9_\-]+$/', $_POST['new_menu_name']))
 * {
 * $errors[] = 'error_menu_spec_chars';
 * $action = 'new';
 * }
 * else
 * {
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $_POST['new_menu_name']);
 * exit();
 * }
 * }
 * if (isset($_POST['new_menu_item']))
 * {
 * $dbr = Database::$content->prepare("SELECT sequence FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence DESC LIMIT 1");
 * $dbr->bindValue(':menu', trim($_POST['menu']), PDO::PARAM_STR);
 * $dbr->execute();
 * $data = $dbr->fetch();
 * if (!isset($data['sequence']))
 * {
 * $new_sequence = 1;
 * }
 * else
 * {
 * $new_sequence = $data['sequence'] + 1;
 * }
 * $dbr = Database::$content->prepare("INSERT INTO " . Database::$db_settings['menu_table'] . " (menu,sequence,name,title,link,section,accesskey) VALUES (:menu,:sequence,:name,:title,:link,:section,:accesskey)");
 * $dbr->bindValue(':menu', trim($_POST['menu']), PDO::PARAM_STR);
 * $dbr->bindValue(':sequence', $new_sequence, PDO::PARAM_INT);
 * $dbr->bindValue(':name', trim($_POST['name']), PDO::PARAM_STR);
 * $dbr->bindValue(':title', trim($_POST['title']), PDO::PARAM_STR);
 * $dbr->bindValue(':link', trim($_POST['link']), PDO::PARAM_STR);
 * $dbr->bindValue(':section', trim($_POST['section']), PDO::PARAM_STR);
 * $dbr->bindValue(':accesskey', trim($_POST['accesskey']), PDO::PARAM_STR);
 * $dbr->execute();
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $_POST['menu']);
 * exit();
 * }
 */

/**
 * Neues Menü erstellen
 */
function newMenu( )
{
  global $template, $errors;

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_menu_name']))
  {
    $menuName = trim($_POST['new_menu_name']);
    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $menuName))
    {
      $errors[] = 'error_menu_spec_chars';
    }

    $dbr = Database::$content->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['menu_table'] . " WHERE lower(menu)=:menu");
    $dbr->bindValue(':menu', strtolower($menuName), PDO::PARAM_STR);
    $dbr->execute();

    if ($dbr->fetchColumn() > 0)
    {
      $errors[] = 'menu_already_exists';
    }

    if (!$errors)
    {
      header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . urlencode($menuName));
      exit();
    }
  }

  $template->assign('errors', $errors ?? []);
  $template->assign('subtemplate', 'menus_new.inc' . TPX);
}

/*
 * if (isset($_POST['edit_item']))
 * {
 * $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['menu_table'] . " SET name=:name, title=:title, link=:link, section=:section, accesskey=:accesskey WHERE id=:id");
 * $dbr->bindValue(':name', trim($_POST['name']), PDO::PARAM_STR);
 * $dbr->bindValue(':title', trim($_POST['title']), PDO::PARAM_STR);
 * $dbr->bindValue(':link', trim($_POST['link']), PDO::PARAM_STR);
 * $dbr->bindValue(':section', trim($_POST['section']), PDO::PARAM_STR);
 * $dbr->bindValue(':accesskey', trim($_POST['accesskey']), PDO::PARAM_STR);
 * $dbr->bindParam(':id', $_POST['edit_item'], PDO::PARAM_INT);
 * $dbr->execute();
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $_POST['menu']);
 * exit();
 * }
 * if (isset($_GET['action']))
 * $action = $_GET['action'];
 * if (isset($_POST['action']))
 * $action = $_POST['action'];
 */

/*
 * if (empty($action))
 * $action = 'show_menus';
 */

/**
 * Anzeige der Menüs
 */
function showMenus( )
{
  global $template;

  $menu_result = Database::$content->query("SELECT DISTINCT menu FROM " . Database::$db_settings['menu_table'] . " ORDER BY menu ASC");
  $menus = $menu_result->fetchAll(PDO::FETCH_COLUMN);

  $template->assign('menus', $menus);
  $template->assign('subtitle', Localization::$lang['menus']);
  $template->assign('subtemplate', 'menus.inc' . TPX);
}

/*
 * if (isset($_GET['move_up']))
 * {
 * $dbr = Database::$content->prepare("SELECT menu, sequence FROM " . Database::$db_settings['menu_table'] . " WHERE id=:id LIMIT 1");
 * $dbr->bindParam(':id', $_GET['move_up'], PDO::PARAM_INT);
 * $dbr->execute();
 * $data = $dbr->fetch();
 * if (isset($data['sequence']) && $data['sequence'] > 1)
 * {
 * Database::$content->beginTransaction();
 * $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['menu_table'] . " SET sequence=:new_sequence WHERE menu=:menu AND sequence=:sequence");
 * $dbr->bindParam(':menu', $data['menu'], PDO::PARAM_STR);
 * $dbr->bindValue(':new_sequence', 0, PDO::PARAM_INT);
 * $dbr->bindValue(':sequence', $data['sequence'] - 1, PDO::PARAM_INT);
 * $dbr->execute();
 * $dbr->bindValue(':new_sequence', $data['sequence'] - 1, PDO::PARAM_INT);
 * $dbr->bindValue(':sequence', $data['sequence'], PDO::PARAM_INT);
 * $dbr->execute();
 * $dbr->bindValue(':new_sequence', $data['sequence'], PDO::PARAM_INT);
 * $dbr->bindValue(':sequence', 0, PDO::PARAM_INT);
 * $dbr->execute();
 * Database::$content->commit();
 * }
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $data['menu']);
 * exit();
 * }
 * if (isset($_GET['move_down']))
 * {
 * $dbr = Database::$content->prepare("SELECT menu, sequence FROM " . Database::$db_settings['menu_table'] . " WHERE id=:id LIMIT 1");
 * $dbr->bindParam(':id', $_GET['move_down'], PDO::PARAM_INT);
 * $dbr->execute();
 * $data = $dbr->fetch();
 * if (isset($data['sequence']))
 * {
 * $dbr = Database::$content->prepare("SELECT sequence FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence DESC LIMIT 1");
 * $dbr->bindParam(':menu', $data['menu'], PDO::PARAM_STR);
 * $dbr->execute();
 * $last = $dbr->fetchColumn();
 * if ($data['sequence'] < $last)
 * {
 * Database::$content->beginTransaction();
 * $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['menu_table'] . " SET sequence=:new_sequence WHERE menu=:menu AND sequence=:sequence");
 * $dbr->bindParam(':menu', $data['menu'], PDO::PARAM_STR);
 * $dbr->bindValue(':new_sequence', 0, PDO::PARAM_INT);
 * $dbr->bindValue(':sequence', $data['sequence'] + 1, PDO::PARAM_INT);
 * $dbr->execute();
 * $dbr->bindValue(':new_sequence', $data['sequence'] + 1, PDO::PARAM_INT);
 * $dbr->bindValue(':sequence', $data['sequence'], PDO::PARAM_INT);
 * $dbr->execute();
 * $dbr->bindValue(':new_sequence', $data['sequence'], PDO::PARAM_INT);
 * $dbr->bindValue(':sequence', 0, PDO::PARAM_INT);
 * $dbr->execute();
 * Database::$content->commit();
 * }
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $data['menu']);
 * exit();
 * }
 * }
 * if (isset($_REQUEST['reorder_items']) && isset($_REQUEST['item']))
 * {
 * $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['menu_table'] . " SET sequence=:sequence WHERE id=:id");
 * $dbr->bindParam(':id', $id, PDO::PARAM_INT);
 * $dbr->bindParam(':sequence', $sequence, PDO::PARAM_INT);
 * Database::$content->beginTransaction();
 * $sequence = 1;
 * foreach ($_REQUEST['item'] as $id)
 * {
 * $dbr->execute();
 * ++$sequence;
 * }
 * Database::$content->commit();
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * exit();
 * }
 */

/**
 * Speichert Änderungen im Menü
 */
function saveMenu( )
{
  global $template;

  $menuName = trim($_POST['menu']);
  $dbr = Database::$content->prepare("INSERT INTO " . Database::$db_settings['menu_table'] . " (menu, name, sequence, title, link, section, accesskey) VALUES (:menu, :name, :sequence, :title, :link, :section, :accesskey)");
  $dbr->bindValue(':menu', $menuName, PDO::PARAM_STR);
  $dbr->bindValue(':name', trim($_POST['name']), PDO::PARAM_STR);
  $dbr->bindValue(':sequence', intval($_POST['sequence']), PDO::PARAM_INT);
  $dbr->bindValue(':title', trim($_POST['title']), PDO::PARAM_STR);
  $dbr->bindValue(':link', trim($_POST['link']), PDO::PARAM_STR);
  $dbr->bindValue(':section', trim($_POST['section']), PDO::PARAM_STR);
  $dbr->bindValue(':accesskey', trim($_POST['accesskey']), PDO::PARAM_STR);
  $dbr->execute();
}

/*
 * // first actions:
 * switch ($action)
 * {
 * case 'delete_menu_item':
 * {
 * // get menu:
 * $dbr = Database::$content->prepare("SELECT menu FROM " . Database::$db_settings['menu_table'] . " WHERE id=:id LIMIT 1");
 * $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
 * $dbr->execute();
 * $menu = $dbr->fetchColumn();
 * // delete menu item:
 * $dbr = Database::$content->prepare("DELETE FROM " . Database::$db_settings['menu_table'] . " WHERE id=:id");
 * $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
 * $dbr->execute();
 * // reorder items:
 * $dbr = Database::$content->prepare("SELECT id FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence ASC");
 * $dbr->bindParam(':menu', $menu, PDO::PARAM_STR);
 * $dbr->execute();
 * while ($data = $dbr->fetch())
 * {
 * $ids[] = $data['id'];
 * }
 * if (isset($ids))
 * {
 * $new_sequence = 1;
 * Database::$content->beginTransaction();
 * $dbr = Database::$content->prepare("UPDATE " . Database::$db_settings['menu_table'] . " SET sequence=:sequence WHERE id=:id");
 * $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
 * $dbr->bindParam(':id', $id, PDO::PARAM_INT);
 * foreach ($ids as $id)
 * {
 * $dbr->execute();
 * ++$new_sequence;
 * }
 * Database::$content->commit();
 * }
 * if (isset($cache) && $cache->autoClear)
 * $cache->clear();
 * header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus&edit=' . $menu);
 * exit();
 * }
 * break;
 * }
 * // second actions:
 * switch ($action)
 * {
 * case 'show_menus':
 * $menu_result = Database::$content->query("SELECT DISTINCT menu FROM " . Database::$db_settings['menu_table'] . " ORDER BY menu ASC");
 * while ($menu_data = $menu_result->fetch())
 * {
 * $menus[] = $menu_data['menu'];
 * }
 * if (isset($menus))
 * {
 * $template->assign('menus', $menus);
 * }
 * $template->assign('subtitle', Localization::$lang['menus']);
 * $template->assign('subtemplate', 'menus.inc'.TPX);
 * break;
 * case 'edit':
 * $template->assign('menu', htmlspecialchars($_GET['edit']));
 * $dbr = Database::$content->prepare("SELECT id, name, sequence, title, link, section, accesskey FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence ASC");
 * $dbr->bindValue(':menu', trim($_GET['edit']), PDO::PARAM_STR);
 * $dbr->execute();
 * $i = 0;
 * while ($data = $dbr->fetch())
 * {
 * $items[$i]['id'] = intval($data['id']);
 * $items[$i]['name'] = htmlspecialchars($data['name']);
 * # $items[$i]['sequence'] = $data['sequence'];
 * $items[$i]['title'] = htmlspecialchars($data['title']);
 * $items[$i]['link'] = htmlspecialchars($data['link']);
 * $items[$i]['section'] = htmlspecialchars($data['section']);
 * $items[$i]['accesskey'] = htmlspecialchars($data['accesskey']);
 * ++$i;
 * }
 * if (isset($items))
 * {
 * $template->assign('items', $items);
 * }
 * $template->assign('subtitle', Localization::$lang['menus']);
 * $template->assign('subtemplate', 'menus_edit.inc'.TPX);
 * break;
 * case 'edit_menu_item':
 * $dbr = Database::$content->prepare("SELECT menu FROM " . Database::$db_settings['menu_table'] . " WHERE id=:id LIMIT 1");
 * $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
 * $dbr->execute();
 * $menu_data = $dbr->fetch();
 * if (isset($menu_data['menu']))
 * {
 * $dbr = Database::$content->prepare("SELECT id, name, sequence, title, link, section, accesskey FROM " . Database::$db_settings['menu_table'] . " WHERE menu=:menu ORDER BY sequence ASC");
 * $dbr->bindParam(':menu', $menu_data['menu'], PDO::PARAM_STR);
 * $dbr->execute();
 * $i = 0;
 * while ($data = $dbr->fetch())
 * {
 * $items[$i]['id'] = intval($data['id']);
 * $items[$i]['name'] = htmlspecialchars($data['name']);
 * $items[$i]['title'] = htmlspecialchars($data['title']);
 * $items[$i]['link'] = htmlspecialchars($data['link']);
 * $items[$i]['section'] = htmlspecialchars($data['section']);
 * $items[$i]['accesskey'] = htmlspecialchars($data['accesskey']);
 * ++$i;
 * }
 * if (isset($items))
 * {
 * $template->assign('items', $items);
 * }
 * $template->assign('menu', htmlspecialchars($menu_data['menu']));
 * $template->assign('edit_item', intval($_GET['id']));
 * $template->assign('subtitle', Localization::$lang['menus']);
 * $template->assign('subtemplate', 'menus_edit.inc' . TPX);
 * }
 * break;
 * case 'delete':
 * $template->assign('subtemplate', 'menus_delete.inc' . TPX);
 * break;
 * case 'new':
 * if (isset($errors))
 * {
 * $template->assign('errors', $errors);
 * }
 * if (isset($_POST['new_menu_name']))
 * {
 * $template->assign('new_menu_name', htmlspecialchars($_POST['new_menu_name']));
 * }
 * $template->assign('subtemplate', 'menus_new.inc' . TPX);
 * break;
 * }
 */

try
{
  if (isset($_SESSION[$settings['session_prefix'] . 'user_id']) && $_SESSION[$settings['session_prefix'] . 'user_type'] == 1)
  {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'show_menus';

    switch ($action)
    {
      case 'show_menus':
        showMenus();
        break;

      case 'edit':
        editMenu($_GET['edit']);
        break;

      case 'delete':
        deleteMenuItem($_GET['id']);
        break;

      case 'new':
        newMenu();
        break;

      case 'save_menu':
        saveMenu();
        break;

      default:
        header('Location: ' . BASE_URL . ADMIN_DIR . 'index.php?mode=menus');
        exit();
    }
  }
  else
  {
    header('Location: ' . BASE_URL);
    exit();
  }
}
catch (Exception $e)
{
  error_log($e->getMessage());
  header('HTTP/1.1 500 Internal Server Error');
  echo "Ein Serverfehler ist aufgetreten.";
}
#}

/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2024-12-18 $Date$ $LastChangedDate: 2024-12-18 14:40:23 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * @see change.log
 *
 * $Date$ : $Revision$ - Description
 * 2024-12-18 : 4.4.0.2024.12.18 - modifid: Getrennte Logik: Für jede Aktion 
 *                                 (show_menus, edit, delete, newMenu, saveMenu) 
 *                                 eine eigene Funktion erstellt.
 *                                 try-catch, preg_match und Validierungen hinzugefügt.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
 