@extends('layouts.app')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small> {{ $pageNote }} </small> </h2></div>
<div class="toolbar-nav">
	<a href="{{ url('sximo/rac?return='.$return) }}" class="tips btn btn-sm  " title="{{ __('core.btn_back') }}"><i class="fa  fa-times"></i></a>
</div>	
<div class="card">
	<div class="card-body">	

		<ul class="parsley-error-list">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>	

		 {!! Form::open(array('url'=>'sximo/rac?return='.$return, 'class'=>'form-horizontal form-material validated','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}
		 <div class="row">
<div class="col-md-12">
						<fieldset><legend> RestAPI Client</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									  <div class="form-group row  " >
										<label for="Apiuser" class=" control-label col-md-4 text-left"> Apiuser </label>
										<div class="col-md-7">
										  <select name='apiuser' rows='5' id='apiuser' class='select2 form-control form-control-sm ' required="true"   ></select> 
										 </div> 
										 <div class="col-md-1">
										 	
										 </div>
									  </div> 					
										@if($row['id'] !='')
											<div class="form-group row  " >
												<label for="Apikey" class=" control-label col-md-4 text-left"> 
												Api Key </label>
												<div class="col-md-6">
												  {!! Form::text('apikey', $row['apikey'],array('class'=>'form-control', 'placeholder'=>'','readonly'=>'1' ,'style'=>'background : #f0f0f0 !important;'   )) !!} 
												 <p><i>  Use this apikey with useremail as basic authorization access to all your registered modules </i> </p>
												 </div> 
												 <div class="col-md-2">
												 	
												 </div>
											</div> 
										@endif
				
									  <div class="form-group row  " >
										<label for="Modules" class=" control-label col-md-4 text-left"> Modules </label>
										<div class="col-md-7">
										  <select name='modules[]' multiple rows='5' id='modules' required="true" class='select2 form-control form-control-sm'   ></select> 
										 </div> 
										 <div class="col-md-1">
										 	
										 </div>
									  </div> 

									  <div class="form-group row">
					<label class="col-sm-4 text-right">&nbsp;</label>
					<div class="col-sm-8">	
					<button type="submit" name="apply" class="btn btn-info btn-sm" ><i class="icon-checkmark-circle2"></i> {{ __('core.sb_apply') }}</button>
					<button type="submit" name="submit" class="btn btn-primary btn-sm" ><i class="icon-bubble-check"></i> {{ __('core.sb_save') }}</button>
					<button type="button" onclick="location.href='{{ URL::to('sximo/rac?return='.$return) }}' " class="btn btn-warning btn-sm "><i class="icon-cancel-circle2 "></i>  {{ __('core.sb_cancel') }} </button>
					</div>	  
			
				  </div>
									  	 {!! Form::hidden('created', $row['created']) !!}	
							</fieldset>
			</div>
					
				   
		 <input type="hidden" name="action_task" value="save" />
		</div>
		 {!! Form::close() !!}
	</div>	 
</div>		 
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		
		$("#apiuser").jCombo("{!! url('sximo/rac/comboselect?filter=tb_users:id:email') !!}",
		{  selected_value : '{{ $row["apiuser"] }}' });
		
		$("#modules").jCombo("{!! url('sximo/rac/comboselect?filter=tb_module:module_name:module_title&limit=WHERE:module_type:!=:core') !!}",
		{  selected_value : '{{ $row["modules"] }}' });	
		
	});
	</script>		 
@stop