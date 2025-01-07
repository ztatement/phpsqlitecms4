<?php
/**
 * exception.template
 */
?>
  <!-- head -->
  <?php 
    // Sicherstellen, dass die Datei existiert, bevor sie eingebunden wird
    $header_path = BASE_PATH . 'cms/templates/head' . TPX;
    if (file_exists($header_path)) {
      require($header_path);
    } else {
      echo '<!-- Header template not found -->';
    }
  ?><!-- ./head -->
 <body>

  <section class="errors">
    <div class="body-content album text-muted">
      <div class="container">
        <div class="row">
          <div class="col-md-12 main-content">

            <h2><?= htmlspecialchars($exception_title, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?= htmlspecialchars($exception_message, ENT_QUOTES, 'UTF-8'); ?></p>

            <?php if ($debug_mode && isset($exception)): ?>
            <p><strong>Message:</strong> <?= htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Code:</strong> <?= htmlspecialchars($exception->getCode(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>File:</strong> <?= htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Line:</strong> <?= htmlspecialchars($exception->getLine(), ENT_QUOTES, 'UTF-8'); ?></p>

            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </section>

 </body>
</html>
<?php
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-07 $Date$ $LastChangedDate: 2025-01-07 22:33:21 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * @version 4.5.0.2025.01.07 $Id: cms/includes/exception.inc.php 1Z $
 * -------------
 * changelog:
 * @see change.log
 * 
 * $Date$     : $Revision$        - Description
 * 2025-01-07 : 4.5.0.2025.01.07  - auslagern der Fehlerseite "exception.template" aus exceptin.inc.php.
 *                                  kleine Format korrekturen, Escaping von Ausgaben mit htmlspecialchars()
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */