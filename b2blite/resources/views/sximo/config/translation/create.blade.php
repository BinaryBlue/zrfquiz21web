 {!! Form::open(array('url'=>'sximo/config/addtranslation/', 'class'=>'form-horizontal   validated')) !!}
 <div class="">
    <div class="form-group row">
      <label for="ipt" class=" control-label col-md-4"> Languange Name </label>
    	<div class="col-md-8">
    	<input name="name" type="text" id="name" class="form-control form-control-sm" value="" required="true" /> 
    	</div> 
    </div>   	
   
    <div class="form-group row">
      <label for="ipt" class=" control-label col-md-4"> Folder Name </label>
  	<div class="col-md-8">
  	<input name="folder" type="text" id="folder" class="form-control form-control-sm" value="" required /> 
  	 </div> 
    </div>   	
    
     <div class="form-group row">
      <label for="ipt" class=" control-label col-md-4"> Author </label>
  	<div class="col-md-8">
  	<input name="author" type="text" id="author" class="form-control form-control-sm" value="" required /> 
  	 </div> 
    </div>   	
    
    <div class="form-group row">
      <label for="ipt" class=" control-label col-md-4">  </label>
    	<div class="col-md-8">
    		<button type="submit" name="submit" class="btn btn-info btn-sm"> Submit </button>
    	</div> 
    </div>  
  </div> 	    
 
 {!! Form::close() !!}