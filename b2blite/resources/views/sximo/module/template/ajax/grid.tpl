@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>	

<div class="resultData"></div>
<div id="{{ $pageModule }}View"></div>			
<div id="{{ $pageModule }}Grid"></div>
	

<script>
$(document).ready(function(){
	reloadData('#{{ $pageModule }}','{{ $pageModule }}/data');	
});	
</script>	
@endsection