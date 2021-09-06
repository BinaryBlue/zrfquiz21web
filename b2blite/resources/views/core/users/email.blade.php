<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<p>Hello  , <b>{{ $row->first_name}} {{ $row->last_name }}</b> </p>
		<p> You have Message From <b>{{ $cnf_appname }}</b> , Bellow is Message Detail  </p>
		<p><b> Message :</b> </p>
		<div>
			{{ $note }}
		</div>
		
		<p> Thank You </p><br /><br />
		
		<h3>{{ $cnf_appname }}</h3> 
	</body>
</html>