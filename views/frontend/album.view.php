
<div class="row">
  <div class="col-sm-12 thumbnail">
    <?php
	foreach ($album_list as $key => $value) {
    ?>
    <div class="col-sm-3 wrapper<?php echo $un_num; ?>">
	<a href="?fb_gallery_id=<?php echo $value['aid'];?>&title=<?php echo urlencode($value['name']); ?>" rel="tooltip" data-placement="bottom" title="<?php echo $value['name']; ?> (<?php echo $value['size']; ?>)">
        	<img class="img-responsive img-thumbnail" src="http://graph.facebook.com/<?php echo $value['object_id']; ?>/picture?type=album">
                <div class="caption post-content<?php echo $un_num; ?>">
                <?php echo $value['name']; ?>
		</div>
	</a>
    </div>

    <?php
    }
    ?>
  </div>
</div>
