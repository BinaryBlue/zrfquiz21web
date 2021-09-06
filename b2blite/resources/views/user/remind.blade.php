@extends('layouts.login')

@section('content')
  {!! Form::open(array('url' => 'user/doreset/'.$verCode, 'class'=>'form-horizontal form-material sky-form boxed')) !!}

      @if(Session::has('message'))
      {!! Session::get('message') !!}
    @endif
  <h1 class="text-center"> {{ config('sximo.cnf_appname') }} </h1>
  <p class="text-center"> {{ config('sximo.cnf_appdesc') }}  </p> 
  <hr />

    <h3 class="box-title m-t-40 m-b-0">Recover Password</h3><small> Type new password</small> 

    <ul class="parsley-error-list">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul> 


        <div class="form-group m-t-20">
          <div class="col-xs-12">
            {!! Form::password('password',  array('class'=>'form-control', 'placeholder'=>'New Password')) !!}  
          </div>
        </div>
        <div class="form-group ">
          <div class="col-xs-12">
           {!! Form::password('password_confirmation', array('class'=>'form-control', 'placeholder'=>'Confirm Password')) !!}
          </div>
        </div>

        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Save New Password</button>
          </div>
        </div> 

        <div class="form-group m-b-0">
          <div class="col-sm-12 text-center">
            <p>  <a href="{{ url('user/login') }}" class="text-primary m-l-5"><b>{{ Lang::get('core.signin') }} </b></a></p>
          </div>
        </div>               
    </form>    

 

@stop