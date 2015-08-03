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
    
    
    class fb_gallery extends Frontend{
        
        public static $id = "";
        
        
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
                    .post-content {
                        margin: 0 auto;
                        margin-top: -20px;
                        text-align:center
                        position: relative;
                    }
                    .thumbnail {
                        margin:0 auto;
                        text-align:center;
                    }

                    .wrapper {
                        text-align:center;
                        padding:0;
                    }
                </style>');
        }
        

        public static function main(){
            Action::add('theme_header', 'fb_gallery::theme_header');
            Action::add('theme_footer', 'fb_gallery::theme_footer');
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
            fb_gallery::$id = fb_gallery::getPageId("finlitsa");
            $id = Request::get('id');
            if(empty($id)){
                return fb_gallery::displayAlbums();
            }
            else{
                return fb_gallery::displayPhotos(Request::get('id'),Request::get('title'));
            }

            //return  View::factory('monstra-cms-fb-gallery/views/frontend/index')
            //        ->display();
        }
        
        
         public static function displayPhotos($album_id,$title='Photos')
	{
		//$this->loadCache($album_id); // loads cached file
                $gallery = '';
		$json_array = fb_gallery::getData($album_id,$type='photos');
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
            //$this->loadCache(fb_gallery::$id); // loads cached file
            $gallery = '';
            $json_array = fb_gallery::getData(fb_gallery::$id,$type='albums');
            $data_count = count($json_array['data']);
            for($x=0; $x<$data_count; $x++)
            {
                if(!empty($json_array['data'][$x]['object_id']) AND $json_array['data'][$x]['size'] > 0) // do not include empty albums
                {
                    if ($json_array['data'][$x]['name'] != "Cover Photos"){
                        $gallery .= '
                            <div class="col-sm-3 wrapper">
                                <a href="?id='.$json_array['data'][$x]['aid'].'&title='.urlencode($json_array['data'][$x]['name']).'" rel="tooltip" data-placement="bottom" title="'.$json_array['data'][$x]['name'].' ('.$json_array['data'][$x]['size'].')">
                                <img class="img-responsive img-thumbnail" src="http://graph.facebook.com/'.$json_array['data'][$x]['object_id'].'/picture?type=album"> 
                                <div class="caption post-content">

                                    <h3>'.$json_array['data'][$x]['name'].'</h3>

                                </div>
                                </a>

                            </div>
                        ';
                    }
                }

            }
            $gallery = '<div class="row"><div class="col-sm-12">'.$gallery.'</div></div>';
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
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $return_data = curl_exec($ch);
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
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HEADER,0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                    $return_data = curl_exec($ch);
                    $json_array = json_decode($return_data,true);
                    return $json_array;
            }
            else{return 'id was empty';}
	}
        
    }