<?php
/**
  * Das Brotkrümel Template wird häufig verwendet, es wird in den entsprechenden Seiten includiert.
  *
  * @version 4.5.0.2025.02.03 
  * @file $Id: static/theme/default/templates/breadcrumbs.template.php 1 2025-02-03 09:31:14Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
?>
      <!-- Breadcrumb -->
      <div class="container">
        <nav aria-label="breadcrumb">
          <?php if ($breadcrumbs): ?>
          <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary">
            <?php foreach ($breadcrumbs as $breadcrumb): ?>
            <li class="breadcrumb-item">
              <a class="link-body-emphasis" href="<?= htmlspecialchars(BASE_URL . $breadcrumb['page']); ?>">
                <svg class="bi" width="16" height="16"><use xlink:href="#house-door-fill"></use></svg>
                <span class="visually-hidden"><?= htmlspecialchars(html_entity_decode($breadcrumb['title'] ?? '')); ?></span>
              </a>
            </li>
            <?php endforeach; ?>
            <li class="breadcrumb-item active" aria-current="page">
              <a class="link-body-emphasis fw-semibold text-decoration-none" href="#"><?= Helpers::escapeAnddecodeHtml($title ?? ''); ?></a>
            </li>
          </ol>
          <?php endif; ?>

          <?php if (empty($sidebar_1) && empty($breadcrumbs)): ?>
          <hr class="topsep hidden-xs">
          <?php endif; ?>
        </nav>
      </div>
      <!-- ./Breadcrumb -->
<?php
/**
  * Änderungen:
  *
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * @LastModified: 2025-02-03 $
  * @date $LastChangedDate: 2025-02-03 09:31:14 +0100 $
  * @editor: $LastChangedBy: ztatement $
  * -------------
  * changelog:
  * @see change.log
  *
  * $Date$     : $Revision$          : $LastChangedBy$   - Description
  * 2025-02-03 : 4.5.0.2025.02.03    : @ztatement        - breadcrumbs.template neu angelegt
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
  * Local variables:
  * tab-width: 2
  * c-basic-offset: 2
  * c-hanging-comment-ender-p: nil
  * End:
  */