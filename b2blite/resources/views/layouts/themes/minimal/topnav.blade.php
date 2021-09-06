<!-- Top Navigation -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header">
                <!-- Toggle icon for mobile view -->
                <div class="top-left-part">
                    <!-- Logo -->
                    <a class="logo" href="{{ url('dashboard') }}">
                        <!-- Logo icon image, you can use font-icon also --><b>
                        <!--This is dark logo icon--><img src="{{ asset('')}}assets/plugins/images/admin-logo.png" alt="home" class="dark-logo" /><!--This is light logo icon--><img src="{{ asset('')}}assets/plugins/images/admin-logo-dark.png" alt="home" class="light-logo" />
                     </b>
                    <span class="hidden-xs">
                        
                         @if(file_exists(public_path().'/assets/plugins/images/'.$sximoconfig['cnf_logo']) && $sximoconfig['cnf_logo'] !='')
                        <img src="{{ asset('assets/plugins/images/'.$sximoconfig['cnf_logo'])}}" alt="{{ $sximoconfig['cnf_appname'] }}"  />
                        @else
                        <img src="{{ asset('assets/plugins/images/logo.png')}}" alt="{{ $sximoconfig['cnf_appname'] }}"  />
                        @endif 
                    </span> 

                     </a>
                </div>
                <!-- /Logo -->
                <!-- Search input and Toggle icon -->
                <ul class="nav navbar-top-links navbar-left">
                    <li><a href="{{ url('') }}" class="" target="_blank"><i class="icon-globe"></i></a></li>

                    <li><a href="javascript:void(0)" class="open-close waves-effect waves-light visible-xs"><i class="ti-close ti-menu"></i></a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"> <i class="mdi mdi-bell-outline"></i>
                            <div style="display: none;" class="notify"><span class="heartbit"></span><span class="point"></span></div>
                        </a>
                        <ul class="dropdown-menu mailbox animated bounceInDown ">
                            <li>
                                <div class="drop-title">You have (<b class="notif-alert">0</b>) new Notification</div>
                            </li>
                            <div class="notification-menu">

                            </div>
                            <li>
                                <a class="text-center" href="{{ url('notification') }}"> <strong>See all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                            </li>
                        </ul>
                        <!-- /.dropdown-messages -->
                    </li>
                    <!-- .Task dropdown -->
                    
                    <!-- .Megamenu -->
                    @if(Auth::user()->group_id == 1 or Auth::user()->group_id == 2 )
                    <li class="mega-dropdown"> <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"><span class="hidden-xs">Control Panel </span> <i class="icon-options-vertical"></i></a>
                        <ul class="dropdown-menu mega-dropdown-menu animated bounceInDown">
                             @if(Auth::user()->group_id == 1 )
                            <li class="col-sm-3">
                                <ul>
                                    <li class="dropdown-header"> Control Panel </li>
                                     <li><a href="{{ url('sximo/config')}}"><i class="icon-screen-desktop"></i> {{ Lang::get('core.tab_siteinfo') }}</a> </li> 
                                     <li><a href="{{ url('sximo/config/email')}}"><i class="icon-link"></i> {{ Lang::get('core.tab_email') }}</a> </li> 
                                     <li><a href="{{ url('sximo/config/security')}}"><i class="icon-lock"></i> {{ Lang::get('core.tab_loginsecurity') }}</a> </li> 
                                     <li><a href="{{ url('sximo/config/translation')}}"><i class="icon-map"></i> {{ Lang::get('core.tab_translation') }}</a> </li>  
                                     <li> <a href="{{ url('sximo/config/clearlog')}}" class="clearCache"><i class="icon-trash"></i> Clear Log & Caches </a> </li>

                                </ul>
                            </li>
                            @endif
                           @if(Auth::user()->group_id == 1 or Auth::user()->group_id == 2 )
                            <li class="col-sm-3">
                                <ul>
                                    <li class="dropdown-header"> Administrator </li>
                                    <li ><a href="{{ url('core/users')}}"><i class="icon-user"></i> 
                                    {{ Lang::get('core.m_users') }} </a> </li> 
                                    <li ><a href="{{ url('core/groups')}}"><i class="icon-people"></i>  {{ Lang::get('core.m_groups') }}</a> </li>
                                    <li><a href="{{ url('core/users/blast')}}"><i class="icon-envelope"></i> Send Mail</a></li> 
                                    <li><a href="{{ url('core/pages')}}"><i class="icon-notebook"></i> {{ Lang::get('core.m_pagecms')}}</a></li> 
                                    <li ><a href="{{ url('core/posts')}}"><i class="icon-docs"></i> Post Management</a></li>   
                                     <li><a href="{{ url('core/logs')}}"><i class="icon-clock"></i> {{ Lang::get('core.m_logs') }}</a></li>
                                </ul>
                            </li>
                            @endif
                             @if(Auth::user()->group_id == 1  )
                            <li class="col-sm-3">
                                <ul>
                                    <li class="dropdown-header"> Superadmin </li> 
                                            
                                    <li><a href="{{ url('sximo/module')}}"><i class="icon-fire"></i> Module {{ Lang::get('core.m_codebuilder') }}</a>  </li>
                                    <li><a href="{{ url('sximo/menu')}}"><i class="icon-menu"></i> {{ Lang::get('core.m_menu') }}</a> </li>              
                                    <li><a href="{{ url('sximo/tables')}}"><i class="fa fa-database"></i> {{ Lang::get('core.m_database') }} </a> </li>
                                    <li> <a href="{{ url('sximo/code')}}"><i class="icon-note"></i> {{ Lang::get('core.m_sourceeditor') }}</a>  </li>            
                                    <li><a href="{{ url('core/forms')}}"><i class="icon-list"></i> {{ Lang::get('core.m_formbuilder') }}</a> </li>
                                    <li ><a href="{{ url('sximo/rac')}}"><i class="icon-shuffle"></i> RestAPI Generator </a> </li>
                                    
                                </ul>
                            </li>   

                            <li class="col-sm-3">
                                <ul>
                                    <li class="dropdown-header"> Utility </li> 
                                    <li><a href="{{ url('core/elfinder')}}"><i class="icon-folder"></i>  File Manager</a>  </li>

                                </ul>
                            </li>        
                            @endif 
                        </ul>
                    </li>
                    @endif
                    <!-- /.Megamenu -->
                </ul>
                <!-- This is the message dropdown -->
                <ul class="nav navbar-top-links navbar-right pull-right">
                    <!-- /.Task dropdown -->
                    <!-- /.dropdown -->
                     @if($sximoconfig['cnf_multilang'] ==1)
                    <li class="dropdown tasks-menu">
                      <?php 
                      $flag ='en';
                      $langname = 'English'; 
                      foreach(SiteHelpers::langOption() as $lang):
                        if($lang['folder'] == session('lang') or $lang['folder'] == $sximoconfig['cnf_lang']) {
                          $flag = $lang['folder'];
                          $langname = $lang['name']; 
                        }
                        
                      endforeach;?>
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img class="flag-lang" src="{{ asset('assets/plugins/images/flags/'.$flag.'.png') }}" width="16" height="12" alt="lang" /> {{ strtoupper($flag) }}
                        <span class="hidden-xs">
                         <i class="fa caret"></i>
                        </span>
                      </a>

                       <ul class="dropdown-menu dropdown-menu-right icons-right">
                        @foreach(SiteHelpers::langOption() as $lang)
                          <li><a href="{{ URL::to('home/lang/'.$lang['folder'])}}"><img class="flag-lang" src="{{ asset('assets/plugins/images/flags/'. $lang['folder'].'.png')}}" width="16" height="11" alt="lang"  /> {{  $lang['name'] }}</a></li>
                        @endforeach 
                      </ul>

                    </li> 
                    @endif                    

                    <li class="dropdown">
                        <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">  {!! SiteHelpers::avatar( 36 ) !!}<b class="hidden-xs">{{ Session::get('fid')}}</b><span class="caret"></span> </a>
                        <ul class="dropdown-menu dropdown-user animated flipInY">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"> {!! SiteHelpers::avatar( 80 ) !!}</div>
                                    <div class="u-text"><h4>{{ Session::get('fid')}}</h4><p class="text-muted">{{ Session::get('eid')}}</p><a href="{{ url('user/profile')}}" class="btn btn-rounded btn-danger btn-sm">View Profile</a></div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ url('user/profile')}}"><i class="ti-user"></i> My Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ url('user/logout')}}"><i class="fa fa-power-off"></i> Logout</a></li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="theme-toggle waves-effect waves-light ">
                            <i class="icon-menu "></i>
                        </a>
                   
                    </li>
                    
                    <!-- /.dropdown -->
                </ul>
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <!-- End Top Navigation -->