<?php
/**
  * Das Exception Template wird verwendet, um eine Fehlerseite anzuzeigen.
  *
  * @version 4.5.0.2025.02.04 
  * @file $Id: cms/templates/exception.template.php 1 2016-02-25 09:00:32Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
?>
<!-- head -->
<?php 
    // Sicherstellen, dass die Datei existiert, bevor sie eingebunden wird
    $header_path = THEME_TEMPLATES . 'head' . TPX;
    try {
        if (file_exists($header_path)) {
            require($header_path);
        } else {
            throw new Exception($lang['error_Header_template_not_found']);
        }
    } catch (Exception $e) {
        echo '<!-- Header loading failed: ' . $e->getMessage() . ' -->';
    }
?>
<!-- ./head -->
  <body>
    <section class="errors">
      <div class="body-content album text-muted">
        <div class="container">
          <div class="row">
            <div class="col-md-12 main-content">

              <!-- Fehlerüberschrift und -nachricht -->
              <h2><?= Helpers::escapeHtml($exception_title); ?></h2>
              <p><?= Helpers::escapeHtml($exception_message); ?></p>

              <?php if ($debug_mode && isset($exception)): ?>
                  <!-- Wenn der Debug-Modus aktiv ist, zusätzliche Details anzeigen -->
                  <p><strong>Message:</strong> <?= Helpers::escapeHtml($exception->getMessage()); ?></p>
                  <p><strong>Code:</strong> <?= Helpers::escapeHtml($exception->getCode()); ?></p>
                  <p><strong>File:</strong> <?= Helpers::escapeHtml($exception->getFile()); ?></p>
                  <p><strong>Line:</strong> <?= Helpers::escapeHtml($exception->getLine()); ?></p>
                  <pre><strong>Trace:</strong> <?= Helpers::escapeHtml($exception->getTraceAsString()); ?></pre>
              <?php else: ?>
                  <!-- Generische Fehlernachricht im nicht-Debug-Modus -->
                  <p>Ein Fehler ist aufgetreten. Bitte versuche es später noch einmal oder wende dich an den Support.</p>
              <?php endif; ?>

              <?php
              // Überprüfung, ob ein Log-Level gesetzt wurde und Anzeige des Log-Levels
              if (isset($log_level)) {
                  echo '<p><strong>Log-Level:</strong> ' . Helpers::escapeHtml($log_level) . '</p>';
              }
              ?>

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
 * @LastModified: $2025-02-04 $
 * @date $LastChangedDate: Tuesday, 04-Feb-25 19:34:49 UTC+1 $
 * @editor: $LastChangedBy: ztatement $
 * @version 4.5.0.2025.02.04 $Id: cms/templates/exception.template.php 1Z $
 * -------------
 * changelog:
 * @see change.log
 * 
 * $Date$     : $Revision$        - Description
 * 2025-01-08 : 4.5.0.2025.01.08  - die require()-Anweisung für das Head-Template in einen try-catch-Block gesetzt,
 *                                  um Ausnahmen zu vermeiden, wenn die Datei nicht existiert.
 * 2025-01-07 : 4.5.0.2025.01.07  - auslagern der Fehlerseite "exception.template" aus exceptin.inc.php.
 *                                  kleine Format korrekturen, Escaping von Ausgaben mit htmlspecialchars()
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */