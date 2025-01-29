<?php
/**
  * Errors and error description
  * @version 4.5.0.2025.01.29 
  * @file $Id: static/languages/english_en-US.error.lang.php 1 2025-01-29 09:30:00Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

  // Meta-Information:
  $lang['lang']                           = 'en';
  $lang['charset']                        = 'utf-8';
  $lang['locale']                         = array('en_US.utf8', 'en-US','en','eng');
  $lang['time_format']                    = 'm/d/Y, H:i:s'; // short time format
  $lang['time_format_full']               = 'l, F j, Y, H:i:s'; // long time format
  $lang['dir']                            = 'ltr';

  // errors
  $lang['error']                          = 'Error!';
  // database
  $lang['error_database']                 = 'Database connection error:';
  $lang['db_type_not_supp']               = 'Database type not supported:';
  // templates
  $lang['error_Head_template_not_found']  = 'The <head> template was not found.';
  // content
  $lang['error_page_name_empty']          = 'No address was provided';
  $lang['error_page_name_spec_chars']     = 'The page name contains invalid characters';
  $lang['error_page_name_alr_exists']     = 'The page name already exists';
  $lang['error_no_title']                 = 'No title was provided';
  // admin menu
  $lang['error_headline']                 = 'Error!';
  // menus
  $lang['error_menu_spec_chars']          = 'The menu name contains special characters';
  // gcb
  $lang['gcb_error_no_identifier']        = 'No identifier was provided';
  $lang['gcb_error_invalid_identifier']   = 'Invalid identifier';
  // notes
  $lang['error_note_sect_name_invalid']   = 'The note section name is invalid (e.g., contains spaces or special characters)';
  $lang['error_notes_no_title']           = 'No title was provided';
  $lang['error_notes_no_text']            = 'No text was provided';
  $lang['error_notes_time_invalid']       = 'The entered time is invalid';
  // photos
  $lang['error_gallery_spec_chars']       = 'The photo gallery name contains special characters';
  $lang['error_no_gallery']               = 'No photo gallery was provided';
  $lang['error_no_thumbnail']             = 'No thumbnail was provided';
  $lang['error_no_photo']                 = 'No photo was provided';
  $lang['error_no_photo_title']           = 'No title was provided';
  // user
  $lang['error_username_special_chars']   = 'The username contains invalid characters';
  $lang['error_username_alr_exists']      = 'The username is already taken.';
  $lang['error_pw_doesnt_comply']         = 'The entered password does not match the repeated one';
  $lang['error_form_uncomplete']          = 'You have not filled out all the form fields';
  $lang['error_pw_wrong']                 = 'Incorrect password!';
  // filemanager
  $lang['error_no_file']                  = 'No file was provided for upload';
  $lang['error_no_image']                 = 'No image was provided for upload';
  // settings
  $lang['settings_error_page']            = 'Error page';
  $lang['error_settings_spec_chars']      = 'The variable contains special characters';
  // spam protection
  $lang['error_own_ip_banned']            = 'You have banned your own IP!';
  $lang['error_own_user_agent_banned']    = 'You have banned your own user agent!';
  // exception
  $lang['exception_title']                = 'Error';
  $lang['exception_message']              = 'An error occurred while processing this command.';
