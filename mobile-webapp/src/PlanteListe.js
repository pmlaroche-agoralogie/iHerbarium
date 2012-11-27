floribalade.views.ListePlante = Ext.extend(Ext.Panel, {
    layout: 'card',
    //la barre en haut qui permet de les groupé
    
    initComponent: function() {
        
        if (navigator.geolocation)
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
          else
            alert("Votre navigateur ne prend pas en compte la géolocalisation HTML5");
           
          function successCallback(position){
            localStorage.setItem("lati",position.coords.latitude) ;
            localStorage.setItem("longo", position.coords.longitude);
          }
           
          function errorCallback(error){
            switch(error.code){
              case error.PERMISSION_DENIED:
                alert("Vous n'avez pas autoris\351 l'acc\350s \340 votre position. La carte ne vous affichera pas les observations les plus proches de votre position. Pour autoriser la localisation recharger la page, puis choisissez: Autoriser.");
                break;
              case error.POSITION_UNAVAILABLE:
                alert("Votre emplacement n'a pas pu être d\351termin\351");
                break;
              case error.TIMEOUT:
                alert("Le service n'a pas répondu à temps");
                break;
              }
          }
                       
      // Initialisation des données
     
        var urlListe;
        if (window.location.href == 'http://localhost/sencha/examples/floribalade/' || (window.location.href == 'http://192.168.0.40/sencha/examples/floribalade/')){
                if ((! localStorage.getItem("numUser")) ||(localStorage.getItem("numUser")==-1) || (localStorage.getItem("numUser")== 0))  {
                    urlListe = 'src/source2.json';
                }
                else {
                    urlListe = 'src/source.json';
                }
        }
        else {
            if ((! localStorage.getItem("numUser")) ||(localStorage.getItem("numUser")==-1) || (localStorage.getItem("numUser")== 0))  {
                    urlListe = 'http://balade.iherbarium.net/data/baladeData.php?lat=' + localStorage.getItem("lati") + '&long=' +  localStorage.getItem("longo");
                }
                else {
                    urlListe = 'http://balade.iherbarium.net/data/baladeData.php?id_user=' + localStorage.getItem("numUser") + '&lat=' + localStorage.getItem("lati") + '&long=' +  localStorage.getItem("longo");
                }
        }
        
        this.list = new Ext.List({
            itemTpl: '<div class="avatar"  style="background-image: url({nomphoto})"></div><span class="name">{nom} <br/>  <span class="tertiary"><FONT SIZE=2>{nomdeposant}</FONT></span></span><span class="secondary">{tsdepot}&nbsp<br/>{distance}&nbsp</span>',
            loadingText: false,
            store: new Ext.data.Store({   
                sorters:{ property : 'heure',direction :  'DESC' },
                model: 'FichePlante',
                proxy: {
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
            })
        });
        
       // permet d'afficher la fiche détailler d'une plante
        this.list.on('selectionchange', this.onSelect, this);
        this.list.store.load();
        
        // permet d'afficher la barre de menu 
        this.listpanel = new Ext.Panel({
            items: this.list,
            layout: 'fit',
            dockedItems: [{
                xtype: 'toolbar',
                title: 'iHerbarium'
            }],
            listeners: {
                activate: { fn: function(){
                    this.list.getSelectionModel().deselectAll();
                    Ext.repaint();
                    },
                    scope: this
                }
            }
        })
        
       
        
        this.items = this.listpanel;
        
        floribalade.views.ListePlante.superclass.initComponent.call(this);
    },
    
    // Cette fonction inialise le role des deux boutons voir une toutes les plantes et voir mes Plantes
    initializeData: function(data) {

        
        // Création des boutons 
        var buttons =[];  
        
        if (!( localStorage.getItem("numUser"))) {
            buttons.push ({
                text : 'Observations',
                pressed : false,
                scope : this,
                handler: function (){
                    this.disabled = true;
                    var pDeco = new Ext.Panel({
                       floating: true,
                       modal: true,
                        scope : this,
                       centered: false,
                       styleHtmlContent: true,
                       dockedItems: new Ext.Toolbar({
                                    dock: 'top',
                                    title : 'Vous n&#39;etes pas connect&#233;'
                       }),
                       scroll: 'vertical',
                       cls: 'htmlcontent',
                       items : [{contentEl: 'deco'},
                                {xtype: 'button',
                                   text: 'ok',
                                    handler : function(){
                                        pDeco.hide();
                                    }
                                }
                       ]
                    });
                    
                    pDeco.setCentered(true);
                    pDeco.show();
                 }
           });  
            
          
            
            buttons.push ({
            text : 'Mes Observations',
            scope : this,
            disabled : true
            });
        }
        else {
            buttons.push ({
            text : 'Observations',
            scope : this,
            handler: this.AfficherToutesPlantes,
            pressed : true
            });
            
            buttons.push ({
            text : 'Mes Observations',
            scope : this,
            handler: this.AfficherMesPlantes
            });
        }
    
        // assoncie les bouttons à la un nouvelle barre
        this.PlantesButtons = new Ext.SegmentedButton({
                items: buttons,
                defaults: { flex: 1 },
                style: 'width: 88%'
            });
        
        this.listpanel.addDocked({
                xtype: 'toolbar',
                ui: 'gray',
                items: this.PlantesButtons,
                layout: { pack: 'center' }
            });
        this.list.store.filter('cas','tous');
    },

    // Fonctions qui permet de changer la liste des plantes
    AfficherMesPlantes: function(btn) {   
        this.list.store.clearFilter();
        this.list.store.filter('cas','lesmiennes');
        this.list.scroller.scrollTo({y: 0}, false);
    },
    
    AfficherToutesPlantes: function(btn) {
        this.list.store.clearFilter();
        this.list.store.filter('cas','tous');
    },
    

    // Affiche la fiche d'uen plante
    onSelect: function(selectionmodel, records){
        if (records[0] !== undefined) {
            var sessionCard = new floribalade.views.FichePlante({
                prevCard: this.listpanel,
                record: records[0]
            });
            this.setActiveItem(sessionCard, 'slide');
        }
    }
    
});

Ext.reg('listeplante', floribalade.views.ListePlante);
