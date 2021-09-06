<?php $sidebar = SiteHelpers::menus('sidebar') ;
$active = Request::segment(1);
?>
<aside class="left-sidebar ">
    <div class="sidebar-profile" >
        <div class="row">
            <div class="col-3">
                {!! SiteHelpers::avatar( 42 ) !!}
            </div>
            <div class="col-9">
                <a href="{{ url('user/profile') }}">
                    <b>{{ Session::get('fid')}}</b>
                    <p>{{ Session::get('eid')}} </p>
                </a>
            </div>
        </div>    
    </div>
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                @foreach ($sidebar as $menu)   

                    @if($menu['module'] =='separator')
                      <li class="nav-small-cap"> <span> {{$menu['menu_name']}} </span></li>  

                    @else
                    <li>
                        @if(count($menu['childs']) > 0 ) 
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="{{$menu['menu_icons']}}"></i>
                                <span class="hide-menu">
                                    {{ (isset($menu['menu_lang']['title'][session('lang')]) ? $menu['menu_lang']['title'][session('lang')] : $menu['menu_name']) }}
                                </span>
                            </a>
                        @else
                            <a href="{{ url($menu['module'])}}" >
                                    <i class="{{$menu['menu_icons']}}"></i>
                                    <span class="hide-menu">
                                        {{ (isset($menu['menu_lang']['title'][session('lang')]) ? $menu['menu_lang']['title'][session('lang')] : $menu['menu_name']) }}
                                    </span>
                            </a>                               

                        @endif 

                            <!-- LEVEL 2 -->
                         @if(count($menu['childs']) > 0 ) 
                            <ul aria-expanded="false" >
                             @foreach ($menu['childs'] as $menu2)
                                 <li>
                                    @if(count($menu2['childs']) > 0 )
                                    <a class="has-arrow" href="#" aria-expanded="false">
                                        {{ (isset($menu2['menu_lang']['title'][session('lang')]) ? $menu2['menu_lang']['title'][session('lang')] : $menu2['menu_name']) }}
                                    </a>
                                    @else 
                                    <a href="{{ url($menu2['module'])}}">
                                        {{ (isset($menu2['menu_lang']['title'][session('lang')]) ? $menu2['menu_lang']['title'][session('lang')] : $menu2['menu_name']) }}
                                    </a>

                                    @endif

                                    @if(count($menu2['childs']) > 0 )
                                        <ul aria-expanded="false" >
                                            @foreach ($menu2['childs'] as $menu3)
                                                 <li>
                                                    <a href="{{ url($menu3['module'])}}">
                                                        {{ (isset($menu3['menu_lang']['title'][session('lang')]) ? $menu3['menu_lang']['title'][session('lang')] : $menu3['menu_name']) }}
                                                    </a>
                                                  </li>
                                            @endforeach    


                                        </ul>


                                    @endif 


                                </li>

                             @endforeach

                            </ul> 
                         @endif


                    </li>      
                    @endif
                @endforeach        

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>