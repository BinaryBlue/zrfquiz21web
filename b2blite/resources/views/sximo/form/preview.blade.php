 {!! Form::open(array('url'=>'home/submit', 'class'=>'','files' => true , 'parsley-validate'=>'','novalidate'=>' ' ,'id'=>'formField')) !!}
 	@if(Session::has('status') )
 		@if(session('status') =='success')
	 		<p class="alert alert-success text-center">
	 		{{ session('message') }}
	 		</p> 		
 		@else
	 		<p class="alert alert-error text-center">
	 			{{ session('message') }}
	 		</p>

		<ul class="parsley-error-list">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>

	 	@endif	
 	@endif

	<div class="form-horizontal">
	@foreach($preview as $form)
		<div class="form-group row">
			<label class="text-right col-md-3"> {{ $form['title']}} </label>			
	       	<div class="col-md-9">
	       		{!! $form['form'] !!} 
	       	</div>       
		</div>
	@endforeach	
		<div class="form-group row">	
			<label class="text-right col-md-3">  </label>	
			<div class="col-md-9">			
	       		<button class="btn btn-primary"> Submit Form </button>      
	       	</div>
		</div>
	</div>
	<input type="hidden" name="form_builder_id" value="{{ $form_builder_id }}">
{!! Form::close() !!}
<script type="text/javascript">
	$('#formField').parsley();
</script>