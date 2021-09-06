<div class="row">
	@foreach($rows as $row)
		<div class="col-md-3 col-6">
			<div class="card">
				<div class="card-header">
				<b class="card-title"> {{ $row->Title }} </b>
				</div>
				<div class="card-body">
					<div class="img" style="height: 120px; background: #f9f9f9; margin: -20px -20px 0 -20px;  ">
						<img src="{{ $row->Preview }}" class="img-responsive">
					</div>
					<div class="sinopsis"> {{ $row->Sinopsis }} </div>
					<div class="text-center">
					<h5>
					   
					</h5>            
					</div>	
					<div class="row">
						<div class="col-4">
							<button class="btn btn-sm">
								@if($row->PriceReguler >=1)
							$ {{ $row->PriceReguler }}
							@else
							Free
							@endif
							</button>
							

						</div>
						
						<div class="col-8">
							<div class="btn-group  btn-block">
								<a href="http://sximo5.net/product/{{ $row->Slug }}" target="_blank" class="btn btn-sm  btn-info"> Detail </a>	<a href="javascript://ajax" 
						onclick="SximoModal('{{ url("sximo/server/install?id=".$row->ProductID."&t=".$row->ScriptType) }}' ,'Download & Install' ); return false;" class="btn btn-sm btn-primary "> Install </a>
							</div>
						</div>
					</div>	

				</div>
				

			</div>
		</div>	
	@endforeach
</div>

<style type="text/css">
	.card {
		box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.1) !important;
		border:solid 1px #eee;
		margin-bottom: 20px;
	}
	.card-header {

    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
	}
	.card .img {
		overflow: hidden;
		border: none !important;
		margin: -10px;
	}
	.card .sinopsis {
		overflow: hidden;
		height: 50px;
		white-space: nowrap;
		text-overflow: ellipsis;
	   
	    overflow: hidden;
	}
	.card-footer {
		border-top : 1px solid #eee;
	}
	.card-header {
		border-bottom : 1px solid #eee;
	}
	.spanel.item {
		
		margin-bottom: 20px; 
		border:solid 1px #eee;
	}
	.spanel.item .panel-body{
		height: 160px;
		padding: 0;
		
	}
	.spanel.item .panel-body h3 {
		padding: 5px 0;
		margin: 0;font-weight: 700;
	}
	.spanel.item .panel-body .img{
		height: 120px;
		overflow: hidden;
	}
</style>