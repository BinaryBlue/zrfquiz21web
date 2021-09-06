			<!-- Toolbar Top -->
		<div class="toolbar-nav">
			<div class="row ">
				<div class="col-md-4 ">
					<div class="input-group text-left">
					      
					      <input type="text" class="form-control form-control-sm dosearch" data-target="{{ url($pageModule) }}" data-div="{{ $pageModule }}" aria-label="..." placeholder=" search  ">
					</div>  
					    
				</div>
				<div class="col-md-8 text-right"> 	
					<div class="btn-group">
						@if($access['is_add'] ==1)
							{!! SiteHelpers::buttonActionCreate($pageModule,$setting) !!}
						@endif
						<div class="btn-group ">
							<button type="button" class="btn  btn-sm  btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Bulk Action </button>
					        <ul class="dropdown-menu">
					        @if($access['is_remove'] ==1)
								 <li class="nav-item"><a href="javascript://ajax"   class="nav-link tips Action_Row" code="remove" title="{{ __('core.btn_remove') }}">
								Remove Selected </a></li>
							@endif
							@if($access['is_add'] ==1)							
								<li class="nav-item"><a href="javascript://ajax" class="nav-link Action_Row" code="copy" title="Copy" > Copy selected</a></li>
								<div class="dropdown-divider"></div>
								<li class="nav-item"><a href="{{ url($pageModule .'/import?return='.$return) }}" onclick="SximoModal(this.href, 'Import CSV'); return false;" class="nav-link">Import CSV</a></li>
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
					        <a href="{{ url($pageModule) }}" class="tips btn btn-sm btn-warning" title="{{ __('core.btn_reload') }}" ><i class="fa  fa-sync-alt"></i></a>
					    </div>    

					    
					</div>	
				</div>
				    
			</div>					
			<!-- End Toolbar Top -->
		</div>	