<?php

$tabs = array(
		'' 		        => ''. Lang::get('core.tab_siteinfo'),
		'email'			=> ' '. Lang::get('core.tab_email'),
		'security'		=> ' '. Lang::get('core.tab_loginsecurity') ,
		'translation'	=>' '.Lang::get('core.tab_translation')
	);

?>
<div class="p-2">
<ul class="nav nav-tabs" class="p-2">
@foreach($tabs as $key=>$val)
	<li class="nav-item">
		 <a class="nav-link @if($key == $active) active @endif" href="{{ URL::to('sximo/config/'.$key)}}">{!! $val !!} </a>
	</li>
@endforeach

</ul>
</div>