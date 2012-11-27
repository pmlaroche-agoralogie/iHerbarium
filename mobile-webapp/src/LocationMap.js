floribalade.views.LocationMap = Ext.extend(Ext.Panel, {
    permLink: '',
    
    initComponent: function(){
        
        //Permet de gerer le store
        var urlListe;
         if ((localStorage.getItem("longo")&&localStorage.getItem("lati"))){
           urlListe = "http://balade.iherbarium.net/data/baladeDataGeoloc.php?lat=" + localStorage.getItem("lati") + "&long=" + localStorage.getItem("longo") ;

        }
        else {
            urlListe = "http://balade.iherbarium.net/data/baladeDataGeoloc.php";

        }
        
           this.store = new Ext.data.Store({   
                model: 'FichePlante',
                sorters : 'distance',
                proxy: {
                    scope: this,
                    type: 'ajax',
                    url : urlListe,
                    reader: {
                        type: 'json',
                        root: 'plante'
                    }
                },
                listeners: {
                    load: { fn: this.initializeData, scope: this }
                }
            });
        this.store.load()
            
        // Bouton qui permet d'accŽder ˆ Google Maps
        this.dockedItems = [{
            xtype: 'toolbar',
            title: 'Observations Proches',
            items: [{xtype: 'spacer', flex: 1}, {
                ui: 'plain',
                iconCls: 'action',
                iconMask: true,
                scope: this,
                handler: function(){
                    
                    Ext.Msg.confirm('External Link', 'Open in Google Maps?', function(res){
                        if (res == 'yes') window.location = this.permLink;
                    }, this);
                }
            }]
        }]
        
        // Le centre de la carte
        if ((localStorage.getItem("longo")&&localStorage.getItem("lati"))){
            var position = new google.maps.LatLng(localStorage.getItem("lati"), localStorage.getItem("longo"));
        }
        else {
            var position = new google.maps.LatLng(48.8894, 2.3924);
        }
        
        //DŽclaration de la carte
        this.map = new Ext.Map({
            mapOptions : {
                zoom : 12,
                center : position, 
                navigationControlOptions: {
                    style: google.maps.NavigationControlStyle.DEFAULT
                }
            }
        });
   
        this.items = this.map;
        
        floribalade.views.LocationMap.superclass.initComponent.call(this);
    },
    
    initializeData: function(data){
        
        //RŽcuoŽration des donnŽes
        var plantes = data.data;

        if ((localStorage.getItem("longo")&&localStorage.getItem("lati"))){
            var position = new google.maps.LatLng(localStorage.getItem("lati"), localStorage.getItem("longo"));
        }
        else {
            var position = new google.maps.LatLng(48.8894, 2.3924);
        }
        
        //Ajout ) la carte
        this.map.addListener ({
                    
            maprender : function(comp, map){
            //console.log(plantes.last().data.distance)        
              //console.log(data.data.length)
                //console.log(this.dis)
                //this.map.mapOptions.zoom=plantes.length      
               //this.map.zoom=2;
               
           
              /*google.maps.event.addListener(map, 'bounds_changed', function() {
                
               
                //Je calcule l'Žtendu de la largeur de la carte
               var dLat1InRad = map.getBounds().ma.b * (Math.PI / 180);
                var dLong1InRad = map.getBounds().ta.b * (Math.PI / 180);
                var dLat2InRad = map.getBounds().ma.b * (Math.PI / 180);
                var dLong2InRad = map.getBounds().ta.d * (Math.PI / 180);
                
                var dLongitude = dLong2InRad - dLong1InRad;
                var dLatitude = dLat2InRad - dLat1InRad;
                
                // Intermediate result a.
                var a = Math.pow(Math.sin(dLatitude / 2.0), 2.0) + Math.cos(dLat1InRad) * Math.cos(dLat2InRad) * Math.pow(Math.sin(dLongitude / 2.0), 2.0);
                
                // Intermediate result c (great circle distance in Radians).
                var c = 2.0 * Math.atan2(Math.sqrt(a), Math.sqrt(1.0 - a));
                
                var kEarthRadiusKms = 6376.1;
                var resultLong = kEarthRadiusKms * c;
                
                //Je calcule l'Žtendu de la longueur de la carte
                dLat2InRad = map.getBounds().ma.d * (Math.PI / 180);
                dLong2InRad = map.getBounds().ta.b * (Math.PI / 180);
                
                dLongitude = dLong2InRad - dLong1InRad;
                dLatitude = dLat2InRad - dLat1InRad;
                
                // Intermediate result a.
                a = Math.pow(Math.sin(dLatitude / 2.0), 2.0) + Math.cos(dLat1InRad) * Math.cos(dLat2InRad) * Math.pow(Math.sin(dLongitude / 2.0), 2.0);
                
                // Intermediate result c (great circle distance in Radians).
                c = 2.0 * Math.atan2(Math.sqrt(a), Math.sqrt(1.0 - a));
                var resultLarg = kEarthRadiusKms * c;
                

                if (    (resultLong > resultLarg)   ){
                    var longeurPlusGrandLargeur = true;
                }
                else{
                    var longeurPlusGrandLargeur = false;
                }   
                console.log(resultLong);
                console.log(resultLarg);
                console.log(longeurPlusGrandLargeur);   
               var position = new google.maps.LatLng(45.0000000002,9.000000000);
               
                        var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        center : 2
                    });*/
                        
                    
                    /*console.log(map.zoom)
                    map.setZoom(50);
                    console.log(map.zoom)
                    console.log (ok)*/
             //   });
                
                    //Affichage des marqueurs et de la vignette plantes
             plantes.each(function(item, index, length) {
                     
                    var plante = item.data;
                  
                    if ( plante.latitude == 0 || plante.longitude == 0 ) return;
                  
                    var contentString = '<html><body><img src=' +   plante.allPhotos[0].vignetteURL + ' height=50 width="60"> <b>'+ plante.nom + '</b> </body></html>'
                    var position = new google.maps.LatLng(plante.latitude, plante.longitude);
                    var infowindow = new google.maps.InfoWindow({content: contentString});
                        
                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        center : 2
                    });
                    
                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map, marker);
                    });
                        
                } );
             
            }

    });

    
    }
    
    
});

Ext.reg('location', floribalade.views.LocationMap);
