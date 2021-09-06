@extends('layouts.app')

@section('content')
<div class="page-header"><h2> {{ $pageTitle }}  <small> {{ $pageNote }} </small> </h2></div>
<div class="card">
	<div class="card-body">
			@include('sximo.config.tab')
			<div class="pt-5"> 
			{!! Form::open(array('url'=>'sximo/config/login/', 'class'=>'form-horizontal  validated')) !!}
			
			<div class="row" > 		

			<div class="col-sm-6">				

		 		  <div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4">  {{ Lang::get('core.fr_emailsys') }}  </label>	
					<div class="col-sm-8 ">
							
							<div class="">
								<input type="radio" name="CNF_MAIL" value="phpmail"   @if($sximoconfig['cnf_mail'] =='phpmail') checked @endif class="filled-in" id="p-mail"  /> 
								<label for="p-mail">PHP MAIL System</label>
							</div>
							
							<div class="">
								<input type="radio" name="CNF_MAIL" value="swift"   @if($sximoconfig['cnf_mail'] =='swift') checked @endif class="minimal-green"  id="p-swift" /> 
								<label for="p-swift">SWIFT Mail ( Required Configuration )</label>
							</div>			
					</div>
				</div>					
		  
				  <div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4"> {{ Lang::get('core.fr_registrationdefault') }}  </label>	
					<div class="col-sm-8">
							<div >
								
								<select class="form-control form-control-sm" name="CNF_GROUP">
									@foreach($groups as $group)
									<option value="{{ $group->group_id }}"
									 @if($sximoconfig['cnf_group'] == $group->group_id ) selected @endif
									>{{ $group->name }}</option>
									@endforeach
								</select>
								
							</div>				
					</div>	
							
				  </div> 

				  <div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4">{{ Lang::get('core.fr_registration') }} </label>	
					<div class="col-sm-8 " >
						<div class=" radio-success">
							
							<div class="">
							<input type="radio" name="CNF_ACTIVATION" value="auto" @if($sximoconfig['cnf_activation'] =='auto') checked @endif  class="minimal-green"  id="r-auto" /> 
							<label for="r-auto">{{ Lang::get('core.fr_registrationauto') }}</label>
							</div>
							
							<div class=" ">
								<input type="radio" name="CNF_ACTIVATION" value="manual" @if($sximoconfig['cnf_activation'] =='manual') checked @endif   class="minimal-green" id="r-manual" />  
								<label for="r-manual">{{ Lang::get('core.fr_registrationmanual') }}</label>
							</div>								
							<div class=" ">
								<input type="radio" name="CNF_ACTIVATION" value="confirmation" @if($sximoconfig['cnf_activation'] =='confirmation') checked @endif  class="minimal-green" id="r-confirm"  />
								<label for="r-confirm">{{ Lang::get('core.fr_registrationemail') }}</label>
							</div>
						</div>						
									
					</div>	
							
				  </div> 
				  
		 		  <div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4"> {{ Lang::get('core.fr_allowregistration') }} </label>	
					<div class="col-sm-8">
						<div class="">
							<input type="checkbox" name="CNF_REGIST" value="true"  @if($sximoconfig['cnf_regist'] =='true') checked @endif class="minimal-green" id="cnf-regist"  /> 
							<label for="cnf-regist">{{ Lang::get('core.fr_enable') }}</label>
						</div>			
					</div>
				</div>	
				
		 		<div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4"> {{ Lang::get('core.fr_allowfrontend') }} </label>	
					<div class="col-sm-8">
						<div class="">
							<input type="checkbox" name="CNF_FRONT" value="false" @if($sximoconfig['cnf_front'] =='true') checked @endif class="minimal-green" id="cnf-front"  /> 
							<label for="cnf-front">{{ Lang::get('core.fr_enable') }}</label>
						</div>			
					</div>
				</div>		
			
		 		<div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4">Google reCaptcha </label>	
					<div class="col-sm-8">
						<div class="">
						
							<input type="checkbox" name="cnf_recaptcha" value="false" @if(config('sximo.cnf_recaptcha') =='true') checked @endif class="minimal-green" id="cnf-recaptcha" /> <label for="cnf-recaptcha"> {{ Lang::get('core.fr_enable') }} </label>
							<br /><br />

							<label> Site key</label>
							<input type="text" name="cnf_recaptchapublickey" value="{{ config('sximo.cnf_recaptchapublickey') }}" class="input-sm form-control form-control-sm"  /> 
							<label> Secret key</label>
							<input type="text" name="cnf_recaptchaprivatekey" value="{{ config('sximo.cnf_recaptchaprivatekey') }}" class="input-sm form-control form-control-sm"  /> 
							
						</div>	
												
					</div>
				</div>		
				

		 		<div class="form-group row">
					<label for="ipt" class=" control-label col-sm-4"> Google Maps API Key </label>	
					<div class="col-sm-8">
						<div class="">
							<input type="text" name="CNF_MAPS" value="{{ config('sximo.cnf_maps') }}" class="input-sm form-control form-control-sm"  /> 
							<small><i>* This is required if you use google Maps form .</i></small>
						</div>	
												
					</div>
				</div>		
				

			  	<div class="form-group row">
					<label for="ipt" class=" control-label col-md-4">&nbsp;</label>
					<div class="col-md-8">
						<button class="btn btn-primary" type="submit"> {{ Lang::get('core.sb_savechanges') }}</button>
				 	</div>
			  	</div>	  
			
		 	</div>

			<div class="col-sm-6">	
				<div class="form-vertical">
					<div class="form-group row">
						<label> {{ Lang::get('core.fr_restrictip') }} </label>	
						
						<p><small><i>
							
							{{ Lang::get('core.fr_restrictipsmall') }}  <br />
							{{ Lang::get('core.fr_restrictipexam') }} : <code> 192.116.134 , 194.111.606.21 </code>
						</i></small></p>
						<textarea rows="5" class="form-control form-control-sm" name="CNF_RESTRICIP">{{ $sximoconfig['cnf_restrictip'] }}</textarea>
					</div>
					
					<div class="form-group row">
						<label> {{ Lang::get('core.fr_allowip') }} </label>	
						<p><small><i>
							
							{{ Lang::get('core.fr_allowipsmall') }}  <br />
							{{ Lang::get('core.fr_allowipexam') }} : <code> 192.116.134 , 194.111.606.21 </code>
						</i></small></p>							
						<textarea rows="5" class="form-control form-control-sm" name="CNF_ALLOWIP">{{ $sximoconfig['cnf_allowip'] }}</textarea>
					</div>

					<p> {{ Lang::get('core.fr_ipnote') }} </p>
				</div>
			</div>
		</div>
			{!! Form::close() !!}

	</div>	
</div>
</div>

@stop




