// JavaScript Document
/* fonction permettant que la carte soit centrée sur l\'adresse entrée par l\'utilisateur */
	function codeAddress() {
		    var address = document.getElementById("address").value;
		    if (geocoder) {
			      geocoder.geocode( { 'address': address}, function(results, status) {
			        if (status == google.maps.GeocoderStatus.OK) {
				        	map.setCenter(results[0].geometry.location);
				          
				            /* quand on trouve une réponse à l\'adresse que l\'on a tapé, on fait un zoom sur la position recherchée
				            L\'utilisateur pourra ainsi faire sa sélection en cliquant sur le lieu qu\'il souhaite */
				            map.setZoom(13);
				          
				     } 
			         else {
					        alert("La localisation n'a pas pu se faire pour les raisons suivantes : " + status);
			           }
	              });
	       }
	  }

