				<!-- Toolbar Top -->
<div class="toolbar-nav">				
	<div class="row">
		<div class="col-md-4 text-left">
			<div class="input-group">
			     
			      <input type="text" class="form-control form-control-sm onsearch" data-target="{{ url($pageModule) }}" data-div="{{ $pageModule }}" aria-label="..." placeholder=" Type And Hit Enter  ">
			    </div>
		</div>
		<div class="col-md-4"> 	
			<div class="input-group filter table-actions" id="<?php echo $pageModule;?>Filter">					      
			  
			<input type="hidden" name="page" value="{{ $param['page']}}" />
			<input type="hidden" name="search" value="<?php if(isset($_GET['search'])) echo $_GET['search'] ;?>" />


			<select name="rows" class="select-alt" style="width:70px; float:left;"  >
			@foreach(array(10,20,30,50); as $p) 
			<option value="{{ $p }}" 
			@if(isset($pager['rows']) && $pager['rows'] == $p) 
			selected="selected"
			@endif	
			>{{ $p }}</option>
			@endforeach
			</select>
			<select name="sort" class="select-alt" style="width:100px;float:left;" >
			<option value=""><?php echo Lang::get('core.grid_sort');?></option>	 
			@foreach($tableGrid as $field)
			@if($field['view'] =='1' && $field['sortable'] =='1') 
			<option value="{{ $field['field'] }}" 
			@if(isset($pager['sort']) && $pager['sort'] == $field['field']) 
			selected="selected"
			@endif
			>{{ $field['label'] }}</option>
			@endif	  
			@endforeach

			</select>	
			<select name="order" class="select-alt" style="width:70px;float:left;">
			<option value="">{{ Lang::get('core.grid_order') }}</option>
			@foreach(array('asc','desc');  as $o)
			<option value="{{ $o }}"
			@if(isset($pager['order']) && $pager['order'] == $o)
			selected="selected"
			@endif	
			>{{ ucwords($o) }}</option>
			@endforeach
			</select>	
			<button type="button" class="btn  btn-default btn-sm" onclick="ajaxFilter('#<?php echo $pageModule;?>','{{ $pageUrl }}/data')" style="float:left;"><i class="fa fa-refresh"></i> GO</button>	

			</div>	
		</div>
		<div class="col-md-4 text-right"> 	
			
			<div class="btn-group">
				@if($access['is_add'] ==1)
					{!! SiteHelpers::buttonActionCreate($pageModule,$setting) !!}
				@endif
				<div class="btn-group ">
					<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-bars"></i> Bulk Action </button>
			        <ul class="dropdown-menu">
			        @if($access['is_remove'] ==1)
						 <li  class="nav-item"><a href="javascript://ajax"  class="nav-link tips delete" title="{{ __('core.btn_remove') }}">
						Remove Selected </a></li>
					@endif	
					@if($access['is_add'] ==1)
						<li  class="nav-item"><a href="javascript://ajax" class=" copy nav-link " title="Copy" > Copy selected</a></li>
						<div class="dropdown-divider"></div>

						<li  class="nav-item"><a class="nav-link" href="{{ url($pageModule .'/import?return='.$return) }}" onclick="SximoModal(this.href, 'Import CSV'); return false;"> Import CSV</a></li>
						
					@endif	
					<div class="dropdown-divider"></div>

			         
					@if($access['is_excel'] ==1)
						<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=excel&return='.$return) }}" class="nav-link "> Export Excel </a></li>	
					@endif
					@if($access['is_csv'] ==1)
						<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=csv&return='.$return) }}" class="nav-link "> Export CSV </a></li>	
					@endif
					@if($access['is_pdf'] ==1)
						<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=pdf&return='.$return) }}" class="nav-link "> Export PDF </a></li>	
					@endif
					@if($access['is_print'] ==1)
						<li class="nav-item"><a href="{{ url( $pageModule .'/export?do=print&return='.$return) }}" class="nav-link "> Print Document </a></li>	
					@endif
					<div class="dropdown-divider"></div>
					<li class="nav-item"><a href="{{ url($pageModule) }}"  class="nav-link "> Clear Search </a></li>
			          
			        </ul>
			    </div> 
		    </div>
		   	
		    

		</div>

			

		    
	</div>					
</div>	
	<!-- End Toolbar Top -->

