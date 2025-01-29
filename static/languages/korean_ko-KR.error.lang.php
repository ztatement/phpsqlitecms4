<?php
/**
  * 한국어로 된 오류 설명
  * @version 4.5.0.2025.01.08 
  * @file $Id: static/languages/korean_ko-KR.error.lang.php 1 2025-01-29 09:47:00Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

  // 메타 정보:
  $lang['lang']                           = 'ko';
  $lang['charset']                        = 'utf-8';
  $lang['locale']                         = array('ko_KR.utf8', 'ko-KR', 'ko','kor');
  $lang['time_format']                    = 'Y년 m월 d일, H:i:s'; // 짧은 시간 형식
  $lang['time_format_full']               = 'Y년 m월 d일, H:i:s A'; // 긴 시간 형식
  $lang['dir']                            = 'ltr';

  // 오류
  $lang['error']                          = '오류!';
  // 데이터베이스
  $lang['error_database']                 = '데이터베이스 연결 오류:';
  $lang['db_type_not_supp']               = '지원되지 않는 데이터베이스 유형:';
  // 템플릿
  $lang['error_Head_template_not_found']  = '<head> 템플릿을 찾을 수 없습니다.';
  // 콘텐츠
  $lang['error_page_name_empty']          = '주소가 제공되지 않았습니다';
  $lang['error_page_name_spec_chars']     = '페이지 이름에 유효하지 않은 문자가 포함되어 있습니다';
  $lang['error_page_name_alr_exists']     = '페이지 이름이 이미 존재합니다';
  $lang['error_no_title']                 = '제목이 제공되지 않았습니다';
  // 관리자 메뉴
  $lang['error_headline']                 = '오류!';
  // 메뉴
  $lang['error_menu_spec_chars']          = '메뉴 이름에 특수 문자가 포함되어 있습니다';
  // gcb
  $lang['gcb_error_no_identifier']        = '식별자가 제공되지 않았습니다';
  $lang['gcb_error_invalid_identifier']   = '유효하지 않은 식별자';
  // 메모
  $lang['error_note_sect_name_invalid']   = '메모 섹션 이름이 유효하지 않습니다 (예: 공백 또는 특수 문자가 포함되어 있음)';
  $lang['error_notes_no_title']           = '제목이 제공되지 않았습니다';
  $lang['error_notes_no_text']            = '텍스트가 제공되지 않았습니다';
  $lang['error_notes_time_invalid']       = '입력된 시간이 유효하지 않습니다';
  // 사진
  $lang['error_gallery_spec_chars']       = '사진 갤러리 이름에 특수 문자가 포함되어 있습니다';
  $lang['error_no_gallery']               = '사진 갤러리가 제공되지 않았습니다';
  $lang['error_no_thumbnail']             = '썸네일이 제공되지 않았습니다';
  $lang['error_no_photo']                 = '사진이 제공되지 않았습니다';
  $lang['error_no_photo_title']           = '제목이 제공되지 않았습니다';
  // 사용자
  $lang['error_username_special_chars']   = '사용자 이름에 유효하지 않은 문자가 포함되어 있습니다';
  $lang['error_username_alr_exists']      = '사용자 이름이 이미 사용 중입니다.';
  $lang['error_pw_doesnt_comply']         = '입력한 비밀번호가 반복된 비밀번호와 일치하지 않습니다';
  $lang['error_form_uncomplete']          = '모든 양식 필드를 작성하지 않았습니다';
  $lang['error_pw_wrong']                 = '잘못된 비밀번호!';
  // 파일 관리자
  $lang['error_no_file']                  = '업로드할 파일이 제공되지 않았습니다';
  $lang['error_no_image']                 = '업로드할 이미지가 제공되지 않았습니다';
  // 설정
  $lang['settings_error_page']            = '오류 페이지';
  $lang['error_settings_spec_chars']      = '변수에 특수 문자가 포함되어 있습니다';
  // 스팸 방지
  $lang['error_own_ip_banned']            = '자신의 IP를 차단했습니다!';
  $lang['error_own_user_agent_banned']    = '자신의 사용자 에이전트를 차단했습니다!';
  // 예외
  $lang['exception_title']                = '오류';
  $lang['exception_message']              = '이 명령을 처리하는 동안 오류가 발생했습니다.'
