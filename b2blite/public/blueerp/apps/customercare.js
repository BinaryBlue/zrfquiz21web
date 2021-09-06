
var baseproductapp = new Vue({
  el: '#baseproduct',
  data: {
      code: '',
      head:null,
      variations:[],
      outlets:[],
      outletwise:[],
      outletwiseDraw:[],
      outletwiseDrawSum:null,
      ecommerce:null,
      barcodes:[],
      imgUrl: 'http://blueerp.diganta.com.bd/window/public/clothes.png',
      dummyAge:{
        '16':'9-12 Months ',
        '18': '1-2 Years ',
        '20': '2-3 Years ',
        '22': '3-4 Years ',
        '24' : '4-5 Years ',
        '26' : '5-6 Years ',
        '28' : '6-7 Years ',
        '30' : '7-8 Years',
        '32' : '8-9 Years',
        '34' : '9-10 Years',
        '36' : '10-11 Years',
        '38' : '11-12 Years',
        '40' : '12-13 Years',
        '42' : '13-14 Years',
      }
  },
  watch:{
    code:function(code){
      this.code = code.trim().toUpperCase();
    },
    itemList: function(itemList){
    },

  },
  methods:{
      openUrl: function(url){
        window.open(url, "_blank");   
      },
      copyText: function(text){
        $('.ajaxLoading').show();
        var dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        $('.ajaxLoading').hide();
      },
      calculate_stock:function(variations){
        return variations.reduce((acc, val) => {
          return acc + parseInt(val.current_stock);
        }, 0);
      },
      drawOutletwise:function(){
        this.outletwiseDraw = [];
        this.outletwiseDrawSum = {1:0,2:0,3:0,4:0,5:0,6:0,7:0,8:0,9:0,10:0,11:0};

        this.variations.forEach(variation => { // Variations 
          var obj = {code:variation.code,outletwise:[],total:0,price:variation.selling_price};

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

          // Calculate Total Stock Based On Barcode
          var obj_ttl = 0;
          obj.outletwise.forEach(stock => {
            obj_ttl+= stock.barcodes;
          });
          obj.total = obj_ttl;
          // Push To Draw
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
            const response = await axios.post(api_uri+'api/v1/callcenter/product/base/details',params,p_headers);
            $('.ajaxLoading').hide();
            if(response.data.status==200){ // Found
              baseproductapp.head = response.data.data.head;
              baseproductapp.variations = response.data.data.variations;
              baseproductapp.outlets = response.data.data.outlets;
              baseproductapp.outletwise = response.data.data.outletwise;
              baseproductapp.ecommerce = response.data.data.ecommerce;
              baseproductapp.drawOutletwise();
              baseproductapp.imgUrl = response.data.data.ecommerce.base.images[0].src;
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

