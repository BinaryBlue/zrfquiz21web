Vue.component('v-select', VueSelect.VueSelect);
var oldstock = new Vue({
  el: '#oldstock',
  data: {
      outlet:null,
      outletList:[],
      customer:null,
      customerList:[],
      cmobile:'',
      cname:'',
      paymentmethod:null,
      paymentmethodList:[],
      ptype:'0',
      barcode:null,
      displayId: 0,
      itemList:[],
      total:0,
      profit:0,
      
  },
  watch:{
    total: function(total){
      if(total>=200){
        Swal.fire({type: 'warning',title: 'Warning', text: 'Software Strongly Recommends Maximum 200 Old Products Entry At A Time'});
      }

    },
  },
  methods:{
      getProductfromcode: async function(){
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/productfromcode/'+this.barcode,p_headers);
          if(response.data.length==0){
            Swal.fire({type: 'warning',title: 'Oops...', text: 'No Product Found With This Code'});
          }
          else 
          {
            oldstock.addItem(response.data[0]);
          }
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      confirmStockEntry: function(){
        if(this.itemList.length==0) return;
        if(this.profit==0){
            Swal.fire({type: 'warning',title: 'Oops...', text: 'Profit Can Not Be Zero'});
            return;
        }
        var p_data = 
        {
          outlet: this.outlet,
          itemList : this.itemList,
          profit: this.profit,
          total: this.total
        };
        $('.ajaxLoading').show();
        axios.post(api_uri+'api/v1/management/stock/oldentry', p_data,p_headers)
        .then(function (response) {
          console.log(response);
          $('.ajaxLoading').hide();
          notyMessage(response.data.message); 
          location.reload();
          // this.saveable = false;
          //window.location.href = base_url+'/stockentry/'+response.data.data.id;
          //console.log(response);
        })
        .catch(function (error) {
          notyMessageError(error);
          //console.log(error);
          $('.ajaxLoading').hide();
        });
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
      
      addItem : function (item) {
        this.displayId++;
        var item = {
          displayId: this.displayId,
          code: item.code,
          name: item.name,
          price:item.selling_price,
          qtt: 1,
          id: item.id
        }
        this.itemList.push(item);
        this._calculateTotal();
        this.barcode='';
        //this._updateSaveable();
        //this._calculateTotal();
      },
      deleteItem : function (item){
        var idx = this.itemList.indexOf(item);
        if (idx > -1) {
          this.itemList.splice(idx, 1);
        }
        //this._updateSaveable();
        //this._calculateTotal();
      },
      _calculateTotal: function(){
        var ttl = 0;
        $.each(this.itemList, function(key, value) {
          ttl += parseInt(value.qtt);
        });
        this.total = ttl;
      },
  },
  created: function(){
      this.getOutlets();
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          this.getProductfromcode();
        }
      });
  }
});

