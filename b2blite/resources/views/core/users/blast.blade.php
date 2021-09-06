@extends('layouts.app')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small> {{ $pageNote }} </small> </h2></div>



   <!-- Start blast email -->

    {!! Form::open(array('url'=>'core/users/doblast/', 'class'=>'form-horizontal  ')) !!}
   
       
                  <div class="form-group  " >
                  <label for="ipt" class=" control-label col-md-3">  </label>
                  <div class="col-md-12">
                      <ul class="parsley-error-list">
                        @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>                
                   </div> 
                  </div> 
              
       <div class="row">
                  <div class="col-sm-12">
                          <div class="form-group  " >
                        <label for="ipt" class=" control-label col-md-3">  {{ Lang::get('core.fr_emailsubject') }}   </label>
                        <div class="col-md-9">
                             {!! Form::text('subject',null,array('class'=>'form-control  form-control-sm', 'placeholder'=>'','required'=>'true')) !!} 
                         </div> 
                        </div> 
                  </div>    
                   <div class="col-sm-6">  
                        <div class="form-group  " >
                        <label for="ipt" class=" control-label col-md-3"> {{ Lang::get('core.fr_emailsendto') }}   </label>

                        <div class="col-md-9">
                         @foreach($groups as $row)
                          <div class="checkbox checkbox-success">
                            <input type="checkbox"   name="groups[]" value="{{ $row->group_id}}" 
                            class=" filled-in chk-col-indigo" id="g-{{ $row->group_id}}" /> 
                            <label for="g-{{ $row->group_id}}"> {{ $row->name }}</label>
                          </div>


                         @endforeach
                         </div> 
                        </div>        
                    
              </div>


            <div class="col-sm-6">
            <div class="form-group  " >
                <div for="ipt" class=" control-label col-md-3">  Status   </div>
                <div class="col-md-9"> 

                    <div class="radio radio-success">
                        <input type="radio" name="uStatus" value="all" required="true" class="minimal-green" id="s-all" > 
                        <label for="s-all"> All Status</label>
                    </div>
                    <div class="radio radio-success">
                        <input type="radio"  name="uStatus" value="1" required="true" class="minimal-green" id="s-1"> 
                        <label for="s-1"> Active </label>
                    </div>  
                    <div class="radio radio-success">
                        <input type="radio"  name="uStatus" value="0" required="true" class="minimal-green" id="s-2"> 
                        <label for="s-2"> Unconfirmed</label>
                    </div>
                    <div class="radio radio-success">
                        <input type="radio"  name="uStatus" value="2" required="true" class="minimal-green" id="s-3"> 
                        <label for="s-3"> Blocked</label>
                    </div>                                
                </div> 

            </div>  
        </div>
     
 
 <div class="col-sm-12">


 
          <div class="form-group "  >
         
          <div style=" padding:10px;">
       <label for="ipt" class=" control-label "> {{ Lang::get('core.fr_emailmessage') }} </label>
           <textarea class="form-control editor" rows="10"   name="message"></textarea> 
       </div>
           <p> {{ Lang::get('core.fr_emailtag') }} : </p>
           <small> [fullname] [first_name] [last_name] [email]  </small>
         
          </div> 

            
                    

          
          <div class="form-group" >
          <label for="ipt" class=" control-label col-md-3"> </label>
          <div class="col-md-9">
              <button type="submit" name="submit" class="btn btn-primary">{{ Lang::get('core.sb_send') }} Mail </button>
           </div> 
          </div> 
  </div>                     
     {!! Form::close() !!}



@stop