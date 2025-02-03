<?php
/**
  * Das Header Template enthält den kompletten <header> incl. Navigationsmenü.
  *
  * @version 4.5.0.2025.02.03 
  * @file $Id: static/theme/default/templates/header.template.php 1 2025-01-04 08:43:38Z ztatement $
  * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
?>
      <header class="header">
        <h1 id="logo">
          <a class="brand link-secondary" href="<?= Helpers::escapeHtml(BASE_URL); ?>">
            <?= $settings['website_title']; ?>
          </a>
        </h1>

          <!--button class="navbar-toggler hidden-md-up d-flex justify-content-end" type="button"
                  data-bs-toggle="collapse" data-bs-target="#exCollapsingNavbar" aria-controls="exCollapsingNavbar" aria-expanded="false" aria-label="Toggle navigation">
          &#9776;
          </button-->

        <?php if ($menu_1 && isset($menus[$menu_1])) : ?>

        <nav class="navbar navbar-light navbar-expand-md">
          <ul class="nav nav-pills float-end ms-auto">
            <?php foreach ($menus[$menu_1] as $item) : ?>
            <li class="nav-item <?= (!empty($item['section']) && $item['section'] == $section[0]) ? 'active' : ''; ?>">
              <a class="nav-link link-secondary" href="<?= Helpers::escapeHtml($item['link']);?>" title="<?= Helpers::escapeHtml($item['title']); ?>"
                                                       <?= !empty($item['accesskey']) ? 'accesskey="' . Helpers::escapeHtml($item['accesskey']) . '"' : ''; ?>>
                                                       <?= Helpers::escapeHtml($item['name']); ?>
              </a>
            </li>
            <?php endforeach ; ?>
          </ul>

          <?php endif; ?>
        </nav>
      </header>
<?php
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-02-03 $
 * @date $ $LastChangedDate: 2025-02-03 09:42:56 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * @see change.log
 *
 * $Date$     : $Revision$          : $LastChangedBy$   - Description
 * 2025-02-03 : 4.5.0.2025.02.03    : @ztatement        - @fix Helpers::escapeHtml
 * 2025-01-04 : 4.5.0.2025.01.04    : @ztatement        - @fix Bootstrap5
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
