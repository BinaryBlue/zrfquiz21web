<?php 
/*

	This is general helpers for your applications 
	any library or helper should be write here

*/
class MyHelpers {

	public static function documentation( $id ) {
	//	return $id ;
		$param = explode('-',$id );
		$id 	= $param[0];
		$type 	= isset($param[1]) ? $param[1] : 'default';
		return  \App::call('App\\Http\\Controllers\DocsController@documentation',['id' => $id ,'type' => $type ]);
		return  $param;
	}
	public static function knowledgebase( $id ) {
		return  \App::call('App\\Http\\Controllers\DocsController@knowledgebase');
		return  $param;
	}
}
