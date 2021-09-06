@extends('layouts.app')

@section('content')
<section class="page-header row">
	<h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
	<ol class="breadcrumb">
		<li><a href="{{ url('') }}"> Dashboard </a></li>
		<li><a href="{{ url($pageModule) }}"> {{ $pageTitle }} </a></li>
		<li class="active"> Form  </li>		
	</ol>
</section>
<div class="page-content row">
	<div class="page-content-wrapper no-margin">

	<div class="sbox">
		<div class="sbox-title">
			<h1> Form Update </h1>
		</div>	
		<div class="sbox-content">
			<div class="row">
				<div class="col-md-6 text-left" >
					<a href="{{ url($pageModule.'?return='.$return) }}" class="tips btn btn-sm btn-default "  title="{{ __('core.btn_back') }}" ><i class="fa  fa-arrow-left"></i></a> 
				</div>
				<div class="col-md-6 text-right" >
					@if(Session::get('gid') ==1)
						<a href="{{ URL::to('sximo/module/config/'.$pageModule) }}" class="tips btn btn-sm btn-default " title=" {{ __('core.btn_config') }}" ><i class="fa  fa-ellipsis-v"></i></a>
					@endif 			
				</div> 
			</div>

			{!! Form::open(array('url'=>'notification?return='.$return, 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}
<div class="col-md-12">
						<fieldset><legend> Notification</legend>
									
									  <div class="form-group  " >
										<label for="Date" class=" control-label col-md-4 text-left"> Date </label>
										<div class="col-md-6">
										  
				<div class="input-group m-b" style="width:150px !important;">
					{!! Form::text('created', $row['created'],array('class'=>'form-control input-sm date')) !!}
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div> 
										 </div> 
										 <div class="col-md-2">
										 	
										 </div>
									  </div> 					
									  <div class="form-group  " >
										<label for="Url" class=" control-label col-md-4 text-left"> Url </label>
										<div class="col-md-6">
										  <input  type='text' name='url' id='url' value='{{ $row['url'] }}' 
						     class='form-control input-sm ' /> 
										 </div> 
										 <div class="col-md-2">
										 	
										 </div>
									  </div> 					
									  <div class="form-group  " >
										<label for="Note" class=" control-label col-md-4 text-left"> Note </label>
										<div class="col-md-6">
										  <input  type='text' name='note' id='note' value='{{ $row['note'] }}' 
						     class='form-control input-sm ' /> 
										 </div> 
										 <div class="col-md-2">
										 	
										 </div>
									  </div> </fieldset>
			</div>
			
			

		
			<div style="clear:both"></div>	
				
					
				  <div class="form-group">
					<label class="col-sm-4 text-right">&nbsp;</label>
					<div class="col-sm-8">	
					<button type="submit" name="apply" class="btn btn-success " > {{ __('core.sb_apply') }}</button>
					<button type="submit" name="submit" class="btn btn-primary " > {{ __('core.sb_save') }}</button>
					<button type="button" onclick="location.href='{{ url('notification?return='.$return) }}' " class="btn btn-danger  ">  {{ __('core.sb_cancel') }} </button>
					</div>	  
			
				  </div> 

		 	<input type="hidden" name="action_task" value="save" />
		 	{!! Form::close() !!}
		</div>
	</div>
	</div>
</div>		
	
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		
		 		 

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("notification/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop