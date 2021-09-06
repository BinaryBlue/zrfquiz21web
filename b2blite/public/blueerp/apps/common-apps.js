Vue.component('v-select', VueSelect.VueSelect);
var assignUserApp =  new Vue({
    el: '#assignusertooutlet',
    data:{
      outlet:null,
      outletList: [],
      user:null,
      userList:[],
    },
    methods: {
      getInitData: async function(){
        $('.ajaxLoading').show();
        const response = await axios.get(api_uri+'api/v1/cdn/common/outlets/',p_headers);
        this.outletList = response.data;

        const response2 = await axios.get(api_uri+'api/v1/user/lists/',p_headers);
        this.userList = response2.data;

        $('.ajaxLoading').hide();
      },

      saveaccess: async function(){
        if(this.user==null || this.outlet==null)  Swal.fire({type: 'warning',title: 'Oops...', text: 'Please select employee and outlet'});
        else{
          var p_data = 
          {
            uid : this.user.id,
            oid: this.outlet.id,
          };
          axios.post(api_uri+'api/v1/user/assignuser', p_data,p_headers)
          .then(function (response) {
            //this.user = null;
            location.reload();
          });
        }
      },
    },
    created: function(){
      this.getInitData();
    },
  });