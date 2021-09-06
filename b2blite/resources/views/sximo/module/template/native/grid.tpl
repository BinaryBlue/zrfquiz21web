@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>

<div class="card">
	<div class="card-body">
		<div class="toolbar-nav" >   
			<div class="row">
				<div class="col-md-4 col-4">
					<div class="input-group ">
					      
					      <input type="text" class="form-control form-control-sm onsearch" data-target="{{ url($pageModule) }}" aria-label="..." placeholder=" Type And Hit Enter ">
					    </div>
				</div> 
				<div class="col-md-8 col-8  text-right"> 	
					<div class="btn-group">
						@if($access['is_add'] ==1)
						<a href="{{ url('{class}/create?return='.$return) }}" class="btn  btn-sm btn-primary"  
							title="{{ __('core.btn_create') }}"><i class="fas fa-plus"></i> {{ __('core.btn_create') }}</a>
						@endif
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-bars"></i> Bulk Action </button>
					        <ul class="dropdown-menu">
					        @if($access['is_remove'] ==1)
								 <li class="nav-item"><a href="javascript://ajax"  onclick="SximoDelete();" class="nav-link tips" title="{{ __('core.btn_remove') }}">
								Remove Selected </a></li>
							@endif 
							@if($access['is_add'] ==1)
								<li class="nav-item"><a href="javascript://ajax" class=" copy nav-link " title="Copy" > Copy selected</a></li>
								<div class="dropdown-divider"></div>
								<li class="nav-item"><a href="{{ url($pageModule .'/import?return='.$return) }}" onclick="SximoModal(this.href, 'Import CSV'); return false;" class="nav-link "> Import CSV</a></li>

								
							@endif
							<div class="dropdown-divider"></div>
					        @if($access['is_excel'] ==1)
								<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=excel&return='.$return) }}" class="nav-link "> Export Excel </a></li>	
							@endif
							@if($access['is_csv'] ==1)
								<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=csv&return='.$return) }}" class="nav-link "> Export CSV </a></li>	
							@endif
							@if($access['is_pdf'] ==1)
								<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=pdf&return='.$return) }}" class="nav-link "> Export PDF </a></li>	
							@endif
							@if($access['is_print'] ==1)
								<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=print&return='.$return) }}" class="nav-link "> Print Document </a></li>	
							@endif
							<div class="dropdown-divider"></div>
							<li class="nav-item"><a href="{{ url($pageModule) }}"  class="nav-link "> Clear Search </a></li>
					          	
					        
					          
					        </ul>
					    </div>   
				    </div>
				</div>
				   
			</div>	

		</div>	

			<!-- Table Grid -->
			
 			{!! Form::open(array('url'=>'{class}?'.$return, 'class'=>'form-horizontal m-t' ,'id' =>'SximoTable' )) !!}
			<div class="table-responsive">
		    <table class="table  table-hover table-striped  " id="{{ $pageModule }}Table">
		        <thead>
					<tr>
						<th style="width: 3% !important;" class="number"> No </th>
						<th  style="width: 3% !important;"> 
							<input type="checkbox" class="checkall filled-in" id="checked-all"  />
							<label for="checked-all"></label>
						</th>
						
						
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
							<td class="thead"> {{ ++$i }} </td>
							<td class="tcheckbox">
								<input type="checkbox" class="ids filled-in" name="ids[]" value="{{ $row->{key} }}" id="val-{{ $row->{key} }}" /> 
								<label for="val-{{ $row->{key} }}"></label>
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
								  <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="fas fa-tasks"></i>  </button>
								  <ul class="dropdown-menu">
								 	@if($access['is_detail'] ==1)
									<li class="nav-item"><a href="{{ url('{class}/'.$row->{key}.'?return='.$return)}}" class="nav-link tips" title="{{ __('core.btn_view') }}"> {{ __('core.btn_view') }} </a></li>
									@endif
									@if($access['is_edit'] ==1)
									<li class="nav-item"><a  href="{{ url('{class}/'.$row->{key}.'/edit?return='.$return) }}" class="nav-link  tips" title="{{ __('core.btn_edit') }}"> {{ __('core.btn_edit') }} </a></li>
									@endif
									<div class="dropdown-divider"></div>
									@if($access['is_remove'] ==1)
										<li class="nav-item"><a href="javascript://ajax"  onclick="SximoDelete();" class="nav-link  tips" title="{{ __('core.btn_remove') }}">
										Remove Selected </a></li>
									@endif 
								  </ul>
								</div>

							</td>		 
		                </tr>
						
		            @endforeach
		              
		        </tbody>
		      
		    </table>
		    </div>
			<input type="hidden" name="action_task" value="" />
			
			{!! Form::close() !!}
			
			
			<!-- End Table Grid -->

		</div>
		@include('footer')
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
