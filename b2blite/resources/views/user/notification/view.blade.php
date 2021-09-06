@extends('layouts.app')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small> {{ $pageNote }} </small> </h2></div>
<div class="toolbar-nav">
	<a href="{{ url($pageModule.'?return='.$return) }}" class="tips btn btn-sm  "  title="{{ __('core.btn_back') }}" ><i class="fa  fa-times"></i></a> 

</div>	
<div class="p-5">
	<fieldset>
		<legend> Detail </legend>
	<div class="table-responsive">
		<table class="table table-striped " >
			<tbody>	
		
			<tr>
				<td width='30%' class='label-view text-right'>{{ SiteHelpers::activeLang('Date', (isset($fields['created']['language'])? $fields['created']['language'] : array())) }}</td>
				<td>{{ $row->created}} </td>
				
			</tr>
		
			<tr>
				<td width='30%' class='label-view text-right'>{{ SiteHelpers::activeLang('Url', (isset($fields['url']['language'])? $fields['url']['language'] : array())) }}</td>
				<td>{{ $row->url}} </td>
				
			</tr>
		
			<tr>
				<td width='30%' class='label-view text-right'>{{ SiteHelpers::activeLang('Note', (isset($fields['note']['language'])? $fields['note']['language'] : array())) }}</td>
				<td>{{ $row->note}} </td>
				
			</tr>
		
			</tbody>	
		</table>   

	 	

	</div>
</fieldset>
	
</div>
@stop
