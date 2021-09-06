@extends('layouts.app')
@section('content')
<div class="page-header">
		<h2> Configuration <small> Basic  information and configuration for your application site  </small></h2>
	</div>
<div class="card">
	
	<div class="card-body"> 

 {!! Form::open(array('url'=>'sximo/config/save/', 'class'=>'form-horizontal  validated', 'files' => true)) !!}

		@include('sximo.config.tab')
	
	<div class="pt-5">

<div class="row">
	
  	<div class="col-md-3">
  		<div style="text-align: center; padding-top: 30px;">
      
      			<div class="logo-preview preview-upload">
					@if(file_exists(public_path().'/uploads/images/'.$sximoconfig['cnf_logo']) && $sximoconfig['cnf_logo'] !='')
				 		<img src="{{ asset('uploads/images/'.$sximoconfig['cnf_logo'])}}" alt="{{ $sximoconfig['cnf_appname'] }}" width="100" />
				 	@else
						<img src="{{ asset('uploads/logo.png')}}" alt="{{ $sximoconfig['cnf_appname'] }}"  width="100" />
					@endif	
				</div>

				<p> Please use same dimension image <br />min 100px X 100px </p>
				<div class="fileUpload btn " > 
				    <span>  <i class="fa fa-camera"></i>  </span>
				    <div class="title"> Browse File </div>
				    <input type="file" name="logo" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
				</div>

	    </div>

  	</div>
  	<div class="col-md-9">
  		<div class="row">



	<div class="col-sm-6 animated fadeInRight ">

	  <div class="form-group">
	    <label for="ipt" class=" control-label ">{{ Lang::get('core.fr_appname') }} </label>
		<div class="">
		<input name="cnf_appname" type="text" id="cnf_appname" class="form-control form-control-sm " required  value="{{ $sximoconfig['cnf_appname'] }}" />  
		 </div> 
	  </div>  
	  
	  <div class="form-group">
	    <label for="ipt" class=" control-label ">{{ Lang::get('core.fr_appdesc') }} </label>
		<div class="">
		<input name="cnf_appdesc" type="text" id="cnf_appdesc" class="form-control form-control-sm" value="{{ $sximoconfig['cnf_appdesc'] }}" /> 
		 </div> 
	  </div>  
	  
	  <div class="form-group">
	    <label for="ipt" class=" control-label ">{{ Lang::get('core.fr_comname') }} </label>
		<div class="">
		<input name="cnf_comname" type="text" id="cnf_comname" class="form-control form-control-sm" value="{{ $sximoconfig['cnf_comname'] }}" />  
		 </div> 
	  </div>      

	  <div class="form-group">
	    <label for="ipt" class=" control-label ">{{ Lang::get('core.fr_emailsys') }} </label>
		<div class="">
		<input name="cnf_email" type="text" id="cnf_email" class="form-control form-control-sm" value="{{ $sximoconfig['cnf_email'] }}" /> 
		 </div> 
	  </div>   
	  <div class="form-group">
	    <label for="ipt" class=" control-label "> {{ Lang::get('core.fr_allowfrontend') }} <br />  </label>
		<div class="">
			<div class="">
				<input name="cnf_front" type="checkbox" id="cnf_front" value="true" class="filled-in" 
				@if($sximoconfig['cnf_front'] =='true') checked @endif 
				  /> <label for="cnf_front"> {{ Lang::get('core.fr_enable') }} </label>
			</div>	
		 </div> 
	  </div>

	   
	  <div class="row">

	  	<div class="col-md-6">
		   <div class="form-group">
			    <label for="ipt" class=" control-label "> {{ Lang::get('core.fr_multilanguage') }} <br />  </label>
				<div class="">
					<div class="">
						<input name="cnf_multilang" type="checkbox" id="cnf_multilang" value="1" class="filled-in" 
						@if($sximoconfig['cnf_multilang'] ==1) checked @endif  
						  /> <label for="cnf_multilang"> {{ Lang::get('core.fr_enable') }} </label>
					</div>	
				 </div> 
			  </div>
		</div>  

		<div class="col-md-6">
	     
		   <div class="form-group">
		    <label for="ipt" class=" control-label ">{{ Lang::get('core.fr_mainlanguage') }} </label>
			<div class="">

					<select class="form-control form-control-sm" name="cnf_lang">

					@foreach(SiteHelpers::langOption() as $lang)
						<option value="{{  $lang['folder'] }}"
						@if(config('sximo.cnf_lang') ==$lang['folder']) selected @endif
						>{{  $lang['name'] }}</option>
					@endforeach
				</select>
			 </div> 
		  </div> 
		</div>
	</div>	    
		      

	   <div class="form-group">
	    <label for="ipt" class=" control-label ">{{ Lang::get('core.fr_fronttemplate') }}</label>
		<div class="">
				
				<select class="form-control form-control-sm" name="cnf_theme" required="true">
				<option value=""> Select Frontend Template</option>

				@foreach(SiteHelpers::themeOption() as $t)
					<option value="{{  $t['folder'] }}"
					@if($sximoconfig['cnf_theme'] ==$t['folder']) selected @endif
					>{{  $t['name'] }}</option>
				@endforeach
			</select>
		 </div> 
	  </div> 


	   <div class="form-group" style="display: none;">
	    <label for="ipt" class=" control-label "> Backend Template </label>
		<div class="">
				
				<select class="form-control form-control-sm" name="cnf_backend" required="true">
				<option value="minimal"> Select Backend Template</option>
				@foreach(SiteHelpers::backendOption() as $t)
					<option value="{{  $t['folder'] }}"
					@if($sximoconfig['cnf_backend'] ==$t['folder']) selected @endif
					>{{  $t['name'] }}</option>
				@endforeach
			</select>
		 </div> 
	  </div> 

	  
	  
	  
	</div>

	<div class="col-sm-6 animated fadeInRight ">

	  
	  <div class="form-group">
	    <label for="ipt" class=" control-label "> {{ Lang::get('core.fr_dateformat') }} </label>
		<div class="">
			<select class="form-control form-control-sm" name="cnf_date">
			<?php $dates = array(
					'Y-m-d'=>' ( Y-m-d ) . Example : '.date('Y-m-d'),
					'Y/m/d'=>' ( Y/m/d ) . Example : '.date('Y/m/d'),
					'd-m-y'=>' ( D-M-Y ) . Example : '.date('d-m-y'),
					'd/m/y'=>' ( D/M/Y ) . Example : '.date('d/m/y'),
					'm-d-y'=>' ( m-d-Y ) . Example : '.date('m-d-Y'),
					'm/d/y'=>' ( m/d/Y ) . Example : '.date('m/d/Y'),
				  );
			foreach($dates as $key=>$val) {?>
				<option value="{{  $key }}"
				@if(config('sximo.cnf_date') ==$key) selected @endif
				>{{  $val }}</option>

			<?php } ?>
			</select>
		 </div> 
	  </div>  			

	  <div class="form-group">
	    <label for="ipt" class=" control-label ">Metakey </label>
		<div class="">
			<textarea class="form-control form-control-sm" name="cnf_metakey">{{ $sximoconfig['cnf_metakey'] }}</textarea>
		 </div> 
	  </div> 

	   <div class="form-group">
	    <label  class=" control-label ">Meta Description</label>
		<div class="">
			<textarea class="form-control form-control-sm"  name="cnf_metadesc">{{ $sximoconfig['cnf_metadesc'] }}</textarea>
		 </div> 
	  </div>  


	  <div class="form-group hide">
	    <label for="ipt" class=" control-label "> Development Mode ?   </label>
		<div class="">
			<div class="checkbox">
				<input name="cnf_mode" type="checkbox" id="cnf_mode" value="1" class="minimal-green" 
				@if ($sximoconfig['cnf_mode'] =='production') checked @endif
				  />  Production
			</div>
			<small> If you need to debug mode , please unchecked this option </small>	
		 </div> 
	  </div> 

	  <div class="form-group">
	    <label for="ipt" class=" control-label ">&nbsp;</label>
		<div class="">
			<button class="btn btn-primary" type="submit">{{ Lang::get('core.sb_savechanges') }} </button>
		 </div> 
	  </div> 		  

	</div>  


</div>
</div>
</div></div>

</div></div>
{!! Form::close() !!}  
@stop