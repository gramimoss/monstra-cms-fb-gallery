<?php

    /**
     *	Facebook page Gallery plugin
     *
     *	@package Monstra
     *	@subpackage Plugins
     *	@author Graeme Moss / Gambi
     *	@copyright 2014 Graeme Moss / Gambi
     *	@version 1.0.0
     *
     */


    // Register plugin
    Plugin::register( __FILE__,
        __('Facebook page Gallery'),
        __('Facebook page Gallery plugin for Monstra.'),
        '1.0.0',
        'Gambi',
        'http://www.gambi.co.za',
        'fb_gallery');

    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
      //Plugin::admin('monstra-cms-fb-gallery');
    }

    Shortcode::add('fb_gallery', 'fb_gallery::_shortcode');


    class fb_gallery extends Frontend{

        public static $fb_id = "";
        public static $un_num = "";
        public static $page_name = "XGames";

        public static function _shortcode($attributes) {

          if (!empty($attributes['page'])){
              fb_gallery::$page_name = $attributes['page'];
          }
          fb_gallery::main();
          echo fb_gallery::content();
        }

        public static function theme_footer() {
            echo ('<script src="'.site::url(). DS .'plugins'. DS .'monstra-cms-fb-gallery/lib/ekko-lightbox.min.js"></script>');
            echo ('<script type="text/javascript">
                    $(document).ready(function ($) {
                        $(document).delegate(\'*[data-toggle="lightbox"]\', \'click\', function(event) {
                        event.preventDefault();
                        $(this).ekkoLightbox();
                        });
                    });
                    </script>
                    ');
        }




        public static function theme_header() {
            echo (' <link href="'.site::url(). DS .'plugins'. DS .'monstra-cms-fb-gallery'.DS.'lib/ekko-lightbox.min.css" rel="stylesheet">');
            echo ('<style type="text/css">
                    .post-content'.fb_gallery::$un_num.' {
                        margin: 0 auto;
                        margin-top: 0px;
                        text-align:center
                        position: relative;
                    }
                    .thumbnail'.fb_gallery::$un_num.' {
                        margin:0 auto;
                        text-align:center;
                    }

                    .wrapper'.fb_gallery::$un_num.' {
                        text-align:center;
                        padding:0;
                    }
                </style>');
        }


        public static function main(){
            fb_gallery::$un_num = rand(00000,99999);
            fb_gallery::theme_header();
            Action::add('theme_footer', 'fb_gallery::theme_footer');
            fb_gallery::$fb_id = fb_gallery::getPageId(fb_gallery::$page_name);
           

        }


        /**
        * Set Sandbox title
        */
        public static function title()
        {
            return 'Gallery';
        }

        public static function name()
        {
            return 'Gallery';
        }

        /**
         * Set Sandbox content
         */
        public static function content()
        {
            $id = Request::get('fb_gallery_id');
            if(empty($id)){
                return fb_gallery::displayAlbums();
            }
            else{
                return fb_gallery::displayPhotos(Request::get('fb_gallery_id'),Request::get('title'));
            }

            //return  View::factory('monstra-cms-fb-gallery/views/frontend/index')
            //        ->display();
        }


        public static function displayPhotos($album_id,$title='Photos')
    	{
            Cache::configure('cache_time', 43200);
            $photocache = array();
            $json_array = array();
            $photocache = Cache::get('fb_gallery',$album_id);
            $gallery = '';


            if ($photocache) {
                $json_array['data'] = $photocache;
            }else{
                $json_array = fb_gallery::getData($album_id,$type='photos');
                Cache::put('fb_gallery',$album_id,$json_array['data']);
            }
            $data_count = count($json_array['data']);
    		if($data_count > 0)
    		{
                for($x=0; $x<$data_count; $x++)
                {
                    $gallery .= '
                    <a href="'.$json_array['data'][$x]['src_big'].'" data-toggle="lightbox" data-gallery="'.$album_id.'" data-footer="'.$json_array['data'][$x]['caption'].'">
                    <img src="'.$json_array['data'][$x]['src'].'" class="img-responsive img-thumbnail">
                    </a>';
                }
                $gallery = '<div class="row"><div class="col-sm-12"><h2>'.$title.'</h2></div></div><div class="row"><div class="col-sm-12">'.$gallery.'</div></div>';

                /*
                if($this->breadcrumbs != 'n'){
                    $crumbs = array('Gallery' => $_SERVER['PHP_SELF'],
                    $title => '');
                    $gallery = $this->addBreadCrumbs($crumbs).$gallery;
                }
                 *
                 */
    		}
    		else{$gallery = 'no photos in this gallery';}


    		//$this->saveCache($album_id,$gallery); // saves cached HTML file

    		return $gallery;
    	}



        public static function displayAlbums()
    	{
            Cache::configure('cache_time', 43200);
            $albumcache = array();
            $json_array = array();
            $albumcache = Cache::get('fb_gallery',fb_gallery::$fb_id);

            $gallery = '';
            if ($albumcache) {
                $json_array['data'] = $albumcache;
            }else{
                $json_array = fb_gallery::getData(fb_gallery::$fb_id,$type='albums');
                Cache::put('fb_gallery',fb_gallery::$fb_id,$json_array['data']);
            }
            $data_count = count($json_array['data']);
            for($x=0; $x<$data_count; $x++)
            {
                if(trim($json_array['data'][$x]['name']) == "Cover Photos"){continue;}
                if($json_array['data'][$x]['name'] == "Profile Pictures"){continue;}
                if(!empty($json_array['data'][$x]['object_id']) AND $json_array['data'][$x]['size'] > 0) // do not include empty albums
                {
                    $gallery .= '
                        <div class="col-sm-3 wrapper'.fb_gallery::$un_num.'">
                            <a href="?fb_gallery_id='.$json_array['data'][$x]['aid'].'&title='.urlencode($json_array['data'][$x]['name']).'" rel="tooltip" data-placement="bottom" title="'.$json_array['data'][$x]['name'].' ('.$json_array['data'][$x]['size'].')">
                            <img class="img-responsive img-thumbnail" src="http://graph.facebook.com/'.$json_array['data'][$x]['object_id'].'/picture?type=album">
                            <div class="caption post-content'.fb_gallery::$un_num.'">

                                '.$json_array['data'][$x]['name'].'

                            </div>
                            </a>

                        </div>
                    ';
                }

            }
            $gallery = '<div class="row"><div class="col-sm-12 thumbnail">'.$gallery.'</div></div>';
/*
            if($this->breadcrumbs != 'n'){
                $crumbs = array('Gallery' => $_SERVER['PHP_SELF']);
                $gallery = $this->addBreadCrumbs($crumbs).$gallery;
            }

            $this->saveCache($this->id,$gallery); // saves cached HTML file
*/
            return $gallery;
    	}

        public static function getPageId($string)
    	{
            /**
            * Checks to see if page id is vaild
            */
            if(is_numeric($string)){$query_where = 'page_id';}
            else{$query_where = 'username';}
            $query = "SELECT page_id FROM page WHERE $query_where = '$string'";
            $url = 'https://graph.facebook.com/fql?q='.rawurlencode($query).'&format=json-strings';
            $curlopts = array(CURLOPT_HEADER => '0', CURLOPT_RETURNTRANSFER => '1');
            $return_data = Curl::get($url, $curlopts);
            $json_array = json_decode($return_data,true);

            if(isset($json_array['data'][0]['page_id'])){return $json_array['data'][0]['page_id'];}
            else{die('invalid page id or name');}
    	}

        public static function getData($id,$type='')
    	{
            /**
            * Sends each request Facebook (currently only for 'albums' and 'photos')
            */
            if(!empty($id))
            {
                    if($type == 'photos'){$query = "SELECT src,src_big,caption FROM photo WHERE aid = '$id'";}
                    else{$query = "SELECT aid,object_id,name,size,type FROM album WHERE owner = '$id' ORDER BY modified DESC";}
                    $url = 'https://graph.facebook.com/fql?q='.rawurlencode($query).'&format=json-strings';
                    $curlopts = array(CURLOPT_HEADER => '0', CURLOPT_RETURNTRANSFER => '1');
                    $return_data = Curl::get($url, $curlopts);
                    $json_array = json_decode($return_data,true);
                    return $json_array;
            }
            else{return 'id was empty';}
    	}

    }
