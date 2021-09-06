<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2> Hi There , </h2>
		<p> You have new submited form from visitor . Bellow is form detail :  </p>

		<div class="" style="padding:20px; border: solid 1px #ddd; background: #f9f9f9;">
		<h3> {{ $title }} </h3>	
		<table>	
			@foreach( $content as $key => $value  )	
			<tr>
			
				<td> {{ ucwords($key) }} </td>
				<td> : </td>
				<td> {{ $value }} </td>	
			
			</tr>
			@endforeach
		</table>	
		</div>
		
		<p> Thank You </p><br /><br />
		
		 {{ config('sximo.cnf_appname') }}
	</body>
</html>