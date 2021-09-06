@extends('layouts.app')

@section('content')
{{--*/ usort($tableGrid, "SiteHelpers::_sort") /*--}}
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div  class="card">
	<div class="card-body">
		<div class="toolbar-nav">
			<div class="row">
				<div class="col-md-6">
					<div class="btn-group">
						<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i> Bulk Action </button>
				        <ul class="dropdown-menu">
				         @if($access['is_excel'] ==1)
							<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=excel&return='.$return) }}" class="nav-link"> Export CSV </a></li>	
						@endif
				        </ul>
				    </div>	
				</div>    
			</div>
		<div >

	


		 {!! Form::open(array('url'=>'{class}/delete/', 'class'=>'form-horizontal' ,'id' =>'SximoTable' )) !!}
		 <div class="table-responsive" style="min-height:300px;">
	    <table class="table table-hover table-striped  ">
	        <thead>
				<tr>
					<th class="number"> No </th>			
					@foreach ($tableGrid as $t)
						@if($t['view'] =='1')
							<th>{{ $t['label'] }}</th>
						@endif
					@endforeach
				  </tr>
	        </thead>

	        <tbody>
							
	            @foreach ($rowData as $row)
	                <tr>
						<td width="30"> {{ ++$i }} </td>	
					@foreach ($tableGrid as $field)
						 @if($field['view'] =='1')
						 	<?php $limited = isset($field['limited']) ? $field['limited'] :''; ?>
						 	@if(SiteHelpers::filterColumn($limited ))
							 <td>					 
							 	{!! SiteHelpers::formatRows($row->{$field['field']},$field ,$row ) !!}						 
							 </td>
							@endif	
						 @endif					 
					 @endforeach				 
					
	                </tr>
					
	            @endforeach
	              
	        </tbody>
	      
	    </table>
		<input type="hidden" name="md" value="" />
		</div>
		{!! Form::close() !!}
		
		
		@include('footer')
	</div>  
</div>
@stop