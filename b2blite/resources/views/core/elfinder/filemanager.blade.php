@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2><i class="fas fa-folder"></i> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>


    <div id="elfinder"></div>

<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/pepper-grinder/jquery-ui.css" />
<script type="text/javascript" src="{{ asset('sximo5/js/plugins/elfinder/js/elfinder.min.js') }}"></script>
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sximo5/js/plugins/elfinder/css/elfinder.min.css')}}" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('sximo5/js/plugins/elfinder/css/theme.css')}}" />




<script type="text/javascript" charset="utf-8">
    $().ready(function() {
        var elf = $('#elfinder').elfinder({
            // lang: 'ru',             // language (OPTIONAL)
            url : '{{ url("core/elfinder") }}'  ,// connector URL (REQUIRED)
			height:500,
        }).elfinder('instance');            
    });
</script>
@stop