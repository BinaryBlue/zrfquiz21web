@extends('layouts.app')

@section('content')
<!-- N.R. jqueryFileTree fix -->
<script type="text/javascript" src="{{ asset('sximo5/js/plugins/jquery.fileTree/jqueryFileTree.js') }}"></script>
<link href="{{ asset('sximo5/js/plugins/jquery.fileTree/jqueryFileTree.css') }}" rel="stylesheet">
<!-- end -->

<div class="page-header"><h2> Code Editor <small> Edit and modify codes  </h2></div>
<div class="card">

	<div class="card-body">
		<div class="sbox"  >
			<div class="sbox-title"><h4>File Editor </h4></div>
			<div class="sbox-content">

			<p class="text-center text-danger"></i>Becarefful !! Do with your own Risk  </p>		  

	 		<div class="row">
	 			<div class="col-md-3">
	 				<div id="container_id"></div>
				
	 			</div>

	 			<div class="col-md-9">
	 				<div style="padding:10px; background:#fff; min-height:300px; border:solid 1px #ddd;display:none;" class="result">
	 				{!! Form::open(array('url'=>'sximo/code/save', 'class'=>'form-horizontal','id'=>'FormCode' )) !!}
	 					<b> File Location : </b> <span class="file_location text-danger"></span>  <hr />
	 					<div class="message"></div>
	 					<div id="content-editor" style="min-height: 400px;"></div>
	 					<textarea style="display: none;" id="content_html" name="content_html" ></textarea>
	 					<input type="hidden" name="path" class="path" value="" >
	 					<br />
	 					<button class="btn btn-primary"> Save Change(s) </button>
	 				{!! Form::close() !!}	

	 				</div>

	 			</div>
			</div> 				

		  </div>
	  </div>

</div></div>

	<style type="text/css">
		#content-editor {
			min-height: 500px;
			border:solid 1px #ddd;
		}
	</style>
<script type="text/javascript" src="{{ asset('sximo5/js/plugins/ace/src/ace.js') }}"></script>	
<script type="text/javascript">
    $(document).ready( function() {

		

        $('#container_id').fileTree({
            root: '/',
            script: '{{ url("sximo/code/source")}}',
            expandSpeed: 1000,
            collapseSpeed: 1000,
            multiFolder: false
        }, function(file) {
        	$('.ajaxLoading').show();	
        	$.get( "{{ url('sximo/code/edit/')}}",{ path:file}, function( data ) {

  				var editor = ace.edit("content-editor");
				editor.session.setMode("ace/mode/php");      		//$('.ace_text-input').val(data.content);
        		editor.setValue(data.content);
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
					beforeSubmit :  function(){
						$('.ajaxLoading').show();
						var editor = ace.edit("content-editor");
						$('#content_html').val(editor.getValue());

						
					},
					success:       showResponse  
				}  
				$(this).ajaxSubmit(options); 
				return false;
							
			} else {
				return false;
			}		
		
		});        
    });

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
 	    