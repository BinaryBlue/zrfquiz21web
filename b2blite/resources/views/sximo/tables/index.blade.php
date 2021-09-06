@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2><i class="fas fa-database"></i> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>

	<div class="toolbar-nav">
      	<div class="btn-group">
      		<a href="{{ url('sximo/tables/tableconfig/')}}" class="btn btn-sm btn-info  linkConfig tips" title="New Table "><i class="fa fa-plus"></i> Create New Table  </a>
			<a href="{{ url('sximo/tables/mysqleditor/')}}" class="btn btn-sm  btn-primary linkConfig tips" title="MySQL Editor"><i class="fa fa-pencil"></i> MySQL Editor  </a>	
	    </div>
	</div>


		<div class="row">					
			<div class="col-md-3">

				<div class="table-database">
					<div class="list-group">				 
					  @foreach($tables as $table)
					  	<a href="{{ url('sximo/tables/tableconfig/'.$table)}}" class="list-group-item list-group-item-action linkConfig">  
					  		
					  	{{ $table }} </a>
					  @endforeach
					  
					</div>
				</div>	
			</div>
			<div class="col-md-9" style="margin:  0;">	
				<div class="card" style="padding-bottom: 0 !important;">
					<div class="card-body">
						<div class="table-database">			
							<div class="tableconfig" style=" padding:10px; min-height:550px; ">
						</div>		
					</div>
				</div>		
    		</div>
    	</div>	

<style type="text/css">
	.table-database {
		height: calc(100vh - 105px ) !important;
		overflow: auto;
	}
</style>
 <script type="text/javascript" src="{{ asset('sximo5/js/simpleclone.js') }}"></script>

<script type="text/javascript">
$(document).ready(function(){
	jQuery('.table-scroll').scrollbar();
	$('.linkConfig').click(function(){
		$('.ajaxLoading').show();
		var url =  $(this).attr('href');
		$.get( url , function( data ) {
			$( ".tableconfig" ).html( data );
			$('.ajaxLoading').hide();
			
			
		});
		return false;
	});
});

function droptable()
{
	if(confirm('are you sure remove selected table(s) ?'))
	{
		$('#removeTable').submit();
	} else {
		return false;
	}
}

</script>
@endsection