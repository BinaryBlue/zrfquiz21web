{!! Form::open(array('url'=>'home/proccess/{form_ID}', 'id'=>'formconfiguration','class'=>'form-vertical' ,'files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}
@if(Session::has('message'))	  
		{!! Session::get('message') !!}
@endif	
	
<ul class="parsley-error-list">
	@foreach($errors->all() as $error)
		<li>{{ $error }}</li>
	@endforeach
</ul>

{forms}
		<div class="form-group row  " >					
				<button type="submit" name="submit" class="btn btn-primary"><i class="icon-checkmark-circle2"></i> Submit </button>
		</div>

{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function(){
	});

	{javascript}
</script>