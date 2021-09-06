@extends('layouts.app')

@section('content')

			<div class="page-header">
				<h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
			</div>
			
			<div class="toolbar-nav">
				<a href="{{ url('sximo/config/translation')}}" class="btn"> <i class="fa fa-times"></i></a>

			</div>

			<div class="card">
				<div class="card-body">
			
		 	
			<div class="col-md-8 offset-md-2">
				<ul class="nav nav-tabs" >
				@foreach($files as $f)
					@if($f != "." and $f != ".." and $f != 'info.json')
					<li class="nav-item "   >
					<a href="{{ URL::to('sximo/config/translation?edit='.$lang.'&file='.$f)}}" class="nav-link @if($file == $f) active @endif">{{ $f }} </a></li>
					@endif
				@endforeach
				</ul>
				<hr />
				{!! Form::open(array('url'=>'sximo/config/savetranslation/', 'class'=>'form-vertical form-material ')) !!}
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> Pharse </th>
								<th> Translation </th>

							</tr>
						</thead>
						<tbody>	
							
							<?php foreach($stringLang as $key => $val) : 
								if(!is_array($val)) 
								{
								?>
								<tr>	
									<td><?php echo $key ;?></td>
									<td><input type="text" name="<?php echo $key ;?>" value="{{ $val }}" class="form-control input-sm" />
									
									</td>
								</tr>
								<?php 
								} else {
									foreach($val as $k=>$v)
									{ ?>
										<tr>	
											<td><?php echo $key .' - '.$k ;?></td>
											<td><input type="text" name="<?php echo $key ;?>[<?php echo $k ;?>]" value="{{ $v }}" class="form-control  input-sm" />
											
											</td>
										</tr>						
									<?php }
								}
							endforeach; ?>
						</tbody>
						
					</table>
					<input type="hidden" name="lang" value="{{ $lang }}"  />
					<input type="hidden" name="file" value="{{ $file }}"  />
					<button type="submit" class="btn btn-info"> Save Translation</button>
				{!! Form::close() !!}

			</div>
		</div></div>

@endsection