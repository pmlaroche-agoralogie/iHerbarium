window.onload = function(){
	load_geolocalisation();
}

window.onunload = function(){
	GUnload();
}

	var map = null;
	var geocoder = null;
	var gLangue = "";
	

	// **** INITIALISATIONS AU CHARGEMENT DE LA PAGE **************************************************
    function load_geolocalisation() {
	  if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
		// ajout des options systeme de zooms et de deplacements lateraux
        map.addControl(new GLargeMapControl());
        var mapControl = new GMapTypeControl();
        map.addControl(mapControl);
		
		nivzoom = 4;
       	map.setCenter(new GLatLng(47.131079,2.247360), nivzoom, G_HYBRID_MAP);
        geocoder = new GClientGeocoder();
	  }
	}	
	
	
	// **** AFFICHE UN MARKER A L'ADRESSE GEOLOCALISEE ***************************************
	function zoomAdresse(adresse) {
      if (geocoder) {
        geocoder.getLatLng(
          adresse,
          function(point) {
			if (adresse=="exacentrer") {point = map.getCenter(); }
            if (!point) {
              alert(adresse + " : impossible à géolocaliser");
            } else {
			  // affiche marker de l'adresse saisie
			  html = "<p class='marker_geoloc'>";
			  html = html + "<strong>Latitude : </strong>" + point.lat() + "<br /><strong>Longitude : </strong>" + point.lng();
			  html = html + "</p>";
			  map.clearOverlays();
			  var marker = new GMarker(point, {draggable: true});
			  if (adresse!="exacentrer") { map.setCenter(point, 16); }			  
              map.addOverlay(marker);
              marker.openInfoWindowHtml(html);
			  // memorise latitude et longitude
			  document.forms[1].elements['vlatitude'].value = point.lat();
			  document.forms[1].elements['vlongitude'].value =  point.lng();
			  // si deplace marker, recalcul lat / long
			  GEvent.addListener(marker, "dragstart", function() {
          		map.closeInfoWindow();
        	  });
		      GEvent.addListener(marker, "dragend", function() {
				tmp_latitude = marker.getPoint().lat();
				tmp_longitude = marker.getPoint().lng();
				html = "<p class='marker_geoloc'>";
				html = html + "<strong>Latitude : </strong>" + tmp_latitude + "<br /><strong>Longitude : </strong>" + tmp_longitude;
				html = html + "</p>";
          		marker.openInfoWindowHtml(html);
			  	// memorise latitude et longitude
			  	document.forms[1].elements['vlatitude'].value = tmp_latitude;
			  	document.forms[1].elements['vlongitude'].value =  tmp_longitude;
        	  });
      		  GEvent.addListener(marker, 'click', function() {
      			marker.openInfoWindowHtml(html);
      		  });
            }
          }
        );
      }
    }
