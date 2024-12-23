<<<<<<< HEAD
<?php
/**
  * @file cms/german.page.lang.php
  * @version 4.0.0 $Id: german.age.lang.php 1 2016-12-22 13:08:22Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
  // Meta-Informaton:
  $lang['lang'] =                          'de';
  $lang['charset'] =                       'utf-8';
  $lang['locale'] =                        array('de_DE.utf8', 'de_DE', 'de_DE@euro','de','deu');
  $lang['dir'] =                           'ltr';

  // Allgemein:
  $lang['exception_title'] =               'Fehler';
  $lang['exception_message'] =             'Beim Verarbeiten dieser Direktive ist ein Fehler aufgetreten.';
  $lang['error_headline'] =                'Fehler:';
  $lang['page_time'] =                     '[time|A, e. B Y]';
  $lang['include_news_time'] =             '[time|e. H:i:s]';
  $lang['submit_button_ok'] =              '&nbsp;OK&nbsp;';
  $lang['page_last_modified'] =            '<!--Erstellt: [created|d.m.Y, H:i:s] - -->Zuletzt bearbeitet: [last_modified|d.m.Y, H:i:s]';
  $lang['no_comments'] =                   'Keine Kommentare';
  $lang['one_comment'] =                   '1 Kommentar';
  $lang['several_comments'] =              '[comments] Kommentare';
  $lang['number_of_comments'][0] =         'keine Kommentare';
  $lang['number_of_comments'][1] =         '1 Kommentar';
  $lang['number_of_comments'][2] =         '[comments] Kommentare';
  $lang['pagination'] =                    'Seite [current_page] von [total_pages]';
  $lang['edit'] =                          'bearbeiten';
  $lang['delete'] =                        'l&#246;schen';
  $lang['all_categories'] =                'zur&#252;cksetzen / alle Kategorien anzeigen'; //???

  // Administrations-Men&#252;
  $lang['admin_menu_home'] =               'Home';
  $lang['admin_menu_admin'] =              'Administration';
  $lang['admin_menu_page_overview'] =      'Seiten&#252;bersicht';
  $lang['admin_menu_new_page'] =           'neue Seite erstellen';
  $lang['admin_menu_logout'] =             'Logout';
  $lang['admin_menu_act_page_actions'] =   'Diese Seite:';
  $lang['admin_menu_edit_page'] =          'bearbeiten';
  $lang['admin_menu_delete_page'] =        'l&#246;schen';
  $lang['admin_menu_delete_page_conf'] =   'Wollen Sie diese Seite wirklich l&#246;schen?';

  // Kommentare
  $lang['comment_headline'] =              'Kommentare';
  $lang['pingback_headline'] =             'Pingbacks';
  $lang['comment_no_comments'] =           'Es sind noch keine Kommentare vorhanden.';
  $lang['comments_closed'] =               'Kommentare geschlossen!';
  #$lang['comment_time'] =                  '[time|%A, %d. %B %Y, %H:%M]';
  $lang['comment_time'] =                  '[time|A, d.m.Y, H:i:s]';
  $lang['comments_pagination_info'] =      '[total_comments] Kommentare<!--, Seite [current_page] von [total_pages]-->';
  $lang['comments_add_comment'] =          'Kommentar hinzuf&#252;gen';
  $lang['comment_input_text'] =            'Ihr Kommentar:';
  $lang['comment_edit_text'] =             'Kommentar bearbeiten:';
  $lang['comment_input_name'] =            'Name';
  $lang['comment_input_email_hp'] =        'E-Mail oder Webseite (optional)';
  $lang['comment_input_submit'] =          '&nbsp;OK&nbsp;';
  $lang['comment_input_preview'] =         'Vorschau';
  $lang['comment_preview_hl'] =            'Vorschau:';
  $lang['error_not_accepted_word'] =       'Nicht akzeptiertes Wort: [not_accepted_word]';
  $lang['error_not_accepted_words'] =      'Nicht akzeptierte W&#246;rter: [not_accepted_words]';
  $lang['comment_error'] =                 'Der Kommentar enth&#228;lt mindestens ein nicht akzeptiertes Wort';
  $lang['comment_error_no_name'] =         'Es wurde kein Name eingegeben';
  $lang['comment_error_no_text'] =         'Es wurde kein Kommentar eingegeben';
  $lang['comment_error_name_too_long'] =   'Der eingegebene Name ist zu lang';
  $lang['comment_error_email_hp_too_long'] = 'Ihr Eintrag in E-Mail-Adresse/Webseite ist zu lang';
  $lang['comment_error_email_hp_invalid'] = 'Die eingegebene E-Mail-Adresse/Webseite ist ung&#252;ltig';
  $lang['comment_error_text_too_long'] =   'Der eingegebene Text ist zu lang ([characters] Zeichen; maximal sind [max_characters] Zeichen erlaubt)';
  $lang['comment_error_too_long_word'] =   'Zu langes Wort: [word]';
  $lang['comment_error_too_long_words'] =  'Zu lange W&#246;rter: [words]';
  $lang['comment_error_entry_exists'] =    'Dieser Eintrag ist bereits vorhanden';
  $lang['comment_error_repeated_post'] =   'Es wurde mit dieser IP bereits ein Eintrag vorgenommen - bitte warten Sie einen Moment!';
  $lang['comment_error_too_fast'] =        'Das Formular wurde zu schnell &#252;bermittelt - bitte versuchen Sie es noch einmal!';
  $lang['comment_delete_link'] =           'l&#246;schen';
  $lang['comment_delete_confirm'] =        'Wollen Sie wirklich diesen Kommentar l&#246;schen?';
  $lang['comment_edit_link'] =             'bearbeiten';
  $lang['comment_note_email'] =            '(optional)';
  $lang['comments_open'] =                 '&#246;ffnen';
  $lang['comments_close'] =                'Kommentare schlie&#223;en';
  $lang['comment_notification_subject'] =  'Kommentar zu [page]';
  $lang['comment_notification_message'] =  "[name]\n\n[comment]\n\n[link]";
  $lang['pingback_notification_subject'] = 'Pingback zu [page]';
  $lang['pingback_notification_message'] = "[title]\n[url]\n[link]";

  // News:
  #$lang['news_time'] =                     '[time|%A, %d. %B %Y, %H:%M]';
  $lang['news_time'] =                     '[time|A, d.m.Y, H:i:s]';
  $lang['no_news'] =                       'Keine News verf&#252;gbar';

  // Notes:
  $lang['note_time'] =                     '[time|A, e. B Y]';
  $lang['note_time_short'] =               '[time|e. B Y]';
  $lang['no_notes'] =                      'Keine Notizen verf&#252;gbar';

  // Formularmailer
  $lang['formmailer_label_email'] =        'E-Mail:';
  $lang['formmailer_label_subject'] =      'Betreff:';
  $lang['formmailer_label_message'] =      'Nachricht:';
  $lang['formmailer_button_send'] =        'Absenden';
  $lang['formmail_error_email_invalid'] =  'Die E-Mail-Adresse ist ung&#252;ltig';
  $lang['formmail_error_no_message'] =     'Es wurde keine Nachricht eingegeben';
  $lang['formmail_error_text_too_long'] =  'die Nachricht ist zu lang';
  $lang['formmail_error_subj_too_long'] =  'der Betreff ist zu lang';
  $lang['formmail_error_mailserver'] =     'Mailserver-Fehler - bitte versuchen Sie es sp&#228;ter noch einmal!';
  $lang['formmailer_mail_sent'] =          'Die Nachricht wurde erfolgreich versandt.';
  $lang['formmailer_no_subject'] =         'Es wurde kein Betreff eingegeben';

  // Fotogaerie:
  $lang['gallery_no_photo'] =              'Kein Foto in dieser Galerie vorhanden';

  // Foto:
  $lang['photo_headline'] =                'Foto';
  $lang['previous_photo'] =                'Vorheriges Bild';
  $lang['next_photo'] =                    'N&#228;chstes Bild';
  $lang['enlarge_photo'] =                 'Vergr&#246;&#223;ern';
  $lang['reduce_photo'] =                  'Verkleinern';
  $lang['show_large_photo'] =              'Gro&#223;';
  $lang['show_large_photo_title'] =        'Gro&#223;es Foto ansehen';
  $lang['back_link'] =                     'zur&#252;ck';
  $lang['back_title'] =                    'zur&#252;ck zu &quot;[page]&quot;';
  $lang['photo_comment_link_title'] =      'Die Kommentare zu diesem Foto lesen oder selbst welche schreiben';

  // Simple news:
  $lang['simple_news_time'] =              '[time|A, e. B Y]';
  $lang['simple_news_edit_title'] =        'Titel:';
  $lang['simple_news_edit_teaser'] =       'Teaser:';
  $lang['simple_news_edit_text'] =         'Text:';
  $lang['simple_news_edit_text_format'] =  'Auto-Formatierung';
  $lang['simple_news_edit_linkname'] =     'Linkname:';
  $lang['simple_news_default_linkname'] =  'mehr &#8230;';
  $lang['simple_news_edit_time'] =         'Datum/Uhrzeit:';
  $lang['simple_news_edit_time_format'] =  '(JJJJ-MM-TT SS:MM:SS)';
  $lang['simple_news_add_item'] =          'Eintrag hinzuf&#252;gen';
  $lang['simple_news_edit_item'] =         'Eintrag bearbeiten';
  $lang['simple_news_delete_confirm'] =    'Eintrag wirklich l&#246;schen?';
  $lang['error_news_no_title'] =           'kein Titel eingegeben';
  $lang['error_news_no_text'] =            'kein Text eingegeben';
  $lang['error_news_time_invalid'] =       'ung&#252;ltiges Datum-/Uhrzeitformat';
  $lang['delete_news_title'] =             'Eintrag l&#246;schen';
  $lang['delete_news_confirm_submit'] =    'OK - L&#246;schen';

  // Suche:
  $lang['search_submit'] =                 'Suche';
  $lang['search_number_of_results'][0] =   'Keine Seiten gefunden';
  $lang['search_number_of_results'][1] =   '1 Seite gefunden:';
  $lang['search_number_of_results'][2] =   '[pages] Seiten gefunden:';
  $lang['search_pagination'] =             '[total_results] Ergebnisse, Seite [current_page] von [total_pages]';
  $lang['search_photo'] =                  'Foto';
  $lang['search_no_results'] =             'Keine Seiten gefunden';

  // Akismet:
  $lang['akismet_error_api_key'] =         'Ung&#252;ltiger Akismet-API-Key';
  $lang['akismet_error_connection'] =      'Fehler bei der Serververbindung - bitte versuchen Sie es sp&#228;ter noch einmal';
  $lang['akismet_spam_suspicion'] =        'Spam-Verdacht!';

 /*
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2016-12-22
  * @date $LastChangedDate: 2016-12-22 13:08:22 +0100 (Do, 22. Dez 2016) $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * $Date$ $Revision$ - Description
  * 2016-12-22 - Änderung der Vokalgrapheme (Umlaute - &#228;,&#246;,&#252;,&#223;) in Unicode Zeichen.
  * 2016-07-18 4.0.0 - Erste Ver&#246;ffentlichung des neuen 4.x Stamm
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
=======
<?php
/**
  * @file cms/german.page.lang.php
  * @version 4.0.0 $Id: german.age.lang.php 1 2016-12-22 13:08:22Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
  // Meta-Informaton:
  $lang['lang'] =                          'de';
  $lang['charset'] =                       'utf-8';
  $lang['locale'] =                        array('de_DE.utf8', 'de_DE', 'de_DE@euro','de','deu');
  $lang['dir'] =                           'ltr';

  // Allgemein:
  $lang['exception_title'] =               'Fehler';
  $lang['exception_message'] =             'Beim Verarbeiten dieser Direktive ist ein Fehler aufgetreten.';
  $lang['error_headline'] =                'Fehler:';
  $lang['page_time'] =                     '[time|A, e. B Y]';
  $lang['include_news_time'] =             '[time|e. H:i:s]';
  $lang['submit_button_ok'] =              '&nbsp;OK&nbsp;';
  $lang['page_last_modified'] =            '<!--Erstellt: [created|d.m.Y, H:i:s] - -->Zuletzt bearbeitet: [last_modified|d.m.Y, H:i:s]';
  $lang['no_comments'] =                   'Keine Kommentare';
  $lang['one_comment'] =                   '1 Kommentar';
  $lang['several_comments'] =              '[comments] Kommentare';
  $lang['number_of_comments'][0] =         'keine Kommentare';
  $lang['number_of_comments'][1] =         '1 Kommentar';
  $lang['number_of_comments'][2] =         '[comments] Kommentare';
  $lang['pagination'] =                    'Seite [current_page] von [total_pages]';
  $lang['edit'] =                          'bearbeiten';
  $lang['delete'] =                        'l&#246;schen';
  $lang['all_categories'] =                'zur&#252;cksetzen / alle Kategorien anzeigen'; //???

  // Administrations-Men&#252;
  $lang['admin_menu_home'] =               'Home';
  $lang['admin_menu_admin'] =              'Administration';
  $lang['admin_menu_page_overview'] =      'Seiten&#252;bersicht';
  $lang['admin_menu_new_page'] =           'neue Seite erstellen';
  $lang['admin_menu_logout'] =             'Logout';
  $lang['admin_menu_act_page_actions'] =   'Diese Seite:';
  $lang['admin_menu_edit_page'] =          'bearbeiten';
  $lang['admin_menu_delete_page'] =        'l&#246;schen';
  $lang['admin_menu_delete_page_conf'] =   'Wollen Sie diese Seite wirklich l&#246;schen?';

  // Kommentare
  $lang['comment_headline'] =              'Kommentare';
  $lang['pingback_headline'] =             'Pingbacks';
  $lang['comment_no_comments'] =           'Es sind noch keine Kommentare vorhanden.';
  $lang['comments_closed'] =               'Kommentare geschlossen!';
  #$lang['comment_time'] =                  '[time|%A, %d. %B %Y, %H:%M]';
  $lang['comment_time'] =                  '[time|A, d.m.Y, H:i:s]';
  $lang['comments_pagination_info'] =      '[total_comments] Kommentare<!--, Seite [current_page] von [total_pages]-->';
  $lang['comments_add_comment'] =          'Kommentar hinzuf&#252;gen';
  $lang['comment_input_text'] =            'Ihr Kommentar:';
  $lang['comment_edit_text'] =             'Kommentar bearbeiten:';
  $lang['comment_input_name'] =            'Name';
  $lang['comment_input_email_hp'] =        'E-Mail oder Webseite (optional)';
  $lang['comment_input_submit'] =          '&nbsp;OK&nbsp;';
  $lang['comment_input_preview'] =         'Vorschau';
  $lang['comment_preview_hl'] =            'Vorschau:';
  $lang['error_not_accepted_word'] =       'Nicht akzeptiertes Wort: [not_accepted_word]';
  $lang['error_not_accepted_words'] =      'Nicht akzeptierte W&#246;rter: [not_accepted_words]';
  $lang['comment_error'] =                 'Der Kommentar enth&#228;lt mindestens ein nicht akzeptiertes Wort';
  $lang['comment_error_no_name'] =         'Es wurde kein Name eingegeben';
  $lang['comment_error_no_text'] =         'Es wurde kein Kommentar eingegeben';
  $lang['comment_error_name_too_long'] =   'Der eingegebene Name ist zu lang';
  $lang['comment_error_email_hp_too_long'] = 'Ihr Eintrag in E-Mail-Adresse/Webseite ist zu lang';
  $lang['comment_error_email_hp_invalid'] = 'Die eingegebene E-Mail-Adresse/Webseite ist ung&#252;ltig';
  $lang['comment_error_text_too_long'] =   'Der eingegebene Text ist zu lang ([characters] Zeichen; maximal sind [max_characters] Zeichen erlaubt)';
  $lang['comment_error_too_long_word'] =   'Zu langes Wort: [word]';
  $lang['comment_error_too_long_words'] =  'Zu lange W&#246;rter: [words]';
  $lang['comment_error_entry_exists'] =    'Dieser Eintrag ist bereits vorhanden';
  $lang['comment_error_repeated_post'] =   'Es wurde mit dieser IP bereits ein Eintrag vorgenommen - bitte warten Sie einen Moment!';
  $lang['comment_error_too_fast'] =        'Das Formular wurde zu schnell &#252;bermittelt - bitte versuchen Sie es noch einmal!';
  $lang['comment_delete_link'] =           'l&#246;schen';
  $lang['comment_delete_confirm'] =        'Wollen Sie wirklich diesen Kommentar l&#246;schen?';
  $lang['comment_edit_link'] =             'bearbeiten';
  $lang['comment_note_email'] =            '(optional)';
  $lang['comments_open'] =                 '&#246;ffnen';
  $lang['comments_close'] =                'Kommentare schlie&#223;en';
  $lang['comment_notification_subject'] =  'Kommentar zu [page]';
  $lang['comment_notification_message'] =  "[name]\n\n[comment]\n\n[link]";
  $lang['pingback_notification_subject'] = 'Pingback zu [page]';
  $lang['pingback_notification_message'] = "[title]\n[url]\n[link]";

  // News:
  #$lang['news_time'] =                     '[time|%A, %d. %B %Y, %H:%M]';
  $lang['news_time'] =                     '[time|A, d.m.Y, H:i:s]';
  $lang['no_news'] =                       'Keine News verf&#252;gbar';

  // Notes:
  $lang['note_time'] =                     '[time|A, e. B Y]';
  $lang['note_time_short'] =               '[time|e. B Y]';
  $lang['no_notes'] =                      'Keine Notizen verf&#252;gbar';

  // Formularmailer
  $lang['formmailer_label_email'] =        'E-Mail:';
  $lang['formmailer_label_subject'] =      'Betreff:';
  $lang['formmailer_label_message'] =      'Nachricht:';
  $lang['formmailer_button_send'] =        'Absenden';
  $lang['formmail_error_email_invalid'] =  'Die E-Mail-Adresse ist ung&#252;ltig';
  $lang['formmail_error_no_message'] =     'Es wurde keine Nachricht eingegeben';
  $lang['formmail_error_text_too_long'] =  'die Nachricht ist zu lang';
  $lang['formmail_error_subj_too_long'] =  'der Betreff ist zu lang';
  $lang['formmail_error_mailserver'] =     'Mailserver-Fehler - bitte versuchen Sie es sp&#228;ter noch einmal!';
  $lang['formmailer_mail_sent'] =          'Die Nachricht wurde erfolgreich versandt.';
  $lang['formmailer_no_subject'] =         'Es wurde kein Betreff eingegeben';

  // Fotogaerie:
  $lang['gallery_no_photo'] =              'Kein Foto in dieser Galerie vorhanden';

  // Foto:
  $lang['photo_headline'] =                'Foto';
  $lang['previous_photo'] =                'Vorheriges Bild';
  $lang['next_photo'] =                    'N&#228;chstes Bild';
  $lang['enlarge_photo'] =                 'Vergr&#246;&#223;ern';
  $lang['reduce_photo'] =                  'Verkleinern';
  $lang['show_large_photo'] =              'Gro&#223;';
  $lang['show_large_photo_title'] =        'Gro&#223;es Foto ansehen';
  $lang['back_link'] =                     'zur&#252;ck';
  $lang['back_title'] =                    'zur&#252;ck zu &quot;[page]&quot;';
  $lang['photo_comment_link_title'] =      'Die Kommentare zu diesem Foto lesen oder selbst welche schreiben';

  // Simple news:
  $lang['simple_news_time'] =              '[time|A, e. B Y]';
  $lang['simple_news_edit_title'] =        'Titel:';
  $lang['simple_news_edit_teaser'] =       'Teaser:';
  $lang['simple_news_edit_text'] =         'Text:';
  $lang['simple_news_edit_text_format'] =  'Auto-Formatierung';
  $lang['simple_news_edit_linkname'] =     'Linkname:';
  $lang['simple_news_default_linkname'] =  'mehr &#8230;';
  $lang['simple_news_edit_time'] =         'Datum/Uhrzeit:';
  $lang['simple_news_edit_time_format'] =  '(JJJJ-MM-TT SS:MM:SS)';
  $lang['simple_news_add_item'] =          'Eintrag hinzuf&#252;gen';
  $lang['simple_news_edit_item'] =         'Eintrag bearbeiten';
  $lang['simple_news_delete_confirm'] =    'Eintrag wirklich l&#246;schen?';
  $lang['error_news_no_title'] =           'kein Titel eingegeben';
  $lang['error_news_no_text'] =            'kein Text eingegeben';
  $lang['error_news_time_invalid'] =       'ung&#252;ltiges Datum-/Uhrzeitformat';
  $lang['delete_news_title'] =             'Eintrag l&#246;schen';
  $lang['delete_news_confirm_submit'] =    'OK - L&#246;schen';

  // Suche:
  $lang['search_submit'] =                 'Suche';
  $lang['search_number_of_results'][0] =   'Keine Seiten gefunden';
  $lang['search_number_of_results'][1] =   '1 Seite gefunden:';
  $lang['search_number_of_results'][2] =   '[pages] Seiten gefunden:';
  $lang['search_pagination'] =             '[total_results] Ergebnisse, Seite [current_page] von [total_pages]';
  $lang['search_photo'] =                  'Foto';
  $lang['search_no_results'] =             'Keine Seiten gefunden';

  // Akismet:
  $lang['akismet_error_api_key'] =         'Ung&#252;ltiger Akismet-API-Key';
  $lang['akismet_error_connection'] =      'Fehler bei der Serververbindung - bitte versuchen Sie es sp&#228;ter noch einmal';
  $lang['akismet_spam_suspicion'] =        'Spam-Verdacht!';

 /*
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2016-12-22
  * @date $LastChangedDate: 2016-12-22 13:08:22 +0100 (Do, 22. Dez 2016) $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * $Date$ $Revision$ - Description
  * 2016-12-22 - Änderung der Vokalgrapheme (Umlaute - &#228;,&#246;,&#252;,&#223;) in Unicode Zeichen.
  * 2016-07-18 4.0.0 - Erste Ver&#246;ffentlichung des neuen 4.x Stamm
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
>>>>>>> c975f1ffd942608271d45ab3711bcbf9076ebad4
