<?php

// Admin Navigation: add new item
Navigation::add(__('Facebook Gallery', 'fb_gallery'), 'extends', 'monstra_cms_fb_gallery', 1);

// Add actions
//Action::add('admin_themes_extra_index_template_actions','fb_galleryAdmin::formComponent');
//Action::add('admin_themes_extra_actions','fb_galleryAdminn::formComponentSave');

/**
 * monstra_cms_fb_gallery admin class
 */
class monstra_cms_fb_galleryAdmin extends Backend
{
    /**
     * Main monstra_cms_fb_gallery admin function
     */
    public static function main()
    {
        //
        // Do something here...
        //
        $fb_templates_path = PLUGINS . DS ."monstra_cms_fb_gallery". DS ."views". DS ."frontend" . DS;
        $fb_templates_list = array();
        // Check for get actions
        // -------------------------------------
        if (Request::get('action')) {

            // Switch actions
            // -------------------------------------
            switch (Request::get('action')) {

                // Plugin action
                // -------------------------------------

                case "edit_fb_template":

                  if (Request::post('edit_fb_template') || Request::post('edit_fb_template_and_exit') ) {

                      if (Security::check(Request::post('csrf'))) {

                          // Save Email Template
                          File::setContent($fb_templates_path . Request::post('fb_template_name') .'.view.php', Request::post('content'));

                          Notification::set('success', __('Your changes to the email template <i>:name</i> have been saved.', 'emails', array(':name' => Request::post('fb_template_name'))));

                          if (Request::post('edit_fb_template_and_exit')) {
                              Request::redirect('index.php?id=monstra_cms_fb_gallery');
                          } else {
                              Request::redirect('index.php?id=monstra_cms_fb_gallery&action=edit_fb_template&filename='.Request::post('fb_template_name'));
                          }

                      }

                  }

                  $content = File::getContent($fb_templates_path.Request::get('filename').'.view.php');

                  // Display view
                  View::factory('monstra_cms_fb_gallery/views/backend/edit')
                          ->assign('content', $content)
                          ->display();
                break;

                case "add":
                    //
                    // Do something here...
                    //
                break;

                // Plugin action
                // -------------------------------------
                case "delete":
                    //
                    // Do something here...
                    //
                break;
            }

        } else {

          // Get email templates
          $fb_templates_list = File::scan($fb_templates_path, '.view.php');

          // Display view
          View::factory('monstra_cms_fb_gallery/views/backend/index')
                ->assign('fb_templates_list', $fb_templates_list)
                ->display();
        }

    }

    /**
     * Form Component Save
     */


    public static function formComponentSave()
    {
        if (Request::post('monstra_cms_fb_gallery_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('monstra_cms_fb_gallery_template', Request::post('monstra_cms_fb_gallery_form_template'));
                Request::redirect('index.php?id=themes');
            }
        }
    }

    /**
     * Form Component
     */
    public static function formComponent()
    {
        $_templates = Themes::getTemplates();
        foreach ($_templates as $template) {
            $templates[basename($template, '.template.php')] = basename($template, '.template.php');
        }

        echo (
            '<div class="col-xs-3">'.
            Form::open().
            Form::hidden('csrf', Security::token()).
            Form::label('monstra_cms_fb_gallery_form_template', __('monstra_cms_fb_gallery template', 'monstra_cms_fb_gallery')).
            Form::select('monstra_cms_fb_gallery_form_template', $templates, Option::get('monstra_cms_fb_gallery_template'), array('class' => 'form-control')).
            Html::br().
            Form::submit('monstra_cms_fb_gallery_component_save', __('Save', 'monstra_cms_fb_gallery'), array('class' => 'btn btn-default')).
            Form::close().
            '</div>'
        );
    }

}
