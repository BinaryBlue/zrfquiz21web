@if($setting['view-method'] =='native')
<div class="card">
	<div class="card-body">	
		<div class="toolbar-nav">
			<div class="row">
				<div class="col-md-6" >
			   		<a href="{{ ($prevnext['prev'] != '' ? url('{class}/'.$prevnext['prev'].'?return='.$return ) : '#') }}" class="tips btn btn-sm" onclick="ajaxViewDetail('#{class}',this.href); return false; "><i class="fa fa-arrow-left"></i>  </a>	
					<a href="{{ ($prevnext['next'] != '' ? url('{class}/'.$prevnext['next'].'?return='.$return ) : '#') }}" class="tips btn btn-sm " onclick="ajaxViewDetail('#{class}',this.href); return false; "> <i class="fa fa-arrow-right"></i>  </a>					
				</div>	

				<div class="col-md-6 text-right" >
					<a href="javascript://ajax" onclick="ajaxViewClose('#{{ $pageModule }}')" class="tips btn btn-sm  " title="{{ __('core.btn_back') }}"><i class="fa  fa-times"></i></a>		
				</div>
			</div>
		</div>	
@endif	

		<div class="p-5">
		  <!-- Nav tabs -->
		  <ul class="nav nav-tabs form-tab" role="tablist">
		  	<li class="nav-item">

		  		<a href="#home{{ $row->{key} }}" aria-controls="home" role="tab" data-toggle="tab" class="nav-link active">  {{ $pageTitle}} :   View Detail </a></li>
			@foreach($subgrid as $sub)
				<li class="nav-item">
					<a href="#{{ str_replace(" ","_",$sub['title']) }}{{ $row->{$sub['master_key']} }}" aria-controls="profile" role="tab" data-toggle="tab" class="nav-link ">
					{{ $pageTitle}} :  {{ $sub['title'] }}
					</a>
				</li>
			@endforeach
		  </ul>


		  <!-- Tab panes -->
		  <div class="tab-content m-t">
		  	<div role="tabpanel" class="tab-pane active" id="home{{ $row->{key} }}">

				<div class="table-responsive" >  
					<table class="table table-striped table-bordered" >
						<tbody>	
							{form_view}		
						</tbody>	
					</table>  		
				</div>
				
		  	</div>
		  	@foreach($subgrid as $sub)
		  	<div role="tabpanel" class="tab-pane" id="{{ str_replace(" ","_",$sub['title']) }}{{ $row->{$sub['master_key']} }}"></div>
		  	@endforeach
		  </div>
		</div>
		 	
@if($setting['view-method'] =='native')
	</div>
</div>
@endif		
	

<script type="text/javascript">
	$(function(){
		$('.form-tab a').on('click', function (e) {
		  	e.preventDefault()
		  	$(this).tab('show')
		})
		<?php foreach($subgrid as $sub) { ?>
			$('#{{ str_replace(" ","_",$sub['title']) }}{{ $row->{$sub['master_key']} }}').load('{!! url($sub['module']."/lookup?param=".implode("-",$sub)."-".$row->{$sub['master_key']})!!}')
		<?php } ?>

		
	})

</script>