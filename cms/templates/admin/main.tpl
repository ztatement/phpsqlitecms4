<!DOCTYPE html>
<html lang="<?php echo $lang['lang']; ?>" dir="<?php echo $lang['dir']; ?>">
 <head>
  <meta charset="<?php echo $lang['charset']; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>
   <?php echo $settings['website_title']; ?> - <?php echo $lang['administration'];
        if (isset($subtitle)) echo ' - ' . $subtitle; ?>
  </title>

    <link href="<?php echo BOOTSTRAP_CSS; ?>" rel="stylesheet">
    <link href="<?php echo STATIC_URL; ?>css/style_admin.css" rel="stylesheet">
        <!--link rel=stylesheet href=<?php #echo HTP; ?>//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css crossorigin="anonymous"-->
    <link rel=stylesheet href=<?php echo HTP; ?>//cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css crossorigin="anonymous">
    <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css" crossorigin="anonymous"-->
    <!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous"-->
    <!--link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" crossorigin="anonymous"-->

    <link rel="shortcut icon" href="<?php echo STATIC_URL; ?>img/favicon.png">
<style>
.right{float:right;}
*[hidden] { display: none; }
/*
@media(min-width:767px) {
    .navbar {
        padding: 20px 0;
        transition: background .5s ease-in-out,padding .5s ease-in-out;
    }

    .top-nav-collapse {
        padding: 0;
    }
}
*/
</style>
 </head>
 <body>

<?php include(BASE_PATH . 'cms/templates/admin/subtemplates/admin_menu.inc'.TPX); ?>

<div class="container-fluid">



            <?php if (isset($subtemplate)): ?>

                <?php include(BASE_PATH . 'cms/templates/admin/subtemplates/' . $subtemplate); ?>

            <?php elseif (isset($content)): ?>

                <?php echo $content; ?>

            <?php
            elseif (isset($error_message)): ?>

                <p class="caution"><?php echo $error_message; ?></p>

            <?php
            else: ?>

                <p class="caution"><?php echo $lang['invalid_request']; ?></p>

            <?php endif; ?>



</div>

<script src="<?php echo JQUERY; ?>" crossorigin="anonymous"></script>
<script async src="<?php echo JQUERY_UI; ?>" crossorigin="anonymous"></script>
<script async src="<?php echo TETHER; ?>" crossorigin="anonymous"></script>
<script async src="<?php echo BOOTSTRAP; ?>" crossorigin="anonymous"></script>
<?php if (isset($wysiwyg)): ?>
    <script src="<?php echo WYSIWYG_EDITOR; ?>"></script>
    <script src="<?php echo WYSIWYG_EDITOR_INIT; ?>"></script>
<?php endif; ?>
<!--script src="<?php echo STATIC_URL; ?>js/admin_backend.js"></script-->
<?php if ($mode == 'galleries'): ?>
    <script src="<?php echo STATIC_URL; ?>js/mylightbox.js" type="text/javascript"></script>
<?php endif; ?>
<script>

//jQuery to collapse the navbar on scroll
$(window).scroll(function() {
    if ($(".navbar").offset().top > 70) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
    }
});
//jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});
</script>

</body>
</html>
<comment hidden>Kommentar</comment>

