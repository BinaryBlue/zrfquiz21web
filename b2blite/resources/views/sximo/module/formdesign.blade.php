@extends('layouts.app')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>


		@include('sximo.module.tab',array('active'=>'form','type'=> 'addon'))
<ul class="nav nav-tabs" style="margin-bottom:10px;">
    <li class="nav-item" ><a class="nav-link " href="{{ url('sximo/module/form/'.$module_name)}}">Form Configuration </a></li>
    <li class="nav-item" ><a class="nav-link " href="{{ url('sximo/module/subform/'.$module_name)}}">Sub Form </a></li> 
  <li class="nav-item"><a class="nav-link active" href="{{ url('sximo/module/formdesign/'.$module_name)}}">Form Layout</a></li> 
  
</ul>

	
	<div class="infobox infobox-success ">
		<button type="button" class="close" data-dismiss="alert"> x </button>
		<strong>Tips !</strong> Drag and drop rows to re ordering lists </p>
	</div>
			
 {!! Form::open(array('url'=>'sximo/module/formdesign/'.$module_name,'id'=>'doReorder', 'class'=>'form-vertical ','parsley-validate'=>'','novalidate'=>' ')) !!}
 
 <div class="row">
 <div class="col-md-4">
	  <div class="form-group ">
		<label> Number Of Block : </label>
		
		<select class="form-control"  required name="column" style="width:200px;" onchange="location.href='?block='+this.value">
		<?php for($i=1 ; $i<5;$i++) {?>	
				<option value="<?php echo $i;?>" <?php if($form_column == $i) echo 'selected';?> ><?php echo $i;?> Block </option>	
		<?php  } ?>	
		</select>
		
	</div>

 </div>
 

 <div class="col-md-4">
	<div class="form-group">
		<label> Display Form As : </label>
		<div class="radio-group">		
			<input type="radio" name="format" value="grid" class="filled-in" id="r-grid"
			<?php if($format == 'grid') echo 'checked';?>
			  /> <label for="r-grid"> Grid </label>		
			<input type="radio" name="format" value="tab" class="filled-in" id="r-tab"
			<?php if($format == 'tab') echo 'checked';?>
			  /> <label for="r-tab"> Tab </label>		
			<input type="radio" name="format" value="groupped" class="filled-in" id="r-groupped"
			<?php if($format == 'groupped') echo 'checked';?>
			  /> <label for="r-groupped"> Grouped </label>		
			<input type="radio" name="format" value="wizzard" class="filled-in" id="r-wizzard"
			<?php if($format == 'wizzard') echo 'checked';?>
			  /> <label for="r-wizzard"> Wizzard / Step Form </label>	 	  	
		</div>		
	</div> 
	
	<div class="form-group">
		<label> Form Layout : </label>
		<div class="radio-group">
		
			<input type="radio" name="display" value="horizontal" class="minimal-green"  id="c-horizontal"
			<?php if($display == 'horizontal') echo 'checked';?>
			  /> <label for="c-horizontal"> Normal </label>	
		
			<input type="radio" name="display" value="vertical" class="minimal-green" id="c-vertical"
			<?php if($display == 'vertical') echo 'checked';?>
			  /> <label for="c-vertical"> Vertical </label>	
		
		</div>				
	</div>		

 </div>
 <div class="col-md-4">
	<div class="form-group">
	
			<label> Once you made changes on layout , please rebuild Form Files to take affect </label>
			<input type="hidden" name="reordering" id="reordering" value="" class="form-control" />
			<input type="hidden" name="module_id" value="{{ $row->module_id }}" />
			<button type="button" class="btn btn-primary" id="saveLayout"> Save Layout </button>
	
	 </div>	 
 </div>


</div>
<div style="margin-bottom:20px; clear:both; border-bottom:dashed 1px #ddd; padding:5px;"></div>
 


				 
			<!-- BEGIN: XHTML for example 1.2 -->
			
			<div id="FormLayout" class="row">
				<?php
					
					for($i=0;$i<$form_column;$i++)
					{
						if($form_column == 4) {
							$class = 'col-md-3';
						}  elseif( $form_column ==3 ) {
							$class = 'col-md-4';
						}  elseif( $form_column ==2 ) {
							$class = 'col-md-6';
						} else {
							$class = 'col-md-12';
						}
						?>
						<div class="column left  <?php echo $class ;?>">
							  <div class="form-group">
								<label for="ipt" class=" "> Block Title <?php echo $i+1;;?></label>
								<input type="type" name="title[]" class="form-control" required placeholder=" Title Block "
								 value="<?php if(isset($title[$i])) echo $title[$i];?>"
								 />
							  </div>  
 							
							<ul class="sortable-list">
							<?php foreach($forms as $rows){
								if($rows['form_group'] == $i) {
									echo '<li class="sortable-item" id="'.$rows['field'].'"> '.$rows['label'].' </li>';
								}
							}?>
							</ul>
						</div>
						<?php
					}
				
				?>

				<div class="clearer">&nbsp;</div>
				<div class="col-md-12" style="margin:10px 0;">

				</div>	
				

			</div>
 {!! Form::close() !!}


<script>
$(document).ready(function() {
	$('#saveLayout').click(function(){
		val = getItems('#FormLayout');
		$('#reordering').val(val);
		$('#doReorder').submit();

		//alert('Items saved (' + val + ')');
	});
	// Example 1.2: Sortable and connectable lists
	$('#FormLayout .sortable-list').sortable({
		connectWith: '#FormLayout .sortable-list'
	});
	

});

	function getItems(exampleNr)
	{
		var columns = [];

		$(exampleNr + ' ul.sortable-list').each(function(){
			columns.push($(this).sortable('toArray').join(','));				
		});

		return columns.join('|');
	}
</script>
<style>

/* Floats */


.clear,.clearer {clear: both;}
.clearer {
	display: block;
	font-size: 0;
	height: 0;
	line-height: 0;
}


/*
	Example specifics
------------------------------------------------------------------- */

/* Layout */





/* Sortable items */

.sortable-list {
	background-color: #fff; border: 1px solid #ddd;
	list-style: none;
	margin: 0;
	min-height: 60px;
	padding: 10px;
}
.sortable-item {
	background-color: #fafafa;
	border: 1px solid #ddd;
	cursor: move;
	display: block;
	margin-bottom: 5px;
	padding: 5px 20px;
}

/* Containment area */

#containment {
	background-color: #FFA;
	height: 230px;
}


/* Item placeholder (visual helper) */

.placeholder {
	background-color: #ddd;
	border: 1px dashed #666;
	height: 58px;
	margin-bottom: 5px;
}
</style>
@stop