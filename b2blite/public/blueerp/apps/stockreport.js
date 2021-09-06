Vue.component('v-select', VueSelect.VueSelect);
var stockreportapp = new Vue({
  el: '#stockreport',
  data: {
      code: '',
      product: null,
      productList: [],
      current_stock:0,
      barcode:[],
      singlebarcode:null,
      barcodeList:[],
      itemList:[],
      total_qtt: 0,
      live_stock:[],
      live_stock_ttl:0,
      live_stock_total: {'ofc':0,'met':0,'sav':0,'orc':0,'ecom':0,'fb':0,'dar':0,'ttl':0},
      ttotal: {'ofc':0,'met':0,'sav':0,'orc':0,'ecom':0,'fb':0,'dar':0,'ttl':0},
      reporttype:3
  },
  watch:{
    product: function(pr){
      if(this.outlet==null) Swal.fire({type: 'warning',title: 'Oops...', text: 'Please Select Outlet First'});
      this.getBarcodes(pr.id);
    },
  },
  methods:{
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
      getStockreport: async function(){
        $('.ajaxLoading').show();
        var data = {code: this.code}
        axios.post(api_uri+'api/v1/cdn/report/currentstock', data,p_headers)
        .then(function (response) {
          var total = {'ofc':0,'met':0,'sav':0,'orc':0,'ecom':0,'fb':0,'dar':0,'north':0,'ttl':0};
          var total_live = {'ofc':0,'met':0,'sav':0,'orc':0,'ecom':0,'fb':0,'dar':0,'north':0,'ttl':0};
          
          response.data.summery.forEach(element => {
            // POS Stock (Summery/Old one)
            total.ofc+=element.stock_report.office_stock;
            total.met+=element.stock_report.metro_stock;
            total.sav+=element.stock_report.savar_stock;
            total.orc+=element.stock_report.orchid_stock;
            total.ecom+=element.stock_report.ecom_stock;
            total.fb+=element.stock_report.fcom_stock;
            total.dar+=element.stock_report.daraz_stock;
            total.north+=element.stock_report.north_stock;

            // Order Stock (Live One)
            total_live.ofc+=element.live_stock_report.office_stock;
            total_live.met+=element.live_stock_report.metro_stock;
            total_live.sav+=element.live_stock_report.savar_stock;
            total_live.orc+=element.live_stock_report.orchid_stock;
            total_live.ecom+=element.live_stock_report.ecom_stock;
            total_live.fb+=element.live_stock_report.fcom_stock;
            total_live.dar+=element.live_stock_report.daraz_stock;
            total_live.north+=element.live_stock_report.north_stock;
          });
          total.ttl+= parseInt(total.ofc+total.met+total.sav+total.orc+total.ecom+total.fb+total.dar+total.north);
          total_live.ttl+= parseInt(total_live.ofc+total_live.met+total_live.sav+total_live.orc+total_live.ecom+total_live.fb+total_live.dar+total_live.north);
          stockreportapp.ttotal = total;
          stockreportapp.live_stock_total = total_live;


          stockreportapp.itemList = response.data.summery;
          stockreportapp.barcodeList = response.data.barcodes;
          //console.log(response.data);
          $('.ajaxLoading').hide();
        });
      },
  },
  created: function(){
      this.getProducts();
      window.addEventListener('keydown', (e) => {
        if (e.key == 'Enter') {
          event.preventDefault();
          this.getStockreport();
        }
      });
  }
  
});

