Vue.component('v-select', VueSelect.VueSelect);
var deliverymanager = new Vue({
  el: '#deliverymanager',
  data: {
      
      pendings:[],
      confirms:[],
      packages:[],
      warehouses:[],
      picks:[],
      packets:[],
      shipments:[],
      delivers:[],
      exchanges:[],
      cancels:[],
      dues:[],
      vips:[],
      total_due:0,

      delivery_boys:[],
      delivery_boy:{id:0,username:'Not Selected'},
      

      activePacket:{id:0,chanel_name:'',order_no:'',packet_no:''},
      tempPacket:null,
      delivery_memo_file:'',
      remarks:'',
      activePage:'Vips',
      activeImage:'',
      statusClasses:{
        Warehoused:{bgClass:'bg-orange',next:'Picked',buttons:[{status:'Picked',bgClass:'bg-cyan'}]},
        Picked:{bgClass:'bg-cyan',next:'Warehoused',buttons:[{status:'Warehoused',bgClass:'bg-orange'}]},
        Shipped:{bgClass:'bg-purple'},
        Delivered:{bgClass:'bg-green'},
        Returned:{bgClass:'bg-red'},
        Completed:{bgClass:'bg-green'},
        Initiated:{bgClass:'bg-purple',buttons:[{status:'Delivered',bgClass:'bg-green'}]},
      },
      filter:'',

      
  },
  watch:{
    code:function(code){
      this.code = code.trim().toUpperCase();
    },

  },
  methods:{
      redraw: async function(){
        this.getPendingPackets();
        this.getAllOrders();
        this.getPendingShipments();
      },
      showPage: function(m){
        this.filter = '';
        this.activePage = m;
      },
      getDeliveryChannels: async function() {
        //length
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/delivery/getchannel',p_headers);
          //this.deliveryChannels = response.data.channels;
          this.delivery_boys = response.data.boys;
          //autocomplete(document.getElementById("autocomplete"), this.productList );
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }

      },
      setDeliveryBoy: async function(){
        if(this.delivery_boy == null || this.delivery_boy.id==0 ){
          this._showAlert('error','Delivery Boy Missing ','Please Select Delivery Boy');
        }
        else{
          var data = {id:this.tempPacket.id,order_no:this.tempPacket.order_no,boy_id:this.delivery_boy.id, boy_name: this.delivery_boy.username};
          const response = await axios.post(api_uri+'api/v1/packaging/assignboy', data,p_headers);
          if(response.data.status=='success'){
            $('#package-box').hide();
            var msg = response.data.message;
            this._showAlert('success','Boy Assigned',msg);
            this.getPendingPackets();

          }
          else this._showAlert('error','Not Possible',response.data.message);
        }
      },
      getPendingPackets: async function(){
        const response = await axios.get(api_uri+'api/v1/delivery/getmypackets',p_headers);
        this.warehouses = [];
        this.picks = [];
        response.data.forEach(em => {
          if(em.status == 'Warehoused') this.warehouses.push(em);
          else if(em.status == 'Picked') this.picks.push(em);
        });
      },
      getPendingShipments: async function(){
        this.shipments = [];
        const response = await axios.get(api_uri+'api/v1/delivery/getmyshipments',p_headers);
        this.shipments = response.data;
      },
      getAllOrders: async function(){
        // Get Orders From Last Month
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/order/incompleteOrders',p_headers);
        this.pendings = [];
        this.confirms = [];
        this.packages = [];
        this.dues = [];
        this.delivers = [];
        this.exchanges = [];
        this.cancels = [];
        this.vips = [];
        this.total_due = 0;
        response.data.forEach(em => {
          //VIP
          if(em.status!='Delivered' && em.status != 'Exchanged' && em.status!='Canceled' ){
            this.vips.push(em);
          }
          //DUE
          if(em.net_payable > em.paid){
            var du = em.net_payable - em.paid;
            this.dues.push(em);
            this.total_due += du;
          }
          //Others
          if(em.status == 'Pending') this.pendings.push(em);
          else if(em.status == 'Confirmed') this.confirms.push(em);
          else if(em.status == 'Packaged') this.packages.push(em);
          //else if(em.status == 'Shipped') this.shipments.push(em);
          else if(em.status == 'Delivered') this.delivers.push(em);
          else if(em.status == 'Exchanged') this.exchanges.push(em);
          else if(em.status == 'Canceled') this.cancels.push(em);
        });

        $('.ajaxLoading').hide();
      },
      ___shipmentFileHandler: function(){
        this.delivery_memo_file = this.$refs.file.files[0];
      },
      approveRequest: async function(order,pkt,stts,cid){
        if(stts=='Picked' || stts=='Warehoused' || stts=='Shipped'){
          $('.ajaxLoading').show();
          var data = {order_no:order,packet_no:pkt,approve:stts,channel_id:cid};
          const response = await axios.post(api_uri+'api/v1/delivery/approve', data,p_headers);
          this._showAlert(response.data.status,response.data.message,'');
          this.redraw();
          $('.ajaxLoading').hide();
        }
        else{
          this.openOrderNewTab(order);
        }
      },
      makeAction: async function(pkt,stts,order){
        if(stts=='Picked' || stts=='Warehoused'){
          $('.ajaxLoading').show();
          var data = {order_no:order,packet_no:pkt,action:stts};
          const response = await axios.post(api_uri+'api/v1/packaging/action', data,p_headers);
          $('.ajaxLoading').hide();
          this._showAlert('success',stts,response.data.message);
          this.redraw();
        }
        else{
          $('#shipment-box').show();
        }
      },
      __shipmentEntry: async function(){
          let data = new FormData();
          if(this.delivery_memo_file!='') data.append('file', this.delivery_memo_file);
          data.append('order_no',this.orderno);
          data.append('channel_id',this.selectedChannel.id);
          data.append('remarks',this.delivery_remarks);
          const response = await axios.post(api_uri+'api/v1/delivery/makeshipment', data,p_headers);

          this.delivery_memo_file = '';
          //this.openOrder(this.orderno);
          //console.log(response);
          if(response.data.status=='success'){
            $('#shipment-box').hide();
            var msg = response.data.message;
            this._showAlert('success','Shipment Initiated',msg);
            var dataO = {order_no:this.orderno};
            const responseO = await axios.post(api_uri+'api/v1/order/productshipped', dataO,p_headers);
            this.openOrder(this.orderno);
          }
          else this._showAlert('error','Not Possible',response.data.message);
        
      },
      _makeShipment: async function(index){
        this.activePacket = this.packets[index];
        $('#shipment-box').show();
      },
      _showAlert:function(type,title,text){
        Swal.fire({type: type,title: title, text: text});
      },
      _showModal:function(e){
        $(e).show();
      },
      _hideModal:function(e){
        $(e).hide();
      },
      openImage:async function(url){
        if(url==null){
          this._showAlert('error','Nothing Uploaded','Delivery Boy Didn\'t Upload Any File');
        }
        else{
          this._showModal('#shipment-image');
    
          let data = new FormData();
          data.append('path',url);
          $('.ajaxLoading').show();
          const response = await axios.post(api_uri+'api/v1/utility/getimage', data,p_headers);
          this.activeImage = api_uri+response.data.img.substring(1);
          $('.ajaxLoading').hide();

        }

      },
      openPdf:function(order){
        window.open(api_uri+'api/v1/order/print/a4/'+order, "_blank");    
      },
      openOrderNewTab:function(order){
        window.open(base_url+'/orderscontroller/order/'+order, "_blank");    
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
    filteredPackets() {
      return this.packets.filter(row => {
        const packet_no = row.packet_no.toString().toLowerCase();
        const order_no = row.order_no.toString().toLowerCase();
        const delivery_boy = row.delivery_boy_name.toString().toLowerCase();
        const delivery_channel = row.channel_name.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        // return (
        //   packet_no.includes(searchTerm) || employees.includes(searchTerm)
        // );
        return (
          packet_no.includes(searchTerm) || order_no.includes(searchTerm) || delivery_boy.includes(searchTerm) 
          || delivery_channel.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredWarehoused() {
      return this.warehouses.filter(row => {
        const packet_no = row.packet_no.toString().toLowerCase();
        const order_no = row.order_no.toString().toLowerCase();
        const delivery_boy = row.delivery_boy_name.toString().toLowerCase();
        const delivery_channel = row.channel_name.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        // return (
        //   packet_no.includes(searchTerm) || employees.includes(searchTerm)
        // );
        return (
          packet_no.includes(searchTerm) || order_no.includes(searchTerm) || delivery_boy.includes(searchTerm) 
          || delivery_channel.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredPicked() {
      return this.picks.filter(row => {
        const packet_no = row.packet_no.toString().toLowerCase();
        const order_no = row.order_no.toString().toLowerCase();
        const delivery_boy = row.delivery_boy_name.toString().toLowerCase();
        const delivery_channel = row.channel_name.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        // return (
        //   packet_no.includes(searchTerm) || employees.includes(searchTerm)
        // );
        return (
          packet_no.includes(searchTerm) || order_no.includes(searchTerm) || delivery_boy.includes(searchTerm) 
          || delivery_channel.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredShipped() {
      return this.shipments.filter(row => {
        console.log(row);
        const packet_no = row.packet_no.toString().toLowerCase();
        const order_no = row.order_no.toString().toLowerCase();
        const tracking_no = row.tracking_no.toString().toLowerCase();
        const delivery_boy = row.delivery_boy_name.toString().toLowerCase();
        const delivery_channel = row.channel_name.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        // return (
        //   packet_no.includes(searchTerm) || employees.includes(searchTerm)
        // );
        return (
          tracking_no.includes(searchTerm) || packet_no.includes(searchTerm) || order_no.includes(searchTerm) || delivery_boy.includes(searchTerm) 
          || delivery_channel.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredPendings() {
      return this.pendings.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return (
          order_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredConfirms() {
      return this.confirms.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return (
          order_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredPackages() {
      return this.packages.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return (
          order_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredDelivered() {
      return this.delivers.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return (
          order_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredExchanged() {
      return this.exchanges.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return (
          order_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredCanceled() {
      return this.cancels.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return (
          order_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredDues() {
      this.total_due = 0;
      return this.dues.filter(row => {
        const stts = row.status;
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        if(stts!='Pending' && stts!='Canceled' && (order_no.includes(searchTerm) || mobile.includes(searchTerm))) this.total_due = this.total_due + (row.net_payable - row.paid);
        return (
          stts!='Pending' && stts!='Canceled' && (order_no.includes(searchTerm) || mobile.includes(searchTerm))
        );
      });
    },

    filteredVips() {
      return this.vips.filter(row => {
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        return ( order_no.includes(searchTerm) || mobile.includes(searchTerm) );
      });
    },
    
  },
  created: function(){
      this.getDeliveryChannels();
      this.redraw();
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          //this.getbarcode();
        }
      });
  }
  
});

