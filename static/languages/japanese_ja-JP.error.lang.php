<?php
/**
  * 日本語でのエラー説明
  * @version 4.5.0.2025.01.29 
  * @file $Id: static/languages/japanese_ja-JP.error.lang.php 1 2025-01-29 09:44:00Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

  // メタ情報:
  $lang['lang']                           = 'ja';
  $lang['charset']                        = 'utf-8';
  $lang['locale']                         = array('ja_JP.utf8', 'ja-JP', 'ja_JP@euro','ja','jpn');
  $lang['time_format']                    = 'Y年m月d日, H:i:s'; // 短い時間形式
  $lang['time_format_full']               = 'Y年m月d日, H:i:s A'; // 長い時間形式
  $lang['dir']                            = 'ltr';

  // エラー
  $lang['error']                          = 'エラー!';
  // データベース
  $lang['error_database']                 = 'データベース接続エラー:';
  $lang['db_type_not_supp']               = 'サポートされていないデータベースタイプ:';
  // テンプレート
  $lang['error_Head_template_not_found']  = '<head> テンプレートが見つかりませんでした。';
  // コンテンツ
  $lang['error_page_name_empty']          = '住所が提供されていません';
  $lang['error_page_name_spec_chars']     = 'ページ名に無効な文字が含まれています';
  $lang['error_page_name_alr_exists']     = 'ページ名はすでに存在します';
  $lang['error_no_title']                 = 'タイトルが提供されていません';
  // 管理メニュー
  $lang['error_headline']                 = 'エラー!';
  // メニュー
  $lang['error_menu_spec_chars']          = 'メニュー名に特殊文字が含まれています';
  // gcb
  $lang['gcb_error_no_identifier']        = '識別子が提供されていません';
  $lang['gcb_error_invalid_identifier']   = '無効な識別子';
  // メモ
  $lang['error_note_sect_name_invalid']   = 'メモセクション名が無効です (例: 空白または特殊文字が含まれています)';
  $lang['error_notes_no_title']           = 'タイトルが提供されていません';
  $lang['error_notes_no_text']            = 'テキストが提供されていません';
  $lang['error_notes_time_invalid']       = '入力された時間が無効です';
  // 写真
  $lang['error_gallery_spec_chars']       = 'フォトギャラリー名に特殊文字が含まれています';
  $lang['error_no_gallery']               = 'フォトギャラリーが提供されていません';
  $lang['error_no_thumbnail']             = 'サムネイルが提供されていません';
  $lang['error_no_photo']                 = '写真が提供されていません';
  $lang['error_no_photo_title']           = 'タイトルが提供されていません';
  // ユーザー
  $lang['error_username_special_chars']   = 'ユーザー名に無効な文字が含まれています';
  $lang['error_username_alr_exists']      = 'ユーザー名はすでに使用されています。';
  $lang['error_pw_doesnt_comply']         = '入力されたパスワードが繰り返しのパスワードと一致しません';
  $lang['error_form_uncomplete']          = 'すべてのフォームフィールドが入力されていません';
  $lang['error_pw_wrong']                 = 'パスワードが間違っています!';
  // ファイルマネージャー
  $lang['error_no_file']                  = 'アップロードするファイルが提供されていません';
  $lang['error_no_image']                 = 'アップロードする画像が提供されていません';
  // 設定
  $lang['settings_error_page']            = 'エラーページ';
  $lang['error_settings_spec_chars']      = '変数に特殊文字が含まれています';
  // スパム保護
  $lang['error_own_ip_banned']            = '自分のIPを禁止しました!';
  $lang['error_own_user_agent_banned']    = '自分のユーザーエージェントを禁止しました!';
  // 例外
  $lang['exception_title']                = 'エラー';
  $lang['exception_message']              = 'このコマンドを処理中にエラーが発生しました。'
