<h2 class="margin-bottom-1"><?php echo __('Edit Gallery Template', 'fb_gallery'); ?></h2>
<script>
  $(document).ready(function() {
      var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
          lineNumbers: false,
          styleActiveLine: true,
          matchBrackets: true,
          indentUnit: 4,
          mode:  "application/x-httpd-php",
          indentWithTabs: true,
          theme: "mdn-like"
      });
  });
</script>
<?php
if ($content !== null) {
    echo (Form::open());
    echo (Form::hidden('csrf', Security::token()));
    echo (Form::hidden('fb_template_name', Request::get('filename')));
?>
<?php echo (Form::label('name', __('Name', 'fb_gallery'))); ?>
<div class="input-group">
    <?php echo (Form::input('name', Request::get('filename'), array('disabled', 'class' => 'form-control'))); ?><span class="input-group-addon">.view.php</span>
</div>

<div class="margin-top-2 margin-bottom-2">
<?php
    echo (
       Form::label('content', __('Gallery template content', 'fb_gallery')).
       Form::textarea('content', Html::toText($content), array('style' => 'width:100%;height:400px;', 'class' => 'source-editor form-control'))
    );
?>
</div>

<?php
    echo (

       Form::submit('edit_fb_template_and_exit', __('Save and Exit', 'fb_gallery'), array('class' => 'btn btn-phone btn-primary')).Html::nbsp(2).
       Form::submit('edit_fb_template', __('Save', 'fb_gallery'), array('class' => 'btn btn-phone btn-primary')). Html::nbsp(2).
       Html::anchor(__('Cancel', 'fb_gallery'), 'index.php?id=monstra_cms_fb_gallery', array('title' => __('Cancel', 'fb_gallery'), 'class' => 'btn btn-phone btn-default')).
       Form::close()
    );

} else {
    echo '<div class="message-error">'.__('This Gallery template does not exist', 'fb_gallery').'</div>';
}
?>
