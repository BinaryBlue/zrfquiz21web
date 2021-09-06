<?php 

class PostHelpers {

	public function __construct()
	{

		$this->data = json_decode(file_get_contents(base_path().'/resources/views/core/posts/config.json'),true);
	}



	static function latestpost(  )
	{
		$sql = Post::latestposts();
		return $sql;
		$content = '
		
		<ul class="widgeul"> ';
		foreach($sql as $row) :
		$content .='<li>
		<b><a href="'. url('posts/'.$row->alias).'"> '. $row->title .'</a></b><br />
		<span> '. date("M j, Y " , strtotime($row->created)) .' </span>
		</li>';
		endforeach ;
		$content .='</ul>';

		return $content;
	}

	public static function cloudtags()
	{
		$tags = array();	
		$keywords = array();
		$word = '';
		$data = \DB::table('tb_pages')->where('pagetype','post')->get();
		foreach($data as $row)
		{
			$clouds = explode(',',$row->labels);
			foreach($clouds as $cld)
			{
				$cld = strtolower($cld);
				if (isset($tags[$cld]) )
				{
					$tags[$cld] += 1;
				} else {
					$tags[$cld] = 1;
				}
				//$tags[$cld] = trim($cld);
			}
		}

		ksort($tags);
		$cats = [];
		foreach($tags as $tag=>$size)
		{
			//$size += 12;
			$cats[] = array(
				'tags'	=> trim($tag) ,
				'size'	=> $size
			);
			
		}

		return $cats;
	}	

	static function formatContent( $content )
	{
	    // character(s) to escape
	    
	    $x = '`~!#^*()-_+={}[]:\'"<>.';
	    $content = preg_replace_callback('#(?<!\\\)!!([^\n]+?)!!#', function($m) use($x) {
	        $s = htmlentities($m[1], ENT_NOQUOTES);
	        return  self::__fnc($s, $x);
	    }, $content);
	    
	    $content = preg_replace_callback('#\<php\>(.+?)\<\/php\>#s',function($matches) use($x) {

		    $attr["code"] = $matches[1];
		    return  view("core.code", $attr);
		  }, $content);
		/*
	    $content = preg_replace_callback('#\<pre\>(.+?)\<\/pre\>#s',create_function(
		    '$matches',
		    'return "<pre class=\"prettyprint lang-php\">".htmlentities($matches[1])."</pre>";'
		  ), $content);	

		*/
	    $sortcodes = preg_match_all('#\[sc:.*\](.+?)\[\/sc\]#Ui', $content,$matches);
	    if(count($matches[0]))
	    {
	    	foreach($matches[0] as $code)
	    	{
	    		$sortcode = self::sortcode($code );	
	    		$result = ucwords($sortcode['code']).'Helpers|'.$sortcode['params']['fnc'].'|'.$sortcode['params']['id'];
	    		$result = self::__fnc($result);
	    		$content = str_replace( $code ,$result, $content)	;
	    	}
	    }
		
		
	    return $content;
	} 


	static function sortcode( $content){

	    preg_match_all('/\[sc:([a-zA-Z0-9-_: |=\.]+)]/', $content, $shortcodes);

	    if ($shortcodes == NULL) {
           return $content;
        }

        $array = array();
        // Tidy up
        foreach ($shortcodes[1] as $key => $shortcode) {
            if (strstr($shortcode, ' ')) {
                $code = substr($shortcode, 0, strpos($shortcode, ' '));
                $tmp = explode('|', str_replace($code . ' ', '', $shortcode));
                $params = array();
                if (count($tmp)) {
                    foreach ($tmp as $param) {
                        $pair = explode('=', $param);
                        $params[$pair[0]] = $pair[1];
                    }
                }
                $array = array('code' => $code, 'params' => $params);
            }
            else {
                $array = array('code' => $shortcode, 'params' => array());
            }
            
            $shortcode_array[$shortcodes[0][$key]] = $array;
        }   

        if (count($shortcode_array)) {
         	
            foreach ($shortcode_array as $search => $shortcode) {
                $shortcode_model = $shortcode['code'];
            	$text = ' Code = '.$shortcode['code'];
            	$array = ['code'=> $shortcode['code'] , 'params'=> $shortcode['params'] ];
            }
            
            $array =  $array;
        }  

        return $array;

	}	

    static function __fnc( $args){

            $c = explode("|",$args);
            if(file_exists( app_path() .'/Library/'.$c[0].'.php'))
            {
            	include( app_path() .'/Library/'.$c[0].'.php');
            	
            }

            if(isset($c[0]) && class_exists($c[0]) )
            {
                $args = explode(':',$c[2]);
                if(count($args)>=2)
                {
                    $value = call_user_func( array($c[0],$c[1]), $args);    
                } else {
                    $value = call_user_func( array($c[0],$c[1]), str_replace(":","','",$c[2]));     
                }
                
            } else {
                    $value = '<pre>Class Doest Not Exists</pre>';
            }

            return $value;

    } 		
}