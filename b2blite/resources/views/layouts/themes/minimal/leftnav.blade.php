  <?php $sidebar = SiteHelpers::menus('sidebar') ;

  $MenuActive =  (Request::segment(1) !='' ? Request::segment(1) : 'no');
  ?>


<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav slimscrollsidebar">
        <div class="sidebar-head">
            <h3><span class="fa-fw open-close"><i class="ti-menu hidden-xs"></i><i class="ti-close visible-xs"></i></span> <span class="hide-menu">Navigation</span></h3> </div>
        <ul class="nav" id="side-menu">
    @foreach ($sidebar as $menu)
       
      @if($menu['module'] =='separator')
        <li class="divider">
              @if($sximoconfig['cnf_multilang'] ==1 && isset($menu['menu_lang']['title'][Session::get('lang')]))
                {{ $menu['menu_lang']['title'][Session::get('lang')] }}
              @else
                {{$menu['menu_name']}}
              @endif


         </li>        
      @else
          <li class=" @if($MenuActive == $menu['module']) active @endif">
          <a 
            @if($menu['menu_type'] =='external')
              href="{{ $menu['url'] }}" 
            @else
              href="{{ URL::to($menu['module'])}}" 
            @endif

            class="waves-effect" 
          >
            <i class="{{$menu['menu_icons']}}"></i> 
            <span class="hide-menu">
              @if($sximoconfig['cnf_multilang'] ==1 && isset($menu['menu_lang']['title'][Session::get('lang')]))
                {{ $menu['menu_lang']['title'][Session::get('lang')] }}
              @else
                {{$menu['menu_name']}}
              @endif              
            
            @if(count($menu['childs']) > 0 )
              <span class="fa arrow"></span>
            @endif

            </span>
          </a>
          <!--- LEVEL II -->
            @if(count($menu['childs']) > 0 )

              <ul class="nav nav-second-level ">
               @foreach ($menu['childs'] as $menu2)
                <li @if($MenuActive == $menu2['module']) class="active" @endif >
                  <a 
                    @if($menu2['menu_type'] =='external')
                      href="{{ $menu2['url']}}" 
                    @else
                      href="{{ url($menu2['module'])}}"  
                    @endif  

                     class="waves-effect"                 
                  >                
                  <i class="{{$menu2['menu_icons']}}"></i>
                  <span class="hide-menu">
                  @if($sximoconfig['cnf_multilang'] ==1 && isset($menu2['menu_lang']['title'][Session::get('lang')]))
                    {{ $menu2['menu_lang']['title'][Session::get('lang')] }}
                  @else
                    {{$menu2['menu_name']}}
                  @endif

                @if(count($menu2['childs']) > 0 )
                  <span class="fa arrow"></span>
                @endif

                  </span>

                </a>
                  <!-- LEVEL III -->

                    @if(count($menu2['childs']) > 0)
                    <ul class="nav nav-third-level ">
                       @foreach ($menu2['childs'] as $menu3)
                            <li  @if($MenuActive == $menu3['module']) class="active" @endif>
                                <a 
                                  @if($menu3['menu_type'] =='external')
                                    href="{{ $menu3['url']}}" 
                                  @else
                                    href="{{ url($menu3['module'])}}"  
                                  @endif   

                                   class="waves-effect"                
                                >                
                                <i class="{{$menu3['menu_icons']}}"></i>
                                <span class="hide-menu">
                                @if($sximoconfig['cnf_multilang'] ==1 && isset($menu3['menu_lang']['title'][Session::get('lang')]))
                                  {{ $menu3['menu_lang']['title'][Session::get('lang')] }}
                                @else
                                  {{$menu3['menu_name']}}
                                @endif
                                @if(count($menu3['childs']) > 0 )
                                  <span class="fa arrow"></span>
                                @endif

                                </span>
                              </a>

                           </li> 
                        @endforeach  

                    </ul>  
                     @endif 
                  <!-- END LEVEL III -->
                </li>
                @endforeach 
              </ul>
            @endif 
            <!-- END LEVEL II -->
          </li>
          @endif 
        @endforeach  
 

            
        </ul>
    </div>
</div>
<!-- Left navbar-header end -->