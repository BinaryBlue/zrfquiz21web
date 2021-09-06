 @extends('layouts.app')

@section('content')
<!-- N.R. jqueryFileTree fix -->
<script type="text/javascript" src="{{ asset('sximo5/js/plugins/jquery.fileTree/jqueryFileTree.js') }}"></script>
<link href="{{ asset('sximo5/js/plugins/jquery.fileTree/jqueryFileTree.css') }}" rel="stylesheet">
<!-- end -->
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>


		@include('sximo.module.tab',array('active'=>'source'))
	<div class="row">			
	 		
	 			<div class="col-md-2">
				<div id="container_id"></div>

	 			</div>

	 			<div class="col-md-10" style="height: 600px;">
	 				<div style="padding:10px; background:#fff; min-height:300px; border:solid 1px #ddd;display:none;" class="result">
	 				{!! Form::open(array('url'=>'sximo/module/code/'.$module_name, 'class'=>'form-horizontal','id'=>'FormCode' )) !!}
	 					<b> File Location : </b> <span class="file_location text-danger"></span>  <hr />
	 					<div class="message"></div>
	 					<textarea id="content_html" name="content_html" class="form-control form-control-sm markItUp" rows="20"></textarea>
	 					<input type="hidden" name="path" class="path" value="" >
	 					<br />
	 					<button class="btn btn-primary"> Save Change(s) </button>
	 				{!! Form::close() !!}	

	 				</div>

	 			</div>
		</div>	



<script type="text/javascript">
    $(document).ready( function() {
        $('#container_id').fileTree({
            root: '/{{ $module_name}}/',
            script: '{{ url("sximo/module/source/folder")}}',
            expandSpeed: 1000,
            collapseSpeed: 1000,
            multiFolder: false
        }, function(file) {
        	$('.ajaxLoading').show();	
        	$.get( "{{ url('sximo/module/code/'.$module_name)}}",{ path:file}, function( data ) {
        		$('#content_html').val(data.content);
        		$('.file_location').html(data.path);
        		$('.path').val(data.path);
				 $('.ajaxLoading').hide();	
				 $('.result').show();
			});
           
        });

		var form = $('#FormCode'); 
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
		$('.ajaxLoading').hide();
		$('.message').html(data.message);
					
	} else {
		//$('.message').html(data.message)	
		$('.ajaxLoading').hide();
		$('.message').html(data.message);
	}	
}	


</script>

@stop
