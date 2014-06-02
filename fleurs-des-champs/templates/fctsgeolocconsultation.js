window.onload = function(){
	load_geolocalisation();
}

window.onunload = function(){
	GUnload();
}


	var map = null;
	var geocoder = null;
	var fichier_xml = null;
	
    var iconfleurs = new GIcon(); 
    iconfleurs.image = 'http://www.fleurs-des-champs.com/interface/marker_fleurs.png';
    iconfleurs.shadow = 'http://www.fleurs-des-champs.com/interface/marker_shadow.png';
    iconfleurs.iconSize = new GSize(12, 20);
    iconfleurs.shadowSize = new GSize(22, 20);
    iconfleurs.iconAnchor = new GPoint(6, 20);
    iconfleurs.infoWindowAnchor = new GPoint(6, 1);
	iconfleurs.infoShadowAnchor = new GPoint(12, 25);

    var iconarbre = new GIcon(); 
    iconarbre.image = 'http://www.fleurs-des-champs.com/interface/marker_arbres.png';
    iconarbre.shadow = 'http://www.fleurs-des-champs.com/interface/marker_shadow.png';
    iconarbre.iconSize = new GSize(12, 20);
    iconarbre.shadowSize = new GSize(22, 20);
    iconarbre.iconAnchor = new GPoint(6, 20);
    iconarbre.infoWindowAnchor = new GPoint(6, 1);
	iconarbre.infoShadowAnchor = new GPoint(12, 25);

    var iconchampi = new GIcon(); 
    iconchampi.image = 'http://www.fleurs-des-champs.com/interface/marker_champi.png';
    iconchampi.shadow = 'http://www.fleurs-des-champs.com/interface/marker_shadow.png';
    iconchampi.iconSize = new GSize(20, 34);
    iconchampi.shadowSize = new GSize(22, 20);
    iconchampi.iconAnchor = new GPoint(6, 20);
    iconchampi.infoWindowAnchor = new GPoint(6, 1);
	iconchampi.infoShadowAnchor = new GPoint(12, 25);

    var customIcons = [];
    customIcons["fleurs"] = iconfleurs;
    customIcons["arbre"] = iconarbre;
    customIcons["champi"] = iconchampi;


	// **** INITIALISATIONS AU CHARGEMENT DE LA PAGE **************************************************
    function load_geolocalisation() {
	  if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
		// ajout des options systeme de zooms et de deplacements lateraux
        map.addControl(new GLargeMapControl());
        var mapControl = new GMapTypeControl();
        map.addControl(mapControl);
		// initialement, se positionne sur le niveau 4 de zoom
		nivzoom = 5;
       	map.setCenter(new GLatLng(47.131079,2.247360), nivzoom);
  		fichier_xml = refFichierXml ();
		// ajout d'un ecouteur sur le zoom de la carte
		//GEvent.addListener(map, 'zoomend', function(oldzoom,newzoom) {
		//	affiche_markers ();
		//}); 
		// ajout d'un ecouteur sur le deplacement de la carte
		//GEvent.addListener(map, 'dragend', function() {
		//	affiche_markers ();
		//}); 
		// affiche les markers
		affiche_markers ();
	  }
	}	
	
	
	// **** SELECTION DU FICHIER XML EN FONCTION DU TYPE DE MARKERS ATTENDU **************************
	function refFichierXml () {
		fic_xml = "geoloc_xml.php";
  		if ((param_idt == "") && (param_pwd == "")) {  // consultation publique
			if (param_ns != "") { fic_xml = fic_xml + "?ns=" + param_ns; }
		} else {  // consultation privee
			fic_xml = fic_xml + "?idt=" + param_idt + "&pwd=" + param_pwd;
			if (param_ns != "") { fic_xml = fic_xml + "&ns=" + param_ns; }
		}
		return fic_xml;
	}


	// **** AFFICHE LES MARKERS DE GEOLOCALISATION **************************************************
	function affiche_markers () {
	if (GBrowserIsCompatible()) {
		map.clearOverlays();
  		// selectionne le fic xml correspondant aux markers attendus
  		// fichier_xml = refFichierXml ();
    	geocoder = new GClientGeocoder();
        GDownloadUrl(fichier_xml, function(data) {
          var xml = GXml.parse(data);
          var markers = xml.documentElement.getElementsByTagName("marker");
		  //var bounds = map.getBounds();
		  //alert(markers.length);
		  for (var i = 0; i < markers.length; i++) {
            var point = new GLatLng(parseFloat(markers[i].getAttribute("lat")), parseFloat(markers[i].getAttribute("lng")));
			// calcule le rectangle de zoom
			if (i == 0) {
				coordonnees_max = new Array(point.lat(),point.lng(),point.lat(),point.lng());
			} else {
				if (point.lat() < coordonnees_max[0]) { coordonnees_max[0] = point.lat(); }
				else { if (point.lat() > coordonnees_max[2]) { coordonnees_max[2] = point.lat(); } }
				if (point.lng() < coordonnees_max[1]) { coordonnees_max[1] = point.lng(); }
				else { if (point.lng() > coordonnees_max[3]) { coordonnees_max[3] = point.lng(); } }
			}
			// affiche les markers seulement dans la partie visible de la carte
			//if (bounds.contains(point) == true) {
				var typem = markers[i].getAttribute("base");
            	var name = markers[i].getAttribute("ns") + " (" + markers[i].getAttribute("nc") + ")";
            	var address = "<strong>Date de l'observation : </strong>" + markers[i].getAttribute("dateobs");
            	if (markers[i].getAttribute("qui") != "") { address = address + "<br /><strong>Auteur : </strong>" + markers[i].getAttribute("qui"); }
            	if (markers[i].getAttribute("commentairepublic") != "") { address = address + "<br /><strong>Commentaire : </strong>" + markers[i].getAttribute("commentairepublic"); }
            	if (markers[i].getAttribute("commentaireprive") != "") { address = address + "<br /><strong>Commentaire privé : </strong>" + markers[i].getAttribute("commentaireprive"); }
            	var marker = createMarker(point, name, address, typem);
           		map.addOverlay(marker);
			//}
          }
		  zoomZoneObservations(coordonnees_max);
        });
      }
    }


	// **** CREATION DU MARKER CORRESPONDANT AU POINT EN PARAMETRE ***************************************
    function createMarker(point, name, address, type) {
      var marker = new GMarker(point, customIcons[type]);
      var html = "<p class='marker_geoloc'>";
	  html = html + "<strong>" + name + "</strong>";
	  if (address != "") { html = html + "<br /><br />" +  address; }
	  html = html + "</p>";
      GEvent.addListener(marker, 'click', function() {
      	marker.openInfoWindowHtml(html);
      });
      return marker;
    }


	// **** ZOOM SUR LA ZONE QUI CONTIENT TOUTES LES OBSERVATIONS A AFFICHER *****************************
	function zoomZoneObservations (coordonnees) {
		ecart_x = coordonnees[2] - coordonnees[0];
		centre_x = coordonnees[0] + (ecart_x / 2);
		ecart_y = coordonnees[3] - coordonnees[1];
		centre_y = coordonnees[1] + (ecart_y / 2);
		plus_grand_ecart = Math.max(ecart_x,ecart_y);
		if (plus_grand_ecart < 1) {
			nivzoom = 8;
		} else {
			if (plus_grand_ecart < 2) {
				nivzoom = 7;
			} else {
				nivzoom = 6;
			}
		}
		map.setCenter(new GLatLng(centre_x,centre_y), nivzoom);
	}
