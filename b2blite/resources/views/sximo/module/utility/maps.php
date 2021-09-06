
 <script type="text/javascript">
	$(document).ready(function() { 
    var map;
    function initialize() {
        var myLatlng = new google.maps.LatLng(<?php echo $lat;?>, <?php echo $long;?>);

        var myOptions = {
            zoom: 12,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("<?php echo $id;?>"), myOptions);

        var marker = new google.maps.Marker({
            draggable: true,
            position: myLatlng,
            map: map,
            title: "Your location"
        });

        google.maps.event.addListener(marker, "dragend", function (event) {


            document.getElementById("location").value = event.latLng.lat()+","+event.latLng.lng();
            
        });
    }
    google.maps.event.addDomListener(window, "load", initialize());

		
	});
</script>		 