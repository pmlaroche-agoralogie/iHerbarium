Ext.regModel('OfflineData', {
    fields: ['id', 'feedname', 'json'],
    proxy: {type: 'localstorage', id: 'floribaladeproxy'}
});
Ext.regModel('FichePlante', {
    hasMany: {
        model: 'FichePlante',
        name: 'plante'
    },
    fields: ['nom', 'latitude', 'longitude', 'id_user', 'position', 'nomphoto', 'nomdeposant', 'tsdepot', 'grandephoto', 'cas', 'distance', 'heure', 'allPhotos' ]
});
