<?php namespace App\Library;

class DataHelpers {

	static function limit ( $request, $columns )
	{
		
		$page = (intval($request['start']) != 0 ? ($request['start'] + $request['length']) / $request['length'] : 1 );
		return array(
			'page'	=> $page ,
			'limit'	=> intval($request['length'])
		);
	}

	static function order ( $request, $columns )
	{
		$order = array('sort'=>'','by'=>'');

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = self::pluck( $columns, 'dt' );

			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				$order = array(
					'sort'		=> $column['db'].'.'.$column['dt'] ,
					'by'		=> $request['order'][$i]['dir'] === 'asc' ?	'ASC' :	'DESC'
				);
			}

		}

		return $order;
	}

	static function filter ( $request, $columns, &$bindings )
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck( $columns, 'dt' );
		/*
		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['searchable'] == 'true' ) {
					$binding = self::bind( $bindings, '%'.$str.'%','text' );
					$globalSearch[] = "`".$column['db']."`.`".$column['dt']."` LIKE ".$binding;
				}
			}
		}
		*/

		// Individual column filtering
		if ( isset( $request['columns'] ) ) {
			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				//print_r($column);

				$str = $requestColumn['search']['value'];

				if ( $requestColumn['searchable'] == 'true' && $str != '' ) 
				{
					$binding = self::bind( $bindings, '%'.$str.'%', 'text' );
					$columnSearch[] = "`".$column['db']."`.`".$column['dt']."` LIKE '%".$str."%'";
				}
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'AND '.$where;
		}

		return $where;
	}


	static function simple ( $request,   $columns )
	{
		$bindings = array();
		$limit = self::limit( $request, $columns );
		$return = array(
			'page'	=> $limit['page'],
			'limit'	=> $limit['limit'],
			'order'	=> self::order( $request, $columns ),
			'params'=> self::filter( $request, $columns, $bindings )
		); 
		return $return ;
		
	}


	static function bind ( &$a, $val, $type )
	{
		$key = ':binding_'.count( $a );

		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}


	static function pluck ( $a, $prop )
	{
		$out = array();

		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			$out[] = $a[$i][$prop];
		}

		return $out;
	}


}

