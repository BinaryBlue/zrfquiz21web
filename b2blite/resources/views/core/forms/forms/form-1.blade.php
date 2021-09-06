{!! Form::open(array('url'=>'home/proccess/1', 'id'=>'formconfiguration','class'=>'form-vertical' ,'files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}
@if(Session::has('message'))	  
		{!! Session::get('message') !!}
@endif	
	
<ul class="parsley-error-list">
	@foreach($errors->all() as $error)
		<li>{{ $error }}</li>
	@endforeach
</ul>

<div class="form-group row  " >
					<label for="ipt" class="  "> Full Name  </label>
				{!! Form::text('name','',array('class'=>'form-control', 'placeholder'=>'', 'required'=>'true'  )) !!}
		</div>

		<div class="form-group row  " >
					<label for="ipt" class="  "> Subject  </label>
				{!! Form::text('subject','',array('class'=>'form-control', 'placeholder'=>'', 'required'=>'true'  )) !!}
		</div>

		<div class="form-group row  " >
					<label for="ipt" class="  "> Email Address  </label>
				{!! Form::text('email','',array('class'=>'form-control', 'placeholder'=>'', 'required'=>'true', 'parsley-type'=>'email'   )) !!}
		</div>

		<div class="form-group row  " >
					<label for="ipt" class="  "> Your Message  </label>
				<textarea name='message' rows='5' id='message' class='form-control '  
				         required  ></textarea>
		</div>

		
		<div class="form-group row  " >					
				<button type="submit" name="submit" class="btn btn-primary"><i class="icon-checkmark-circle2"></i> Submit </button>
		</div>

{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function(){
	});

	
</script>