Vue.component('v-select', VueSelect.VueSelect);
var orderapp = new Vue({
  el: '#orderslanding',
  data: {
      mode: 'Customer',
      fromDate:'',
      toDate:'',

      pendingTransfers:[],
      outletOrders:[],
      dueBills:[],
      allPayments:[],
      current_ttl_paid:0,

      invoiceno:'',
      invoiceid:'',


      orderid:'',
      orderno:'',
      orderstatus:0,
      orderremarks:'',

      initCustomer: {id:0,name:'',mobile:'',email:'',address:'',deliveryaddress:''},
      customer: {id:0,name:'',mobile:'',email:'',address:'',deliveryaddress:''},
      customerList:[],
      previousOrders:[],
      initCustomerScore:[
        {"statusno":"50","statusname":'Total Order',"counter":0},
        {"statusno":"51","statusname":'Overall Score (Out of 100)',"counter":0}
      ],
      customerScore:[
        // {"statusno":"0","statusname":'Pending',"counter":0},
        // {"statusno":"1","statusname":'Delivery Channel',"counter":0},
        // {"statusno":"2","statusname":'Completed',"counter":0},
        // {"statusno":"3","statusname":'Canceled',"counter":0},
        {"statusno":"50","statusname":'Total Order',"counter":0},
        {"statusno":"51","statusname":'Overall Score (Out of 100)',"counter":0}

      ],

      payments:[],
      paymentmethodList:[],
      paymentmethod:null,
      payment_amount:0,

      outlet_id:0,
      outlet_name:'',
      outlet_barcode:'',
      outlet_barcode_id:0,

      code:'',
      current:{},
      outlet:0,
      itemList:[],
      subtotal:0,
      discp:null,
      discamount:0,
      vatp:null,
      delivery_channel:null,
      delivery_fee:0,
      vatamount:0,
      netpayable:0,
      netpaid:0,
      netdue:0,


      discamount:0,
      netpayable:0,
      netpaid:0,
      netdue:0,
      vatp:null,
      vatamount:0,
      delivery_chalan:'',
      delivery_fee:0,
      outletwisebarcodes:[],

      activefocus:'productcode',
      
  },
  watch:{
    mode: function(mode){

      if(mode=='Outlet Order') this.getOutletOrders();
      else if(mode=='Transfer') this.getPendingTransfer();
      else if(mode=='Due Payment') this.getdueBills();
      else if(mode=='Payment') this.getallPayments();
      else if(mode=='New') this.getProducts();

      if(mode=='Transfer' || mode=='Payment' || mode=='Due Payment' || mode=='Outlet Order'){
        setTimeout(() => { 
          $('#fromdate').datepicker({format: 'yyyy-mm-dd',}).on(
            'changeDate', () => { this.fromDate = $('#fromdate').val() }
          );
          $('#todate').datepicker({format: 'yyyy-mm-dd',}).on(
            'changeDate', () => { this.toDate = $('#todate').val() }
          );
        }, 500);
        
      }
    },
    allPayments: function(allPayments){
      this.current_ttl_paid = 0;
      allPayments.forEach(e => {
        this.current_ttl_paid+=e.amount;
      });
    },
    discp: function(discp){
      this.discamount = (this.subtotal*parseFloat(discp)/100).toFixed(2);
    },
    discamount: function(discamount){
      orderapp.redraw();
      orderapp.serversync();
    },
    delivery_fee: function(delivery_fee){
      orderapp.redraw();
      orderapp.serversync();
    },
    vatp: function(vatp){
      this.vatamount = ((parseFloat(this.subtotal) - parseFloat(this.discamount))*parseFloat(vatp)/100).toFixed(2);
    },
    vatamount: function(vatamount){
      orderapp.redraw();
      orderapp.serversync();
    },
    itemList: function(itemList){
      orderapp.redraw(itemList);
      orderapp.serversync();
    },

  },
  methods:{
      resetForNewOrder:function(){
        this.orderno = '';
        this.orderid = 0;
        this.orderstatus = 0;
        this.itemList = [];
        this.orderremarks = '';
        //this.outlet = 0;
        this.subtotal=0;
        this.discp=null;
        this.discamount=0;
        this.vatp=null;
        this.delivery_channel=null;
        this.delivery_fee=0;
        this.vatamount=0;
        this.netpayable=0;
        this.netpaid=0;
        this.netdue=0;
        this.discamount=0;
        this.netpayable=0;
        this.netpaid=0;
        this.netdue=0;
        this.vatp=null;
        this.vatamount=0;
        this.delivery_chalan='';
        this.delivery_fee=0;
        $("#displayqr").empty();
      },
      displayOrder: function(order){
        // Load Data


        this.orderstatus = parseInt(order.order_info[0].status);
        this.orderno = order.order_info[0].order_no;
        this.subtotal = order.order_info[0].sub_total;
        this.netpaid = order.order_info[0].paid;
        this.discamount = order.order_info[0].discount;
        this.delivery_fee = order.order_info[0].delivery_fee;
        this.delivery_chalan = order.order_info[0].delivery_chalan;
        this.subtotal = order.order_info[0].sub_total;
        this.netpayable = order.order_info[0].net_payable;
        this.itemList = order.order_info[0].items;
        this.orderremarks = order.order_info[0].remarks;
        this.payments = order.payment_info;
        this.customer = order.customer_info[0];
      

        // Load QR Code
        $("#displayqr").empty();
        $('#displayqr').qrcode(order.order_info[0].order_no);


      },
      openOrder: async function(order){
        this.resetForNewOrder();
        $('.ajaxLoading').show();
        this.mode = 'Display';
        const response = await axios.get(api_uri+'api/v1/order/get/'+order,p_headers);
        //console.log(response.data[0].outlet);
        if(orderapp.outlet == parseInt(response.data.order_info[0].outlet)) orderapp.mode = 'New';
        else orderapp.mode = 'Display';
        orderapp.displayOrder(response.data);

        $('.ajaxLoading').hide();
      },
      getPendingTransfer: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/order/pendingtransfers/'+this.outlet,p_headers);
        this.pendingTransfers = response.data;
        $('.ajaxLoading').hide();
      },
      getOutletOrders: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/order/outletorders/'+this.outlet+'/'+this.fromDate+'/'+this.toDate,p_headers);
        this.outletOrders = response.data;
        $('.ajaxLoading').hide();
      },
      getdueBills: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/order/dueBills/'+this.outlet+'/'+this.fromDate+'/'+this.toDate,p_headers);
        this.dueBills = response.data;
        $('.ajaxLoading').hide();
      },
      getallPayments: async function(){
        $('.ajaxLoading').show();
        var data = 
        {
          outlet: this.outlet,
          fromDate: this.fromDate,
          toDate: this.toDate,
        };
        axios.post(api_uri+'api/v1/payment/list', data,p_headers)
          .then(function (response) {
            $('.ajaxLoading').hide();
            orderapp.allPayments = response.data;
            console.log(response);
          });
      },
      redraw: function(itemList=null){
        console.log('Redraw called');
        if(itemList==null) itemList = this.itemList;
        var ttl = 0;
        for (const obj of itemList) {
          ttl+=obj.price;
        }
        //update dependencies
        this.subtotal = ttl;
        this.netpayable = parseFloat(this.subtotal) - parseFloat(this.discamount) + parseFloat(this.vatamount) + parseFloat(this.delivery_fee);
      },
      _validation: function(){
        return true;
      },
      serversync: async function(){
        if(this._validation()){
          var data = 
          {
            seller_outlet: this.outlet,
            order_no: this.orderno,
            itemList: this.itemList,
            orderremarks:this.orderremarks,
            subtotal: this.subtotal,
            discamount: this.discamount,
            vatamount: this.vatamount,
            deliveryfee: this.delivery_fee,
            netpayable: this.netpayable,

          };
          axios.post(api_uri+'api/v1/order/itemupdate', data,p_headers)
            .then(function (response) {
              $('.ajaxLoading').hide();
              console.log(response);
            });
        }
      },
      checkcustomer: async function(){
        if(this.customer.mobile.length==11){
          $('.ajaxLoading').show();
          const response = await axios.get(api_uri+'api/v1/cdn/common/customer/idfrommobile/'+this.customer.mobile,p_headers);
          this.customer = response.data;
          const score = await axios.get(api_uri+'api/v1/cdn/common/customer/score/'+this.customer.id,p_headers);
          this.previousOrders = score.data.orders;
          this.customerScore = [];
          var total=0,canceled=0,calculated_score=0;
          
          score.data.score.forEach(e => {
            switch(e.statusno) {
              case '0':
                this.customerScore.push({"statusno":"0","statusname":'Pending',"counter":e.counter});
                total+=e.counter;
                break;
              case '1':
                this.customerScore.push({"statusno":"1","statusname":'Delivery Channel',"counter":e.counter});
                total+=e.counter;
                break;
              case '2':
                  this.customerScore.push({"statusno":"2","statusname":'Completed',"counter":e.counter});
                  total+=e.counter;
                  break;
              case '3':
                  this.customerScore.push({"statusno":"3","statusname":'Canceled',"counter":e.counter});
                  total+=e.counter;
                  canceled+=e.counter;
                  break;
              default:
                // code block
            }
          });
          if(this.customerScore.length!=0){
            calculated_score = ((1 - (canceled)/total)*100).toFixed(2);
          }
          //else calculated_score = 0;
          
          this.customerScore.push({"statusno":"50","statusname":'Total Order',"counter":total});
          this.customerScore.push({"statusno":"51","statusname":'Overall Score (Out of 100)',"counter":calculated_score});
          $('.ajaxLoading').hide();
        }
      },
      updateCustomer: async function(){
        if(!this.isCustomerReady()) return;
        var data = 
        {
          id: this.customer.id,
          mobile : this.customer.mobile,
          name: this.customer.name,
          address: this.customer.address,
          delivery: this.customer.deliveryaddress,
          email: this.customer.email,
        };
        const response = await axios.post(api_uri+'api/v1/cdn/common/customer/sync', data,p_headers);
      },

      getPaymentmethod: async function() {
        $('.ajaxLoading').show();
        try {
          //const response = await axios.get(api_uri+'api/v1/cdn/common/paymentmethods',p_headers);
          this.paymentmethodList = 
            [
              {"id":1,"name":"Cash"},
              {"id": 4,"name": "Bkash"},
              {"id": 5,"name": "Rocket"},
              {"id": 6,"name": "City Amex"},
              {"id": 7, "name": "DBBL"},
              {"id": 9, "name": "UCB"},
              {"id": 10, "name": "Nexus"},
              { "id": 11, "name": "UKash"}
            ]
          this.paymentmethod = this.paymentmethodList[0];
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
          autocomplete(document.getElementById("autocomplete"), this.productList );
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      _findCurrent: function(){
        this.current = this.productList.find(product => product.code === this.code);
      },
      _showAlert:function(type,title,text){
        Swal.fire({type: type,title: title, text: text});
      },
      getbarcode: async function(){
        this.outletwisebarcodes = [];
        this.current = {};
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/cdn/common/products/outletwisebarcode/'+this.code,p_headers);
        this.outletwisebarcodes = response.data;
        $('.ajaxLoading').hide();
        if(this.outletwisebarcodes.length == 0) Swal.fire({type: 'error',title: 'No Barcode Found', text: 'Either you have entered wrong product code or your stock is empty.'});
        else this._findCurrent();
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
      isStockOutletChecked:function(){
        var ret = false;
        this.outlet_name = $('input[name=outlet]:checked').val();
        this.outlet_id = parseInt($('input[name=outlet]:checked').attr('outlet_id'));
        this.outlet_barcode = $('input[name=outlet]:checked').attr('barcode');
        this.outlet_barcode_id = parseInt($('input[name=outlet]:checked').attr('barcode_id'));
        if(isNaN(this.outlet_id)){
          Swal.fire({type: 'error',title: 'Select Stock', text: 'Please select stock of the outlet for this order'});
        }
        else{
          ret = true;
        }
        return ret;
      },
      isCustomerReady: function(){
        var ret = false;
        if(this.customer.id==0) {
          Swal.fire({type: 'error',title: 'Customer ID Missing', text: 'Please provide 11 digit mobile number and collect ID'});
        }
        else{
          ret = true;
        }
        return ret;
      },
      addItem: async function(){
        if(!this.isStockOutletChecked()) return;
        if(!this.isCustomerReady()) return;

        // It Means all done but order number is not found
        if(this.orderno==''){
          await this.collectOrderNo();
          var item = {
            orderno: this.orderno,
            code: this.code,
            barcode: this.outlet_barcode,
            barcode_id: this.outlet_barcode_id,
            name: this.current.name,
            price: parseFloat(this.current.selling_price),
            outlet_id: this.outlet_id,
            outlet_name: this.outlet_name,
            pid: this.current.id,
            purchase_price: 0,
            customer:this.customer.id,
          }
          this.itemList.push(item);
          this.outletwisebarcodes = [];
        }
        else{
          var item = {
            orderno: this.orderno,
            code: this.code,
            barcode: this.outlet_barcode,
            barcode_id: this.outlet_barcode_id,
            name: this.current.name,
            price: parseFloat(this.current.selling_price),
            outlet_id: this.outlet_id,
            outlet_name: this.outlet_name,
            pid: this.current.id,
            purchase_price: 0,
            customer:this.customer.id,
          }
          this.itemList.push(item);
          this.outletwisebarcodes = [];
        }
        
      },
      deleteItem : function (item){

          axios.post(api_uri+'api/v1/order/deleteitem', item,p_headers)
            .then(function (response) {
              var idx = orderapp.itemList.indexOf(item);
              if (idx > -1) {
                orderapp.itemList.splice(idx, 1);
              }
            });
      },

      updateOrder: async function(){
        
      },

      collectOrderNo: async function(){
        const response = await axios.get(api_uri+'api/v1/order/getorderno/'+this.outlet+'/'+this.customer.id,p_headers);
        console.log(response.data);
        this.orderid = response.data.id;
        this.orderno = response.data.order_no;
        $('#displayqr').qrcode(this.orderno); // Generate QR Code
        return response.data.order_no;
      },
      _getNameFromMethodId: function(id){
        return this.paymentmethodList.find(method => method.id === id).name;
      },
      makePayment:async function(){
        if(this.payment_amount!=null && this.payment_amount>0){
          var payment = {
            paymentmethod: this.paymentmethod,
            amount: this.payment_amount,
            outlet:this.outlet,
            order_no:this.orderno
          };
          const response = await axios.post(api_uri+'api/v1/payment/deposit', payment,p_headers);
          //console.log(response.data);
          
          var payment_item = {
            date: response.data.date,
            payment_no: response.data.payment_no,
            payment_method: response.data.payment_method,
            order_no:response.data.order_no,
            amount:response.data.amount,
            id: response.data.id,
          }
          this.netpaid = this.netpaid + response.data.amount;
          this.payments.push(payment_item);
        }
        else{
          Swal.fire({type: 'error',title: 'Incorrect Amount', text: 'Please provide payment amount correctly.'});
        }
      },
      openPdf:function(type){
        if(type=='sell_3in'){
          window.open(api_uri+'api/v1/order/print/threeinch/'+this.orderno, "_blank");    
        }

      },
      createDate: function(){
        var d = new Date();
        var month = '', day = '';

        if(d.getMonth()<9) month = '0'+(d.getMonth()+1);
        else month = d.getMonth();

        if(d.getDate()<10) day = '0'+d.getDate();
        else day = d.getDate();

        this.fromDate = d.getFullYear()+'-'+month+'-'+d.getDate();
        this.toDate = d.getFullYear()+'-'+month+'-'+d.getDate();
      },

  },
  created: function(){
      //this.getProducts();
      this.createDate();
      this.getPaymentmethod();
      if(usr_outlet==0) this.outlet = 1;
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          //this.getbarcode();
        }
      });
  }
  
});

