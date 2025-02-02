<?php
/*
 * errors.inc.template
 */
?>
<?php if (isset($errors) && !empty($errors)): ?>
<div class="alert alert-danger">
  <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
  <h3>
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
      <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
    </svg> <strong><?= htmlspecialchars(html_entity_decode($lang['error_headline']), ENT_QUOTES, 'UTF-8'); ?></strong>
  </h3>
  <ul>
    <?php foreach ($errors as $error): ?>
      <li>
        <?php 
          // Überprüfen, ob der Fehler in der Sprachdatei existiert und sicher ausgeben
          echo isset($lang[$error]) ? htmlspecialchars(html_entity_decode($lang[$error]), ENT_QUOTES, 'UTF-8') : htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); 
        ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<!-- Erfolgsmeldung -->
<?php if (isset($success) && !empty($success)): ?>
<div class="alert alert-success">
  <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
  <h3>
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg> <strong><?= htmlspecialchars(html_entity_decode($lang['success_headline']), ENT_QUOTES, 'UTF-8'); ?></strong>
  </h3>
  <ul>
    <?php foreach ($success as $message): ?>
      <li><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
<?php
/*
 * Was wurde verbessert?
 * Icons: Die Icons auf bi-exclamation-triangle-fill (für Fehler) und bi-check-circle-fill (für Erfolg) geändert, 
 * um Bootstrap Icons zu verwenden. Diese Icons sind modern und standardisiert.
 * Schließen-Button: Der Schließen-Button hat jetzt das Attribut aria-label="Close", was sicherstellt,
 * dass der Button für Screenreader zugänglich ist.
 * Klarere Struktur und Konsistenz:
 * Die Fehler- und Erfolgsnachricht mit Icons versehen und die Struktur etwas vereinfacht.
 * Dies sorgt für eine bessere visuelle Unterscheidung zwischen den Nachrichten.
 * Optimierung der Darstellung:
 * Die ul-Liste bleibt erhalten, um mehrere Fehler- oder Erfolgsmeldungen darzustellen,
 * aber die Icons und Überschriften sorgen dafür, dass die Bedeutung der Nachrichten klarer wird.
 * weitere Änderungen:
 * Sicherstellung der richtigen Ausgabe mit htmlspecialchars(): Alle dynamischen Inhalte, die in HTML ausgegeben werden, 
 * sind nun mit htmlspecialchars() geschützt, um sicherzustellen, dass keine ungewollten HTML- oder JavaScript-Injektionen erfolgen. 
 * Ich habe auch explizit ENT_QUOTES und 'UTF-8' als Parameter hinzugefügt, um sicherzustellen, 
 * dass sowohl doppelte als auch einfache Anführungszeichen korrekt behandelt werden.
 * Sicherstellung der existierenden Variablen:
 * Vor der Ausgabe wird überprüft, ob die Variablen wie $errors oder $success gesetzt und nicht leer sind.
 * Dies stellt sicher, dass keine PHP-Warnungen auftreten, falls eine dieser Variablen nicht vorhanden oder leer ist.
 * data-bs-dismiss="alert":
 * Ich habe den data-dismiss="alert"-Attribut zu data-bs-dismiss="alert" geändert, 
 * um mit der neuesten Version von Bootstrap 5 kompatibel zu sein. 
 * In Bootstrap 5 hat sich die data-*-Attributbezeichnung geändert (von data-dismiss zu data-bs-dismiss).
 * Sicherstellen, dass auch benutzerdefinierte Fehler angezeigt werden:
 * Die Fehlerbehandlung stellt sicher, dass auch benutzerdefinierte Fehler, 
 * die nicht in der Sprachdatei existieren, sicher ausgegeben werden.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-08 $Date$ $LastChangedDate: 2025-01-08 23:43:45 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$   - Description
 * 2025-01-08 : 4.5.0.2025.01.08    : @ztatement        - bs-icons durch svg icons ersetzt
 * 2024-12-27 : 4.4.3.2024.12.27    : @ztatement        - @fix: div. korrekturen für PHP8 und Bootstrap5
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */