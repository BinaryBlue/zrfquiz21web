@extends('layouts.app')

@section('content')
<?php 
	$formats = array(
			'date'	=> 'Date',
			'image'	=> 'Image',
			'link'	=> 'Link',
			'radio'	=> 'Checkbox/Radio',
			'file'	=> 'Files',	
			'function'=> 'Function',
			'database'	=> 'Database',
			'maps'	=> 'Google Maps'								
		);
	?>
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>


		@include('sximo.module.tab',array('active'=>'table','type'=>$type))
 {!! Form::open(array('url'=>'sximo/module/savetable/'.$module_name, 'class'=>'form-horizontal ','id'=>'grTable')) !!}

		
		
	<div class="infobox infobox-success">
	  <p> <strong>New Feature ! ( LIMIT TO ) </strong> Type User ID's using (,) into spesific column to limit the column only viewd by them </p>	
	</div>

	 <div class="table-responsive">
			<table class="table table-" id="table">
			<thead class="no-border">
			  <tr>
				<th class="thead">No</th>
				
				<th class="thead">Field</th>
				<th class="thead" width="70"> Limit</th>
				<th class="thead"><i class="icon-link"></i></th>
				<th class="thead" data-hide="phone">Title / Caption </th>
				<th class="thead" data-hide="phone">Show</th>
				<th class="thead" data-hide="phone">VD </th>
				<th class="thead" data-hide="phone">ST</th>
				<th class="thead" data-hide="phone">DW</th>
				<th class="thead" data-hide="phone" style="width:70px;">Widtd</th>
				<th class="thead" data-hide="phone" style="width:70px;">Align</th>				
				<th class="thead" data-hide="phone"> Format As </th>
			  </tr>
			 </thead> 
			<tbody class="no-border-x no-border-y">	
			<?php usort($tables, "SiteHelpers::_sort"); ?>
			  <?php $num=0; foreach($tables as $rows){
					$id = ++$num;
			  ?>
			  <tr >
				<td class="index thead"><?php echo $id;?></td>
				
				<td ><strong><?php echo $rows['field'];?></strong>
				<input type="hidden" name="field[<?php echo $id;?>]" id="field" value="<?php echo $rows['alias'];?>" />			</td>
				<td>
					<?php
						 $limited_to = (isset($rows['limited']) ? $rows['limited'] : '');
					?>
					<input type="text" class="form-control form-control-sm" width="40" name="limited[<?php echo $id;?>]" class="limited" value="<?php echo $limited_to;?>" style="width: 30px" />

				</td>
				<td>				
				<span class=" xlick " title="Lookup Display" 
					onclick="SximoModal('{{ URL::to('sximo/module/conn/'.$row->module_id.'?field='.$rows['field'].'&alias='.$rows['alias']) }}' ,' Connect Field : {{ $rows['field']}} ' )"
					>
						<i class="fa fa-link"></i>
					</span>
				</td>
				<td >           
					<div class="input-group ">
						<span class="input-group-prepend xlick btn-primary btn-sm" >EN</span>
						<input name="label[<?php echo $id;?>]" type="text" class="form-control form-control-sm " id="label" value="<?php echo $rows['label'];?>" />
					</div>

					@if($config->lang =='true')
				  <?php $lang = SiteHelpers::langOption();
				  if($sximoconfig['cnf_multilang'] ==1) {
					foreach($lang as $l) { if($l['folder'] !='en') {
				   ?>
				   <div class="input-group " style="margin:1px 0 !important;">
				   	<span class="input-group-prepend xlick btn-primary btn-sm" ><?php echo strtoupper($l['folder']);?></span>
					 <input name="language[<?php echo $id;?>][<?php echo $l['folder'];?>]" type="text" class="form-control form-control-sm " 
					 value="<?php echo (isset($rows['language'][$l['folder']]) ? $rows['language'][$l['folder']] : '');?>"
					 placeholder="Label for <?php echo ucwords($l['name']);?>"
					  />
					 
				  </div>
				  <?php } } }?>	
				  @endif
				</td>					
				<td>
				
				<input name="view[<?php echo $id;?>]" type="checkbox" id="view-<?php echo $id;?>" value="1"  class="filled-in chk-col-blue"
				<?php if($rows['view'] == 1) echo 'checked="checked"';?>/>
				<label for="view-<?php echo $id;?>"></label>
				</td>
				<td>
				
				<input name="detail[<?php echo $id;?>]" type="checkbox" id="detail-<?php echo $id;?>" value="1"  class="filled-in chk-col-blue"
				<?php if($rows['detail'] == 1) echo 'checked="checked"';?>/>
				<label for="detail-<?php echo $id;?>"></label>
				</td>
				<td>
				
				<input name="sortable[<?php echo $id;?>]" type="checkbox" id="sortable-<?php echo $id;?>" value="1"  class="filled-in chk-col-blue"
				<?php if($rows['sortable'] == 1) echo 'checked="checked"';?>/>
				<label for="sortable-<?php echo $id;?>"></label>
				</td>
				<td>
				
				<input name="download[<?php echo $id;?>]" type="checkbox" id="download-<?php echo $id;?>" value="1"  class="filled-in chk-col-blue"
				<?php if($rows['download'] == 1) echo 'checked="checked"';?>/>
				<label for="download-<?php echo $id;?>"></label>
				</td>
				<td>
					<input type="text" class="form-control form-control-sm" name="width[<?php echo $id;?>]" value="<?php echo $rows['width'];?>" />
				</td>
				<td>
					<?php $aligns = array('left','center','right'); ?>
					<select class="form-control form-control-sm" name="align[<?php echo $id;?>]">
					<?php foreach ($aligns as $al) { ?>
						<option value="<?php echo $al;?>" <?php if(isset($rows['align']) && $rows['align'] == $al) echo 'selected';?> ><?php echo ucwords($al);?></option>
					<?php } ?>
					</select>
				</td>	


				<td>
				<select class="form-control form-control-sm" name="format_as[<?php echo $id;?>]" style="width:100px;">
					<option value=''> None </option>
					@foreach($formats as $key=>$val)
					<option value="{{ $key }}" <?php if(isset($rows['format_as']) && $rows['format_as'] ==$key) echo 'selected';?> > {{ $val }} </option>
					@endforeach
				</select>	
				
				<input type="text" name="format_value[<?php echo $id;?>]"  value="<?php if(isset($rows['format_value'])) echo $rows['format_value'];?>" class="form-control form-control-sm" style="width:auto !important; display:inline;">

		
				<a href="javascript://ajax" data-html="true" class="text-success format_info" data-toggle="popover" title="Example Usage" data-content="  <b>Date </b> = dd-yy-mm <br /> <b>Image</b> = /uploads/path_to_upload <br />  <b>Link </b> = http://domain.com ? <br /> <b> Function </b> = class|method|params <br /> <b>Checkbox</b> = value:Display,...<br /> <b>Database</b> = table|id|field <br /><br /> All Field are accepted using tag {FieldName} . Example {<b><?php echo $rows['field'];?></b>} " data-placement="left">
				<i class="ti-help-alt"></i>
				</a>

				
				<input type="hidden" name="frozen[<?php echo $id;?>]" value="<?php echo $rows['frozen'];?>" />
				<input type="hidden" name="search[<?php echo $id;?>]" value="<?php echo $rows['search'];?>" />
				<input type="hidden" name="hidden[<?php echo $id;?>]" value="<?php if(isset($rows['hidden'])) echo $rows['hidden'];?>" />
				<input type="hidden" name="alias[<?php echo $id;?>]" value="<?php echo $rows['alias'];?>" />
				<input type="hidden" name="field[<?php echo $id;?>]" value="<?php echo $rows['field'];?>" />
				<input type="hidden" name="sortlist[<?php echo $id;?>]" class="reorder" value="<?php echo $rows['sortlist'];?>" />
	
				<input type="hidden" name="conn_valid[<?php echo $id;?>]"   
				value="<?php if(isset($rows['conn']['valid'])) echo $rows['conn']['valid'];?>"  />
				<input type="hidden" name="conn_db[<?php echo $id;?>]"   
				value="<?php if(isset($rows['conn']['db'])) echo $rows['conn']['db'];?>"  />	
				<input type="hidden" name="conn_key[<?php echo $id;?>]"  
				value="<?php if(isset($rows['conn']['key'])) echo  $rows['conn']['key'];?>"   />
				<input type="hidden" name="conn_display[<?php echo $id;?>]" 
				value="<?php if(isset($rows['conn']['display'])) echo   $rows['conn']['display'];?>"    />			 
				
				</td>
				
			  </tr>
			  <?php } ?>
			  </tbody>
			</table>
			</div>
	 <div class="infobox infobox-warning mt-2">
	  
	   <b> NOTE :  </b> | <b>(DW)</b>  = Download | <b> (VD) </b> = View Detail | <b>( ST )</b> = Sortable <br />
	  <p> <strong>Tips !</strong> Drag and drop rows to re ordering lists </p>	
	</div>	
					
	<button type="submit" class="btn btn-primary btn-sm"> Save Changes </button>
	<input type="hidden" name="module_id" value="{{ $row->module_id }}" />
	{!! Form::close() !!}	


<style type="text/css">
	.popover-content { font-size: 13px; }

</style>

<script type="text/javascript">

$(document).ready(function() {

	$('.format_info').popover()

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

    <?php echo SximoHelpers::sjForm('grTable'); ?>

  })
</script> 

<style>
	.xlick { cursor:pointer;}
	.popover { width:600px;}
</style>

@stop