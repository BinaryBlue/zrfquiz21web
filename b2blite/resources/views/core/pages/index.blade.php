@extends('layouts.app')

@section('content')
<?php usort($tableGrid, "SiteHelpers::_sort") ?>
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div  class="card">
	<div class="card-body">


			<div class="toolbar-nav">
			<!-- Toolbar Top -->
			<div class="row">
				<div class="col-md-4 t">
					
				</div>
				<div class="col-md-8 text-right"> 	
					<div class="btn-group">
						 @if($access['is_add'] ==1)
						<a href="{{ url('cms/pages/create?return='.$return) }}" class="btn btn-info btn-sm"  
							title="{{ __('core.btn_create') }}"><i class="fas fa-plus"></i></a>
						@endif 
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Bulk Action </button>
					        <ul class="dropdown-menu">
					         @if($access['is_excel'] ==1)
								<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=excel&return='.$return) }}" class="tips nav-link"> Export CSV </a></li>	
							@endif
							@if($access['is_add'] ==1)
								<li class="nav-item"><a href="{{ url($pageModule .'/import?return='.$return) }}"  class=" nav-link" onclick="SximoModal(this.href, 'Import CSV'); return false;"> Import CSV</a></li>
								<li class="nav-item"><a href="javascript://ajax" class=" copy nav-link" title="Copy" > Copy selected</a></li>
							@endif	
								<li class="nav-item"><a href="{{ url($pageModule) }}"  class=" nav-link"> Clear Search </a></li>
					          	<li role="separator" class="divider"></li>
					         @if($access['is_remove'] ==1)
								 <li class="nav-item"><a href="javascript://ajax"  onclick="SximoDelete();" class="tips nav-link" title="{{ __('core.btn_remove') }}">
								Remove Selected </a></li>
							@endif 
					          
					        </ul>
					    </div>  
					</div>
				</div>
				    
			</div>			
			</div>		
			<!-- End Toolbar Top -->

			<!-- Table Grid -->
			<div class="table-responsive" >
 			{!! Form::open(array('url'=>'cms/pages?'.$return, 'class'=>'form-horizontal m-t' ,'id' =>'SximoTable' )) !!}
			
		    <table class="table table-hover table-striped" id="{{ $pageModule }}Table">
		        <thead>
					<tr>
						<th style="width: 3% !important;" class="number"> No </th>
						<th  style="width: 3% !important;"> 
							<input type="checkbox" class="checkall filled-in " id="checked-all"/>
							<label for="checked-all"></label></th>
						
						
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
						<th  style="width: 10% !important;">{{ __('core.btn_action') }}</th>
						
					  </tr>
		        </thead>

		        <tbody>        						
		            @foreach ($rowData as $row)
		                <tr>
							<td > {{ ++$i }} </td>
							<td >
								<input type="checkbox" class="ids filled-in" name="ids[]" value="{{ $row->pageID }}" id="val-{{ $row->pageID }}" />
								<label for="val-{{ $row->pageID }}"></label>
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
						 <td>

							 	<div class="dropdown">
								  <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> {{ __('core.btn_action') }} </button>
								  <ul class="dropdown-menu">
								 	@if($access['is_detail'] ==1)
									<li class="nav-item"><a href="{{ url($row->alias)}}" target="_blank" class="nav-link tips nav-link" title="{{ __('core.btn_view') }}"> {{ __('core.btn_view') }} </a></li>
									@endif
									@if($access['is_edit'] ==1)
									<li class="nav-item"><a  href="{{ url('cms/pages/'.$row->pageID.'/edit?return='.$return) }}" class="nav-link tips nav-link" title="{{ __('core.btn_edit') }}"> {{ __('core.btn_edit') }} </a></li>
									@endif
									<li class="divider" role="separator"></li>
									@if($access['is_remove'] ==1)
										 <li class="nav-item"><a href="javascript://ajax"  onclick="SximoDelete();" class="nav-link tips nav-link" title="{{ __('core.btn_remove') }}">
										Remove Selected </a></li>
									@endif 
								  </ul>
								</div>

							</td>			 
		                </tr>
						
		            @endforeach
		              
		        </tbody>
		      
		    </table>
			<input type="hidden" name="action_task" value="" />
			
			{!! Form::close() !!}
			
			</div>
			<!-- End Table Grid -->
			@include('footer')


			</div>
	</div>
</div>


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