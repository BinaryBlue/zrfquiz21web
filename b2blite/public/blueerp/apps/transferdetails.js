var stockentrystatementapp = new Vue({
    el: '#stocktransferDetails',
    data: {
      transfercode:null,
      status:null,
      statusText:'',
      ini_by:null,
      ini_date:null,
      conf_by:null,
      conf_date:null,
      barcode:[],
      total_qtt: 0,
      saveable:false,
      printable:false,
      stt_id:null,
    },
    watch:{
      status:function(st){
        if(st=='0') this.statusText = 'Pending for Receive';
        else this.statusText = 'Product Received';
      },

    },
    methods:{
        confirmReceive: function(){
          var p_data = 
          {
            transfercode: this.transfercode,
          };
          $('.ajaxLoading').show();
          axios.post(api_uri+'api/v1/management/stock/receive', p_data,p_headers)
          .then(function (response) {
            console.log(response);
            $('.ajaxLoading').hide();
            notyMessage(response.message); 
            stockentrystatementapp.saveable = false;
            stockentrystatementapp.printable = true;
            stockentrystatementapp.statementDetails(stockentrystatementapp.stt_id);
            //window.location.href = base_url+'/productreceive/'+response.data.data.id;
            //console.log(response);
          })
          .catch(function (error) {
            notyMessageError(error);
            //console.log(error);
            $('.ajaxLoading').hide();
          });
        },
        statementDetails: async function(id) {
            try {
              const response = await axios.get(api_uri+'api/v1/statement/stock_transfer/details/'+id,p_headers);
              //console.log(response);
              var codes = response.data.statement.items;
              //codes.sort((a, b) => (a.code > b.code) ? 1 : -1);

              this.transfercode = response.data.statement.transfer_code;
              this.fy = response.data.statement.fy;
              this.total_qtt = response.data.statement.items.length;
              this.status = response.data.statement.status;
              this.barcode = codes;
              this.ini_by = response.data.statement.initiated_name;
              this.ini_date = response.data.statement.initiated_date;
              this.conf_by = response.data.statement.confirmed_name;
              this.conf_date = response.data.statement.confirmed_date;
              if(this.conf_by=='') this.saveable = true;
              else this.printable = true;
            } catch (error) {
              console.error(error);
            }
          },
    },
    created: function(){
        var url = window.location.pathname.split("/");
        this.stt_id = url[url.length-1];
        this.statementDetails(this.stt_id);
        
    }
});

