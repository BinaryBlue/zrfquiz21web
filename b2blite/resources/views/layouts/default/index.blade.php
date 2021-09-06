<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ config('sximo.cnf_appname') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico')}}" type="image/x-icon">

    <!-- CSS Files -->
    <link href="{{ asset('sximo5/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    
    <link href="{{ asset('sximo5/fonts/awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ asset('frontend/default/css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons/material-design-iconic-font/css/materialdesignicons.min.css')}}" rel="stylesheet">
    <!-- CSS Just for demo purpose, don't include it in your project --> 
    <script src="{{ asset('')}}assets/plugins/moment/moment.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js/sximo.min.js') }}"></script>
    
      



    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js">
    </script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js">
    </script>
    <![endif]-->
  </head>
<body id="body">
  <!--==========================
    Header
  ============================-->
  <header id="header">
    <div class="container">

      <div id="logo" class="pull-left">
       
        <!-- Uncomment below if you prefer to use an image logo -->
        <h1>
        <a href="{{ url('')}}"> 
            
             @if(file_exists(public_path().'/uploads/images/'.config('sximo')['cnf_logo']) && config('sximo')['cnf_logo'] !='')
              <img src="{{ asset('uploads/images/'.config('sximo')['cnf_logo'])}}" alt="{{ config('sximo')['cnf_appname'] }}" width="30" />
              @else
              <img src="{{ asset('uploads/logo.png')}}" alt="{{ config('sximo')['cnf_appname'] }}"  width="30" />
              @endif
               {{ config('sximo.cnf_appname') }} </h1>
        </a>
        </h1>
      </div>
      <nav id="nav-menu-container">
          @include('layouts.default.navigation')
        </nav>  
    </div>
  </header><!-- #header -->


   <main id="main">

        @include($pages)
     
   </main>


  <!--==========================
    Footer
  ============================-->
  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong>{{ config('sximo.cnf_comname') }}</strong>. All right reserved
      </div>
      <div class="credits">
        <!--
          All the links in the footer should remain intact.
          You can delete the links only if you purchased the pro version.
          Licensing information: https://bootstrapmade.com/license/
          Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Reveal
        -->
       
      </div>
    </div>
  </footer><!-- #footer -->
 <script type="text/javascript" src="{{ asset('frontend/default/js/default.min.js') }}"></script>

 <script type="text/javascript">
     // Preloader
  

 </script>
<style type="text/css">
    #particles-js canvas {
        position: absolute;
        z-index: 1;
    }
</style>
  </body>
</html>