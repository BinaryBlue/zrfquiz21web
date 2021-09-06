  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      
      @if(Session::get('gid') =='1')
      <li class="active"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gear"></i></a></li>
      <li><a href="#control-sidebar-info-tab" data-toggle="tab"><i class="fa fa-info-circle"></i></a></li>
      @endif
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      
    </ul>
    <!-- Tab panes -->
    

    <div class="tab-content">
      <!-- Changelog tab content -->
       <div class="tab-pane " id="control-sidebar-info-tab">
          <div class="text-center">
              <h3 class="control-sidebar-heading">ABOUT SXIMO 5 </h3>
              Version : 5.1.7 <br />
              Date : Nov 26 , 2016 <br /><br />
              <b>Changelog</b>
            </div>
              <hr />
            
              <ol style="padding: 0; margin: 0 ; list-style: none;">
                <li> <b>Changed</b> : Core Admin Template </li>
                 <li> <b>Added</b> : RestAPI CLient Editor </li>

              </ol>

              <hr/>
              <div class="text-center">
                <a href="javascript:void(0)" onclick="SximoModal('{{ url('core/template/changelog') }}','Sximo5 Changelog')"> All Changelogs </a> 
              </div>
      </div>   
      <!-- Home tab content -->
      <div class="tab-pane " id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Sample Widget</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-user bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                <p>New phone +1(800)555-1234</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                <p>nora@example.com</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-file-code-o bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                <p>Execution time 5 seconds</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="label label-danger pull-right">70%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Update Resume
                <span class="label label-success pull-right">95%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Laravel Integration
                <span class="label label-warning pull-right">50%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Back End Framework
                <span class="label label-primary pull-right">68%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
       @if(Session::get('gid') =='1')
      <div class="tab-pane " id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane active" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading"><b>ROOT ACCESS</b></h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              {{ Lang::get('core.m_codebuilder') }}
              <i class="icon-spinner7 pull-right" ></i>
            </label>

            <p>
             <a href="{{ url('sximo/module')}}"> {{ Lang::get('core.dash_module') }} </a>
            </p>
          </div>
          <!-- /.form-group -->
          
          <div class="form-group">
            <label class="control-sidebar-subheading">
              {{ Lang::get('core.m_setting') }}
              <i class=" fa fa-cog pull-right " ></i>
            </label>

            <p>
             <a href="{{ url('sximo/config')}}"> {{ Lang::get('core.dash_setting') }} </a>
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
             {{ Lang::get('core.m_menu') }}
              <i class="fa  fa-navicon pull-right" ></i>
            </label>

            <p>
             <a href="{{ url('sximo/menu')}}">  {{ Lang::get('core.dash_sitemenu') }} </a>
            </p>
          </div>
          <!-- /.form-group -->       




          <div class="form-group">
            <label class="control-sidebar-subheading">
             {{ Lang::get('core.m_formbuilder') }}
              <i class="fa fa-th pull-right" ></i>
            </label>

            <p>
             <a href="{{ url('core/forms')}}">  {{ Lang::get('core.dash_formbuilder') }} </a>
            </p>
          </div>
          <!-- /.form-group -->


          <div class="form-group">
            <label class="control-sidebar-subheading">
             {{ Lang::get('core.m_sourceeditor') }}
               <i class="icon-code pull-right" ></i>
            </label>

            <p>
               <a href="{{ url('sximo/code')}}"> {{ Lang::get('core.dash_sourceeditor') }} </a> 
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              {{ Lang::get('core.m_database') }}
              <i class="fa fa-database pull-right" ></i>
            </label>

            <p>
             <a href="{{ url('sximo/tables')}}"> {{ Lang::get('core.dash_database') }} </a>
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              RestAPI Client Editor
              <i class="fa fa-exchange pull-right" ></i>
            </label>

            <p>
             <a href="{{ url('sximo/rac')}}"> Manage Access to modules </a>
            </p>
          </div>
          <!-- /.form-group -->          

          <h3 class="control-sidebar-heading">{{ Lang::get('core.m_clearcache') }} </h3>

          

          <div class="form-group">
            <label class="control-sidebar-subheading">
             {{ Lang::get('core.dash_clearcache') }}
              <a href="{{ url('sximo/config/clearlog') }}"  class="text-red pull-right clearCache"><i class="fa fa-trash-o"></i></a>
            </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      @endif
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
