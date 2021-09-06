Vue.component('v-select', VueSelect.VueSelect);
var stocktransferapp = new Vue({
  el: '#stocktransfer',
  data: {
      product: null,
      productList: [],
      outlet:null,
      outletList:[],
      current_stock:0,
      barcode:[],
      singlebarcode:null,
      barcodeList:[],
      total_qtt: 0,
      saveable:false,
      from_outlet:null,
      from_outlet_name:null,
  },
  watch:{
    product: function(pr){
      //this.current_stock = pr.current_stock;
      if(this.outlet==null) Swal.fire({type: 'warning',title: 'Oops...', text: 'Please Select Outlet First'});
      this.getBarcodes(pr.id);
    },
    outlet: function(newval,oldOutlet){
      if(newval!=null && newval.id==1){
        console.log(newval);
        Swal.fire({type: 'warning',title: 'Oops...', text: 'You can not transfer product to '+newval.name
        }).then(function(){
          this.outlet = null;
          this.$refs.outletRef.onEscape();
        });
        
        //this.$refs.outletRef.onEscape();
      }
    //  if(stocktransferapp.barcode.length >0 && outlet.id!=oldOutlet.id){
    //     Swal.fire({type: 'warning',title: 'Oops...', text: 'You can not initiate multiple transfer in a single transfer code.'});
    //     this.outlet = oldOutlet;
    //   }
    },
    barcode: function(barcode){
      if(barcode.length> 0 && this.outlet!=null ) this.saveable = true;
      else this.saveable = false;
      this.total_qtt = barcode.length;
      barcode.forEach(function(b){
        b.to_name = stocktransferapp.outlet.name;
        b.from_name = stocktransferapp.from_outlet_name;
      });

    },
  },
  methods:{
      confirmTransfer: function(){
        var p_data = 
        {
          itemList : this.barcode,
          fromOutlet : this.from_outlet,
          fromName : this.from_outlet_name,
          toName: this.outlet.name,
          toOutlet : this.outlet.id,
        };
        $('.ajaxLoading').show();
        axios.post(api_uri+'api/v1/management/stock/transfer', p_data,p_headers)
        .then(function (response) {
          console.log(response);
          $('.ajaxLoading').hide();
          notyMessage(response.message); 
          this.saveable = false;
          //window.location.href = base_url+'/productreceive/'+response.data.data.id;
          //console.log(response);
        })
        .catch(function (error) {
          notyMessageError(error);
          //console.log(error);
          $('.ajaxLoading').hide();
        });

      },
      _findAvailablilityofBarcode: function(arr,barcode){
        var result = arr.filter(obj => obj.barcode == barcode);
        return result.length;
      },
      getbarcodeDetails: function(){
        if(this._findAvailablilityofBarcode(this.barcode,this.singlebarcode)>0){
          Swal.fire({type: 'warning',title: 'Oops...', text: 'This Barcode Is Already Selected : '+stocktransferapp.singlebarcode});
          stocktransferapp.singlebarcode = '';
          return;
        }

        if(this.outlet==null){
          Swal.fire({type: 'warning',title: 'Oops...', text: 'Please Select Transfer To Outlet First'});
          stocktransferapp.singlebarcode = '';
          return;
        }
        $('.ajaxLoading').show();
        axios.get(api_uri+'api/v1/cdn/common/barcode/'+usr_outlet+'/'+this.singlebarcode+'/3',p_headers)
        .then(function (response) {
          $('.ajaxLoading').hide();
          if(response.data.length==0){
            Swal.fire({type: 'warning',title: 'Oops...', text: 'No available stock found for the barcode : '+stocktransferapp.singlebarcode});
          }
          else{
            var r = response.data[0];
            stocktransferapp.barcode.push(r);
            stocktransferapp.from_outlet = r.outlet_id;
            stocktransferapp.from_outlet_name = r.outlet_name;
          }
          stocktransferapp.singlebarcode = '';
        })
        .catch(function (error) {
          notyMessageError(error);
          //console.log(error);
          $('.ajaxLoading').hide();
        });
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
      getBarcodes: async function(pid) {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/barcodes/'+pid,p_headers);
          this.barcodeList = response.data;
          this.current_stock = response.data.length;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getOutlets: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/alloutlets/',p_headers);
          this.outletList = response.data;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
  },
  created: function(){
      //this.getProducts();
      this.getOutlets();

      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          this.getbarcodeDetails();
        }
      });
  }
  
});

