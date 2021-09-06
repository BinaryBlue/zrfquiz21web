<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class post extends Sximo  {
	
	protected $table = 'tb_pages';
	protected $primaryKey = 'pageID';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return " 
			SELECT 
				tb_pages.* , COUNT(commentID) AS comments , username
			FROM tb_pages 
			LEFT JOIN tb_comments ON tb_comments.pageID = tb_pages.pageID
			LEFT JOIN tb_users ON tb_users.id = tb_pages.userid
		  ";
	}	

	public static function queryWhere(  ){
		
		return " WHERE tb_pages.pageID IS NOT NULL AND pagetype = 'post'";
	}
	
	public static function queryGroup(){
		return " GROUP BY tb_pages.pageID ";
	}
	

	public static function commentsx( $pageID)
	{
		$sql = \DB::select("
			SELECT tb_comments.* ,username , avatar , email
			FROM tb_comments LEFT JOIN tb_users ON tb_users.id = tb_comments.userid
			WHERE pageID ='".$pageID."'
			");
		return $sql;
	}


	public static function latestposts( )
	{
		$sql = \DB::select("
			SELECT * FROM tb_pages WHERE pagetype ='post' ORDER BY created DESC LIMIT 5
			");
		return $sql;
	}



    public static function lists( $tag = null) {

        switch($tag) :
            default:
                 $rows  = DB::table('tb_pages')
                         ->select('tb_pages.*','tb_categories.name as category')
                         ->leftJoin('tb_categories', 'tb_categories.cid', '=', 'tb_pages.cid');
                if(isset($_GET['cid'])) {
                  $rows = $rows->where('tb_categories.alias' , $_GET['cid'] );
                }                         
                $rows   = $rows->where('pagetype','post')->orderBy('created','desc')->limit(10)->get();
                break;

            case 'categories':
                $rows = DB::table('tb_pages')
                         ->select('tb_pages.*','tb_categories.name as category')
                         ->leftJoin('tb_categories', 'tb_categories.cid', '=', 'tb_pages.cid');

                         
                $rows = $rows->where('pagetype','post')->orderBy('created','desc')->limit(10)->get();
                break; 

             case 'popular':
                $rows = DB::table('tb_pages')
                         ->select('tb_pages.*','tb_categories.name as category')
                         ->leftJoin('tb_categories', 'tb_categories.cid', '=', 'tb_pages.cid')
                         ->where('pagetype','post')->orderBy('created','desc')->limit(10)->get();
                break; 
    
            case 'latests':
               $rows = DB::table('tb_pages')
                         ->select('tb_pages.*','tb_categories.name as category')
                         ->leftJoin('tb_categories', 'tb_categories.cid', '=', 'tb_pages.cid')
                         ->where('pagetype','post')->orderBy('created','desc')->limit(10)->get();
                break; 

            case 'headline':
               $rows = DB::table('tb_pages')
                         ->select('tb_pages.*','tb_categories.name as category')
                         ->leftJoin('tb_categories', 'tb_categories.cid', '=', 'tb_pages.cid')
                         ->where('pagetype','post')->where('headline' ,'1')
                         ->orderBy('created','desc')->limit(10)->get();
                break;          


        endswitch;
        $data = [];
        foreach($rows as $row) {
            $row->created = date("l , d M Y",strtotime($row->created));
            $row->labels = explode("," , $row->labels);
            $row->updated = self::timeAgo( strtotime($row->updated ));

            if(file_exists('./uploads/images/posts/'.$row->image) and $row->image !='') {
                $row->image = asset('/uploads/images/posts/'.$row->image);
            } else {
                $row->image = asset('/uploads/images/no-image.png');
            }
            if(file_exists('./uploads/images/posts/'.$row->thumbnail) and $row->thumbnail !='') {
                $row->thumbnail = asset('/uploads/images/posts/'.$row->thumbnail);
            } else {
                $row->thumbnail = asset('/uploads/images/no-image.png');
            }

           $data[] = $row;

        }

       return $data ;

    }

    public static function read( $alias )  {

        $posts = DB::table('tb_pages')
                    ->select('tb_pages.*', 'tb_categories.name', 'tb_users.username', DB::raw('tb_categories.alias AS category_alias')  ,\DB::raw('COUNT(commentID) AS comments'))
                    ->leftJoin('tb_users','tb_users.id','tb_pages.userid')
                    ->leftJoin('tb_comments','tb_comments.pageID','tb_pages.pageID')        
                    ->leftJoin('tb_categories','tb_categories.cid','tb_pages.cid')                  
                    ->where('pagetype','post');
                    $posts = $posts->where('tb_pages.alias',$alias )->get(); 
        $row = $posts[0];           
        DB::table('tb_pages')->where('pageID',$row->pageID)->update(array('views'=> \DB::raw('views+1'))); 
        return $row;            

    }
    public static function comments( $pageId )  {

        $rows = DB::table('tb_comments')
                ->select('tb_comments.*','username','avatar','email')
                ->leftJoin('tb_users','tb_users.id','tb_comments.UserID')
                ->where('PageID',  $pageId )
                ->get();
        return $rows;            

    }    
    public static function categories( ) {

        return $categories = DB::table('tb_categories')
                            ->select('tb_categories.*', DB::raw('COUNT(pageID) AS total'))
                            ->leftJoin('tb_pages','tb_pages.cid','tb_categories.cid')
                            ->groupBy('tb_categories.cid')
                            ->get();
    }
   public static function categoryDetail( $alias )  {

        $row = DB::table('tb_categories')->where('alias', $alias )->get();
        return $row[0];            

    }
    public static function select_categories() {
        $data = [] ;
        foreach(DB::table('tb_categories')->get() as $row) {
            $data[] = [ 'text' => $row->name ,'value' => $row->cid ];
        }
        return $data;
    }
    public static function select_parent_pages( $cid ) {
        $data = [] ;
        foreach(DB::table('tb_pages')->where('cid', $cid)->get() as $row) {
            $data[] = [ 'text' => $row->title ,'value' => $row->id ];
        }
        return $data;
    }
    public static function select_access( ) {
        $data = [] ;
        foreach(DB::table('groups')->get() as $row) {
            $data[] = [ 'text' => $row->name ,'value' => $row->group_id ];
        }
        return $data;
    }

     public static function ebook( $id ) {

        return  DB::table('tb_pages')->where('parent_id' , $id )->get();
     }
    public static function timeAgo($time_ago) {
      //  $time_ago =  strtotime($time_ago) ? strtotime($time_ago) : $time_ago;

      //  return $time_ago ;
        $time  = time() - $time_ago;

        switch($time):
        // seconds
        case $time <= 60;
        return 'lessthan a minute ago';
        // minutes
        case $time >= 60 && $time < 3600;
        return (round($time/60) == 1) ? 'a minute' : round($time/60).' minutes ago';
        // hours
        case $time >= 3600 && $time < 86400;
        return (round($time/3600) == 1) ? 'a hour ago' : round($time/3600).' hours ago';
        // days
        case $time >= 86400 && $time < 604800;
        return (round($time/86400) == 1) ? 'a day ago' : round($time/86400).' days ago';
        // weeks
        case $time >= 604800 && $time < 2600640;
        return (round($time/604800) == 1) ? 'a week ago' : round($time/604800).' weeks ago';
        // months
        case $time >= 2600640 && $time < 31207680;
        return (round($time/2600640) == 1) ? 'a month ago' : round($time/2600640).' months ago';
        // years
        case $time >= 31207680;
        return (round($time/31207680) == 1) ? 'a year ago' : round($time/31207680).' years ago' ;

        endswitch;
    }


}
