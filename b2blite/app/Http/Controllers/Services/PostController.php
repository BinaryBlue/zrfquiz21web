<?php

namespace App\Http\Controllers\Services;
use App\User;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Library\SximoHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class PostController extends Controller
{
    public $user ;
    public function __construct( Request $request )
    {
       // $this->user = SximoHelper::getUserByToken( $request );
    }

    public function page() {

         return response()->json(array('status'=>'success' , 'data' => [] ));


    }

    public function index( Request $request )
    {
        $config = config('sximo');
        $data = [
            'categories'    => Post::categories(),
            'popular'       => Post::lists('popular') ,
            'latests'       => Post::lists('latests') ,  
            'headline'       => Post::lists('headline') ,          

        ];
        return response()->json(array('status'=>'success' , 'data' => $data ));

    }  


    public function show( Request $request , $type = null ) {
        

        switch($type) :
            case 'categories':
            return $this->categories();
                break;

            case 'lists':            
            return $this->listsData( 'lists');
                break;

            case 'ebook':
            return $this->ebook( $request->get('id') );
                break;    



        endswitch;

    }

    public function listsData(  $tag = null ) {
        $data = Post::lists( $tag ) ;
        return response()->json(array('status'=>'success' , 'data' => $data ));

    }    
    public function categories( ) {
        $data = Post::categories( ) ;
        return response()->json(array('status'=>'success' , 'data' => $data ));

    }    
    public function ebook( $id ) {
        $data = Post::ebook( $id ) ;
        return response()->json(array('status'=>'success' , 'data' => $data ));

    } 
}
