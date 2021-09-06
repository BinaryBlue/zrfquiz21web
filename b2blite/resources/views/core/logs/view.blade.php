@extends('layouts.app')

@section('content')
<div class="page-header ">
	<h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
	<div class="toolbar-nav">

		<div class="row">
			<div class="col-md-6">
				
			   		<a href="{{ ($prevnext['prev'] != '' ? url('core/logs/'.$prevnext['prev'].'?return='.$return ) : '#') }}" class="tips btn btn-sm"><i class="fa fa-arrow-left"></i>  </a>	
					<a href="{{ ($prevnext['next'] != '' ? url('core/logs/'.$prevnext['next'].'?return='.$return ) : '#') }}" class="tips btn btn-sm "> <i class="fa fa-arrow-right"></i>  </a>					
			</div>	
			<div class="col-md-6 text-right" >
				<a href="{{ url('core/logs?return='.$return) }}" class="tips btn btn-sm "  title="{{ Lang::get('core.btn_back') }}" ><i class="fa  fa-times"></i></a> 		
			</div>

			
		</div>
</div>

<div class="p-5">



	
	<table class="table table-striped table-bordered" >
		<tbody>	
	
					<tr>
						<td width='30%' class='label-view text-right'>IPs</td>
						<td>{{ $row->ipaddress }} </td>
						
					</tr>
				
					<tr>
						<td width='30%' class='label-view text-right'>Users</td>
						<td>{{ SiteHelpers::gridDisplayView($row->user_id,'user_id','1:tb_users:id:first_name') }} </td>
						
					</tr>
				
					<tr>
						<td width='30%' class='label-view text-right'>Module</td>
						<td>{{ $row->module }} </td>
						
					</tr>
				
					<tr>
						<td width='30%' class='label-view text-right'>Task</td>
						<td>{{ $row->task }} </td>
						
					</tr>
				
					<tr>
						<td width='30%' class='label-view text-right'>Note</td>
						<td>{{ $row->note }} </td>
						
					</tr>
				
					<tr>
						<td width='30%' class='label-view text-right'>Logdate</td>
						<td>{{ $row->logdate }} </td>
						
					</tr>
				
		</tbody>	
	</table>    
	

</div>	  
@stop