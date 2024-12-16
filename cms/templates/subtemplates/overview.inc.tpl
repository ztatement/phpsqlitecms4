<?php if (isset($included_pages)): ?>


        
    <?php for ($i = 0; $i < $included_pages_number; $i = $i + 2): ?>
    
            <?php for ($n = $i; $n < $i + 2; ++$n): ?>
                

                    <?php if (isset($included_pages[$n])): ?>
<!--div class="card-columns"-->
                    <div class="card">
                    <div class="card-block">
                        <h2 class="card-title"><a
                                href="<?php echo BASE_URL . $included_pages[$n]['page']; ?>"><?php echo $included_pages[$n]['teaser_headline']; ?></a>
                        </h2>

                        <div class="media">
                            <?php if ($included_pages[$n]['teaser_img']): ?>
                                <a class="thumbnail thumbnail-left card-img-left"
                                   href="<?php echo BASE_URL . $included_pages[$n]['page']; ?>">
                                   <img class=img-fluid
                                        src="<?php echo MEDIA_DIR . $included_pages[$n]['teaser_img']; ?>"
                                        alt="<?php echo $included_pages[$n]['teaser_headline']; ?>"
                                        width="<?php echo $included_pages[$n]['teaser_img_width']; ?>"
                                        height="<?php echo $included_pages[$n]['teaser_img_height']; ?>"/></a>
                            <?php endif; ?>
                            <p class="card-text"><?php echo $included_pages[$n]['teaser']; ?></p>

                            <p><a class="btn btn-primary float-xs-right"
                                  href="<?php echo BASE_URL . $included_pages[$n]['page']; ?>"><?php echo $included_pages[$n]['link_name']; ?></a>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
 
<!--/div-->
            <?php endfor; ?>


    <?php endfor; ?>
        

<?php endif; ?>
