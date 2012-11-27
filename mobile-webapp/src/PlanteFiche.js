floribalade.views.FichePlante = Ext.extend(Ext.Panel, {
    
    // fenetre du navigateur
    scroll: 'vertical',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    
    cls: 'session-detail',
    
    //Affichage de la barre en haut avec ses attributs 
    initComponent: function(){
        this.dockedItems = [{
            xtype: 'toolbar',
            items: [{
                ui: 'back',
                text: 'Retour',
                scope: this,
                handler: function(){
                    this.ownerCt.setActiveItem(this.prevCard, {
                        type: 'slide',
                        reverse: true,
                        scope: this
                        });
                }
            }
            ]
           
        }];
    
        var i=0, toutePhoto='', tof;
       
       for (i=0; i<this.record.data.allPhotos.length ; i++){
            tof = this.record.data.allPhotos[i].vignetteURL;
            toutePhoto +='<div class="grandeimage"> <img src="'+ tof +'" alt="image de plante"></div>'
        }
        
        //contenu de la liste 
        this.items = [{
            tpl: new Ext.XTemplate('<div class="bio_overview"><h3>&nbsp {nom}</h3><h4>&nbsp&nbsp {nomdeposant} <br/> &nbsp&nbsp {tsdepot}<tpl if="distance">, {distance} </tpl> </div></h4> '+toutePhoto),
            data: this.record.data,
            styleHtmlContent: true
            }
        ];
        
     
        floribalade.views.FichePlante.superclass.initComponent.call(this);
    }
    
});

Ext.reg('ficheplantex', floribalade.views.FichePlante);

