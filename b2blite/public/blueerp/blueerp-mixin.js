var stt_details_m = {
    data: function(){
        return {
            statement: {},
            statementNo: 2
        }
    },
    methods: {
        statementDetails: async function() {
            const response = await axios.get(api_uri+'api/v1/statement/stock_entry/details/'+this.id,p_headers);
            this.statement = response.statement;
        },
     }
 }