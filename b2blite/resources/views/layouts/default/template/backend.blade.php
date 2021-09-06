@extends('layouts.app')

@section('content')
<div class="page-header"><h2> {{ $title }} </h2></div>
<div class="table-container">
	<div class="">
	<?php echo $content ;?>
	</div>
</div>
@endsection
