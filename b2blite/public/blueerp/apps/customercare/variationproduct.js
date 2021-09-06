
var variationproductapp = new Vue({
  el: '#variationproduct',
  data: {
      code: '',
      head:null,
      statuswise:[],
      outlets:[],
      outletwise:[],
      outletwiseDraw:[],
      outletwiseDrawSum:null,
      ecommerce:null,
      barcodes:[],
      totalProduction: {counter:0,barcodes:[]},
      inWarehouse: {counter:0,barcodes:[]},
      transferChannel: {counter:0,barcodes:[]},
      sellAble: {counter:0,barcodes:[]},
      orderChannel: {counter:0,barcodes:[]},
      sold: {counter:0,barcodes:[]},
      damaged: {counter:0,barcodes:[]},

      drawableBarcodes:[],

      dt_currentStock:null,


      utility_selected:null,
      utility_barcode:'',
      //statuses:['1''aa']
  },
  watch:{
    code:function(code){
      this.code = code.trim().toUpperCase();
    },
    utility_barcode:function(utility_barcode){
      this.utility_barcode = utility_barcode.trim().toUpperCase();
    }

  },
  methods:{
      openUrl: function(url){
        window.open(url, "_blank");   
      },
      calculate_stock:function(variations){
        return variations.reduce((acc, val) => {
          return acc + parseInt(val.current_stock);
        }, 0);
      },
      
      makeDamage: async function(){

        $('.ajaxLoading').show();
          try {
            var params = { code : this.utility_barcode};
            const response = await axios.post(api_uri+'api/v1/utility/barcode/makedamage',params,p_headers);
            $('.ajaxLoading').hide();
            this._showAlert(response.data.status,response.data.title,response.data.message);
            //this.barcodes = response.data;
          } catch (error) {
            console.log(error);
          }
      },
      makeSellable: async function(){

        $('.ajaxLoading').show();
          try {
            var params = { code : this.utility_barcode,outlet_id: this.utility_selected};
            const response = await axios.post(api_uri+'api/v1/utility/barcode/makesellable',params,p_headers);
            $('.ajaxLoading').hide();
            this._showAlert(response.data.status,response.data.title,response.data.message);
            //this.barcodes = response.data;
          } catch (error) {
            console.log(error);
          }
      },
      drawOutletwise:function(){
        this.outletwiseDraw = [];

        this.outlets.forEach(outlet => {
          var obj = {"code": this.code,"outlet_id": outlet.id,"outlet_name": outlet.name,"barcodes": 0};
          var gotit = false;
          var gotObj = null;
          this.outletwise.forEach(stocks => { // Distributions
            if(stocks.outlet_id == obj.outlet_id ){
              gotit = true;
              gotObj = stocks;
            }
          });
          if(gotit==true){
            this.outletwiseDraw.push(gotObj);
          }
          else{
            this.outletwiseDraw.push(obj);
          }

        });


        this.variations.forEach(variation => { // Variations 
          var obj = {code:variation.code,outletwise:[],};

          this.outlets.forEach(outlet => { // Outlets
            var outletwise_obj = {outlet_id:outlet.id,outlet_name:outlet.name,barcodes:0};
           
            this.outletwise.forEach(stocks => { // Stocks
              if(stocks.code == obj.code && stocks.outlet_id==outletwise_obj.outlet_id){
                outletwise_obj.barcodes = stocks.barcodes;
                this.outletwiseDrawSum[stocks.outlet_id] += stocks.barcodes; 
                //obj.outletwise.push(outletwise_obj);
              }
            });
            
            obj.outletwise.push(outletwise_obj);

          });
          this.outletwiseDraw.push(obj);

        });
      },
      getBarcode: async function(id,cd){
        this.barcodes = [];
        $('.ajaxLoading').show();
          try {
            var params = { code : cd,outlet_id: id};
            const response = await axios.post(api_uri+'api/v1/callcenter/product/base/barcodes',params,p_headers);
            $('.ajaxLoading').hide();
            this.barcodes = response.data;
          } catch (error) {
            console.log(error);
          }
      },
      reset:function(){
        this.head = null;
        this.variations = [];
        this.outlets = [];
        this.outletwise = [];
        this.outletwiseDraw = [];
        this.outletwiseDrawSum = null;
        this.ecommerce = null;
        this.barcodes = [];
      },
      getData: async function() {
        this.reset();
        $('.ajaxLoading').show();
          try {
            var params = { code : this.code};
            const response = await axios.post(api_uri+'api/v1/callcenter/product/variation/details',params,p_headers);
            $('.ajaxLoading').hide();
            if(response.data.status==200){ // Found
              variationproductapp.head = response.data.data.head;
              variationproductapp.statuswise = response.data.data.statuswise;
              variationproductapp.outlets = response.data.data.outlets;
              variationproductapp.outletwise = response.data.data.outletwise;
              variationproductapp.totalProduction = response.data.data.totalProduction;
              variationproductapp.inWarehouse = response.data.data.inWarehouse;
              variationproductapp.transferChannel = response.data.data.transferChannel;
              variationproductapp.sellAble = response.data.data.sellAble;
              variationproductapp.orderChannel = response.data.data.orderChannel;
              variationproductapp.sold = response.data.data.sold;
              variationproductapp.damaged = response.data.data.damaged;
              variationproductapp.drawOutletwise();
              variationproductapp.drawableBarcodes = response.data.data.totalProduction.barcodes;
              // setTimeout(
              //   function() {
              //     if(variationproductapp.dt_currentStock == null){
              //       variationproductapp.dt_currentStock = $('#table_currentStock').DataTable(
              //         {
              //           pageLength: 10,
              //           filter: true,
              //           deferRender: true,
              //           scrollY: 200,
              //           scrollCollapse: true,
              //           scroller: true
              //         }
              //       );
              //     }
              //     else{
              //       variationproductapp.dt_currentStock.clear();
              //       variationproductapp.dt_currentStock.row.add(variationproductapp.totalProduction.barcodes).draw(true);
              //     }
                  

              //   }, 500);
              
            }
            else{
              this._showAlert('error',response.data.title,response.data.message);
            }
          } catch (error) {
            console.log(error);
          }

      },

      _showAlert:function(type,title,text){
        Swal.fire({type: type,title: title, text: text});
      },

  },
  created: function(){
      //this.getProducts();
      if(usr_outlet==0) this.outlet = 1;
      else this.outlet = usr_outlet;
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          this.getData();
        }
      });
  }
  
});

