Vue.component('v-select', VueSelect.VueSelect);
var posapp = new Vue({
  el: '#pos',
  data: {
      invoiceno:'',
      invoiceid:'',
      returnno:'',
      returnid:'',
      outlet:null,
      outletList:[],
      customer:null,
      customerList:[],
      cmobile:'',
      cname:'',
      caddress:'',
      paymentmethod:{'name':'','id':''},
      paymentmethodList:[],
      paymentmethod2:{'name':'','id':''},
      paymentmethodList2:[],
      paymentmethod3:{'name':'','id':''},
      paymentmethodList3:[],
      ptype:'0',
      otype:'0',
      barcode:null,
      returncode:null,
      displayId: 0,
      displayReturnId: 0,
      itemList:[],
      returnList:[],
      method1: true,
      method2: false,
      method3: false,
      subtotal:0,
      returnsubtotal:0,
      sellsubtotal:0,
      discp:null,
      discamount:0,
      vatp:null,
      vatamount:0,
      netpayable:0,
      netpaid:0,
      netdue:0,
      cashpaid:null,
      cashreturn:null,
      activefocus:'barcode',
      submitfocus:false,
      partialpaid:null,
      dualamount:null,
      
      sellScreen: true,
      returnScreen: false,
      browseScreen: false,
      returnsellScreen: false,
      active_css_class: 'sell_active',
      cashreturned: false,
      requesting:false,

      
  },
  watch:{
    otype: function(otype){
      if(otype=='0'){
        this.sellScreen = true;
        this.returnScreen = false;
        this.browseScreen = false;
        this.returnsellScreen = false;
        this.active_css_class = 'sell_active';
        this.reset();
      }
      else if(otype=='1')
      {
        
        this.sellScreen = false;
        this.returnScreen = true;
        this.browseScreen = false;
        this.returnsellScreen = false;
        this.active_css_class = 'return_active';
        this.reset();
      }
      else if(otype=='2'){
        this.sellScreen = false;
        this.returnScreen = false;
        this.browseScreen = true;
        this.returnsellScreen = false;
        this.active_css_class = 'show_active';
        
      }
      else if(otype=='3'){
        this.sellScreen = false;
        this.returnScreen = false;
        this.browseScreen = false;
        this.returnsellScreen = true;
        this.active_css_class = 'sell_and_return_active';
        this.reset();
      }
    },
    customer: function(customer){
      this.cmobile = customer.mobile;
      this.cname = customer.name;
      this.caddress = customer.address;
    },
    discp: function(discp){
      this.discamount = (this.subtotal*parseFloat(discp)/100).toFixed(2);
    },
    ptype:function(ptype){
      this.$refs.barcodescanner.focus();
    },
    
    discamount: function(discamount){
      this.netpayable = parseFloat(this.subtotal) - parseFloat(discamount) + parseFloat(this.vatamount);
    },
    vatp: function(vatp){
      this.vatamount = ((parseFloat(this.subtotal) - parseFloat(this.discamount))*parseFloat(vatp)/100).toFixed(2);
    },
    vatamount: function(vatamount){
      this.netpayable = parseFloat(this.subtotal) - parseFloat(this.discamount) + parseFloat(vatamount);
    },
    subtotal: function(subtotal){
      this.netpayable = subtotal - this.discamount + parseFloat(this.vatamount);
    },
    cashpaid: function(cashpaid){
      this.cashreturn = cashpaid - this.netpayable;
    },
    itemList: function(itemList){
      var ttl = 0;
      for (const obj of itemList) {
        ttl+=obj.price;
      }
      this.sellsubtotal = ttl;
    },
    sellsubtotal: function(sellsubtotal){
      this.subtotal = sellsubtotal - this.returnsubtotal;
    },
    returnList: function(returnList){
      var ttl = 0;
      for (const obj of returnList) {
        ttl+=obj.price;
      }
      this.returnsubtotal = ttl;
    },
    returnsubtotal: function(returnsubtotal){
      this.subtotal = this.sellsubtotal - returnsubtotal;
    },
    paymentmethod: function(paymentmethod){
      if(paymentmethod.id==1){
        this.method1=true;this.method2=false;this.method3=false;
        //posapp.$refs.cashpaid.focus();
      }
      else if(paymentmethod.id==2){
        this.method1=false;this.method2=false;this.method3=true;
        posapp.$refs["ppaid"].focus();
        //posapp.$refs.ppaid.focus();
      }
      else if(paymentmethod.id==8){
        this.method1=false;this.method2=true;this.method3=false;
        //posapp.$refs.damount.focus();
      }
      else{
        this.method1=false;this.method2=false;this.method3=false;
      }
      console.log(paymentmethod);
    },
  },
  methods:{
      invoiceDisplay: function(data){
        console.log(data);
        this.otype = 2;
        this.customer = data.customer;
        this.outlet = data.outlet;
        this.customerList = [];
        this.customerList.push(data.customer);
        this.itemList = data.itemList;
        this.invoiceno = data.invoiceno;
        this.subtotal = data.amount;
        this.discamount = data.discamount;
        this.vatamount = data.vatamount;
        this.netpayable = data.netpayable;
        this.netpaid = data.netpaid;
        this.netdue = data.netdue;
        this.paymentmethod = data.paymentmethod;
        this.paymentmethod2 = data.paymentmethod2;
        this.paymentmethod3 = data.paymentmethod3;
        this.partialpaid = data.partialpaid;
        this.dualamount = data.dualamount;
      },
      getInvoiceDetails: function(){
        $('.ajaxLoading').show();
        var i_data = {invoice_no: this.invoiceno}
        axios.post(api_uri+'api/v1/management/sell/invoice', i_data,p_headers)
        .then(function (response) {
          posapp.invoiceDisplay(response.data[0].screen_data);
          posapp.invoiceid = response.data[0].id;
          $('.ajaxLoading').hide();
        });
      },
      discountFocus:function(){
        this.discamount = NaN;
      },
      discountBlur:function(){
        if(isNaN(this.discamount)) this.discamount = 0;
      },
      vatFocus:function(){
        this.vatamount = NaN;
      },
      vatBlur:function(){
        if(isNaN(this.vatamount)) this.vatamount = 0;
      },
      openPdf:function(type){
        if(type=='sell_a4'){
          window.open(api_uri+'api/v1/downloads/receipt/sell/invoice/threeinch/'+this.invoiceid, "_blank");    
        }
        else if(type=='sell_3in'){
          window.open(api_uri+'api/v1/downloads/receipt/sell/invoice/threeinch/'+this.invoiceid, "_blank");    
        }
        else if(type=='return_3in'){
          window.open(api_uri+'api/v1/downloads/receipt/return/invoice/threeinch/'+this.returnid, "_blank");    
        }
      },
      confirmreturn: function(){
        this.requesting = true;
        var s_data = 
          {
            outlet: this.outlet,
            returnList: this.returnList,
            retamount: this.returnsubtotal,
          };
          var r_data = {data:s_data};
          axios.post(api_uri+'api/v1/management/sell/return', r_data,p_headers)
            .then(function (response) {
              posapp.requesting = false;
              posapp.cashreturned = true;
              posapp.returnid = response.data.receiptid;
              posapp.returnno = response.data.receiptno;
              $('.ajaxLoading').hide();
            });
      },
      confirmsell: function(){
        
        if(this._validation()){
          this.requesting = true;
          var s_data = 
          {
            outlet: this.outlet,
            customer: this.customer,
            itemList: this.itemList,
            paymentmethod: this.paymentmethod,
            paymentmethod2: this.paymentmethod2,
            paymentmethod3: this.paymentmethod3,
            partialpaid:this.partialpaid,
            dualamount: this.dualamount,
            discamount: this.discamount,
            vatamount: this.vatamount,
            netpayable: this.netpayable,
            amount: this.subtotal,
            operationtype: this.otype,
            returnList: this.returnList,
            retamount: this.returnsubtotal,

          };
          var data= {data:s_data};
          axios.post(api_uri+'api/v1/management/sell/entry', data,p_headers)
            .then(function (response) {
                posapp.requesting = false;
                window.open(api_uri+'api/v1/downloads/receipt/sell/invoice/threeinch/'+response.data.receiptid, "_blank");
                posapp.reset();

              //posapp.invoiceDisplay(response.data);
              //posapp.invoiceid = response.data.invoiceid;
              $('.ajaxLoading').hide();
              location.reload();
            });
        }
        
      },
      checkcustomer: function(){
        if(this.cmobile=='') return;
        var exist = this.customerList.findIndex(item =>{ return item.mobile === this.cmobile});
        if(exist>-1) // Exists
        {
          this.customer = this.customerList[exist];
        }
        else // Not Exists
        {
          this.cname = '';
          this.caddress = '';
          Swal.fire({type: 'info',title: 'New Customer Found', text: 'Please Provide Name Of the Customer And Click On Plus Icon'});
        }
      },
      getreturncode: async function(){
        $('.ajaxLoading').show();
        try {
          var p_data = 
          {
            barcode : this.returncode,
            outlet_id: this.outlet.id,
          };
          axios.post(api_uri+'api/v1/statement/product/return/barcode', p_data,p_headers)
          .then(function (response) {
            if(response.data==""){
              Swal.fire({type: 'warning',title: 'Oops...', text: 'This Product Is Not Found In Your Stock'});
            }
            else{
              posapp.addItem(response.data,2);
              posapp.returncode = '';
            }
            $('.ajaxLoading').hide();
          });
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getbarcode: async function(){
        $('.ajaxLoading').show();
        try {

          var count = 0;
          for (const obj of this.itemList) {
            if (obj.code === this.barcode) count++;
          }
          var p_data = 
          {
            barcode : this.barcode,
            outlet_id: this.outlet.id,
            ptype: this.ptype,
            scount: count,
          };
          axios.post(api_uri+'api/v1/cdn/common/oldbarcode', p_data,p_headers)
          .then(function (response) {
            if(response.data==""){
              Swal.fire({type: 'warning',title: 'Oops...', text: 'This Product Is Not Found In Your Stock'});
            }
            else{
              posapp.addItem(response.data,1);
              posapp.barcode = '';
            }
            $('.ajaxLoading').hide();
          });
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getOutlets: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/outlets/',p_headers);
          this.outletList = response.data;
          this.outlet = response.data[0];
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      createcustomer: function(){
        var p_data = 
          {
            cmobile : this.cmobile,
            cname: this.cname,
            caddress: this.caddress,
          };
          axios.post(api_uri+'api/v1/cdn/common/createcustomer', p_data,p_headers)  
          .then(function (response) {
            console.log(response.data);
            posapp.customerList = response.data;
            posapp.checkcustomer();
            $('.ajaxLoading').hide();
          });
      },
      getCustomers: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/customers/',p_headers);
          this.customerList = response.data;
          this.customer = response.data[0];
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getPaymentmethod: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/paymentmethods',p_headers);
          this.paymentmethodList = response.data;
          this.paymentmethodList2 = response.data;
          this.paymentmethodList3 = response.data;
          this.paymentmethod = response.data[0];
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      addItem : function (item,type) {
          console.log(item);
        if(type==1) {
          this.displayId++;
          var itema = {
            displayId: this.displayId,
            code: item.code,
            barcode: item.barcode,
            name: item.name,
            //price: parseFloat(item.price).toFixed(2),
            price: parseFloat(item.price),
            arcid: item.id,
            pid: item.product_id,
            purchase_price: parseFloat(item.purchase),
            //purchase_price: parseFloat(item.purchase).toFixed(2), 
            
          }
          // var nofound = true;
          // this.itemList.forEach(element => {
          //   if(element.arcid==itema.arcid) nofound=false;
          // });
          // if(nofound) this.itemList.push(itema);
          this.itemList.push(itema);
        }
        else{
          this.displayReturnId++;
          var itema = {
            displayId: this.displayReturnId,
            code: item.code,
            barcode: item.barcode,
            name: item.name,
            price:item.price,
            arcid: item.id,
            pid: item.product_id,
            sell_statement: item.sell_statement,
            purchase_price: item.purchase,
          }
          var nofound = true;
          this.returnList.forEach(element => {
            if(element.arcid==itema.arcid) nofound=false;
          });
          if(nofound) this.returnList.push(itema);
        }
      },
      deleteItem : function (item){
        if(this.otype!='2'){
          var idx = this.itemList.indexOf(item);
          if (idx > -1) {
            this.itemList.splice(idx, 1);
          }
        }
      },
      deleteReturnItem : function (item){
        if(this.otype!='2'){
          var idx = this.returnList.indexOf(item);
          if (idx > -1) {
            this.returnList.splice(idx, 1);
          }
        }
      },
      _validation : function(){
        var ret = true;
        if(this.itemList.length==0){
          Swal.fire({type: 'warning',title: 'Oops...', text: 'Please Scan Items To Sell'});
          ret = false;
        }
        if(this.paymentmethod.id==2)// Partial Payment
        {
          if(this.partialpaid==null){
            Swal.fire({type: 'warning',title: 'Oops...', text: 'For Partial Payment Please Provide Paid Amount'});
            ret = false;
          }
        }
        
        if(this.paymentmethod.id==8)// Dual Payment
        {
          if(this.paymentmethod2==null || this.paymentmethod3==null || this.dualamount==""){
            Swal.fire({type: 'warning',title: 'Oops...', text: 'For Dual Payment Please Provide Method And Amount Correctly'});
            ret = false;
          }
        }
        return ret;
      },
      reset: function(){

        this. invoiceno = '';
        this.paymentmethod2 = {'name':'','id':''};
        this.paymentmethod3 = {'name':'','id':''};
        this.itemList=[];
        this.returnList=[];
        this.subtotal = 0;
        this.returnsubtotal = 0;
        this.discp = null;
        this.discamount = 0;
        this.netpayable = 0;
        this.netpaid = 0;
        this.netdue = 0;
        this.cashpaid = null;
        this.cashreturn = null;
        this.activefocus = 'barcode';
        this.partialpaid = null;
        this.dualamount = null;
        this.vatp = null;
        this.vatamount = 0;
        this.cashreturned = false;
        this.returnno = '';
        this.returnid = '';
        this.sellsubtotal = 0;
        this.returnsubtotal = 0;
      }

  },
  created: function(){
      this.getOutlets();
      this.getCustomers();
      this.getPaymentmethod();
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          if(this.activefocus=='confirm'){
            //this.confirmsell();
          }
          else if(this.activefocus=='customercheck'){
            this.checkcustomer();
          }
          else if(this.activefocus=='customerentry'){
            this.createcustomer();
          }
          else {
            this.getbarcode();
          }
        }
      });
  },
  mounted: function () {
    this.$refs.barcodescanner.focus();
  },
});

