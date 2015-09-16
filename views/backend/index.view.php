<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Facebook Gallery Editor', 'fb_gallery'); ?></h2>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Gallery templates', 'fb_gallery'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($fb_templates_list) != 0) foreach ($fb_templates_list as $email_template) { ?>
        <tr>
            <td><?php echo basename($email_template, '.view.php'); ?></td>
            <td>
                <div class="pull-right">
                    <div class="btn-group">
                        <?php echo Html::anchor(__('Edit', 'emails'), 'index.php?id=monstra_cms_fb_gallery&action=edit_fb_template&filename='.basename($email_template, '.view.php'), array('class' => 'btn btn-primary')); ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
