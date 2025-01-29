<?php
/**
  * 错误和错误描述
  * @version 4.5.0.2025.01.08 
  * @file $Id: static/languages/chinese_zh-CN.error.lang.php 1 2025-01-08 17:37:00Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

  // 元信息:
  $lang['lang']                           = 'zh';
  $lang['charset']                        = 'utf-8';
  $lang['locale']                         = array('zh_CN.utf8', 'zh-CN', 'zh_CN@euro','zh','chi');
  $lang['time_format']                    = 'Y年m月d日, H:i:s'; // 短时间格式
  $lang['time_format_full']               = 'Y年m月d日, H:i:s A'; // 长时间格式
  $lang['dir']                            = 'ltr';

  // 错误
  $lang['error']                          = '错误!';
  // 数据库
  $lang['error_database']                 = '数据库连接错误:';
  $lang['db_type_not_supp']               = '不支持的数据库类型:';
  // 模板
  $lang['error_Head_template_not_found']  = '未找到 <head> 模板。';
  // 内容
  $lang['error_page_name_empty']          = '未提供地址';
  $lang['error_page_name_spec_chars']     = '页面名称包含无效字符';
  $lang['error_page_name_alr_exists']     = '页面名称已存在';
  $lang['error_no_title']                 = '未提供标题';
  // 管理菜单
  $lang['error_headline']                 = '错误!';
  // 菜单
  $lang['error_menu_spec_chars']          = '菜单名称包含特殊字符';
  // gcb
  $lang['gcb_error_no_identifier']        = '未提供标识符';
  $lang['gcb_error_invalid_identifier']   = '无效的标识符';
  // 笔记
  $lang['error_note_sect_name_invalid']   = '笔记部分名称无效 (例如，包含空格或特殊字符)';
  $lang['error_notes_no_title']           = '未提供标题';
  $lang['error_notes_no_text']            = '未提供文本';
  $lang['error_notes_time_invalid']       = '输入的时间无效';
  // 照片
  $lang['error_gallery_spec_chars']       = '相册名称包含特殊字符';
  $lang['error_no_gallery']               = '未提供相册';
  $lang['error_no_thumbnail']             = '未提供缩略图';
  $lang['error_no_photo']                 = '未提供照片';
  $lang['error_no_photo_title']           = '未提供标题';
  // 用户
  $lang['error_username_special_chars']   = '用户名包含无效字符';
  $lang['error_username_alr_exists']      = '用户名已被占用。';
  $lang['error_pw_doesnt_comply']         = '输入的密码与重复的密码不匹配';
  $lang['error_form_uncomplete']          = '您尚未填写所有表单字段';
  $lang['error_pw_wrong']                 = '密码错误!';
  // 文件管理器
  $lang['error_no_file']                  = '未提供要上传的文件';
  $lang['error_no_image']                 = '未提供要上传的图像';
  // 设置
  $lang['settings_error_page']            = '错误页面';
  $lang['error_settings_spec_chars']      = '变量包含特殊字符';
  // 垃圾邮件保护
  $lang['error_own_ip_banned']            = '您已禁止自己的 IP!';
  $lang['error_own_user_agent_banned']    = '您已禁止自己的用户代理!';
  // 异常
  $lang['exception_title']                = '错误';
  $lang['exception_message']              = '处理此命令时发生错误。'
