Vue.component('v-select', VueSelect.VueSelect);
var orderapp = new Vue({
  el: '#orderslanding',
  data: {
      mode: 'New',
      draftMode: false,
      activeColor: '#ffffff',
      fromDate:'',
      toDate:'',
      filter:'',

      productList:[],
      selectedProduct:null,


      pendingTransfers:[],
      receiveTransferList:[],
      sellerTransfers:[],
      seller_outlet:0,
      outletOrders:[],
      dueBills:[],
      allPayments:[],
      current_ttl_paid:0,
      current_ttl_sub:0,
      current_ttl_dis:0,
      current_ttl_delivery:0,
      current_ttl_vat:0,
      current_ttl_net:0,
      current_ttl_due:0,
      active_transfer_code:'',

      invoiceno:'',
      invoiceid:'',


      orderid:'',
      orderno:'',
      orderstatus:'Pending',
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
      outletList:[],

      code:'',
      current:{},
      outlet:0,
      itemList:[],
      returnList:[],
      returnTotal:0,
      globalDiscPercentage:0.0,
      subtotal:0,
      discp:null,
      vatp:null,
      delivery_channel:null,
      delivery_fee:0,
      netpayable:0,
      netpaid:0,
      netdue:0,

      discamount:0,
      vatamount:0,
      delivery_chalan:'',
      outletwisebarcodes:[],

      logInfo:[],

      activefocus:'productcode',
      statusClasses:{
        Pending:{iconClass:'fas fa-cogs',bgClass:'bg-warning',width:'50%',next:'Confirmed',prev:'Canceled',msg:'Confirmation Required'},
        Confirmed:{iconClass:'fas fa-check-square',bgClass:'bg-success',width:'80%',next:'Completed',prev:'Pending',msg:'Stock Adjustment Required'},
        Completed:{iconClass:'fas fa-recycle',bgClass:'bg-info',width:'100%',next:'Pending',prev:'Delivered',msg:'Sell Completed'},
        Canceled:{iconClass:'fas fa-trash-alt',bgClass:'bg-dark',width:'100%',msg:'Sell Canceled',next:'',prev:''},
      },
      menuStatus:{
        1:'Requested',
        2:'Transfered',
        3:'Received',
        4:'Canceled',
      },
      
      packet_remarks:'',
      packets:[],
      delivery_boys:[],
      delivery_boy:{id:0,username:'Not Selected'},
      allUsers:[],

      shipments:[],

      deliveryChannels:[],
      selectedChannel:{"id":0,"uid":null,"name":"Not Selected","rate":0,"address":null,"mobile":null,"entry_at":"2021-06-08 13:18:59","entry_by":null,"update_at":"2021-06-08 13:19:07","update_by":null},

      delivery_memo_file:'',
      delivery_remarks:'',
      
      expenses:[],
      expense_remarks:'',
      expense_amount:0.

      
  },
  watch:{
    draftMode: function(draftMode){
      if(this.mode=='New'){
        if(draftMode==true) this.activeColor = '#f5cb8e';
        else this.activeColor = '#ffffff';
      }

    },
    mode: function(mode){

      // if(mode=='Outlet Order') this.getOutletOrders();
      // else if(mode=='Transfer') this.getPendingTransfer();
      // else if(mode=='Due Payment') this.getdueBills();
      // else if(mode=='Payment') this.getallPayments();
      // else if(mode=='New') this.getProducts();
      this.format = '';
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
    code:function(code){
      this.code = code.trim().toUpperCase();
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
      this.draftMode = true;
      orderapp.redraw();
      //orderapp._calculateDiscountedPrice();
      //orderapp.serversync();
    },
    delivery_fee: function(delivery_fee){
      this.draftMode = true;
      orderapp.redraw();
      //orderapp.serversync();
    },
    vatp: function(vatp){
      this.vatamount = ((parseFloat(this.subtotal) - parseFloat(this.discamount))*parseFloat(vatp)/100).toFixed(2);
    },
    vatamount: function(vatamount){
      this.draftMode = true;
      orderapp.redraw();
      //orderapp.serversync();
    },
    itemList: function(itemList){
      orderapp.redraw(itemList);
      orderapp.watchForReturnList();
      //orderapp.serversync();
    },
    returnList: function(returnList,oldList){
      //console.log(oldList);
      //console.log(returnList);
      //var newItem = 
      
      this.returnTotal = 0;
      returnList.forEach(e => {
        this.returnTotal += this._calculateDiscountedPrice(e.price);
      });
      orderapp.redraw();

    },
    selectedChannel: function(selectedChannel){
      if(selectedChannel != null){
        this.draftMode = true;
        this.delivery_fee = selectedChannel.rate;
      }
      else this.delivery_fee = 0;
    }

  },
  methods:{
      watchForReturnList:function(){
        this.returnList = [];
        this.itemList.forEach(e => {
          if(e.returned == true){
            this.returnList.push(e);
          }
        });
      },
      _calculateGlobalDiscPercentage:function(net_payable,discount,delivery_fee,vat){
        var discttl = parseFloat(net_payable) + parseFloat(discount) - parseFloat(delivery_fee) - parseFloat(vat);
        if(discount >0) orderapp.globalDiscPercentage = (parseFloat(discount /discttl).toFixed(2));
        else orderapp.globalDiscPercentage = 1.00;
      },
      _calculateDiscountedPrice:function(price){
        return (Math.ceil(this.globalDiscPercentage*price));
      },
      // _calculateReturnedAmount:function(price){
      //   if(this.discamount==0) return price;
      //   else{
      //     return (Math.ceil(this.globalDiscPercentage*price));
      //   }
      // },
      resetForNewOrder:function(){
        //this.orderno = '';
        this.orderid = 0;
        this.orderstatus = 'Pending';
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
        this.payments=[];
        this.expenses=[];
        this.sellerTransfers=[];
        this.seller_outlet = 0;
        this.customer = this.initCustomer;
        this.customerScore = this.initCustomerScore;
        this.previousOrders = [];
        this.payment_amount = 0;
        this.selectedChannel = {id:0,rate:0,name:'Not Selected'};
        this.delivery_boy = {id:0,username:'Not Selected'};
        this.shipments = [];
        this.packets = [];

        $("#displayqr").empty();
      },
      displayOrder: function(order){
        // Load Data

        //this.selectedChannel = this.deliveryChannels.find(e => e.id == order.order_info[0].delivery_channel);
        this.orderstatus = order.order_info[0].status;
        this.orderno = order.order_info[0].order_no;
        //this.seller_outlet = order.order_info[0].outlet;
        this.subtotal = order.order_info[0].sub_total;
        this.netpaid = order.order_info[0].paid;
        this.discamount = order.order_info[0].discount;
        this.vatamount = order.order_info[0].vat;
        this.delivery_fee = order.order_info[0].delivery_fee;
        //this.delivery_chalan = order.order_info[0].delivery_chalan;
        this.subtotal = order.order_info[0].sub_total;
        this.netpayable = order.order_info[0].net_payable;
        this.itemList = [];

        orderapp._calculateGlobalDiscPercentage(order.order_info[0].net_payable,order.order_info[0].discount,order.order_info[0].delivery_fee,order.order_info[0].vat);
        // var discttl = parseFloat(order.order_info[0].net_payable) + parseFloat(order.order_info[0].discount) - parseFloat(order.order_info[0].delivery_fee) - parseFloat(order.order_info[0].vat);
        // if(order.order_info[0].discount >0) orderapp.globalDiscPercentage = order.order_info[0].discount /discttl;
        // else orderapp.globalDiscPercentage = 1.00;
        if(order.order_info[0].items!=null)
        {
          order.order_info[0].items.forEach(e => {
            if(!('returned' in e)){
              e['returned'] = false;
            }
            orderapp.itemList.push(e);
          });
        } 

        //this.itemList = order.order_info[0].items;a
        this.orderremarks = order.order_info[0].remarks;
        this.payments = order.payment_info;
        this.expenses = order.expense_info;

        this.customer = order.customer_info[0];
        //this.sellerTransfers = order.transfer_info;
        this.logInfo = order.log_info;
        //this.shipments = order.shipment_info;
        //this.packets = order.packet_info;
        
        //if(order.packet_info.length > 0) this.delivery_boy = this.delivery_boys.find(e => e.id == order.packet_info[0].delivery_boy_id);
        


      },
      loadQr: function(){
        $("#displayqr").empty();
        $('#displayqr').qrcode({width: 180,height: 180,text: this.orderno});
      },
      openOrderNewTab:function(order){
        window.open(base_url+'/orderscontroller/order/'+order, "_blank");    
      },
      openOrder: async function(order){
        //this.resetForNewOrder();
        $('.ajaxLoading').show();
        this.mode = 'Display';
        const response = await axios.get(api_uri+'api/v1/order/get/'+order,p_headers);
        $('.ajaxLoading').hide();
        //console.log(response.data[0].outlet);
        if((this.outlet == parseInt(response.data.order_info[0].outlet)) || (b_usr_gp < 3) || (_usr == 26)) this.mode = 'New';
        else this.mode = 'Display';
        this.displayOrder(response.data);
        this.loadQr();
        this.draftMode = false;
        // Load QR Code
        //console.log('Comes to qr'+response.data.order_info[0].order_no);

        
      },
      getPendingTransfer: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/order/pendingtransfers/'+this.outlet,p_headers);
        this.pendingTransfers = response.data;
        $('.ajaxLoading').hide();
      },
      getReceiveTransfer: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/order/receivealltransfers/'+this.outlet,p_headers);
        this.receiveTransferList = response.data;
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
            //console.log(response);
          });
      },
      redraw: function(itemList=null){
        //console.log('Redraw called');
        if(itemList==null) itemList = this.itemList;
        var ttl = 0;
        for (const obj of itemList) {
          ttl += ((obj.price - obj.disc) * obj.qtt);
        }
        //update dependencies
        this.subtotal = ttl;
        this.netpayable = parseFloat(this.subtotal) - parseInt(this.returnTotal) - parseFloat(this.discamount) + parseFloat(this.vatamount) + parseFloat(this.delivery_fee);
      },
      _validation: function(showmessage=false){
        if(this.orderno==''){
          if(showmessage===true){
            this._showAlert('error','Invalid Order','Please Collect Order No. First');
            console.log(showmessage);
          }
          return false;
        }
        else return true;
      },
      _expenseEntry: async function(){
        if(this.expense_amount > 0){
          var data = {order_no:this.orderno,amount:this.expense_amount,remarks:this.expense_remarks,sector_id:5,sector_name:'Delivery Cost'};
          const response = await axios.post(api_uri+'api/v1/expense/entry', data,p_headers);
          this._showAlert(response.data.status,response.data.title,response.data.message);
          this.openOrder(this.orderno);
          this.expense_amount = 0;
          this.expense_remarks = '';

        }
        else{
          this._showAlert('error','Incorrect Amount','Please Provide Amount Correctly');
        }
      },
      
      _makeCompleted: async function(){
        var data = {order_no:this.orderno};
        const response = await axios.post(api_uri+'api/v1/order/makecompleted', data,p_headers);
        this._showAlert(response.data.status,response.data.title,response.data.message);
        this.openOrder(this.orderno);
      },
      _makeCanceled: async function(){
        var type = 'success';
        var msg = 'Canceled';

        if(this.netpaid != 0){
          type = 'error';
          msg = 'You Can Not Cancel Order For Which Customer Already Paid';
        }
        if(type=='success'){
          var data = {order_no:this.orderno};
          const response = await axios.post(api_uri+'api/v1/order/makecanceled', data,p_headers);
          this._showAlert(response.data.status,response.data.title,response.data.message);
          this.openOrder(this.orderno);
          //this._showAlert(type,msg,'');
        }
        else this._showAlert(type,msg,'');
      },
      _makeConfirmed: async function(){
        const sync = await this.serversync();
        var smsAction = 'no';
        var data = {order_no:orderapp.orderno,sms:smsAction};
        const response = await axios.post(api_uri+'api/v1/order/makeconfirmed', data,p_headers);
        orderapp.openOrder(orderapp.orderno);
        orderapp._showAlert('success','Sell Confirmed',response.data);
      },
      _makePending: async function(){
        var data = {order_no:this.orderno};
        const response = await axios.post(api_uri+'api/v1/order/makepending', data,p_headers);
        this.openOrder(this.orderno);
        this._showAlert('success','Pending Mode','Sell Is Now In Pending Mode');
      },
      _statusChange: async function(newStatus){
        // if(this.draftMode ==true){
        //   this._showAlert('error','Draft Mode','Please Save First');
        // }
        // else{
          if(newStatus=='Pending') this._makePending();
          if(newStatus=='Confirmed') this._makeConfirmed();
          // if(newStatus=='Packaged') this.__makePackaged();
          // if(newStatus=='Shipped') this._makeShipment();
          // if(newStatus=='Delivered') this._makeDelivered();
          if(newStatus=='Completed') this._makeCompleted();
          if(newStatus=='Canceled') this._makeCanceled();
        // }

      },
      serversync: async function(newItem = ''){

        if(this._validation(true)){
          var newOne = '';
          if(newItem!=''){
            //console.log('New Item Found');
            newOne = newItem;
          }
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
            newItem: newOne,
            delivery_channel: this.selectedChannel.id,

          };
          axios.post(api_uri+'api/v1/order/itemupdate', data,p_headers)
            .then(function (response) {
              $('.ajaxLoading').hide();
              orderapp.openOrder(data.order_no);
            });
        }
      },
      receiveTransfer: async function(barcode,order='',open=true){
        $('.ajaxLoading').show();
        if(order=='') order = this.orderno;
        //const outlet_name = this._getNameFromOutletId(this.outlet);
        var data = {
          orderno:order,
          outlet_id:this.outlet,
          outlet_name: outlet_name,
          barcode:barcode,
        };

        axios.post(api_uri+'api/v1/order/receivetransfer', data,p_headers)
            .then(function (response) {
              $('.ajaxLoading').hide();
              orderapp._showAlert(response.data.status,response.data.message,response.data.message2);         
              if(open==true) orderapp.openOrder(data.orderno);
              else orderapp.getReceiveTransfer();
            });
      },
      confirmTransfer: async function(orderno,barcode_init,barcode_final,product){
        $('.ajaxLoading').show();
        var data = {
          outlet:this.outlet,
          orderno:orderno,
          barcode_init:barcode_init,
          barcode_final:barcode_final,
          code: product,
        };
        axios.post(api_uri+'api/v1/order/confirmtransfer', data,p_headers)
            .then(function (response) {
              $('.ajaxLoading').hide();
              orderapp._showAlert(response.data.status,response.data.message,response.data.message2);         
              orderapp.getPendingTransfer();
            });
      },
      checkcustomer: async function(){
        if(this.customer.mobile.length==11){
          $('.ajaxLoading').show();
          const response = await axios.get(api_uri+'api/v1/cdn/common/customer/idfrommobile/'+this.customer.mobile,p_headers);
          this.customer = response.data;
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
      getOutletList: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/alloutlets',p_headers); 
          this.outletList = response.data;
            // [
            //   {"id":1,"name":"Head Office"},
            //   {"id": 2,"name": "Metro"},
            //   {"id": 3,"name": "Savar"},
            //   {"id": 4,"name": "Orchid"},
            //   {"id": 5, "name": "E-Commerce"},
            //   {"id": 6, "name": "F-Commerce"},
            //   {"id": 7, "name": "Daraz"},
            //   {"id": 8, "name": "North"},
            //   { "id": 9, "name": "PriyoShop"},
            //   { "id": 10, "name": "Qcoom"},
            //   { "id": 11, "name": "Evaly"}
            // ]
          this.paymentmethod = this.paymentmethodList[0];
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getProducts: async function() {
        //length
        if(this.productList.length==0){
          $('.ajaxLoading').show();
          try {
            const response = await axios.get(api_uri+'api/v1/cdn/common/products',p_headers);
            this.productList = response.data;
            //autocomplete(document.getElementById("autocomplete"), this.productList );
            $('.ajaxLoading').hide();
          } catch (error) {
            console.error(error);
            $('.ajaxLoading').hide();
          }
        }

      },
      getDeliveryChannels: async function() {
        //length
        if(this.productList.length==0){
          $('.ajaxLoading').show();
          try {
            const response = await axios.get(api_uri+'api/v1/delivery/getchannel',p_headers);
            this.deliveryChannels = response.data.channels;
            this.delivery_boys = response.data.boys;
            this.allUsers = response.data.allUsers;
            //autocomplete(document.getElementById("autocomplete"), this.productList );
            $('.ajaxLoading').hide();
          } catch (error) {
            console.error(error);
            $('.ajaxLoading').hide();
          }
        }

      },
      _showAlert:function(type,title,text){
        Swal.fire({type: type,title: title, text: text});
      },
      _showModal:function(){
        $('#shipment-box').show();
      },
      _hideModal:function(e){
        $(e).hide();
      },
      // getbarcode: async function(){
      //   this.outletwisebarcodes = [];
      //   this.current = {};
      //   $('.ajaxLoading').show();
      //   const response = await axios.get(api_uri+'api/v1/cdn/common/products/outletwisebarcode/'+this.code,p_headers);
      //   this.outletwisebarcodes = response.data;
      //   $('.ajaxLoading').hide();
      //   if(this.outletwisebarcodes.length == 0) Swal.fire({type: 'error',title: 'No Barcode Found', text: 'Either you have entered wrong product code or your stock is empty.'});
      //   else this._findCurrent();
      // },
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
      isCustomerReady: function(){
        var ret = false;
        if(this.customer.id==0) {
          this._showAlert('error','Customer ID Missing','Please provide 11 digit mobile number and collect ID');
        }
        else{
          ret = true;
        }
        return ret;
      },
      addItem: async function(){
        if(!this.isCustomerReady()) return;
        // It Means all done but order number is not found
        if(this.orderno==''){
          var order = await this.collectOrderNo();
          this.orderno = order;
          var item = {
            orderno: this.orderno,
            code: this.selectedProduct.code,
            name: this.selectedProduct.code,
            price: parseFloat(this.selectedProduct.mrp),
            pid: this.selectedProduct.id,
            customer:this.customer.id,
            returned:false,
            metric: this.selectedProduct.metric.name,
            discounted_price: this._calculateDiscountedPrice(parseFloat(this.selectedProduct.mrp)),
            qtt: 1,
            disc:0,
            brand: this.selectedProduct.brand.name,
            category: this.selectedProduct.category.name,
            size: this.selectedProduct.size.name
          }
          this.itemList.push(item);
          this.serversync();
        }
        else{
          var item = {
            orderno: this.orderno,
            code: this.selectedProduct.code,
            name: this.selectedProduct.code,
            price: parseFloat(this.selectedProduct.mrp),
            pid: this.selectedProduct.id,
            customer:this.customer.id,
            returned:false,
            metric: this.selectedProduct.metric.name,
            discounted_price: this._calculateDiscountedPrice(parseFloat(this.selectedProduct.mrp)),
            qtt:1,
            disc:0,
            brand: this.selectedProduct.brand.name,
            category: this.selectedProduct.category.name,
            size: this.selectedProduct.size.name
          }
          this.itemList.push(item);
          this.serversync();
        }
        
      },
      deleteItem : function (item){
        var idx = orderapp.itemList.indexOf(item);
        if (idx > -1) {
          orderapp.itemList.splice(idx, 1);
        }
        orderapp.serversync();
          // axios.post(api_uri+'api/v1/order/deleteitem', item,p_headers)
          //   .then(function (response) {
          //     var idx = orderapp.itemList.indexOf(item);
          //     if (idx > -1) {
          //       orderapp.itemList.splice(idx, 1);
          //     }
          //     orderapp.serversync();
          //   });
      },

      updateOrder: async function(){
        
      },

      collectOrderNo: async function(){
        const response = await axios.get(api_uri+'api/v1/order/getorderno/'+this.outlet+'/'+this.customer.id,p_headers);
        //console.log(response.data);
        this.orderid = response.data.id;
        //this.orderno = response.data.order_no;  It is added in addItem Function
        $('#displayqr').qrcode(response.data.order_no); // Generate QR Code
        return response.data.order_no;
      },
      _getNameFromMethodId: function(id){
        iid = parseInt(id);
        return this.paymentmethodList.find(method => method.id === iid).name;
      },
      _getNameFromUserId: function(id){
        iid = parseInt(id);
        return this.allUsers.find(method => method.id === iid).username;
      },
      makePayment:async function(type){
        if(this.orderno!='' && this.payment_amount!=null && this.payment_amount>0){
          var payment = {
            paymentmethod: this.paymentmethod,
            amount: type*this.payment_amount,
            outlet:this.outlet,
            order_no:this.orderno,
            type: type,
          };
          var typedisplay = 'Deposited';
          if(type==-1) typedisplay = 'Withdrawl';
          $('.ajaxLoading').show();
          const response = await axios.post(api_uri+'api/v1/payment/deposit', payment,p_headers);
          $('.ajaxLoading').hide();
          this._showAlert('success','Payment '+typedisplay+' Successful','Operation Successful');
          var payment_item = {
            date: response.data.date,
            payment_no: response.data.payment_no,
            payment_method: response.data.payment_method,
            order_no:response.data.order_no,
            amount:response.data.amount,
            type: type,
            id: response.data.id,
          }
          this.netpaid = this.netpaid + response.data.amount;
          this.payments.push(payment_item);
          this.payment_amount = 0;
        }
        else{
          this._showAlert('error','Incorrect Payment','Please confirm item first.');
        }
      },
      openPdf:function(type){
        if(this._validation()){
          if(type=='sell_3in'){
            window.open(api_uri+'api/v1/order/print/threeinch/'+this.orderno, "_blank");    
          }
          else if(type=='order_a4'){
            window.open(api_uri+'api/v1/order/print/a4/'+this.orderno, "_blank");    
          }
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
  computed: {
    filteredPendings() {
      return this.pendingTransfers.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const product = row.product_code.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return ( order_no.includes(searchTerm) || product.includes(searchTerm) );
      });
    },
    filteredReceives(){
      return this.receiveTransferList.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const product = row.product_code.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return ( order_no.includes(searchTerm) || product.includes(searchTerm) );
      });
    },
    filteredPayments(){
      this.current_ttl_paid = 0;
      return this.allPayments.filter(row =>{
        const order_no = row.order_no.toString().toLowerCase();
        //const outlet = this._getNameFromOutletId(row.outlet).toString().toLowerCase();
        const method = this._getNameFromMethodId(row.payment_method).toString().toLowerCase();
        const user = this._getNameFromUserId(row.entry_by).toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        if( order_no.includes(searchTerm) || outlet.includes(searchTerm) || method.includes(searchTerm) || user.includes(searchTerm) ) this.current_ttl_paid += row.amount;
        return ( order_no.includes(searchTerm) || outlet.includes(searchTerm) || method.includes(searchTerm) || user.includes(searchTerm));
      });
    },
    filteredoutletOrders(){
      this.current_ttl_paid = 0;
      this.current_ttl_sub=0;
      this.current_ttl_dis=0;
      this.current_ttl_delivery=0;
      this.current_ttl_vat=0;
      this.current_ttl_net=0;
      this.current_ttl_due=0;
      return this.outletOrders.filter(row=>{
        const order_no = row.order_no.toString().toLowerCase();
        //const outlet = this._getNameFromOutletId(row.outlet).toString().toLowerCase();
        const status = row.status.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        
        const searchTerm = this.filter.toLowerCase();
        if( (order_no.includes(searchTerm) || outlet.includes(searchTerm) || status.includes(searchTerm) || mobile.includes(searchTerm)) && status!='canceled' ){
          this.current_ttl_paid += row.paid;
          this.current_ttl_sub += row.sub_total;
          this.current_ttl_dis += row.discount;
          this.current_ttl_delivery += row.delivery_fee;
          this.current_ttl_vat += row.vat;
          this.current_ttl_net += row.net_payable;
          this.current_ttl_due += (row.net_payable - row.paid);
        }
        return ( order_no.includes(searchTerm) || outlet.includes(searchTerm) || status.includes(searchTerm) || mobile.includes(searchTerm) );
      });
    }
  },
  created: function(){
      this.getProducts();
      this.createDate();
      this.getPaymentmethod();
      //this.getOutletList();
      //this.getDeliveryChannels();
      this.outlet = usr_outlet;

      if(landed_order_no!=''){
        this.mode='New';
        this.openOrder(landed_order_no);
      }

      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          //this.getbarcode();
        }
      });
  }
  
  
});

