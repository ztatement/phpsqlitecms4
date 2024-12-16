<?php
include (BASE_PATH . 'cms/templates/header'.TPX);
?>

 <body <?php if ($admin): ?>class=admin<?php endif; ?>>

  <?php if ($admin) include(BASE_PATH . 'cms/templates/admin/subtemplates/admin_menu.inc.tpl'); ?>

  <div class=container>

   <header class="header">
    <h1 id=logo><a class=brand href="<?php echo BASE_URL; ?>"><?php echo $settings['website_title']; ?></a></h1>

     <nav id=nav class="navbar navbar-light">
     <button class="navbar-toggler hidden-md-up float-xs-right" type=button data-toggle=collapse data-target=#exCollapsingNavbar aria-controls=exCollapsingNavbar aria-expanded=false aria-label="Toggle navigation">
     &#9776;
     </button>
      <?php if ($menu_1 && isset($menus[$menu_1])): ?>
      <ul class="nav nav-pills float-xs-right">
       <?php foreach ($menus[$menu_1] as $item): ?>
       <li class="nav-item <?php if (!empty($item['section']) && $item['section'] == $section[0]): ?>active<?php endif; ?>">
        <a class="nav-link" href="<?php echo $item['link']; ?>"
           title="<?php echo $item['title']; ?>"<?php if ($item['accesskey'] != ''): ?>
           accesskey="<?php echo $item['accesskey']; ?>"<?php endif; ?>><?php echo $item['name']; ?>
        </a>
       </li><?php endforeach; ?>
      </ul>
      <?php endif; ?>
     </nav>
   </header>

   <?php if ($sidebar_1): ?>
    <?php echo $sidebar_1; ?>
   <?php endif; ?>

   <?php if ($breadcrumbs): ?>
   <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb): ?>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL . $breadcrumb['page']; ?>"><?php echo $breadcrumb['title']; ?></a></li>
    <?php endforeach; ?>
    <li class="breadcrumb-item active"><?php echo $title; ?></li>
   </ul>
   <?php endif; ?>

   <?php if (empty($sidebar_1) && empty($breadcrumbs)): ?>
   <hr class="topsep hidden-xs">
   <?php endif; ?>


   <div class="body-content album text-muted">
    <div class="container">

    <div class="row<?php if (isset($tv['nocolumns'])): ?> main-content<?php endif; ?>">

     <?php if (empty($tv['nocolumns'])): ?>
     <div class="col-md-12 main-content">
     <?php endif; ?>

      <?php if (empty($hide_content)) {echo $content;} ?>
      <?php if (isset($subtemplate)) {include(BASE_PATH . 'cms/templates/subtemplates/' . $subtemplate);} ?>
     </div>

     <?php if (empty($tv['nocolumns'])): ?>
    </div>
    <?php endif; ?>

    </div>
   </div>

     <?php if ($sidebar_2): ?>
      <?php echo $sidebar_2; ?>
     <?php endif; ?>

    <?php if ($sidebar_3): ?>
        <?php echo $sidebar_3; ?>
    <?php endif; ?>

    <hr class="closure">
<?php
include (BASE_PATH .'cms/templates/footer'.TPX);

