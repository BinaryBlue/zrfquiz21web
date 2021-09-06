@extends('layouts.app')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>

	 
		@include('sximo.module.tab',array('active'=>'config','type'=> $type))
		
	<div class="row ">
			

	<div class="col-md-6">
	{!! Form::open(array('url'=>'sximo/module/saveconfig/'.$module_name, 'class'=>'form-horizontal ','id'=>'configA' , 'parsley-validate'=>'','novalidate'=>' ')) !!}
	<input  type='hidden' name='module_id' id='module_id'  value='{{ $row->module_id }}'   />
  	<fieldset>
		<legend> Module Info </legend>	
  		<div class=" form-group row">
    		<label for="ipt" class=" control-label col-md-4">Name / Title </label>
			<div class="col-md-8">
				<div class="input-group input-group-sm" style="margin:1px 0 !important;">
				<input  type='text' name='module_title' id='module_title' class="form-control  form-control-sm " required="true" value='{{ $row->module_title }}'  />
					
					<span class="input-group-addon xlick bg-default btn-sm" >EN</span>
				
			</div> 		
			@if($config->lang =='true')
			  <?php $lang = SiteHelpers::langOption();
			   if($sximoconfig['cnf_multilang'] ==1) {
				foreach($lang as $l) { if($l['folder'] !='en') {
			   ?>
			   <div class="input-group input-group-sm mb-1" >			   	
					 <input name="language_title[<?php echo $l['folder'];?>]" type="text"   class="form-control " placeholder="Label for <?php echo $l['name'];?>"
					 value="<?php echo (isset($module_lang['title'][$l['folder']]) ? $module_lang['title'][$l['folder']] : '');?>" />
						 
						<span class="input-group-addon xlick bg-default btn-sm" ><?php echo strtoupper($l['folder']);?></span>
					
			   </div> 
	 		
 			 <?php } } }?>	  
 			  @endif
			 </div> 
			
  		</div>   

		<div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4">Module Note</label>
			<div class="col-md-8">
				<div class="input-group input-group-sm" style="margin:1px 0 !important;">
				<input  type='text' name='module_note' id='module_note'  value='{{ $row->module_note }}' class="form-control form-control-sm "  />
				<span class="input-group-addon xlick bg-default btn-sm" >EN</span>
			</div> 
			@if($config->lang =='true')	
		  <?php $lang = SiteHelpers::langOption();
		   if($sximoconfig['cnf_multilang'] ==1) {
			foreach($lang as $l) { if($l['folder'] !='en') {
		   ?>
		   <div class="input-group input-group-sm" style="margin:1px 0 !important;">
			 <input name="language_note[<?php echo $l['folder'];?>]" type="text"   class="form-control " placeholder="Note for <?php echo $l['name'];?>"
			 value="<?php echo (isset($module_lang['note'][$l['folder']]) ? $module_lang['note'][$l['folder']] : '');?>" />
			 <span class="input-group-addon xlick bg-default btn-sm" ><?php echo strtoupper($l['folder']);?></span>
		   </div> 
			 
		  <?php } } }?>	
		  	 @endif	

			 </div> 
		 </div>   
		
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4">Class Controller </label>
		<div class="col-md-8">
		<input  type='text' name='module_name' id='module_name' readonly="1"  class="form-control form-control-sm" required value='{{ $row->module_name }}'  />
		 </div> 
	  </div>  
  
	   <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4">Table Master</label>
		<div class="col-md-8">
		<input  type='text' name='module_db' id='module_db' readonly="1"  class="form-control form-control-sm" required value='{{ $row->module_db}}'  />
		  
		 </div> 
	  </div>  
  
	  <div class=" form-group row" style="display:none;" >
		<label for="ipt" class=" control-label col-md-4">Author </label>
		<div class="col-md-8">
		<input  type='text' name='module_author' id='module_author' class="form-control form-control-sm"  readonly="1"  value='{{ $row->module_author }}'  />
		 </div> 
	  </div>  

		<div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> ShortCode </label>
			<div class="col-md-8 " >
				
						<b>Form Shortcode : </b><code><br /><?php echo "[sc:Sximo fnc=showForm|id=".$row->module_name."] [/sc]"; ?></code><br />
					<b>Table Shortcode : </b><br />
					<code><?php echo "[sc:Sximo fnc=render|id=".$row->module_name."] [/sc]"; ?></code>
			</div> 
		</div>  	  
	 
		<div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"></label>
			<div class="col-md-8">
			<button type="submit" name="submit" class="btn btn-primary btn-sm"> Update Module </button>
			 </div> 
		</div>   



	</fieldset>
  	{!! Form::close() !!}
	
  
	</div>


	 @if($config->advance =='true') 
 <div class="col-sm-6 col-md-6"> 

 @if($type !='report' && $type !='generic')
  {!! Form::open(array('url'=>'sximo/module/savesetting/'.$module_name, 'class'=>'form-horizontal  ' ,'id'=>'configB')) !!}
  <input  type='text' name='module_id' id='module_id'  value='{{ $row->module_id }}'  style="display:none; " />
  	<fieldset>
		<legend> Module Setting </legend>

		  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> Grid Table Type </label>
			<div class="col-md-8">			

				<select class="form-control form-control-sm" name="module_type">
					<?php if($row->module_type  =='addon') $row->module_type ='native'; ?>
					@foreach($cruds as $crud)
						<option value="{{ $crud->type }}" 
						@if($crud->type == $row->module_type ) selected @endif
						>{{ $crud->name }} </option>
					@endforeach
				</select>	
				
			 </div> 
		  </div> 


	
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4"> Default Order  </label>
		<div class="col-md-8">
			<select class="form-control  form-control-sm" name="orderby" style="width: 50%">
			@foreach($tables as $t)
				<option value="{{ $t['field'] }}"
				@if($setting['orderby'] ==$t['field']) selected="selected" @endif 
				>{{ $t['label'] }}</option>
			@endforeach
			</select>
			<select class="form-control  form-control-sm" name="ordertype" style="width: 50%">
				<option value="asc" @if($setting['ordertype'] =='asc') selected="selected" @endif > Ascending </option>
				<option value="desc" @if($setting['ordertype'] =='desc') selected="selected" @endif > Descending </option>
			</select>
			
		 </div> 
	  </div> 
	  
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4"> Display Rows </label>
		<div class="col-md-8">
			<select class="form-control  form-control-sm" name="perpage" style="width: 50%">
				<?php $pages = array('10','20','30','50');
				foreach($pages as $page) {
				?>
				<option value="<?php echo $page;?>"  @if($setting['perpage'] ==$page) selected="selected" @endif > <?php echo $page;?> </option>
				<?php } ?>
			</select>	
			
		 </div> 
	  </div>   
		
	</fieldset>	
	 @if($config->setting->method =='true') 
  	<fieldset>
	<legend> Form & View Setting </legend>
		<p> <i>You can switch this setting and applied to current module without have to rebuild </i></p>

		  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> Form Method </label>
			<div class="col-md-8">
				
				<input type="radio" value="native" name="form-method" class="filled-in" id="n-p" 
				 @if($setting['form-method'] == 'native') checked="checked" @endif 
				 /> 
				 <label for="n-p"> New Page </label>
				
				<input type="radio" value="modal" name="form-method"  class="filled-in" id="n-m" 
				 @if($setting['form-method'] == 'modal') checked="checked" @endif 			
				/> 
				<label for="n-m">	Modal  </label>							
			 </div> 
		  </div> 

		  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> View  Method </label>
			<div class="col-md-8">
				
				<input type="radio" value="native" name="view-method" class="filled-in" id="v-n" 
				 @if($setting['view-method'] == 'native') checked="checked" @endif 
				 /> 
				 <label for="v-n">	New Page  </label>		
				
				<input type="radio" value="modal" name="view-method" class="filled-in" id="v-m"   
				 @if($setting['view-method'] == 'modal') checked="checked" @endif 			
				/> 
				 <label for="v-m">	Modal  </label>	   
				
				<input type="radio" value="expand" name="view-method"  class="filled-in" id="v-e" 
				 @if($setting['view-method'] == 'expand') checked="checked" @endif 			
				/>  
				 <label for="v-e">	Expand Grid    </label>	  

			 </div> 
		  </div> 		  

		  <div class=" form-group row" >
			<label for="ipt" class=" control-label col-md-4"> Inline add / edit row </label>
			<div class="col-md-8">
				
				<input type="checkbox" value="true" name="inline" class="filled-in" id="new-inline" 
				@if($setting['inline'] == 'true') checked="checked" @endif 	
				 /> 
				 <label for="new-inline"> Yes  Allowed </label>
										
			 </div> 
		  </div> 		  

		  
		   <p class="alert alert-warning"> <strong> Important ! </strong> this setting only work with module where have <strong>Adavance </strong> Option</p>
	</fieldset>


	@endif

			  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"></label>
			<div class="col-md-8">
			<button type="submit" name="submit" class="btn btn-primary btn-sm"> Update Setting </button>
			 </div> 
		  </div> 

	{!! Form::close() !!}
	@endif
	
  </div>
  	@endif
</div>


<script type="text/javascript">
	$(document).ready(function(){

		<?php echo SximoHelpers::sjForm('configA'); ?>
		<?php echo SximoHelpers::sjForm('configB'); ?>

	})
</script>	

@stop