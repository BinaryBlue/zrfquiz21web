@extends('layouts.app')
<link href="{{ asset('blueerp/blueerp.css')}}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('blueerp/vue.js') }}"></script>
<script type="text/javascript" src="{{ asset('blueerp/plugins/axios.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('blueerp/plugins/vueselect/vue-select.js') }}"></script>
<link href="{{ asset('blueerp/plugins/vueselect/vue-select.css')}}" rel="stylesheet">
@section('content')
<h4 class="text-center">Laravel 7 RealTime Google Firebase CRUD Example - Tutsmake.com</h4><br>
<h5># Add User</h5>
<div class="card card-default">
	<div class="card-body">
		<form id="addUser" class="form-inline" method="POST" action="">
			<div class="form-group mb-2">
				<label for="name" class="sr-only">Name</label>
				<input id="name" type="text" class="form-control" name="name" placeholder="Name" required autofocus>
			</div>
			<div class="form-group mx-sm-3 mb-2">
				<label for="email" class="sr-only">Email</label>
				<input id="email" type="email" class="form-control" name="email" placeholder="Email" required autofocus>
			</div>
			<button id="submitUser" type="button" class="btn btn-primary mb-2">Submit</button>
		</form>
	</div>
</div>
<br>
<h5># Users</h5>
<table class="table table-bordered">
	<tr>
		<th>Name</th>
		<th>Email</th>
		<th width="180" class="text-center">Action</th>
	</tr>
	<tbody id="tbody">
	</tbody>
</table>
</div>
<!-- Update Model -->
<form action="" method="POST" class="users-update-record-model form-horizontal">
	<div id="update-modal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog"
		aria-labelledby="custom-width-modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" style="width:55%;">
			<div class="modal-content" style="overflow: hidden;">
				<div class="modal-header">
					<h4 class="modal-title" id="custom-width-modalLabel">Update</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
					</button>
				</div>
				<div class="modal-body" id="updateBody">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">Close
					</button>
					<button type="button" class="btn btn-success updateUser">Update
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
<!-- Delete Model -->
<form action="" method="POST" class="users-remove-record-model">
	<div id="remove-modal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog"
		aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-dialog-centered" style="width:55%;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="custom-width-modalLabel">Delete</h4>
					<button type="button" class="close remove-data-from-delete-form" data-dismiss="modal"
						aria-hidden="true">×
					</button>
				</div>
				<div class="modal-body">
					<p>Do you want to delete this record?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form"
						data-dismiss="modal">Close
					</button>
					<button type="button" class="btn btn-danger waves-effect waves-light deleteRecord">Delete
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
	// Initialize Firebase
	//firebase.initializeApp(firebaseConfig);
	//firebase.analytics();
	
	firebase.initializeApp(firebaseConfig);
    firebase.analytics();
	var database = firebase.database();
	var lastIndex = 0;
	//var thisuser = firebase.auth().currentUser.uid;

	const dbRef = database.ref();
	dbRef.child("users").child("toAC9ETRnig4mzH893AKljyVXpE2").get().then((snapshot) => {
	if (snapshot.exists()) {
		console.log(snapshot.val());
	} else {
		console.log("No data available");
	}
	}).catch((error) => {
		console.error(error);
	});

	{{-- var fUsers = database.collection("users").doc("toAC9ETRnig4mzH893AKljyVXpE2");
	fUsers.get().then((doc) => {
			if (doc.exists) {
				console.log("Document data:", doc.data());
			} else {
				// doc.data() will be undefined in this case
				console.log("No such document!");
			}
		}).catch((error) => {
			console.log("Error getting document:", error);
	}); --}}



	// Get Data
	firebase.database().ref('zrf-quiz-21/users/').on('value', function(snapshot) {
	    var value = snapshot.val();
	    var htmls = [];
	    $.each(value, function(index, value) {
	        if (value) {
	            htmls.push('<tr>\
<td>' + value.name + '</td>\
<td>' + value.email + '</td>\
<td><button data-toggle="modal" data-target="#update-modal" class="btn btn-info updateData" data-id="' + index + '">Update</button>\
<button data-toggle="modal" data-target="#remove-modal" class="btn btn-danger removeData" data-id="' + index + '">Delete</button></td>\
</tr>');
	        }
	        lastIndex = index;
	    });
	    $('#tbody').html(htmls);
	    $("#submitUser").removeClass('desabled');
	});
	// Add Data
	$('#submitUser').on('click', function() {
	    var values = $("#addUser").serializeArray();
	    var name = values[0].value;
	    var email = values[1].value;
	    var userID = lastIndex + 1;
	    console.log(values);
	    firebase.database().ref('zrf-quiz-21/users/' + userID).set({
	        name: name,
	        email: email,
	    });
	    // Reassign lastID value
	    lastIndex = userID;
	    $("#addUser input").val("");
	});
	// Update Data
	var updateID = 0;
	$('body').on('click', '.updateData', function() {
	    updateID = $(this).attr('data-id');
	    firebase.database().ref('zrf-quiz-21/users/' + updateID).on('value', function(snapshot) {
	        var values = snapshot.val();
	        var updateData = '<div class="form-group">\
							<label for="first_name" class="col-md-12 col-form-label">Name</label>\
							<div class="col-md-12">\
							<input id="first_name" type="text" class="form-control" name="name" value="' + values.name + '" required autofocus>\
							</div>\
							</div>\
							<div class="form-group">\
							<label for="last_name" class="col-md-12 col-form-label">Email</label>\
							<div class="col-md-12">\
							<input id="last_name" type="text" class="form-control" name="email" value="' + values.email + '" required autofocus>\
							</div>\
							</div>';
	        $('#updateBody').html(updateData);
	    });
	});
	$('.updateUser').on('click', function() {
	    var values = $(".users-update-record-model").serializeArray();
	    var postData = {
	        name: values[0].value,
	        email: values[1].value,
	    };
	    var updates = {};
	    updates['zrf-quiz-21/users/' + updateID] = postData;
	    firebase.database().ref().update(updates);
	    $("#update-modal").modal('hide');
	});
	// Remove Data
	$("body").on('click', '.removeData', function() {
	    var id = $(this).attr('data-id');
	    $('body').find('.users-remove-record-model').append('<input name="id" type="hidden" value="' + id + '">');
	});
	$('.deleteRecord').on('click', function() {
	    var values = $(".users-remove-record-model").serializeArray();
	    var id = values[0].value;
	    firebase.database().ref('Users/' + id).remove();
	    $('body').find('.users-remove-record-model').find("input").remove();
	    $("#remove-modal").modal('hide');
	});
	$('.remove-data-from-delete-form').click(function() {
	    $('body').find('.users-remove-record-model').find("input").remove();
	});
</script>
<script type="text/javascript" src="{{ asset('blueerp/blueerp-mixin.js') }}"></script> 
<script type="text/javascript" src="{{ asset('blueerp/blueerp.js') }}"></script> 
<script type="text/javascript" src="{{ asset('blueerp/apps/stockentry.js') }}"></script>
@endsection