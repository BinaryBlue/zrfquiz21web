


	 {!! Form::open(array('url'=>'cms/categories', 'class'=>'form-horizontal validated','files' => true , 'parsley-validate'=>'','novalidate'=>' ' ,'id' =>'SximoTable' )) !!}
		  <div class="form-group   " >
			<label for="Name" class=" control-label "> Category Name <span class="asterix"> * </span></label>
			<div class="">
			  {!! Form::text('name', $row['name'],array('class'=>'form-control input-sm', 'placeholder'=>'', 'required'=>'true'  )) !!} 
			 </div> 
			 <div class="col-md-2">
			 	
			 </div>
		  </div> 					
		  <div class="form-group   " >
			<label for="Description" class=" control-label "> Description </label>
			<div class="">
			  <textarea name='desc' rows='2' id='description' class='form-control '  
   >{{ $row['desc'] }}</textarea> 
			 </div> 
			 <div class="col-md-2">
			 	
			 </div>
		  </div> 					
		  <div class="form-group  " >
				<label for="Status" class=" control-label "> Status <span class="asterix"> * </span></label>
				<div class="">
					<input type='radio' name='active' value ='1' required @if($row['active'] == '1') checked="checked" @endif class="filled-in" id="Active">  <label for="Active"> Active </label>  

					<input type='radio' name='active' value ='0' required @if($row['active'] == '0') checked="checked" @endif class="filled-in" id="Inactive">  <label for="Inactive"> Inactive </label> 
  
				 </div> 
				 
			  </div> 

		<div class="form-group   " >
			<label for="Level" class=" control-label "> Upload Image </label>
			<div class="">
				<div class="fileUpload btn " > 
				    <span>  <i class="fa fa-camera"></i>  </span>
				    <div class="title"> Browse File </div>
				    <input type="file" name="image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
				</div>
				<div class="image-preview preview-upload">
					{!! SiteHelpers::showUploadedFile( $row["image"],"/uploads/images/posts/") !!}
				</div>


			 	
			 </div> 
		</div>
		<input type="hidden" name="cid" value="{{ $row['cid'] }}">
		<input type="hidden" name="action_task" value="" />
		<input type="checkbox" class="ids" name="ids[]" value="{{ $row['cid'] }}" checked="checked" style="display: none;" />
	 	
	<div class="form-group   " >
		<div class="row">
 			<div class="col-md-6">
 				 <button type="button" class="btn btn-sm btn-danger " onclick="SximoDelete();"> Delete  </button>
 			</div>
 			<div class="col-md-6 text-right">
 				 <button type="submit" class="btn btn-sm btn-primary"> {{ Lang::get('core.sb_save') }} Category </button>
 			</div>

	</div>

       
      </div>

      {!! Form::close() !!}
 
 <script type="text/javascript">
 	$(document).ready(function(){
 		$('.upload').change(function() {
			var id = $(this).attr('name');

			var files = $(this).prop('files');
			$(this).parent().closest('.title').html(files[0].name)
			console.log(files[0].name)
			const fr = new FileReader()
			fr.readAsDataURL(files[0])
			fr.addEventListener('load', () => {
			 	$('.'+id+'-preview').html('<img src="'+fr.result+'" width="120" />')
			}) 
	 		
		  	
		})

 	})
 </script>
