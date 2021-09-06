Vue.component('v-select', VueSelect.VueSelect);
var stockentryapp = new Vue({
    el: '#stockentry',
    data: {
      displayId: 0,
      statementNo: '0000-00-00-0',
      supplier: null,
      product: null,
      pcode: '',
      outletId: 1,
      outletName: 'Diganta Warehouse',
      productId: 1,
      productCode: 'HF-219',
      itemList:[],
      supplierList: [],
      productList: [],
      total: 0.00,
      total_sell: 0.00,
      total_qtt: 0,
      saveable:false,
      confirmable: false,
    },
    watch:{
    },
    methods: {
      getTotalSelling: function(index){
        return this.itemList[index].price * this.itemList[index].qtt;
      },
      getTotalPurchase: function(index){
        return this.itemList[index].purchase * this.itemList[index].qtt;
      },
      prepareStock: function(){
          var itemList = this.itemList;
          for (let i = 0; i < itemList.length; i++) {
             var qtt = parseInt(itemList[i].qtt);
              if(qtt==0){
                  this.deleteItem(this.itemList[i]);
                  this.prepareStock();
              }
          }
          if(this.itemList.length>0) this.confirmable = true;
          this.saveable = false;
      },
      confirmStock: function(){
        var p_data = 
        {
          itemList : this.itemList,
          total    : this.total
        };
        $('.ajaxLoading').show();
        axios.post(api_uri+'api/v1/management/stock/entry', p_data,p_headers)
        .then(function (response) {
          //console.log(response);
          $('.ajaxLoading').hide();
          notyMessage(response.message); 
          this.saveable = false;
          this.confirmable = false;
          Swal.fire({type: 'success',title: 'Confirmed', text: response.data.message});
          window.setTimeout(function(){location.reload()},2000)
          //window.location.href = base_url+'/stockentry/'+response.data.data.id;
          //console.log(response);
        })
        .catch(function (error) {
          notyMessageError(error);
          //console.log(error);
          $('.ajaxLoading').hide();
        });
  
      },
      getSuppliers: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/suppliers',p_headers);
          this.supplierList = response.data;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getProducts: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/products',p_headers);
          this.productList = response.data;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      redrawfn: function(){
        this.displayId = 0;
        this.total = 0;
        this.itemList = [];
        this.total_sell = 0.00;
        this.total_qtt = 0;
      },
      purchaseCalculate:function(index,pur=false){
        //console.log("profit: "+pro);
        //console.log("Purchase : "+pur);
        var qtt = parseInt(this.itemList[index].qtt);
        var price = parseFloat(this.itemList[index].price);
        var profit = parseFloat(this.itemList[index].profit);
        var purchase = parseFloat(this.itemList[index].purchase);
        //if(pur==true) this.itemList[index].profit = 100*qtt*(price-purchase)/purchase;

        if(pur==true) this.itemList[index].profit =  (100 - ((purchase/price)*100)).toFixed(2);
        else{
          this.itemList[index].purchase = ((100 - profit)*price/100).toFixed(2);
        }
        this._calculateTotal();
        this._updateSaveable();
      },
      _calculateTotal: function(){
        var ttl = [];
        ttl['purchase'] = 0.0;
        ttl['selling'] = 0.0;
        ttl['qtt'] = 0;
        $.each(this.itemList, function(key, value) {
          ttl['purchase'] += parseFloat(value.purchase) * parseInt(value.qtt);
          ttl['selling'] += parseFloat(value.price) * parseInt(value.qtt);
          ttl['qtt'] += parseInt(value.qtt);
          //ttl += value.purchase;
        });
        this.total = ttl['purchase'].toFixed(2);
        this.total_sell = ttl['selling'].toFixed(2);
        this.total_qtt = ttl['qtt'];
      },
      _updateSaveable: function(){
        if(this.itemList.length>0 && this.total!='NaN') this.saveable = true;
        else this.saveable = false;
      },
      _findindex: function(arr,idVal){
        return arr.findIndex(item =>{ return item.id === idVal});
      },
      _findindexProductSupplier: function(arr,product, supplier){
        var result = arr.filter(obj => obj.productId == product && obj.supplierId == supplier);
        return result.length;
      },
      addItem : function (product) {
        //console.log("Index of p1 and s1 is : "+this._findindexProductSupplier(this.itemList,this.supplier.id,this.product.id));
        //this._findindexProductSupplier(this.itemList,1,1);
        if(this.supplier==null){ // Supplier and product not defined
          Swal.fire({type: 'warning',title: 'Oops...', text: 'You can not add item without selecting supplier'});
        }
        else /// Product and supplier selected now look for remain validation
        {
          if(this._findindexProductSupplier(this.itemList,this.product.id,this.supplier.id) > 0){  // Duplicate product for same supplier
            Swal.fire({type: 'warning',title: 'Oops...', text: 'You can not add duplicate product for same supplier in same statement',});
          }
          else{
        this.displayId++;
        var d_profit = 45.0;
        var p_price =   ((100 - d_profit)*product.mrp/100).toFixed(2);
      
        var item = {
          displayId: this.displayId,
          statementNo:this.statementNo, supplierId: this.supplier.id,
          supplierName: this.supplier.name, outletId: usr_outlet,
          outletName: '', productId: this.product.id,
          productCode: this.product.code, productName: this.product.name, remarks: '', qtt: 0,
          price: this.product.mrp, profit:d_profit,purchase:p_price,
          metric: this.product.metric.name,
          brand: this.product.brand.name
        }
        this.itemList.push(item);
        this._updateSaveable();
        this.purchaseCalculate((this.itemList.length -1));
        //this._calculateTotal();
         }
        }
      },
      deleteItem : function (item){
        var idx = this.itemList.indexOf(item);
        console.log(idx);
        if (idx > -1) {
          this.itemList.splice(idx, 1);
        }
        this._updateSaveable();
        this._calculateTotal();
      }
    },
    computed:{},
    created: function(){
      this.getSuppliers();
      this.getProducts();
    }
  });
