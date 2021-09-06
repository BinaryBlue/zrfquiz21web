var deliveryboy = new Vue({
  el: '#deliveryboy',
  data: {
      packets:[],
      shipments:[],
      activePacket:{id:0,chanel_name:'',order_no:'',packet_no:''},
      activeAction:'',
      delivery_memo_file:'',
      remarks:'',
      activePage:'packets',
      statusClasses:{
        Warehoused:{bgClass:'bg-orange',next:'Picked',buttons:[{status:'Picked',bgClass:'bg-cyan'}]},
        Picked:{bgClass:'bg-cyan',next:'Warehoused',buttons:[{status:'Warehoused',bgClass:'bg-orange'},{status:'Shipped',bgClass:'bg-purple'},{status:'Delivered',bgClass:'bg-green'},{status:'Exchanged',bgClass:'bg-red'},{status:'Canceled',bgClass:'bg-dark'}]},
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
      showPage: function(m){
        this.activePage = m;
      },
      getPendingPackets: async function(){
        const response = await axios.get(api_uri+'api/v1/delivery/getmypackets',p_headers);
        this.packets = response.data;
      },
      getPendingShipments: async function(){
        const response = await axios.get(api_uri+'api/v1/delivery/getmyshipments',p_headers);
        this.shipments = response.data;
        
      },
      ___shipmentFileHandler: function(){
        this.delivery_memo_file = this.$refs.file.files[0];
      },
      submitAction: async function(){
          let data = new FormData();
          if(this.delivery_memo_file!='') data.append('file', this.delivery_memo_file);
          data.append('order_no',this.activePacket.order_no);
          data.append('channel_id',this.activePacket.channel_id);
          data.append('remarks',this.remarks);
          data.append('packet_no',this.activePacket.packet_no);
          data.append('action',this.activeAction);
          const response = await axios.post(api_uri+'api/v1/packaging/action', data,p_headers);
          this._showAlert(response.data.status,response.data.title,response.data.msg);
          $('#action-box').hide();
      },
      makeAction: async function(pkt,stts,order,item=null){
        if(stts=='Shipped'){
          this.activeAction = stts;
          this.activePacket = item;
          $('#action-box').show();
        }
        else{
          $('.ajaxLoading').show();
          var data = {order_no:order,packet_no:pkt,action:stts};
          const response = await axios.post(api_uri+'api/v1/packaging/action', data,p_headers);
          $('.ajaxLoading').hide();
          this._showAlert(response.data.status,response.data.title,response.data.msg);
          this.getPendingPackets();
        }
      },
      shipmentRequestedDelivered: async function(ship,order){

        $('.ajaxLoading').show();
        var data = {order_no:order,tracking_no:ship};
        const response = await axios.post(api_uri+'api/v1/delivery/shipmentrequested', data,p_headers);
        $('.ajaxLoading').hide();
        this._showAlert(response.data.status,response.data.message,response.data.message);
        this.getPendingShipments();
      },
      __insertRemarks: async function(){
        var data = {order_no:this.activePacket.order_no,packet_no:this.activePacket.packet_no,remarks:this.remarks};
        const response = await axios.post(api_uri+'api/v1/packaging/addremarks', data,p_headers);
        this.remarks = '';
        this._showAlert(response.data.status,response.data.title,response.data.message);
        this._hideModal('#remarks-box');
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
      openPdf:function(order){
        window.open(api_uri+'api/v1/order/print/a4/'+order, "_blank");    
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
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        // return (
        //   packet_no.includes(searchTerm) || employees.includes(searchTerm)
        // );
        return (
          order_no.includes(searchTerm) || packet_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    },
    filteredShipments() {
      return this.shipments.filter(row => {
        const tracking_no = row.tracking_no.toString().toLowerCase();
        const packet_no = row.packet_no.toString().toLowerCase();
        const order_no = row.order_no.toString().toLowerCase();
        const mobile = row.mobile.toString().toLowerCase();
        const searchTerm = this.filter.toLowerCase();
        // return (
        //   packet_no.includes(searchTerm) || employees.includes(searchTerm)
        // );
        return (
          order_no.includes(searchTerm) || tracking_no.includes(searchTerm) || packet_no.includes(searchTerm) || mobile.includes(searchTerm)
        );
      });
    }
  },
  created: function(){
      this.getPendingPackets();
      this.getPendingShipments();
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          //this.getbarcode();
        }
      });
  }
  
});

