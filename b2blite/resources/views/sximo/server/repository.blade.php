@extends('layouts.app')

@section('content')

<section class="page-header ">
	<h3> Sximo NET <small> Connection to Main Sximo Server </small></h3>
	<p> Official Sximo Repositories </p>
</section>

<div class="box-repos card" style="background: #fff; padding: 20px;">
	<div style="min-height: 450px;" id="items"></div> 
</div>	
<script type="text/javascript">
function page( page ) {
	$('.ajaxLoading').show();
	$.get('{{ url("sximo/server/load?page=" ) }}'+page,function( callback ) {
		$('#items').html(callback)
		$('.ajaxLoading').hide();
	})	
	
}
$(function(){
	
	page();
	

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

 @stop 
 	    