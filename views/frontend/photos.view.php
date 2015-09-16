<div class="row">
  <div class="col-sm-12">
    <h2><?php echo $title; ?></h2>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
<?php
for($x=0; $x<$photo_count; $x++)
{
?>
    <a href="<?php echo $photo_list[$x]['src_big']; ?>" data-toggle="lightbox" data-gallery="<?php echo $album_id; ?>" data-footer="<?php echo $photo_list[$x]['caption']; ?>">
    <img src="<?php echo $photo_list[$x]['src']; ?>" style="width: 150px; height: 150px;" class="img-responsive img-thumbnail">
    </a>
<?php
}
?>
  </div>
</div>
