/**
 * Users panel for AdminBundle
 */
Ext.require("Hatimeria.core.utils.ConfigManager");

Ext.onReady(function() {
    
    var manager = Ext.ClassManager.get("Hatimeria.core.utils.ConfigManager");
    
    manager.register('HatimeriaAdmin.users.UsersPanel', {
        columns: [
            {header: 'Rabat', dataIndex: 'discount'}
        ]
    });
    
    manager.register('HatimeriaAdmin.users.model.UserModel', {
        fields: [
            {name: 'discount', type: 'string'}
        ]
    });
    
    manager.register('HatimeriaAdmin.users.form.UserForm', {
        items: [
            {
                xtype: 'textfield',
                name: 'discount',
                fieldLabel: 'Rabat'
            }
        ],
        submitConfig: {
            
        }
    });
    
});