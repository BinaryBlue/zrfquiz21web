@extends('layouts.app')
<link href="{{ asset('blueerp/blueerp.css')}}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('blueerp/vue.js') }}"></script>
<script type="text/javascript" src="{{ asset('blueerp/plugins/axios.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('blueerp/plugins/vueselect/vue-select.js') }}"></script>
<link href="{{ asset('blueerp/plugins/vueselect/vue-select.css')}}" rel="stylesheet">
@section('content')
<div id="participent">

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
</div>
<script>
	// Initialize Firebase
	//firebase.initializeApp(firebaseConfig);
	//firebase.analytics();
	
	firebase.initializeApp(firebaseConfig);
    firebase.analytics();
	//var database = firebase.database();
</script>
<script type="text/javascript" src="{{ asset('blueerp/blueerp-mixin.js') }}"></script> 
<script type="text/javascript" src="{{ asset('blueerp/blueerp.js') }}"></script> 
<script>
	Vue.component('v-select', VueSelect.VueSelect);
var stockentryapp = new Vue({
    el: '#participent',
    data: {
		participentLists: [],
    },
    watch:{
    },
    methods: {
      getParticipents: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/firebase/participents',p_headers);
          this.participentLists = response.data;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
    },
    computed:{},
    created: function(){
      this.getParticipents();
    }
  });

</script>
@endsection