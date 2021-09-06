Vue.component('v-select', VueSelect.VueSelect);
var ecomproductapp = new Vue({
  el: '#ecommercesend',
  data: {
      product: null,
      productList: [],
      barcode:[],
      singlebarcode:null,
      variationList:[],
      total_qtt: 0,
      saveable:false,
      photo:'#',
      photo2:'#',
      photo3:'#',
      
  },
  watch:{
    product: function(pr){
      this.barcode = [];
      this.getVariations(pr.id);
      this.photo = '../../public/products/images/'+this.product.photo;
      this.photo2 = '../../public/products/images/'+this.product.photo1;
      this.photo3 = '../../public/products/images/'+this.product.photo2;
    },
    barcode: function(barcode){
      if(barcode.length> 0) this.saveable = true;
      else this.saveable = false;
      this.total_qtt = barcode.length;

    },
  },
  methods:{
      confirmTransfer: function(){
        var p_data = 
        {
          itemList : this.barcode,
          basedproduct: this.product
        };
        $('.ajaxLoading').show();
        axios.post(api_uri+'api/v1/ecommerce/product/create', p_data,p_headers)
        .then(function (response) {
          console.log(response);
          $('.ajaxLoading').hide();
          notyMessage(response.message); 
          ecomproductapp.saveable = false;
          //window.location.href = base_url+'/productreceive/'+response.data.data.id;
          //console.log(response);
        })
        .catch(function (error) {
          notyMessageError(error);
          //console.log(error);
          $('.ajaxLoading').hide();
        });

      },
      getImgUrl(pic) {
        return require('../../public/products/images/'+pic)
      },
      // _findAvailablilityofBarcode: function(arr,barcode){
      //   var result = arr.filter(obj => obj.barcode == barcode);
      //   return result.length;
      // },
      // getbarcodeDetails: function(){
      //   if(this._findAvailablilityofBarcode(this.barcode,this.singlebarcode)>0){
      //     Swal.fire({type: 'warning',title: 'Oops...', text: 'This Barcode Is Already Selected : '+stocktransferapp.singlebarcode});
      //     return;
      //   }
      //   $('.ajaxLoading').show();
      //   axios.get(api_uri+'api/v1/cdn/common/barcode/'+this.singlebarcode,p_headers)
      //   .then(function (response) {
      //     $('.ajaxLoading').hide();
      //     if(response.data.length==0){
      //       Swal.fire({type: 'warning',title: 'Oops...', text: 'No available stock found for the barcode : '+stocktransferapp.singlebarcode});
      //     }
      //     else{
      //       var r = response.data[0];
      //       stocktransferapp.barcode.push(r);
      //     }
      //   })
      //   .catch(function (error) {
      //     notyMessageError(error);
      //     //console.log(error);
      //     $('.ajaxLoading').hide();
      //   });
      // },
      getProducts: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/ecomproducts',p_headers);
          this.productList = response.data;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
      getVariations: async function(pid) {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/variables/'+pid,p_headers);
          this.variationList = response.data;
          $('.ajaxLoading').hide();
        } catch (error) {
          console.error(error);
          $('.ajaxLoading').hide();
        }
      },
  },
  created: function(){
      this.getProducts();
  }
});

