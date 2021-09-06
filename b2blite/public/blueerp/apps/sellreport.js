Vue.component('v-select', VueSelect.VueSelect);
var sellreportapp = new Vue({
  el: '#sellreport',
  data: {
      code: '',
      product: null,
      productList: [],
      outlet: {id:''},
      fromd: '',
      tod: '',
      outletList:[],
      show: false,
  },
  watch:{
    product: function(pr){
      // if(this.outlet==null) Swal.fire({type: 'warning',title: 'Oops...', text: 'Please Select Outlet First'});
      // this.getBarcodes(pr.id);
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
      getStockreport: async function(){
        $('.ajaxLoading').show();
        var data = {code: this.code}
        axios.post(api_uri+'api/v1/cdn/report/currentstock', data,p_headers)
        .then(function (response) {
          var total = {'ofc':0,'met':0,'sav':0,'orc':0,'ecom':0,'fb':0,'dar':0,'ttl':0};
          response.data.summery.forEach(element => {
            total.ofc+=element.stock_report.office_stock;
            total.met+=element.stock_report.metro_stock;
            total.sav+=element.stock_report.savar_stock;
            total.orc+=element.stock_report.orchid_stock;
            total.ecom+=element.stock_report.ecom_stock;
            total.fb+=element.stock_report.fcom_stock;
            total.dar+=element.stock_report.daraz_stock;
            total.ttl+= (total.ofc+total.met+total.sav+total.orc+total.ecom+total.fb+total.dar);
          });
          stockreportapp.ttotal = total;
          stockreportapp.itemList = response.data.summery;
          stockreportapp.barcodeList = response.data.barcodes;
          console.log(response.data);
          $('.ajaxLoading').hide();
        });
      },
  },
  created: function(){
      this.getProducts();
      this.getOutlets();
  },
  mounted: function() {

    $('#fromdate').datepicker({format: 'yyyy-mm-dd',}).on(
      'changeDate', () => { this.fromd = $('#fromdate').val() }
    );
    $('#todate').datepicker({format: 'yyyy-mm-dd',}).on(
      'changeDate', () => { this.tod = $('#todate').val() }
    );
  }
});

