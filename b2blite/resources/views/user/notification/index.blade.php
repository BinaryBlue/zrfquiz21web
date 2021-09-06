@extends('layouts.app')

@section('content')
{{--*/ usort($tableGrid, "SiteHelpers::_sort") /*--}}
<div class="page-header"><h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2></div>

			<div class="toolbar-nav">
			<!-- Toolbar Top -->
				<div class="row">
					<div class="col-md-8"> 	

						<div class="btn-group">
							<button type="button" class="btn  btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-menu5"></i> Bulk Action </button>
					        <ul class="dropdown-menu">
					        
					         @if($access['is_remove'] ==1)
								 <li class="nav-item"><a href="javascript://ajax"  onclick="SximoDelete();" class="nav-link tips" title="{{ __('core.btn_remove') }}"><i class="fa fa-trash-o"></i>
								Remove Selected </a></li>
							@endif 
					          
					        </ul>
					    </div>    
					</div>
					<div class="col-md-4 text-right">
						
						      <input type="text" class="form-control form-control-sm onsearch" data-target="{{ url($pageModule) }}" aria-label="..." placeholder=" Type And Hit Enter ">
						   
					</div>    
				</div>		
			</div>				
			<!-- End Toolbar Top -->

			<!-- Table Grid -->
			<div class="card">
			<div class="table-responsive" >
 			{!! Form::open(array('url'=>'notification?'.$return, 'class'=>'form-horizontal m-t' ,'id' =>'SximoTable' )) !!}
			
		    <table class="table table-hover " id="{{ $pageModule }}Table">
		        <thead>
					<tr>
						<th style="width: 3% !important;" class="number"> No </th>
						<th  style="width: 3% !important;">
							<input type="checkbox" class="checkall minimal-green" id="checkall" />
							<label for="checkall"> </label>
						</th>
						<th  style="width: 10% !important;">{{ __('core.btn_action') }}</th>
						
						@foreach ($tableGrid as $t)
							@if($t['view'] =='1')				
								<?php $limited = isset($t['limited']) ? $t['limited'] :''; 
								if(SiteHelpers::filterColumn($limited ))
								{
									$addClass='class="tbl-sorting" ';
									if($insort ==$t['field'])
									{
										$dir_order = ($inorder =='desc' ? 'sort-desc' : 'sort-asc'); 
										$addClass='class="tbl-sorting '.$dir_order.'" ';
									}
									echo '<th align="'.$t['align'].'" '.$addClass.' width="'.$t['width'].'">'.\SiteHelpers::activeLang($t['label'],(isset($t['language'])? $t['language'] : array())).'</th>';				
								} 
								?>
							@endif
						@endforeach
						
					  </tr>
		        </thead>

		        <tbody>        						
		            @foreach ($rowData as $row)
		                <tr>
							<td > {{ ++$i }} </td>
							<td ><input type="checkbox" class="ids minimal-green" name="ids[]" value="{{ $row->id }}" value="{{ $row->id }}" />  <label for="v-{{ $row->id }}"> </label></td>
							<td>

							 	<div class="dropdown">
								  <button class="btn  dropdown-toggle" type="button" data-toggle="dropdown"> Action </button>
								  <ul class="dropdown-menu">
								 	@if($access['is_detail'] ==1)
									<li class="nav-item"><a href="{{ url('notification/'.$row->id.'?return='.$return)}}" class="nav-link tips" title="{{ __('core.btn_view') }}"> {{ __('core.btn_view') }} </a></li>
									@endif
									@if($access['is_edit'] ==1)
									<li class="nav-item"><a  href="{{ url('notification/'.$row->id.'/edit?return='.$return) }}" class="nav-link tips" title="{{ __('core.btn_edit') }}"> {{ __('core.btn_edit') }} </a></li>
									@endif
									<li class="divider" role="separator"></li>
									@if($access['is_remove'] ==1)
										 <li class="nav-item"><a href="javascript://ajax"  onclick="SximoDelete();" class="nav-link tips" title="{{ __('core.btn_remove') }}">
										Remove Selected </a></li>
									@endif 
								  </ul>
								</div>

							</td>														
						 @foreach ($tableGrid as $field)
							 @if($field['view'] =='1')
							 	<?php $limited = isset($field['limited']) ? $field['limited'] :''; ?>
							 	@if(SiteHelpers::filterColumn($limited ))
							 	 <?php $addClass= ($insort ==$field['field'] ? 'class="tbl-sorting-active" ' : ''); ?>
								 <td align="{{ $field['align'] }}" width=" {{ $field['width'] }}"  {!! $addClass !!} >					 
								 	{!! SiteHelpers::formatRows($row->{$field['field']},$field ,$row ) !!}						 
								 </td>
								@endif	
							 @endif					 
						 @endforeach			 
		                </tr>
						
		            @endforeach
		              
		        </tbody>
		      
		    </table>
			<input type="hidden" name="action_task" value="" />
			
			{!! Form::close() !!}
			
			</div>
		</div>

			<!-- End Table Grid -->
@include('footer')


<script>
$(document).ready(function(){
	$('.copy').click(function() {
		var total = $('input[class="ids"]:checkbox:checked').length;
		if(confirm('are u sure Copy selected rows ?'))
		{
			$('input[name="action_task"]').val('copy');
			$('#SximoTable').submit();// do the rest here	
		}
	})	
	
});	
</script>	
	
@stop
