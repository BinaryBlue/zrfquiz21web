 @extends('layouts.app')

@section('content')
<div class="page-header"><h2> {{ $pageTitle }}</h2></div>
<div class="card">
	<div class="card-body">


		@include('sximo.config.tab')

	<div class="pt-5"> 
	 {!! Form::open(array('url'=>'sximo/config/email/', 'class'=>'form-vertical row validated')) !!}
		
	<div class="col-sm-6 animated fadeInRight">
		<div class="form-group">
			<label for="ipt" class=" control-label">{{ Lang::get('core.registernew') }} </label>		
			<textarea rows="20" name="regEmail" class="form-control form-control-sm  markItUp">{{ $regEmail }}</textarea>	
		</div>  
				

		<div class="form-group">   
			<button class="btn btn-primary" type="submit"> {{ Lang::get('core.sb_savechanges') }}</button>	 
		</div>
		
	</div> 


	<div class="col-sm-6 animated fadeInRight">		
		<div class="form-group">
			<label for="ipt" class=" control-label "> {{ Lang::get('core.forgotpassword') }}  </label>					
			<textarea rows="20" name="resetEmail" class="form-control form-control-sm markItUp">{{ $resetEmail }}</textarea>					 
		</div> 

		<div class="form-group">
			<button class="btn btn-primary" type="submit">{{ Lang::get('core.sb_savechanges') }}</button>
		</div> 
		 
	</div>	  

	   {!! Form::close() !!}
	</div>
</div>
</div>

@stop





