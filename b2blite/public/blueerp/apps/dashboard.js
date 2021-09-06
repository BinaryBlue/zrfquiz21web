var DashboardVue =  new Vue({
    el: '#dashboardVue',
    data:{
      products: 0,
      barcodes: 0,
      todaysell: 0,
      todays_sell_amount:0,
      todays_sell_qtt:0,
      todayreturn: 0,
      todays_return_amount:0,
      todays_return_qtt:0,
      expenses: 0,
      dues: 0,
      pendingorders: 0,
      pending_products:0,
      cashbalance: 0,
      citybalance: 0,
      dbblbalance: 0,
      sell_summery:[],
      return_summery:[],
      b_usr_gp: b_usr_gp,
      todays_profit:0,
      outlet_wise_sell:[],
    },
    methods: {
      playSound: function (sound) {
        if(sound) {
          var audio = new Audio(sound);
          audio.play();
        }
      },
      getInitData: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/cdn/common/dashboard/',p_headers);
        this.pending_products = response.data.pending_products;
        this.barcodes = response.data.current_stock;
        this.products = response.data.available_products;
        this.todaysell = response.data.todays_sell;
        if(response.data.todays_sell_amount!=null) this.todays_sell_amount = response.data.todays_sell_amount;
        if(response.data.todays_return_amount!=null) this.todays_return_amount = response.data.todays_return_amount;
        if(response.data.todays_sell_qtt!=null) this.todays_sell_qtt = response.data.todays_sell_qtt;
        if(response.data.todays_return_qtt!=null) this.todays_return_qtt = response.data.todays_return_qtt;
        
        this.todayreturn = response.data.todays_return;
        this.todays_profit = response.data.todays_profit;

        this.sell_summery = response.data.sell_summery;
        this.return_summery = response.data.return_summery;
        this.outlet_wise_sell = response.data.outlet_wise_sell;
        
        if(response.data.balance.length>0){
          this.cashbalance = response.data.balance[0].amount;
          this.citybalance = response.data.balance[1].amount;
          this.dbblbalance = response.data.balance[2].amount;
        }
        //this.outlet = response.data[0];
        $('.ajaxLoading').hide();
      },
    },
    created: function(){
      this.getInitData();
    },
  });