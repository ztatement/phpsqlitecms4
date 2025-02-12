<?php
/**
  * Config
  *
  * @author Mark Hoschek < mail at mark-hoschek dot de >
  * @copyright (c) 2014 Mark Hoschek
  *
  * @version last 3.2015.04.02.18.42
  * @original-file $Id: cms/config/db_settings.conf.php (deleted)
  * @package phpSQLiteCMS
  *
  *
  * @author Thomas Boettcher <github[at]ztatement[dot]com>
  * @copyleft (c) 2025 ztatement
  * 
  * @version 4.5.0.2025.02.12
  * @file $Id: cms/config/mysql_config.php 1 Wed, 12 Feb 2025 13:04:20 +0100Z ztatement $
  * @link https://www.demo-seite.com/path/to/phpsqlitecms/
  * @package phpSQLiteCMS v4
  */


// Beispiel für spezifische Einstellungen
$db_settings = Config::get_db_settings([
  'db_type' => 'mysql',
  'host' => 'localhost',
  'port' => 3306,
  'user' => 'root',
  'password' => '',
  'database' => 'phpsqlitecms'
]);


/**
  * Änderungen:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-12 $
  * @date $LastChangedDate: Wed, 12 Feb 2025 13:04:20 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$  - Description
  * 2025-02-12 : 4.5.0.2025.02.12    : ztatement        - added: mysql_config neu angelegt (zuvor db_settings.conf)
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
