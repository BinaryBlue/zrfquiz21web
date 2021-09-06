@extends('layouts.app')
<link href="{{ asset('blueerp/blueerp.css')}}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('blueerp/vue.js') }}"></script>
<script type="text/javascript" src="{{ asset('blueerp/plugins/axios.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('blueerp/plugins/vueselect/vue-select.js') }}"></script>
<link href="{{ asset('blueerp/plugins/vueselect/vue-select.css')}}" rel="stylesheet">
@section('content')
<div class="page-header"><h2> {{ $pageTitle }} <small> {{ $pageNote }} </small> </h2></div>

	<div class="toolbar-nav">
		<div class="row">
			
			<div class="col-md-6 " >
			</div>
			<div class="col-md-6 text-right " >
				<a href="{{ url($pageModule.'?return='.$return) }}" class="tips btn   btn-sm "  title="{{ __('core.btn_back') }}" ><i class="fa  fa-times"></i></a> 
			</div>
		</div>
	</div>	
	<div class="p-5" id="stockentry">
		<div class="row">
			<div class="col-md-8">
				<fieldset>
					<legend>Select Supplier</legend>
					<div class="form-group row  " >
						<label for="Supplier" class=" control-label col-md-4 text-left"> Supplier </label>
						<div class="col-md-6">
							<v-select label="name" :options="supplierList" v-model="supplier"></v-select>
						 </div> 
						 <div class="col-md-2">
							<span v-on:click="getSuppliers" class="tips btn btn-md blue text-white cursor-pointer"><i class="fa fa-refresh"></i></span> 

						 </div>
					</div> 
				</fieldset>
				<fieldset>
					<legend>Select Item</legend>
					<div class="form-group row  " >
						<label for="product" class=" control-label col-md-4 text-left"> Product </label>
						<div class="col-md-6">
							<v-select label="code" :options="productList" v-model="product"></v-select>
						</div>
						<div class="col-md-2">
							<span v-on:click="getProducts" class="tips btn btn-md blue text-white cursor-pointer"><i class="fa fa-refresh"></i></span> 
							<span v-on:click="addItem" class="tips btn btn-md teal text-white cursor-pointer"><i class="fa fa-plus"></i></span>
						</div> 
					</div>
				</fieldset>
			</div>
			<div class="col-md-4">
				<fieldset>
					<legend>Statement Info</legend>
					<div class="form-group row  " >
						<label for="statementno" class=" control-label col-md-4 text-left">Statement{{' #'}} </label>
						<div class="col-md-8">
						  <input id="statementno" disabled type='text' name='statementno' v-model="statementNo"  class='form-control form-control-sm text-white cyan' /> 
						</div> 
					</div>
				</fieldset>
				<fieldset>
					<legend>Outlet</legend>
					<div class="form-group row  " >
						<label for="outlet" class=" control-label col-md-4 text-left"> Outlet </label>
						<div class="col-md-8">
						  <input disabled type='text' name='outlet' v-model="outletName"  class='form-control form-control-sm text-white indigo' /> 
						</div> 
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<fieldset>
					<legend class="orange">Item Details</legend>
					<table class="table table-hover ">
						<thead class="heightzero">
							<th>Option</th>
							<th>Supplier</th>
							<th>Product</th>
							<th>Remarks</th>
							<th>Quantity</th>
							<th>Price</th>
							<th>Profit (%)</th>
							<th>Purchase Price</th>
							
						</thead>
						<tbody>
							<tr v-for="(item,index ) in itemList" :key="item.displayId">
								<td><span v-on:click="deleteItem(item)" class="tips btn btn-md red text-white cursor-pointer"><i class="fa fa-times"></i></span></td>
								<td><span>@{{item.supplierName}}</span></td>
								<td><span>@{{item.productCode}}</span></td>
								<td><input v-model="itemList[index].remarks" type="text" v-model="item.remarks" class="form-control form-control-sm" /></td>
								<td><input v-model="itemList[index].qtt" v-on:keyup="purchaseCalculate(index)" type="number" v-model="item.qtt" class="form-control form-control-sm width-50p" /></td>
								<td><input v-model="itemList[index].price" v-on:keyup="purchaseCalculate(index)" type="number" v-model="item.price" class="form-control form-control-sm width-60p" /></td>
								<td><input v-model="itemList[index].profit" v-on:keyup="purchaseCalculate(index)" type="number" v-model="item.profit" class="form-control form-control-sm width-60p" /></td>
								<td><input v-model="itemList[index].purchase" v-on:keyup="purchaseCalculate(index,true)" type="number" v-model="item.purchase" class="form-control form-control-sm width-70p" /></td>
							</tr>
							
						</tbody>
						<thead class="heightzero">
							<th class="right" colspan="7">Total</th>
							<th class="center" ><span>@{{total}}</span>/-</th>
						</thead>
					</table>
					<div class="right" v-show="saveable" v-on:click="confirmStock"><span class="tips btn btn-md teal text-white cursor-pointer"><i class="fa fa-save"></i> Confirm</span></div>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{{ asset('blueerp/blueerp.js') }}"></script> 	 
@stop