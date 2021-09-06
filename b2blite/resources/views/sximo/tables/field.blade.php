{!! Form::open(array('url'=>'sximo/tables/tablefieldsave/'.$table, 'class'=>'form-horizontal sximo-form','id'=>'columnTable' )) !!}
	<input type="hidden" value="{{ isset($field) ? $field : ''}}" name="currentfield">
	<div class="form-group row">
		<label class="col-md-4">Column Name </label>
		<div class="col-md-8">
			<input type="text" name="field" value="{{ isset($field) ? $field : ''}}" class="form-control form-control-sm">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4"> DataType </label>
		<div class="col-md-8">
	        <select name="type" class="form-control form-control-sm" >
				@foreach($tbtypes as $t)
				 <option value="{{ $t }}" @if(isset($type) && $type ==$t) selected="selected" @endif >{{ $t }}</option>
				@endforeach
	        </select>	
        </div>
	</div>
	<div class="form-group row">
		<label class="col-md-4">Lenght/Values </label>
		<div class="col-md-8">
			<input type="text" name="lenght" value="{{ isset($lenght) ? $lenght : ''}}" class="form-control form-control-sm">
		</div>	
	</div>
	<div class="form-group row">
		<label class="col-md-4"> Default </label>
		<div class="col-md-8">
			<input type="text" name="default" value="{{ isset($default) ? $default : ''}}" class="form-control form-control-sm">
		</div>	
	</div>		

	<div class="form-group row">
		<label class="col-md-4"> Option  </label>
		<div class="col-md-8">
			
			<input type="checkbox" name="null" value="1" @if(isset($notnull) && $notnull =='NO') checked="checked" @endif class="filled-in" id="nn" /><label for="nn" > Not Null ?</label>
			<input type="checkbox" name="key" value="1"  @if(isset($key) && $key =='PRI') checked="checked" @endif class="filled-in" id="pk" /> <label for="pk"> Primary Key  ?</label>
			<input type="checkbox" name="ai" value="1" @if(isset($ai) && $ai =='auto_increment') checked="checked" @endif class="filled-in" id="ai" /><label for="ai" > Autoincrement </label>
		</div>	
		
		
	</div>	

	<div class="form-group row mt-2">
		<label class="col-md-4">  </label>
		<div class="col-md-8">
			<button type="submit" class="btn btn-sm btn-primary"> Save Column</button>
		</div>	
	</div>

{!! Form::close() !!}

  <script type="text/javascript">
 $(document).ready(function(){
 		var form = $('#columnTable');
		form.parsley();
		form.submit(function(){
			
			if(form.parsley().isValid()){			
				var options = { 
					dataType:      'json', 
					beforeSubmit :  showRequest,
					success:       showResponse  
				}  
				$(this).ajaxSubmit(options); 
				return false;
							
			} else {
				return false;
			}		
		
		});	
 });
function showRequest()
{
	$('.ajaxLoading').show();
}  
function showResponse(data)  {		
	
	if(data.status == 'success')
	{
		url = "{{ URL::TO('sximo/tables/tableconfig/'.$table) }}";	
		$.get( url , function( data ) {
			$('#sximo-modal').modal('hide');
			$( ".tableconfig" ).html( data );
			$('.ajaxLoading').hide();
			
				
		});
	
	} else {
		alert(data.message);
	}	
	$('.ajaxLoading').hide();
} 

</script>	