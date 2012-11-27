Ext.ns('floribalade', 'floribalade.views');

Ext.setup({
    statusBarStyle: 'black',
    onReady: function() {
        floribalade.App = new floribalade.App({
            title: 'iherbarium',
          
            
            gmapLink: 'http://goo.gl/maps/4ROM',
            gmapText: 'Jardins  Passagers',
            gmapCoords: [48.89116, 2.3881],
            
            aboutPages: [{
                title: 'Aide',
                card: {
                    xtype: 'htmlpage',
                    url: 'aide.html' // Faire une fonction javascript qui contient de l'aide pas de type html
                }
            },{
                title: '&Agrave propos',
                card: {
                    xtype: 'htmlpage', //trouver une des pages du site 
                    url: 'about.html'
                }
            }, {
                title: 'Contact',
                card: {
                    xtype: 'htmlpage', //envoyer un mail ˆ iherbarium
                    url: 'contact.html'
                }
            }]
        });
    }
});
