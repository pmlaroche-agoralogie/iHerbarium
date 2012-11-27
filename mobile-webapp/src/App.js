floribalade.cfg = {};

floribalade.OfflineStore = new Ext.data.Store({
    model: 'OfflineData',
    autoLoad: true
});


floribalade.App = Ext.extend(Ext.TabPanel, {
    
    fullscreen: true,
    
    tabBar: {
        ui: 'gray',
        dock: 'bottom',
        layout: { pack: 'center' }
    },
    
    cardSwitchAnimation: false,


                
    
    initComponent: function() {
        
        if (navigator.onLine) {
            this.items = [  {
                xtype: 'listeplante',
                iconCls: 'time',
                title: 'Observations',
                confTitle: this.title,
                shortUrl: this.shortUrl
            },{
                title: 'Profil',
                iconCls: 'team1',
                xtype: 'seconnecter'

            },  {
                title: 'Carte',
                iconCls: 'locate',
                xtype: 'location',
                coords: this.gmapCoords,
                mapText: this.gmapText,
                permLink: this.gmapLink
            }, {
                title: 'Options',
                xtype: 'aboutlist',
                iconCls: 'info',
                pages: this.aboutPages
            }];
        }
        else {
            this.on('render', function(){
                this.el.mask('No internet connection.');
            }, this);
        }

        floribalade.cfg = {};
        floribalade.cfg.shortUrl = this.shortUrl;
        floribalade.cfg.title = this.title;
        
        floribalade.cfg.url1= 'src/sourceConnexion.json';
        floribalade.cfg.url2='';
        if (! ((window.location.href == 'http://localhost/sencha/examples/floribalade/')&&(window.location.href =='http://192.168.0.40/sencha/examples/floribalade/')))
            {
                floribalade.cfg.url1 = 'http://balade.iherbarium.net/data/connexion.php?user='
                floribalade.cfg.url2 = '&psw=';
            }
       
        
        
        floribalade.App.superclass.initComponent.call(this);
    },
    

    
});
