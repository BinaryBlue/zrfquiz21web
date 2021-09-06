
	<div class="text-center">
		 <h1> Sximo Builder  </h1>
		 <p> Version : {{ ucwords($version['Version']) }} <br />
		 	Build Date :   {{ date("F d,Y ",strtotime($version['Build'])) }}
		 </p>
		 <p> <a href="javascript://ajax" class= "btn btn-primary btn-sm autoupdate "><i class="fa fa-recycle"></i> Check for updates  .</a>  </p>

	</div>

			
			  		
							
					  	

<div class="authen-update available" style="display: none; background: none; padding: 20px;" >
		 {!! Form::open(array('url'=>'sximo/server', 'class'=>'form-horizontal ','files' => true , 'parsley-validate'=>'','novalidate'=>' ' ,'id'=>'doUpdate')) !!}

		 <div class="m-t m-b text-center">
			 <i class="fas fa-thumbs-o-up fa-5x"></i>
		<h4> UPDATE AVAILABLE ! <span class="version_check"></span> </h4>
		<p> Be aware , update proses will replacing necessery folder and files from core source code  . If you already make change(s)  , it will be replaced during update   </p>  
	</div>	

		<p> Please make sure you have account credit at <a href="http://sximo.net" target="_blank"> Sximo NET </a></p>
	<div class="form-group row" >
		<label class="col-3">
			Email Address
		</label>
		<div class="col-md-9">
			<input name="email" class="form-control form-control-sm" required="true" type="text" />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-3">
			Password
		</label>
		<div class="col-md-9">
			<input name="password" class="form-control form-control-sm"  required="true" type="password"  />
		</div>
	</div>		                    		
	

	<div class="form-group row">
		<label class="col-3">
			
		</label>
		<div class="col-md-9">
			<button class="btn btn-primary btn-sm"> Download & Update Now </button>
		</div>
	</div>		
	<input type="hidden" name="codename" id="" value="">		
	<input type="hidden" name="version" value="2.0.1">
	<input type="hidden" name="build" value="">
	<input type="hidden" name="process" value="">
	 {!! Form::close() !!}	
	</div>


<div class="progress-update" style="display: none;">
	<p>Downloading New Update</p>
	<p>Update Downloaded And Saved</p>
	<p>Installing ..... Please dont navigate this page ... </p>
	<div class=" progress-result">

	</div>
</div>							

<div class="failed-update" style="display: none; padding: 50px 0; text-align: center;" >
	<i class="fa fa-thumbs-o-up fa-5x"></i>
	<p> There's nothing to update(s) </p>
</div>

<div class="failed-authen" style="display: none; padding: 50px 0; text-align: center;" >
	<i class="fa fa-warning fa-5x text-danger"></i>
	<p>Failed To Access Server  </p>
</div>





</div>				  		

			  		



				


<style type="text/css">
	#sximo-modal-content {
		background: #f1f3f6 ;
	}
	.progress-update {
		text-align: center;
	}
</style>
<script type="text/javascript">
$(function(){
	$('.autoupdate').on('click',function(){
		$('.ajaxLoading').show();
		$.get('{{ url("sximo/server/version?check") }}',function( callback ) {
			console.log( callback )
			if(callback.status =='success'){
				$('.available').show();
				$(' .available h4').html(callback.message + ' To  <span class="badge badge-danger">' + callback.version +'<span>' );
				$('.authen-update').show();
				$('.progress-update').hide();
				$('.failed-authen').hide();

			} else {
				  $('.failed-update').show()
			}
			$('.ajaxLoading').hide();
		})
	})	

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
          },
          success: function( data ) {
	          if(data.status == 'success')
	          {     
	          	notyMessage(data.message);
	          
	         	 $('.progress-result').html(data.message);
	         	 $.get('{{ url("sximo/config/clearlog") }}',function(){})
	          	
	          } else {
	          	$('.failed-authen').show();
	          	$('.progress-update').hide();
	            notyMessageError(data.message);
	          
	           
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

