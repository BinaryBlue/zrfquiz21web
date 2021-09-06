// Vue.use(Vuetable);
var ecommerceapp = new Vue({
  el: '#ecommerce',
  // components:{
  //   'vuetable-pagination': Vuetable.VuetablePagination
  //  },
  data: {
      product: null,
      productList: [],
      productsPaginated:null,
      barcode:[],
      singlebarcode:null,
      variationList:[],
      total_qtt: 0,
      saveable:false,
      photo:'#',
      photo2:'#',
      photo3:'#',
      fields: [
        {
          name: 'code',
          sortField: 'code'
        },
        {
          name: 'name',
          sortField: 'name'
        },
        {
          name: 'selling_price',
          sortField: 'selling_price'
        },
        {
          name: 'barcode',
          sortField: 'barcode',
        },
        {
          name: 'current_stock',
          sortField: 'current_stock',
        },
        {
          name: 'e_id',
          sortField: 'e_id',
        },
        {
          name: 'last_synched',
          sortField: 'last_synched',
        }
      ],
      
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
          ecommerceapp.saveable = false;
          //window.location.href = base_url+'/productreceive/'+response.data.data.id;
          //console.log(response);
        })
        .catch(function (error) {
          notyMessageError(error);
          //console.log(error);
          $('.ajaxLoading').hide();
        });

      },
      softsync: function(p,c){
        $('.ajaxLoading').show();
        var p_data = { pid : p, code: c };
        axios.post(api_uri+'api/v1/ecommerce/product/delete_var', p_data,p_headers)
        .then(function (response) {
            if(response.data.status=='success')
            {
                axios.post(api_uri+'api/v1/ecommerce/product/softsync', p_data,p_headers)
                .then(function (response) {
                    $('.ajaxLoading').hide();
                    notyMessage(response.message);
                    location.reload();
                });
            
            }
            else{
                $('.ajaxLoading').hide();
                notyMessage(response.message);
            }
            
            
          
          //if(response.)
           
          //location.reload();
          //ecommerceapp.saveable = false;
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
      getProducts: async function() {
        $('.ajaxLoading').show();
        try {
          const response = await axios.get(api_uri+'api/v1/cdn/common/ecomproductspaginated',p_headers);
          // this.productsPaginated = response.data;
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
      getImgUrl(file) {
        //var images = require.context('../assets/', false, /\.png$/)
        return 'http://blueerp.diganta.com.bd/window/public/products/images/'+file;
      }
      // onPaginationData (paginationData) {
      //   this.$refs.pagination.setPaginationData(paginationData)
      // },
      // onChangePage (page) {
      //   this.$refs.vuetable.changePage(page)
      // },
      // editRow(rowData){
      //   alert("You clicked edit on"+ JSON.stringify(rowData))
      // },
      // deleteRow(rowData){
      //   alert("You clicked delete on"+ JSON.stringify(rowData))
      // }
  },
  created: function(){
      this.getProducts();
  }
});

