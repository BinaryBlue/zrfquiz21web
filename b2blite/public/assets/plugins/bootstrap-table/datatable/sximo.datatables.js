(function($) {

    $.fn.Sdtable = function( options ) {

    	var settings = $.extend({
            tableId     : 'datatable',
            table   	: 'table',
            action      : 'action'	
     
        }, options);

        return this.each( function() {

			var gridData 	= settings.tableId+'Table';
			var gridTbl 	= settings.tableId+'Grid';
			var gridView 	= settings.tableId+'View';


        	$( gridData +' tbody').on('click', 'tr.odd', function () { $(this).toggleClass('selected'); });
        	$( gridData +' tbody ').on('click', 'tr.even', function () { $(this).toggleClass('selected'); });

    		settings.table.columns().every( function () {
	       		var that = this;
	 
	        	$( 'input', this.footer() ).on( ' change', function () {
	            	if ( that.search() !== this.value ) {
	                that
	                    .search( this.value )
	                    .draw();
	            	}
	        	});
	        	$('select',  this.footer()).on('change',  function () {
	        	//	var optionSelected = $("option:selected", this);
	        		//var valueSelected = this.value;
	        	
	        	//	if ( that.search() !== this.value ) {
		        		that
		        		.search(this.value )
		        		.draw();
	        	//	}
			       // oTable.fnFilter( $(this).val(), i );
			     });
	    	}); 

			$('.Action_Row').click(function () {



				var code = $(this).attr('code');
				if( code =='reload') { settings.table.ajax.reload();}
				if( code =='add') {
			       		var url = settings.action + '/update';			       		
			       		var mode = $(this).attr('mode');
						var title = $(this).attr('data-original-title');
						if(mode =='native')
						{
							Sdt_ViewDetail( settings.tableId , url  );
						} else {
							SximoModal(  url  , title  );
						}					
				}

				var rows = settings.table.rows('.selected').data().length ;
		        if(rows)
		        {
		        	var id = settings.table.row('.selected').data().rowId;
					
					if(code =='view')
					{
						var url =  settings.action + '/show/'+id;
						var mode = $(this).attr('mode');
						var title = $(this).attr('data-original-title');
						if(mode =='native')
						{
							Sdt_ViewDetail( settings.tableId , url  );
						} else {
							SximoModal(  url  , title  );
						}
						
						

					}  else if(code =='copy') {

						var rows = settings.table.rows('.selected').data();
						var ss = [];
			        	for(var i=0; i<rows.length; i++){
			        		var ids = rows[i].rowId;
		                    ss.push(ids) ;
			        	}
			        	if(confirm('Are sure Clone/Copy selected row(s) ?'))
			        	{
			        		var url =  settings.action +'/copy';
							$.post( url  ,{ids:ss},function( data ) {
								if(data.status =='success')
								{
									notyMessage(data.message);	
									 settings.table.ajax.reload();
								} else {
									notyMessageError(data.message);	
								}				
							});	
			        	}	

					} else if(code =='edit') {
			       		var url = settings.action + '/update/'+id;			       		
			       		var mode = $(this).attr('mode');
						var title = $(this).attr('data-original-title');
						if(mode =='native')
						{
							Sdt_ViewDetail( settings.tableId , url  );
						} else {
							SximoModal(  url  , title  );
						}					

					} else if(code =='remove'){

						var rows = settings.table.rows('.selected').data();
						var ss = [];
			        	for(var i=0; i<rows.length; i++){
			        		var ids = rows[i].rowId;
		                    ss.push(ids) ;
			        	}
			        	if(confirm('Are sure Remove selected row(s) ?'))
			        	{
			        		var url =   settings.action + '/delete';
							$.post( url ,{ids:ss},function( data ) {
								if(data.status =='success')
								{
									notyMessage(data.message);	
									settings.table.ajax.reload();
								} else {
									notyMessageError(data.message);	
								}				
							});		
			        	}					

					} else {

					}
				}	
		    });	



        });

    }

}(jQuery));

function Sdt_ViewDetail( id , url )
{
	$('.ajaxLoading').show();
	$.get( url ,function( data ) {
		$( id +'View').html( data );
		$( id +'Grid').hide( );
		$('.ajaxLoading').hide();
	});		
		
}

function Sdt_Close( id )
{
	$( id +'View' ).html('');	
	$( id +'Grid' ).show();	
	$('#sximo-modal').modal('hide');
}

function Sdt_print(url ,w , h)
{
	var w = (w == '' ? w : 800 );	
	var h = (h == '' ? wh: 600 );	
	newwindow=window.open(url,'name','height='+w+',width='+h+',resizable=yes,toolbar=no,scrollbars=yes,location=no');
	if (window.focus) {newwindow.focus()}
}

function loadNestedLookup(url , id )
{
	if($(id).is(':empty'))
	{
		$(id).html('<p class"text-center" style="line-height:100px; text-align:center;"> Loading Content .... Please wait </p>');
		$.get(url,function(data)
		{
			$(id).load(url);	
		})
		
	}	
}