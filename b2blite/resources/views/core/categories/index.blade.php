@extends('layouts.app')

@section('content')

<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div  class="card">
	<div class="card-body">
		<div class="toolbar-nav">
			<div class="row">
				<div class="col-md-8"> 	
					
					


					   
				</div>
				<div class="col-md-4 text-right ">
					<a href="{{ url('cms/categories/create') }}" onclick="SximoModal( this.href ,'Edit Category'); return  false ;" class="btn btn-primary btn-sm"  
						title="{{ __('core.btn_create') }}"><i class="fas fa-plus"></i> </a>
					
				</div>    
			</div>
		</div>	

		<div class="pb-3">
			<ul class="nav nav-tabs " >
			  <li class="nav-item"><a href="{{ url('cms/posts')}}"  class="nav-link "> All Posts </a></li>
			  <li class="nav-item"><a href="#config"  class="nav-link active"> All Categories </a></li>
			</ul>
		</div>	

		<div class="p-2">
			<div class="list-group">
				@foreach($rows as $row)
				<a href="{{ url('cms/categories/'.$row->cid.'/edit')}}" class="list-group-item list-group-item-action " onclick="SximoModal( this.href ,'Edit Category'); return  false ;">
					{{ $row->name }} ( {{ $row->total }} )
				</a>
				@endforeach
			</div>  

		</div>
	</div>
</div>			

@stop