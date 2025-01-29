<?php
/**
  * Erreur et descriptions d'erreur
  * @version 4.5.0.2025.01.29 
  * @file $Id: static/languages/french_fr-FR.error.lang.php 1 2025-01-29 09:50:00Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

  // Informations méta :
  $lang['lang']                           = 'fr';
  $lang['charset']                        = 'utf-8';
  $lang['locale']                         = array('fr_FR.utf8', 'fr-FR','fr','french');
  $lang['time_format']                    = 'd/m/Y, H:i:s'; // format de temps court
  $lang['time_format_full']               = 'l j F Y, H:i:s'; // format de temps long
  $lang['dir']                            = 'ltr';

  // erreurs
  $lang['error']                          = 'Erreur!';
  // base de données
  $lang['error_database']                 = 'Erreur de connexion à la base de données:';
  $lang['db_type_not_supp']               = 'Type de base de données non supporté:';
  // modèles
  $lang['error_Head_template_not_found']  = 'Le modèle <head> n\'a pas été trouvé.';
  // contenu
  $lang['error_page_name_empty']          = 'Aucune adresse fournie';
  $lang['error_page_name_spec_chars']     = 'Le nom de la page contient des caractères non valides';
  $lang['error_page_name_alr_exists']     = 'Le nom de la page existe déjà';
  $lang['error_no_title']                 = 'Aucun titre fourni';
  // menu admin
  $lang['error_headline']                 = 'Erreur!';
  // menus
  $lang['error_menu_spec_chars']          = 'Le nom du menu contient des caractères spéciaux';
  // gcb
  $lang['gcb_error_no_identifier']        = 'Aucun identifiant fourni';
  $lang['gcb_error_invalid_identifier']   = 'Identifiant non valide';
  // notes
  $lang['error_note_sect_name_invalid']   = 'Le nom de la section des notes est invalide (par ex., contient des espaces ou des caractères spéciaux)';
  $lang['error_notes_no_title']           = 'Aucun titre fourni';
  $lang['error_notes_no_text']            = 'Aucun texte fourni';
  $lang['error_notes_time_invalid']       = 'L\'heure saisie est invalide';
  // photos
  $lang['error_gallery_spec_chars']       = 'Le nom de la galerie contient des caractères spéciaux';
  $lang['error_no_gallery']               = 'Aucune galerie de photos fournie';
  $lang['error_no_thumbnail']             = 'Aucune miniature fournie';
  $lang['error_no_photo']                 = 'Aucune photo fournie';
  $lang['error_no_photo_title']           = 'Aucun titre fourni';
  // utilisateur
  $lang['error_username_special_chars']   = 'Le nom d\'utilisateur contient des caractères non valides';
  $lang['error_username_alr_exists']      = 'Le nom d\'utilisateur est déjà pris.';
  $lang['error_pw_doesnt_comply']         = 'Le mot de passe saisi ne correspond pas à celui répété';
  $lang['error_form_uncomplete']          = 'Vous n\'avez pas rempli tous les champs du formulaire';
  $lang['error_pw_wrong']                 = 'Mot de passe incorrect!';
  // gestionnaire de fichiers
  $lang['error_no_file']                  = 'Aucun fichier fourni pour le téléchargement';
  $lang['error_no_image']                 = 'Aucune image fournie pour le téléchargement';
  // paramètres
  $lang['settings_error_page']            = 'Page d\'erreur';
  $lang['error_settings_spec_chars']      = 'La variable contient des caractères spéciaux';
  // protection contre les spams
  $lang['error_own_ip_banned']            = 'Vous avez banni votre propre IP!';
  $lang['error_own_user_agent_banned']    = 'Vous avez banni votre propre agent utilisateur!';
  // exception
  $lang['exception_title']                = 'Erreur';
  $lang['exception_message']              = 'Une erreur s\'est produite lors du traitement de cette commande.';

