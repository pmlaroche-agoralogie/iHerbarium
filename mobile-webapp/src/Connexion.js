floribalade.views.seConnecter = Ext.extend(Ext.Panel, {
layout: 'fit',
vide :false, 

    initComponent: function() {
        // DŽclaration de la liste des ŽlŽments
        this.items = new Ext.Panel({
            dockedItems: new Ext.Toolbar({
                         dock: 'top',
                         title : 'iHerbarium'
            }),
            items: [{
                    xtype: 'fieldset',
                    defaults: {
                        labelAlign: 'right',
                        labelWidth: '80%'
                    },
                    items: [
                    {
                        id: 'ErreurBase',
                        xtype: 'fieldset',
                        contentEl: 'NoBase',
                        hidden : true
                    }, {
                        id: 'ErreurMotdePasse',
                        xtype: 'fieldset',
                        contentEl: 'NoMdp',
                        hidden : true
                    },{
                        id: 'eMail',
                        xtype: 'emailfield',
                        name : 'email',
                        label: 'Login'
                    }, {
                        id: 'decoTxt',
                        xtype: 'hiddenfield',
                        name : 'email',
                        label: 'Vous etes connecter avec le compte: ' + localStorage.getItem("mail") +'\n. Pour vous d&#233connecter cliquer ci dessous',
                        hidden : true,
                        allowBlank: true
                    },{ xtype : 'spacer',
                        height: 10
                    },
                    {
                        id : 'motPass',
                        xtype: 'passwordfield',
                        name : 'password',
                        label: 'Mot de passe'
                    },{ xtype : 'spacer',
                        height: 10
                    }, {
                        xtype: 'button',
                        text: 'Valider',
                        id : 'co',
                        handler : this.controleConnexion
                    },{
                        id: 'PasCompte',
                        xtype: 'fieldset',
                        contentEl: 'PasCompte'   
                    },{ xtype : 'spacer',
                        height: 10
                    }, {
                        xtype: 'button',
                        id : 'deco',
                        text: 'Se deconnecter',
                        handler : this.deconnexion,
                        hidden : true
                    }
                    ]
                }
            ]
        })
        
        //Gestion des messages et des champs ˆ afficher ˆ l'Žtat initial
        if (localStorage.getItem("numUser") >0) {
        Ext.getCmp('motPass').hide();
        Ext.getCmp('eMail').hide();
        Ext.getCmp('co').hide();
        Ext.getCmp('deco').show();
        Ext.getCmp('decoTxt').show();
        Ext.getCmp('PasCompte').hide();
          
    }
    
    
        this.items.fullscreen = true;
        floribalade.views.seConnecter.superclass.initComponent.call(this);
        
            
   },
   

   controleConnexion: function() {
    
    // Gestion des champs incomplet
    if  (((Ext.getCmp('eMail').getValue())[0]==null)&&((Ext.getCmp('motPass').getValue())[0]==null)) {
         
         this.vide = true;
         
         var pLoginMdp = new Ext.Panel({
            floating: true,
            modal: true,
            centered: false,
            styleHtmlContent: true,
            dockedItems: new Ext.Toolbar({
                         dock: 'top',
                         title : 'Saisie Incomp&#232;te'
            }),
            scroll: 'vertical',
            cls: 'htmlcontent',
            items : [{contentEl: 'loginMotdepasse'},
                     {xtype: 'button',
                        text: 'ok',
                        handler : function(){
                            pLoginMdp.hide();  
                        }}
            ]
        });
        pLoginMdp.setCentered(true);
        pLoginMdp.show();
         
         }  else if ((Ext.getCmp('eMail').getValue())[0]==null) {
                this.vide = true;
         
                var pLogin = new Ext.Panel({
                   floating: true,
                   modal: true,
                   centered: false,
                   styleHtmlContent: true,
                   dockedItems: new Ext.Toolbar({
                                dock: 'top',
                                title : 'Saisie Incomp&#232;te'
                   }),
                   scroll: 'vertical',
                   cls: 'htmlcontent',
                   items : [{contentEl: 'login'},
                            {xtype: 'button',
                               text: 'ok',
                               handler : function(){
                                   pLogin.hide();  
                               }}
                   ]
               });
               pLogin.setCentered(true);
               pLogin.show();
            }
                else if ((Ext.getCmp('motPass').getValue())[0]==null){
                     this.vide = true;
         
                    var pMdp = new Ext.Panel({
                       floating: true,
                       modal: true,
                       centered: false,
                       styleHtmlContent: true,
                       dockedItems: new Ext.Toolbar({
                                    dock: 'top',
                                    title : 'Saisie Incomp&#232;te'
                       }),
                       scroll: 'vertical',
                       cls: 'htmlcontent',
                       items : [{contentEl: 'Motdepasse'},
                                {xtype: 'button',
                                   text: 'ok',
                                   handler : function(){
                                       pMdp.hide();  
                                   }}
                       ]
                   });
                   pMdp.setCentered(true);
                   pMdp.show();
                }
                    else {
                        this.vide = false;
                    }
                    
      
    // Connexion au serveur 
    if (!this.vide)
      {        
        Ext.getCmp('ErreurBase').hide();
        Ext.getCmp('ErreurMotdePasse').hide();
        
        var lg = (Ext.getCmp('eMail').getValue());
        var mdp =(Ext.getCmp('motPass').getValue());
         
         lg = '\'' + lg + '\''  ;
         if ((window.location.href == 'http://localhost/sencha/examples/floribalade/')||(window.location.href == 'http://192.168.0.40/sencha/examples/floribalade/')){
            lg='';
            mdp='';
         }
         
         
        Ext.Ajax.request({
       
        url : floribalade.cfg.url1 + lg + floribalade.cfg.url2 + mdp,
        //url : 'src/sourceConnexion.json',
        success: function(response, opts) {
               var obj = Ext.decode(response.responseText);
               
               if ( obj.id_user == -1 ) {
                    Ext.getCmp('ErreurBase').show();
                    Ext.getCmp('eMail').setValue('');
                    Ext.getCmp('motPass').setValue('');
                    Ext.getCmp('eMail').focus();
                    
               }
                else if ( obj.id_user == 0 ) {
                     Ext.getCmp('ErreurMotdePasse').show();
                     Ext.getCmp('motPass').setValue('');
                     Ext.getCmp('motPass').focus();
                }
                    else {
                        localStorage.setItem("mail",Ext.getCmp('eMail').getValue())
                        localStorage.setItem("numUser", obj.id_user);
                        window.location.reload();
                    }
               
               
           },
         // Echec de la connexion au serveur
        failure: function(response, opts) {
            var pServeur = new Ext.Panel({
                floating: true,
                modal: true,
                centered: false,
                styleHtmlContent: true,
                dockedItems: new Ext.Toolbar({
                             dock: 'top',
                             title : 'Serveur'
                }),
                scroll: 'vertical',
                cls: 'htmlcontent',
                contentEl: 'serveur'
             });

            pServeur.setCentered(true);
            pServeur.show();
            }
        });
         
      }
      
    },
    
    deconnexion: function() {
      localStorage.removeItem("numUser");
       localStorage.removeItem("lat");
       localStorage.removeItem("mail");
        localStorage.removeItem("longo");

      window.location.reload();
    }
    
     
});

Ext.reg('seconnecter', floribalade.views.seConnecter );