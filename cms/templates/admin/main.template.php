<?php
/**
 * @version 4.5.0.2025.02.13
 * @file $Id: cms/templates/admin/main.template.php 1 Thu, 25. Feb 2016, 09:00:32Z ztatement $
 * @link https://www.demo-seite.com/path/to/phpsqlitecms/
 * @package phpSQLiteCMS v4
 */
?>
  <!-- head -->
  <?php 
    // Sicherstellen, dass die Datei existiert, bevor sie eingebunden wird
    $head_template = BASE_PATH . 'cms/templates/head' .TPX;
    if (file_exists($head_template)) {
      include($head_template);
    } else {
      echo '<!-- Head template not found -->';
    }
  ?>
  <!-- ./head -->

  <!--body class="d-flex align-items-center py-4 bg-body-tertiary"-->
  <body class="align-items-center py-4 overflow-hidden <?php echo isset($admin) && $admin ? 'admin' : ''; ?>">

  <!-- Admin Navigation -->
  <?php if ($admin): ?>
  <div class="container-fluid overflow-hidden">
    <div class="row flex-nowrap">
      <?php 
        // Admin menu einbinden und sicherstellen, dass die Datei existiert
        $admin_menu_template = BASE_PATH . 'cms/templates/admin/subtemplates/admin_menu.inc' . TPX;
        if (file_exists($admin_menu_template)) {
          include($admin_menu_template);
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
      <!--?php 
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
            <?= Helpers::escapeAndDecodeHtml($content); ?>
          </div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger">
            <strong>Error:</strong> <?= Helpers::escapeHtml($error_message); ?>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            <?= Helpers::escapeAndDecodeHtml($lang['invalid_request']); ?>
          </div>
        <?php endif; ?>
          </div>
        </main>

  <!-- footer -->
  <?php 
    // Footer sicher einbinden und sicherstellen, dass die Datei existiert
    $footer_template = BASE_PATH . 'cms/templates/footer' . TPX;
    if (file_exists($footer_template)) {
      include($footer_template);
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
    <script src="<?= Helpers::escapeHtml(WYSIWYG_EDITOR); ?>"></script>
    <script src="<?= Helpers::escapeHtml(WYSIWYG_EDITOR_INIT); ?>"></script>
  <?php endif; ?>

  <!-- Custom Admin JS -->
  <!--script src="<#?= htmlspecialchars(STATIC_URL . 'theme/' . $settings['theme'] . '/js/admin_backend.js', ENT_QUOTES, 'UTF-8'); ?>"></script-->

  <?php if ($mode == 'galleries'): ?>
    <!-- Gallery JS -->
    <script src="<?= Helpers::escapeHtml(STATIC_URL . 'js/mylightbox.js'); ?>" type="text/javascript"></script>
  <?php endif; ?>

 </body>
</html>
<?php
/**
  * Was wurde verbessert?
  * Prüfung der Existenz von Dateien: file_exists() hinzugefügt,
  * um sicherzustellen, dass die eingebundenen Dateien wirklich vorhanden sind,
  * bevor sie eingebunden werden. Dies verhindert Fehler, wenn eine Datei nicht existiert.
  * Sicherstellung der korrekten Ausgabe von dynamischen Inhalten:
  * htmlspecialchars() und html_entity_decode() sind nun mit ENT_QUOTES und 'UTF-8' als Parameter versehen,
  * um sicherzustellen, dass alle HTML-Entitäten korrekt verarbeitet werden.
  * Bei allen dynamischen Variablen ($content, $error_message, $lang['invalid_request'], etc.)
  * Es wurde darauf geachtet, dass keine nicht-initialisierten Variablen verwendet werden.
  * Alle dynamischen Inhalte werden überprüft, bevor sie eingebunden oder ausgegeben werden.
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-13 $
  * @date $LastChangedDate: Thu, 13 Feb 2025 09:01:32 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$   - Description
  * 2025-02-13 : 4.4.3.2025.02.13    : @ztatement        - update: Verwendung von Helpers::escapeHtml, um den Code wider leserlicher zu machen.
  * 2024-12-29 : 4.4.3.2024.12.29    : @ztatement        - @fix: main.template htmlspecialchars() und html_entity_decode() und add file_exists()
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */
