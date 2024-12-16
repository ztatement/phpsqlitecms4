<h1><?php echo $lang['administration']; ?></h1>

<?php if (isset($msg)): ?>
    <p class="ok"><?php if (isset($lang[$msg])) echo $lang[$msg]; else echo $msg; ?></p>
<?php endif; ?>



<?php include(BASE_PATH . 'cms/templates/admin/subtemplates/admin_sidebar.inc'.TPX); ?>




