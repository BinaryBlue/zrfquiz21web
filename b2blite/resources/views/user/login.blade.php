@extends('layouts.login')
<script type="text/javascript" src="{{ asset('blueerp/vue.js') }}"></script>
<script type="text/javascript" src="{{ asset('blueerp/plugins/axios.min.js') }}"></script>
@section('content')
	
<div class="ajaxLoading"></div>
<div id="vueLogin">

<input name="_token" type="hidden" value="{{csrf_token()}}">
<div class="animated fadeInUp delayp1" id="tab-sign-in" class="authentication-form">
	<div class="row">
		<div class="col-12" id="b2blitefirebaseauth"></div>
	</div>
	<p class="message alert alert-danger " style="display:none;"></p>	
	 
		    	@if(Session::has('status'))
		    		@if(session('status') =='success')
		    			<p class="alert alert-success">
							{!! Session::get('message') !!}
						</p>
					@else
						<p class="alert alert-danger">
							{!! Session::get('message') !!}
						</p>
					@endif		
				@endif

			<ul class="parsley-error-list">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>		
</div>
</div>

	
 

<script src="https://www.gstatic.com/firebasejs/6.3.3/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/6.3.3/firebase-auth.js"></script>
<script src="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.js"></script>
<link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.css"/>
<script>
	var firebaseConfig = {
		apiKey: "AIzaSyBXCiBrbSJjWTOPTS02Ntftrq1u1UxoP9o",
		authDomain: "zrf-quiz-21.firebaseapp.com",
		databaseURL: "https://zrf-quiz-21-default-rtdb.europe-west1.firebasedatabase.app",
		projectId: "zrf-quiz-21",
		storageBucket: "zrf-quiz-21.appspot.com",
		messagingSenderId: "670190433664",
		appId: "1:670190433664:web:a7021491d28f8217c83d65",
		measurementId: "G-K68TNYVKRC"
	  };
	  var api_uri='{{env("API_URL")}}';
	  var app_uri='{{env("APP_URL")}}';
	  var p_headers = {
			crossDomain: true,
			headers: {
			"Access-Control-Allow-Origin": "*",
			"Access-Control-Allow-Methods": "GET, POST, PATCH, PUT, DELETE, OPTIONS",
			"Access-Control-Allow-Headers": "Origin, Content-Type",
			//'Authorization': "Bearer ",
			}
    	}
	var loginApp = new Vue({
		el: '#vueLogin',
		methods: {
			serverSync: async function(accessToken) {
				postForm
			(
				app_uri+'user/signin', 
				{ uid: "toAC9ETRnig4mzH893AKljyVXpE2", password:12345678, access_tokken:"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMmU5OTc5MjZjZTkwZjVjMGI0OGYzNDNmNjEyMGRiMDYyMWU4MWRhM2VhNzU0NWJmYTFlNWJlNjYxNmU4YjM2MmJiYmY1YTBmMTkwNGNjM2UiLCJpYXQiOjE2MzA3NDAzODMsIm5iZiI6MTYzMDc0MDM4MywiZXhwIjoxNjYyMjc2MzgzLCJzdWIiOiIxMyIsInNjb3BlcyI6W119.Ax8sX21EpdX-Jpyip1GaDGPHEt11cdWLjhYADDQDU4grmIVMeFe4Orye6thlHyOleSyI9GyBulsqqwIJyGsK3XiHM8sZozuwqEO7ViT7CRtY5rX-ERFA9iQL-gKCVJjYo6RvwUIkVQjFplONOEQu8l1xVM55moRtcxbgC74zAl5Up5OeeK2vWcKkYGngC2OT2-xRZ4ZNlRjxl7_Xl-erXkgIBF292pSMrrVjJFY3dlgPRczb_8xI83v24ExrtfLkO7o9BcGCzX_RI4fuxm7fRh4u8AwgQNp1kIB1evJrXPktGWaRge2GwRVTH8Cab-RTQXYl6_p6sVp7gGAZ509nmqQg0tWc_fkX0Acn5YLMY-AT6LowGHu6Zuxw1H3regLfdUbVwY2-A_aZodSiqnz17uMj2Q54FGkieNp1Sld-AQp6d9ZG3QuOn4G9bgYvROramKta_Ti_5DsA8tdYyYvg0s_VWezYQ-Wnad0OWTiimXdXgEhAKtNkyQbvZZ3Hswy1Okyl5WsIvOXgdUQL_Y_z76XjZooueUQAiUGJ3BwAfSqinud4oI8QeHzVBBnwNYg92vZa39IcEAa2tm1qs95nwPYYpVzvajBPo_bftXXpBuDOAqbKTt2jLHn-_NGwIA9Wa9lW4E6n6YnPVW_GdXCurFt8yXAaX8407Mj79TuIFYA" }
			);
				axios.post(api_uri+'api/v1/user/firebaselogin/',{Firebasetoken:accessToken} , {
					"Access-Control-Allow-Origin": app_uri,
					"Access-Control-Allow-Methods": "GET, POST, PATCH, PUT, DELETE, OPTIONS",
					"Access-Control-Allow-Headers": "Origin, Content-Type",
					//'Authorization': "Bearer ",
					})
				.then(function (response) {
					//console.log(response);
					$('.ajaxLoading').hide();
					
				})
				.catch(function (error) {
					console.log(error);
					$('.ajaxLoading').hide();
				});
			},
		},

		});
	firebase.initializeApp(firebaseConfig);
	// 1) Create a new firebaseui.auth instance stored to our local variable ui

	firebase.auth().onAuthStateChanged(user => {
		$('.ajaxLoading').show();
		user.getIdToken().then(function(accessToken) {
			console.log(user.uid);
			console.log(accessToken);
			$('.ajaxLoading').hide();
			loginApp.serverSync(accessToken)
			//syncServer(accessToken);
			//postForm('/user/signin', {Firebasetoken: accessToken});
		  // I personally store this token using Vuex 
		  // so i can watch it and detect its change to act accordingly. 
		})   
		
	});

	const ui = new firebaseui.auth.AuthUI(firebase.auth());

	// 2) These are our configurations.
	const uiConfig = {
	callbacks: {
		signInSuccessWithAuthResult(authResult, redirectUrl) {
			authResult.user.getIdToken().then(function(accessToken) {
				//console.log(accessToken);
				//alert(accessToken);
				// I personally store this token using Vuex 
				// so i can watch it and detect its change to act accordingly. 
			  })
			  
			return false;
		},
		uiShown() {
		//document.getElementByClass("ajaxLoading").style.display = "none";
		},
	},
	signInFlow: "popup",
	signInSuccessUrl: "signedIn",
	signInOptions: [
		firebase.auth.EmailAuthProvider.PROVIDER_ID,
		firebase.auth.PhoneAuthProvider.PROVIDER_ID,
		firebase.auth.FacebookAuthProvider.PROVIDER_ID,
		// Additional login options should be listed here
		// once they are enabled within the console.
	],
	};

	// 3) Call the 'start' method on our ui class
	// including our configuration options.
	ui.start("#b2blitefirebaseauth", uiConfig);


	function syncServer(accessToken){
		$.ajax({
			url: api_uri+'api/v1/user/firebaselogin',
			type: 'POST',
			data: {
				Firebasetoken:accessToken
			},
			//headers: {
			//	'Content-Type':'application/json'
			//},
			dataType: 'json',
			success: function (data) {
				postForm
				(
					app_uri+'user/signin', 
					{ uid: data.user.uid, password:12345678, access_tokken:data.access_tokken }
				);
			},
			error: function(re){
				console.log(re);
			}
		});


		{{-- $.post( url: api_uri+'api/v1/user/firebaselogin', data: {Firebasetoken:accessToken}, 
				headers: {
				"Access-Control-Allow-Origin": api_uri,
				"Access-Control-Allow-Methods": "GET, POST, PATCH, PUT, DELETE, OPTIONS",
				"Access-Control-Allow-Headers": "Origin, Content-Type, X-Auth-Token"},
				)
			.done(function(data){
			console.log('line 88');
			console.log(data);
			postForm
			(
				app_uri+'user/signin', 
				{ uid: data.user.uid, password:12345678, access_tokken:data.access_tokken }
			);
		}); --}}

		
	}
	function postForm(path, params, method='post') {

		// The rest of this code assumes you are not using a library.
		// It can be made less verbose if you use one.
		const form = document.createElement('form');
		form.method = method;
		form.action = path;
	  
		for (const key in params) {
		  if (params.hasOwnProperty(key)) {
			const hiddenField = document.createElement('input');
			hiddenField.type = 'hidden';
			hiddenField.name = key;
			hiddenField.value = params[key];
	  
			form.appendChild(hiddenField);
		  }
		}
	  
		document.body.appendChild(form);
		form.submit();
	  }
  </script>
@stop