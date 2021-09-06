@extends('layouts.app')

@section('content')
<div class="page-header"><h2> {{ $pageTitle }} <small> {{ $pageNote }} </small> </h2></div>
<div class="toolbar-nav">
	<div class="row">
		<div class="col-md-6" >
			@if($access['is_add'] ==1)
	   		<a href="{{ url('{class}/'.$id.'/edit?return='.$return) }}" class="tips btn btn-sm  " title="{{ __('core.btn_edit') }}"><i class="fa  fa-pencil-alt"></i></a>
			@endif
			<a href="{{ url('{class}?return='.$return) }}" class="tips btn btn-sm  " title="{{ __('core.btn_back') }}"><i class="fa  fa-times"></i></a>		
		</div>
		<div class="col-md-6 text-right" >
	   		<a href="{{ ($prevnext['prev'] != '' ? url('{class}/'.$prevnext['prev'].'?return='.$return ) : '#') }}" class="tips btn btn-sm"><i class="fa fa-arrow-left"></i>  </a>	
			<a href="{{ ($prevnext['next'] != '' ? url('{class}/'.$prevnext['next'].'?return='.$return ) : '#') }}" class="tips btn btn-sm "> <i class="fa fa-arrow-right"></i>  </a>					
		</div>
	</div>
</div>		

	<!-- Nav tabs -->
		<ul class="nav nav-tabs form-tab" role="tablist">
			<li class="nav-item"><a href="#home" aria-controls="tab-home" role="tab" data-toggle="tab" class="nav-link active"><b>{{ $pageTitle }} : </b>  View Detail </a></li>
		@foreach($subgrid as $sub)
			<li class="nav-item"><a href="#{{ str_replace(" ","_",$sub['title']) }}" aria-controls="tab-{{ str_replace(" ","_",$sub['title']) }}" role="tab" data-toggle="tab" class="nav-link"><b>{{ $pageTitle }}</b>  : {{ $sub['title'] }}</a></li>
		@endforeach
		</ul>

		<!-- Tab panes -->
		<div class="tab-content ">
			<div role="tabpanel" class="tab-pane active" id="home">
			<table class="table table-bordered" >
				<tbody>	
			{form_view}
					
				</tbody>	
			</table>  
		</div>

		@foreach($subgrid as $sub)
			<div role="tabpanel" class="tab-pane" id="{{ str_replace(" ","_",$sub['title']) }}"></div>
		@endforeach	
	 	


<script type="text/javascript">
	$(function(){
		<?php for($i=0 ; $i<count($subgrid); $i++)  :?>
			$('#{{ str_replace(" ","_",$subgrid[$i]['title']) }}').load('{{ url("{class}/lookup?param=".implode("-",$subgrid["$i"])."-".$id)}}')
		<?php endfor;?>
	})

</script>
	  
@stop