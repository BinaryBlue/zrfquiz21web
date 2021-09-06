@if($setting['view-method'] =='native')
<div class="card">
	<div class="card-body">	
		<div class="toolbar-nav">
			<div class="row">
				<div class="col-md-6" >
					<a href="javascript://ajax" onclick="ajaxViewClose('#{{ $pageModule }}')" class="tips btn btn-sm btn-danger  " title="{{ __('core.btn_back') }}"><i class="fa  fa-times"></i></a>	
				</div>
				<div class="col-md-6 text-right " >
					<div class="btn-group">
						<a href="{{ ($prevnext['prev'] != '' ? url('{class}/'.$prevnext['prev'].'?return='.$return ) : '#') }}" class="tips btn btn-primary btn-sm" onclick="ajaxViewDetail('#{class}',this.href); return false; "><i class="fa fa-arrow-left"></i>  </a>	
						<a href="{{ ($prevnext['next'] != '' ? url('{class}/'.$prevnext['next'].'?return='.$return ) : '#') }}" class="tips btn btn-primary  btn-sm " onclick="ajaxViewDetail('#{class}',this.href); return false; "> <i class="fa fa-arrow-right"></i>  </a>
			   		</div>			
				</div>	

				
			</div>
		</div>	
		
@endif	

		<table class="table  table-bordered" >
			<tbody>	
				{form_view}
			</tbody>	
		</table>  
			
		 {masterdetailview}	
		 
@if($setting['view-method'] =='native')
	</div>
</div>
@endif		