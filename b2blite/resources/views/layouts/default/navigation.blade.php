<?php  $menus = SiteHelpers::menus('top') ;?>

    <ul class="nav-menu">
       
        @foreach ($menus as $menu)
        @if($menu['module'] =='separator')
        <li class="divider"></li>        
        @else
            <li  @if(count($menu['childs']) > 0 ) class="menu-has-children" @endif><!-- HOME -->
                <a 
                @if(count($menu['childs']) > 0 ) 
                    href="javascript:void(0);" 
                @elseif($menu['menu_type'] =='external')
                    href="{{ URL::to($menu['url'])}}" 
                @else
                    href="{{ URL::to($menu['module'])}}" 
                @endif >
                                  
                    @if(config('sximo.cnf_multilang') ==1 && isset($menu['menu_lang']['title'][session('lang')]) && $menu['menu_lang']['title'][session('lang')]!='')
                        {{ $menu['menu_lang']['title'][session('lang')] }}
                    @else
                        {{$menu['menu_name']}}
                    @endif             
                   
                </a> 
                @if(count($menu['childs']) > 0)
                <ul>
                @foreach ($menu['childs'] as $menu2)
                    @if($menu2['module'] =='separator')
                        <li class="divider"> </li>        
                    @else
                    <li class="
                       
                        @if(Request::is($menu2['module'])) active @endif">
                        <a 
                            @if($menu2['menu_type'] =='external')
                                href="{{ url($menu2['url'])}}" 
                            @else
                                href="{{ url($menu2['module'])}}" 
                            @endif
                                        
                        >
                            <i class="{{ $menu2['menu_icons'] }}"></i> 
                            @if(config('sximo.cnf_multilang') ==1 && isset($menu2['menu_lang']['title'][session('lang')]))
                                {{ $menu2['menu_lang']['title'][session('lang')] }}
                            @else
                                {{$menu2['menu_name']}}
                            @endif                        
                        </a>
                    @endif
                 @endforeach     
                </ul>
                @endif
            </li>
        @endif
    @endforeach    
     @if(config('sximo.cnf_multilang') ==1)
            <li class="menu-has-children ">
              <?php 
              $flag ='en';
              $langname = 'English'; 
              foreach(SiteHelpers::langOption() as $lang):
                if($lang['folder'] == session('lang') or $lang['folder'] == config('sximo.cnf_lang')) {
                  $flag = $lang['folder'];
                  $langname = $lang['name']; 
                }
                
              endforeach;?>
              <a href="#" >
                <img class="flag-lang" src="{{ asset('sximo5/images/flags/'.$flag.'.png') }}" width="16" height="12" alt="lang" /> {{ strtoupper($flag) }}
                <span class="hidden-xs">
                
                </span>
              </a>

               <ul >
                @foreach(SiteHelpers::langOption() as $lang)
                  <li><a href="{{ url('home/lang/'.$lang['folder'])}}"><img class="flag-lang" src="{{ asset('sximo5/images/flags/'. $lang['folder'].'.png')}}" width="16" height="11" alt="lang"  /> {{  $lang['name'] }}</a></li>
                @endforeach 
              </ul>

            </li> 
            @endif 
            <li><a href="{{ url('dashboard')}}"> Dashboard</a></li>  
   
  </ul>
    <!--
        <li class="menu-has-children"><a href="">Drop Down</a>
        <ul>
              <li><a href="#">Drop Down 1</a></li>
              <li><a href="#">Drop Down 3</a></li>
              <li><a href="#">Drop Down 4</a></li>
              <li><a href="#">Drop Down 5</a></li>
        </ul>
        </li>
        <li><a href="{{ url('dashboard')}}">Dashboard</a></li>   
     
    </ul>
-->


    
       

