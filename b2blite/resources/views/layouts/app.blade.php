<?php $sximoconfig  = config('sximo');?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico')}}" type="image/x-icon">
    <title>{{ config('sximo.cnf_appname') }}</title>
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link href="{{ asset('')}}assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('')}}assets/plugins/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" rel="stylesheet">
    <!-- This page CSS -->
    <!--c3 CSS -->
    
    <link href="{{ asset('adminlte/css/adminlte.min.css')}}" rel="stylesheet">

    <link href="{{ asset('')}}assets/plugins/c3-master/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('')}}assets/css/style.css" rel="stylesheet">
    <link href="{{ asset('blueerp/coreold.css')}}" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="{{ asset('')}}assets/css/legacy.css" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="{{ asset('')}}assets/css/colors/colors.css" id="themes" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="{{ asset('')}}assets/plugins/toast-master/css/jquery.toast.css" rel="stylesheet">
    <link href="{{ asset('')}}assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">





    <script src="{{ asset('')}}assets/plugins/moment/moment.js"></script>
    <script src="{{ asset('')}}assets/js/sximo.min.js"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="{{ asset('')}}assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="{{ asset('')}}assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('')}}assets/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="{{ asset('')}}assets/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('')}}assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
    <script src="{{ asset('')}}assets/plugins/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="{{ asset('')}}assets/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('')}}assets/js/custom.min.js"></script>
   
    <!--c3 JavaScript 
    <script src="{{ asset('')}}assets/plugins/d3/d3.min.js"></script>
    <script src="{{ asset('')}}assets/plugins/c3-master/c3.min.js"></script> --> 
     <script src="{{ asset('')}}assets/js/sximo5.js"></script>
     <script src="{{ asset('')}}assets/plugins/styleswitcher/jQuery.style.switcher.js"></script>
    <!-- Chart JS -->
   
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="{{ asset('blueerp/plugins/pusher.min.js')}}"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-database.js"></script>
    
    <script>
    var _usr= {{Auth::user()->id}};
    var p_token='{{Auth::user()->p_token}}';
    var api_uri='{{env("API_URL")}}';
    var app_uri='{{env("APP_URL")}}';
    var base_url = '{{url("/")}}';

    var p_headers = {
        crossDomain: true,
        headers: {
        "Access-Control-Allow-Origin": app_uri,
        "Access-Control-Allow-Methods": "GET, POST, PATCH, PUT, DELETE, OPTIONS",
        "Access-Control-Allow-Headers": "Origin, Content-Type, X-Auth-Token",
        'Authorization': "Bearer "+p_token,
        }
    };
    var b_usr_gp = {{ Auth::user()->group_id }};
    var usr_outlet = 0;
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
    </script>

</head>

<body class="fix-header fix-sidebar  light-theme  ">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label"> {{ config('sximo.cnf_appname') }} </p>
        </div>
    </div>


    <div id="main-wrapper">
      @include('layouts.topnav')
      @include('layouts.leftnav')   

        <!-- Page Content -->
        <div class="page-wrapper">
          <div class="container-fluid">
          <div class="ajaxLoading"></div>
         
            @yield('content') 
          </div>      
    </div>
    <!-- /#wrapper -->


</div>

<div class="modal" id="sximo-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog  " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">New message</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="sximo-modal-content">
                
            </div>
           
        </div>
    </div>
</div>



    <!-- /#wrapper -->
{{ SiteHelpers::showNotification() }} 
<div style="position: fixed;bottom: 0;right: 0;"> 
    <a href="#" target="_blank" /> <img class="animated bounceIn delayp0" src="{{ asset('backend-logo.png')}}" alt=ZRF" width="150"  /> </a>

</div>
</body>
</html>


