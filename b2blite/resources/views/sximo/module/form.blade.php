@extends('layouts.app')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>


 @include('sximo.module.tab',array('active'=>'form','type'=>  $type ))

<ul class="nav nav-tabs" style="margin-bottom:10px;">
  	<li class="nav-item" ><a class="nav-link active" href="{{ URL::to('sximo/module/form/'.$module_name)}}">Form Configuration </a></li>
  	<li class="nav-item" ><a class="nav-link" href="{{ URL::to('sximo/module/subform/'.$module_name)}}">Sub Form </a></li> 
	<li class="nav-item"><a class="nav-link" href="{{ URL::to('sximo/module/formdesign/'.$module_name)}}">Form Layout</a></li> 
	
</ul>
 <div class="infobox infobox-danger ">
  <button type="button" class="close" data-dismiss="alert"> x </button>  
  <p> <strong>Note !</strong> Your primary key must be <strong>show</strong> and in <strong>hidden</strong> type   <br />
  	All Validatios rule are from laravel rule <a href="https://laravel.com/docs/6.x/validation#available-validation-rules" target="_blank"> Availabe rules </a>
  </p>	
</div>	

 {!! Form::open(array('url'=>'sximo/module/saveform/'.$module_name, 'class'=>'form-horizontal ','id'=>'fForm')) !!}
 <div class="table-responsive">
		<table class="table " id="table">
		<thead class="no-border">
          <tr >
            <th scope="col">No</th>
            <th scope="col">Field</th>
            <th scope="col" width="70"> Limit</th>
            <th scope="col" data-hide="phone">Title / Caption </th>

			<th scope="col" data-hide="phone">Type </th>
            <th scope="col" data-hide="phone">Show</th>
            
            <th scope="col" data-hide="phone">Searchable</th>
			<th scope="col" data-hide="phone">Validation</th>
            <th scope="col">&nbsp;</th>
          </tr>
		  </thead>
		  <tbody class="no-border-x no-border-y">	
		  <?php usort($forms, "SiteHelpers::_sort"); ?>
		  <?php $i=0; foreach($forms as $rows){
		  $id = ++$i;
		  ?>
          <tr>
            <td  class="index"><?php echo $id;?></td>
            <td><?php echo $rows['field'];?></td>
			<td>
				<?php
					 $limited_to = (isset($rows['limited']) ? $rows['limited'] : '');
				?>
				<input type="text" class="form-control form-control-sm" name="limited[<?php echo $id;?>]" class="limited" value="<?php echo $limited_to;?>" />

			</td>            
			<td>
				<div class="input-group ">
				<span class="input-group-prepend xlick  btn-primary btn-sm " >EN</span>		
				<input type="text" name="label[<?php echo $id;?>]" class="form-control form-control-sm" value="<?php echo $rows['label'];?>" />
				
              </div>
					@if($config->lang =='true')
				  <?php $lang = SiteHelpers::langOption();
				  if($sximoconfig['cnf_multilang'] ==1) {
					foreach($lang as $l) { if($l['folder'] !='en') {
				   ?>
				   <div class="input-group" style="margin:1px 0 !important;">
				   <span class="input-group-prepend xlick btn-primary btn-sm " ><?php echo strtoupper($l['folder']);?></span>
					 <input name="language[<?php echo $id;?>][<?php echo $l['folder'];?>]" type="text" class="form-control form-control-sm " 
					 value="<?php echo (isset($rows['language'][$l['folder']]) ? $rows['language'][$l['folder']] : '');?>"
					 placeholder="Label for <?php echo ucwords($l['name']);?>"
					  />
					 
				  </div>
				  <?php } } }?>	
				  @endif			
			</td>

			<td>
            <?php echo $rows['type'];?>
			<input type="hidden" name="type[<?php echo $id;?>]" value="<?php echo $rows['type'];?>"  />
			<label for="type-<?php echo $id;?>"></label>
			</td>
            <td>

            <input type="checkbox" name="view[<?php echo $id;?>]" value="1"  class="filled-in" id="view-<?php echo $id;?>"
			<?php if($rows['view'] == 1) echo 'checked="checked"';?> />
			<label for="view-<?php echo $id;?>"></label>
			</td>
            
            <td>
			
            <input type="checkbox" name="search[<?php echo $id;?>]" value="1" class="filled-in" id="search-<?php echo $id;?>"
			<?php if($rows['search'] == 1) echo 'checked="checked"';?>
			/>
			<label for="search-<?php echo $id;?>"></label>
			</td>
			<td>
				<input type="text" name="required[<?php echo $id;?>]" class="form-control form-control-sm" value="{{ $rows['required'] }}" placeholder="Ex : required|email" />
				
		</td>
            <td>
			<a href="javascript:void(0)" class="btn btn-sm btn-default  editForm"  role="button"  
			onclick="SximoModal('{{ URL::to('sximo/module/editform/'.$row->module_id.'?field='.$rows['field'].'&alias='.$rows['alias']) }}','Edit Field : <?php echo $rows['field'];?>')">
			<i class="fa fa-bars"></i></a>

			
			
			<input type="hidden" name="alias[<?php echo $id;?>]" value="<?php echo $rows['alias'];?>" />
			<input type="hidden" name="field[<?php echo $id;?>]" value="<?php echo $rows['field'];?>" />	
			<input type="hidden" name="form_group[<?php echo $id;?>]" value="<?php echo $rows['form_group'];?>" />	
			<input type="hidden" name="sortlist[<?php echo $id;?>]" class="reorder" value="<?php echo $rows['sortlist'];?>" />		
			<input type="hidden" name="opt_type[<?php echo $id;?>]" value="<?php echo $rows['option']['opt_type'];?>" />
			<input type="hidden" name="lookup_query[<?php echo $id;?>]" value="<?php echo $rows['option']['lookup_query'];?>" />
			<input type="hidden" name="lookup_table[<?php echo $id;?>]" value="<?php echo $rows['option']['lookup_table'];?>" />
			<input type="hidden" name="lookup_key[<?php echo $id;?>]" value="<?php echo $rows['option']['lookup_key'];?>" />
			<input type="hidden" name="lookup_value[<?php echo $id;?>]" value="<?php echo $rows['option']['lookup_value'];?>" />
			<input type="hidden" name="is_dependency[<?php echo $id;?>]" value="<?php echo $rows['option']['is_dependency'];?>" />
			<input type="hidden" name="lookup_dependency_key[<?php echo $id;?>]" value="<?php echo $rows['option']['lookup_dependency_key'];?>" />
			<input type="hidden" name="path_to_upload[<?php echo $id;?>]" value="<?php echo $rows['option']['path_to_upload'];?>" />
			<input type="hidden" name="upload_type[<?php echo $id;?>]" value="<?php echo $rows['option']['upload_type'];?>" />
			<input type="hidden" name="resize_width[<?php echo $id;?>]" value="<?php if(isset($rows['option']['resize_width'])) echo $rows['option']['resize_width'];?>" />
			<input type="hidden" name="resize_height[<?php echo $id;?>]" value="<?php if(isset($rows['option']['resize_height'])) echo $rows['option']['resize_height'];?>" />
			<input type="hidden" name="extend_class[<?php echo $id;?>]" value="<?php if(isset($rows['option']['resize_height'])) echo $rows['option']['resize_height'];?>" />
			<input type="hidden" name="tooltip[<?php echo $id;?>]" value="<?php if(isset($rows['option']['tooltip'])) echo $rows['option']['tooltip'];?>" />
			<input type="hidden" name="attribute[<?php echo $id;?>]" value="<?php if(isset($rows['option']['attribute'])) echo $rows['option']['attribute'];?>" />
			<input type="hidden" name="extend_class[<?php echo $id;?>]" value="<?php if(isset($rows['option']['extend_class'])) echo $rows['option']['extend_class'];?>" />
			<input type="hidden" name="select_multiple[<?php echo $id;?>]" value="<?php if(isset($rows['option']['select_multiple'])) echo $rows['option']['select_multiple'];?>" />
			<input type="hidden" name="image_multiple[<?php echo $id;?>]" value="<?php if(isset($rows['option']['image_multiple'])) echo $rows['option']['image_multiple'];?>" />
			
			</td>
			
          </tr>
		  <?php } ?>
		  </tbody>
        </table>
		</div>

	
		
		<button type="submit" class="btn btn-primary btn-sm"> Save Changes </button>
		<input type="hidden" name="module_id" value="{{ $row->module_id }}" />
 {!! Form::close() !!}		


			</div>
		</div>


<script>
$(document).ready(function() {
	
	$('.expand-row').hide();
	$('.btn-sm').click(function(){
		var id = $(this).attr('rel');
		$('.expand-row').hide();
		$(id).slideDown(100);
		
	});
	var fixHelperModified = function(e, tr) {
		var $originals = tr.children();
		var $helper = tr.clone();
		$helper.children().each(function(index) {
			$(this).width($originals.eq(index).width())
		});
		return $helper;
		},
		updateIndex = function(e, ui) {
			$('td.index', ui.item.parent()).each(function (i) {
				$(this).html(i + 1);
			});
			$('.reorder', ui.item.parent()).each(function (i) {
				$(this).val(i + 1);
			});			
		};
		
	$("#table tbody").sortable({
		helper: fixHelperModified,
		stop: updateIndex
	});		
});


</script>
<script type="text/javascript">
  $(document).ready(function(){

    <?php echo SximoHelpers::sjForm('fForm'); ?>

  })
</script>
@stop