        <header class="topbar ">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{ url('dashboard')}}">
                        <!-- Logo icon -->
                        <b>
                            @if(file_exists(public_path().'/uploads/images/'.config('sximo')['cnf_logo']) && config('sximo')['cnf_logo'] !='')
                              <img src="{{ asset('uploads/images/'.config('sximo')['cnf_logo'])}}" alt="{{ config('sximo')['cnf_appname'] }}" width="40" />
                            @else
                              <img src="{{ asset('')}}assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            @endif

                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="dark-logo" style=" font-weight: 500; padding-left: 10px;">
                            {{ config('sximo.cnf_appname') }} 
                        </span> 
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto ">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler sidebartoggler  waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                       
                        <li class="nav-item "> 
                            <a class="nav-link  waves-effect waves-dark" href="{{  url('')}}">
                                <i class="icon-globe"></i>
                            </a>
                        </li>
                       
                    </ul>

                    
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        

                        
                        <!-- Language -->
                        <!-- ============================================================== -->
                        @if($sximoconfig['cnf_multilang'] ==1)
                         <li class="nav-item dropdown  hidden-xs-down">
                          <?php 
                          $flag ='en';
                          $langname = 'English'; 
                          foreach(SiteHelpers::langOption() as $lang):
                            if($lang['folder'] == session('lang') or $lang['folder'] == $sximoconfig['cnf_lang']) {
                              $flag = $lang['folder'];
                              $langname = $lang['name']; 
                            }
                            
                          endforeach;?>
                          <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img class="flag-lang" src="{{ asset('assets/plugins/images/flags/'.$flag.'.png') }}" width="16" height="16" alt="lang" /> </a>

                          <div class="dropdown-menu dropdown-menu-right animated bounceInDown">
                             @foreach(SiteHelpers::langOption() as $lang)
                             <a class="dropdown-item" href="{{ url('home/lang/'.$lang['folder'])}}">
                                <img class="flag-lang" src="{{ asset('assets/plugins/images/flags/'. $lang['folder'].'.png')}}" width="16" height="16" alt="lang"  /> {{  $lang['name'] }}
                               </a>

                            @endforeach 


                          </div>
                        </li>  
                        @endif 
                         @if(Auth::user()->group_id <= 2  )
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               <i class="mdi mdi-view-grid"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated ">
                                <ul class="dropdown-user">
                                   <li ><a href="{{ url('core/users')}}"><i class="icon-user"></i> 
                                    {{ Lang::get('core.m_users') }} </a> </li> 
                                    <li ><a href="{{ url('core/groups')}}"><i class="icon-people"></i>  {{ Lang::get('core.m_groups') }}</a> </li>
                                    <li><a href="{{ url('core/users/blast')}}"><i class="icon-envelope"></i> Send Mail</a></li> 
                                    <li><a href="{{ url('cms/pages')}}"><i class="icon-notebook"></i> {{ Lang::get('core.m_pagecms')}}</a></li> 
                                    <li ><a href="{{ url('cms/posts')}}"><i class="icon-docs"></i> Post Management</a></li> 
                                    <li ><a href="{{ url('cms/categories')}}"><i class="icon-docs"></i> Post Categories</a></li>  
                                    <li role="separator" class="divider"></li>
                                     <li><a href="{{ url('core/logs')}}"><i class="icon-clock"></i> {{ Lang::get('core.m_logs') }}</a></li>
                                        
                                  
                                </ul>
                            </div>
                        </li>
                        @endif
                        @if(Auth::user()->group_id == 1  )
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti-layers"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated ">
                                <ul class="dropdown-user">
                                    <li><a href="{{ url('sximo/config')}}"><i class="icon-screen-desktop"></i> {{ Lang::get('core.tab_siteinfo') }}</a> </li>
                                    

                                     <li role="separator" class="divider"></li>


                                    <li><a href="{{ url('sximo/module')}}"><i class="icon-fire"></i> Module {{ Lang::get('core.m_codebuilder') }}</a>  </li>
                                    <li><a href="{{ url('sximo/menu')}}"><i class="icon-menu"></i> {{ Lang::get('core.m_menu') }}</a> </li>              
                                    <li><a href="{{ url('sximo/tables')}}"><i class="ti-view-list"></i> {{ Lang::get('core.m_database') }} </a> </li>
                                    <li> <a href="{{ url('sximo/code')}}"><i class="icon-note"></i> {{ Lang::get('core.m_sourceeditor') }}</a>  </li>            
                                    
                                     <li><a href="{{ url('core/elfinder')}}"><i class="icon-folder"></i>  File Manager</a>  </li>
                                    <li><a href="{{ url('sximo/server/repository')}}"><i class="icon-share"></i>  Repositories </a>  </li>
                                      <li role="separator" class="divider"></li>
                                      <li> <a href="{{ url('sximo/server')}}" onclick="SximoModal(this.href, 'About Sximo'); return false "><i class="icon-info"></i> About Sximo  </a> </li>
                                     <li> <a href="{{ url('sximo/config/clearlog')}}" class="clearCache"><i class="icon-trash"></i> Clear Log & Caches </a> </li>
                                    <!--
                                        <li><a href="{{ url('core/forms')}}"><i class="icon-list"></i> {{ Lang::get('core.m_formbuilder') }}</a> </li>
                                    <li ><a href="{{ url('sximo/rac')}}"><i class="icon-shuffle"></i> RestAPI Generator </a> </li> -->
                                        
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        <li class="nav-item"><a href="{{ url('user/logout')}}" class="nav-link confirmLogout"><i class="fa fa-power-off"></i></a></li>
                      
                        
                    </ul>
                </div>
            </nav>
        </header>