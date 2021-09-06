{!! Form::open(array('url'=>'sximo/server?do=install', 'class'=>'form-horizontal well','files' => true , 'parsley-validate'=>'','novalidate'=>' ' ,'id'=>'doUpdate')) !!}
<div class="card">
	<div class="card-body">
	<p> Please make sure you have account credit at <a href="http://sximo.net" target="_blank"> Sximo NET </a></p>
<div class="form-group row">
	<label class=" control-label col-3">
		Email Address
	</label>
	<div class="col-9">
		<input name="email" class="form-control form-control-sm" required="true" type="text" />
	</div>
</div>
<div class="form-group row">
	<label class=" control-label col-3">
		Password
	</label>
	<div class="col-9">
		<input name="password" class="form-control form-control-sm"  required="true" type="password"  />
	</div>
</div>		                    		


<div class="form-group row">
	<label class=" control-label col-3">
		
	</label>
	<div class="col-9">
		<button class="btn btn-primary btn-sm processing"> Download  & Install  </button>
	</div>
</div>		
<input type="hidden" name="ProductID"  value="{{ $id }}">
<input type="hidden" name="do" value="install">
<input type="hidden" name="type" value="{{ $type }}">
</div></div>
 {!! Form::close() !!}	

 <script type="text/javascript">
$(function(){
	var form = $('#doUpdate'); 
    form.parsley();
    form.submit(function()
    {         
      if (form.parsley().isValid())
      {      
        var options = { 
          dataType:      'json', 
          beforeSubmit : function() {
				$('.failed-update').hide()
				$('.authen-update').hide();
				$('.progress-update').show();
				$('.progress-result').html('');
				$('.processing').html(' Processing ..... please wait')
          },
          success: function( data ) {
	          if(data.status == 'success')
	          {     
	         	 notyMessage(data.message);
	         	 $.get('{{ url("sximo/config/clearlog") }}',function(){})
	         	 $('.processing').html('Download & Install Now')
	          	
	          } else {
	          	$('.failed-authen').show();
	          	$('.progress-update').hide();
	            notyMessageError(data.message);
	          	$('.processing').html('Download & Install Now')
	           
	          }
          }  
        }  
        $(this).ajaxSubmit(options); 
        return false;                 
	} 
	else {
		notyMessageError('Error ajax wile submiting !');
		return false;
	}      
	});	
})
</script> 