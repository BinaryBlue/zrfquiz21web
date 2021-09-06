
<div class=" form-vertical">
	
		<div class="form-group">
			<label class=""> Name / Title </label>			
	        <input type="text" name="name" value="" class="form-control input-sm" />		       
		</div>
	
		<div class="form-group">
			<label class=""> Save Metod </label>			
	        <select class="form-control input-sm " name="method">
	        	<option value=""> -- Select Method -- </option>
	        	<option value=""> Database </option>
	        	<option value=""> Send To Email </option>
	        </select>   
		</div>
	
		<div class="form-group">
			<label class=""> Email / Database </label>			
	        <input type="text" name="email" value="" class="form-control input-sm" />		       
		</div>
	



		<div class="form-group">
			<label class=""> Success Note </label>			
	       	<input type="text" name="success" value="" class="form-control input-sm" />	      
		</div>

		<div class="form-group">
			<label class=""> Error / Failed Note </label>			
	        <input type="text" name="failed" value="" class="form-control input-sm" />	
		</div>

		<div class="form-group">
			<label class=""> Redirect After Success </label>			
	        <input type="text" name="redirect" value="" class="form-control input-sm" />		       
		</div>

	</div>



<a href="{{ url('root/form/field') }}" onclick="SximoModal(this.href,'Input Field'); return false;" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Save Form Info </a>
