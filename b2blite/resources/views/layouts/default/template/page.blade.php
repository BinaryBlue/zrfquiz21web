
	<div class="container">		
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		    <li class="breadcrumb-item"><a href="{{  url('') }}">Home</a></li>
		    <li class="breadcrumb-item active" aria-current="page"> {{ $title }} </li>
		  </ol>
		</nav>

		<div class="section-header">
			<h2> {{ $title }}</h2>
			
		</div>	
		<div >
			<?php echo PostHelpers::formatContent($content) ;?>

		</div>
	</div>	
