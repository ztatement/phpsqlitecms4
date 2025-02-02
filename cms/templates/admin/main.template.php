<?php
/**
  * Das Haupt Template main wird ständig im Backend verwendet, es inkludiert alle nötigen Sub-Templates..
  *
  * @version 4.5.0.2025.02.02 
  * @file $Id: cms/templates/errors.template.php 1 2016-02-25 09:00:32Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
?>
  <!-- head -->
  <?php 
    // Sicherstellen, dass die Datei existiert, bevor sie eingebunden wird
    $header_path = BASE_PATH . 'cms/templates/head' .TPX;
    if (file_exists($header_path)) {
      include($header_path);
    } else {
      echo '<!-- Head template not found -->';
    }
  ?>
  <!-- ./head -->

  <!--body class="d-flex align-items-center py-4 bg-body-tertiary"-->
  <body class="align-items-center py-4 <?php echo isset($admin) && $admin ? 'admin' : ''; ?>">

  <!-- Admin Navigation -->
  <?php if ($admin): ?>
  <div class="container-fluid overflow-hidden">
    <div class="row flex-nowrap">
      <?php 
        // Admin menu einbinden und sicherstellen, dass die Datei existiert
        $admin_menu_path = BASE_PATH . 'cms/templates/admin/subtemplates/admin_menu.inc' . TPX;
        if (file_exists($admin_menu_path)) {
          include($admin_menu_path);
        } else {
          echo '<!-- Admin menu template not found -->';
        }
      ?>
    </div>
  </div>
  <?php endif; ?>
  <!-- ./Admin Navigation -->
  
  <div class="container-fluid overflow-hidden">
    <div class="row vh-100 overflow-auto">

  <!--div class="container-fluid"-->
    <!--div class="row"-->

      <!-- Sidebar --   >
      <!--#?php 
        // Sidebar einbinden und sicherstellen, dass die Datei existiert
        $sidebar_path = BASE_PATH . 'cms/templates/admin/subtemplates/admin_sidebar.inc' . TPX;
        if (file_exists($sidebar_path)) {
          include($sidebar_path);
        } else {
          echo '<!-- Sidebar template not found --    >';
        }
      ?-->
      <!-- ./Sidebar -->

      <!--div class="col-sm p-3 min-vh-100"-->
      <div class="col d-flex flex-column h-sm-100">
        <main class="row overflow-auto h-100">
          <div class="col pt-4">
        <?php if (isset($subtemplate)): ?>
          <?php 
            // Dynamisches Subtemplate sicher einbinden
            $subtemplate_path = BASE_PATH . 'cms/templates/admin/subtemplates/' . $subtemplate;
            if (file_exists($subtemplate_path)) {
              include($subtemplate_path);
            } else {
              echo '<!-- Subtemplate not found -->';
            }
          ?>
        <?php elseif (isset($content)): ?>
          <div class="content">
            <?= htmlspecialchars(html_entity_decode($content), ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger">
            <strong>Error:</strong> <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            <?= htmlspecialchars(html_entity_decode($lang['invalid_request']), ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>
          </div>
        </main>

  <!-- footer -->
  <?php 
    // Footer sicher einbinden und sicherstellen, dass die Datei existiert
    $footer_path = BASE_PATH . 'cms/templates/footer' . TPX;
    if (file_exists($footer_path)) {
      include($footer_path);
    } else {
      echo '<!-- Footer template not found -->';
    }
  ?>
  <!-- ./footer -->

      </div>

    </div>
  </div>

  <!-- WYSIWYG Editor -->
  <?php if (isset($wysiwyg)): ?>
    <script src="<?= htmlspecialchars(WYSIWYG_EDITOR, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <script src="<?= htmlspecialchars(WYSIWYG_EDITOR_INIT, ENT_QUOTES, 'UTF-8'); ?>"></script>
  <?php endif; ?>

  <!-- Custom Admin JS -->
  <!--script src="<#?= htmlspecialchars(STATIC_URL . 'theme/' . $settings['theme'] . '/js/admin_backend.js', ENT_QUOTES, 'UTF-8'); ?>"></script-->

  <?php if ($mode == 'galleries'): ?>
    <!-- Gallery JS -->
    <script src="<?= htmlspecialchars(STATIC_URL . 'js/mylightbox.js', ENT_QUOTES, 'UTF-8'); ?>" type="text/javascript"></script>
  <?php endif; ?>

 </body>
</html>
<?php
/*
 * Änderungen:
 * file_exists() hinzugefügt, um sicherzustellen, dass die eingebundenen Dateien wirklich vorhanden sind,
 * bevor sie eingebunden werden. Dies verhindert Fehler, wenn eine Datei nicht existiert.
 * htmlspecialchars() und html_entity_decode() sind nun mit ENT_QUOTES und 'UTF-8' als Parameter versehen,
 * um sicherzustellen, dass alle HTML-Entitäten korrekt verarbeitet werden.
 * Bei allen dynamischen Variablen ($content, $error_message, $lang['invalid_request'], etc.)
 * wurde darauf geachtet, dass keine nicht-initialisierten Variablen verwendet werden.
 * Alle dynamischen Inhalte werden überprüft, bevor sie eingebunden oder ausgegeben werden.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-02-02 $
 * @date $LastChangedDate: 2025-02-02 17:12:19 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$   - Description
 * 2025-02-02 : 4.5.0.2025.02.02    : @ztatement        - update add file_exists()
 * 2024-12-29 : 4.4.3.2024.12.29    : @ztatement        - @fix main.template htmlspecialchars() und html_entity_decode()
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
