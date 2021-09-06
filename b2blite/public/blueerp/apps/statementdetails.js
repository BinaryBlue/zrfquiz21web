var stockentrystatementapp = new Vue({
    el: '#stockentrystatement',
    data: {
        statementNo: null,
        fy: null,
        date_time: null,
        total: null,
        itemList:[],
        total_sell: 0.00,
        total_qtt: 0,
    },
    methods:{
        getTotalSelling: function(index){
          console.log(this.itemList);
          return this.itemList[index].price * this.itemList[index].qtt;
        },
        getTotalPurchase: function(index){
          console.log(this.itemList);
          return this.itemList[index].purchase * this.itemList[index].qtt;
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
        statementDetails: async function(id) {
            try {
              const response = await axios.get(api_uri+'api/v1/statement/stock_entry/details/'+id,p_headers);
              console.log(response);
              this.statementNo = response.data.statement.statement;
              this.fy = response.data.statement.fy;
              this.total = response.data.statement.total;
              this.date_time = response.data.statement.entry_at;
              this.itemList = response.data.statement.items;
              this._calculateTotal();
            } catch (error) {
              console.error(error);
            }
          },
        rollback: async function(id){
          const response = await axios.get(api_uri+'api/v1/management/rollback/stockentry/'+id,p_headers);
          console.log(response);
          window.location.href = base_url+'/stockentry/';
        }
    },
    created: function(){
        var url = window.location.pathname.split("/");
        this.statementDetails(url[url.length-1]);
    }
});

