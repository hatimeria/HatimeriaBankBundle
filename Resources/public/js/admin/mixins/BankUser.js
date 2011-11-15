/**
 * Min-in users panel for AdminBundle
 */
(function() {
    
    Ext.define('HatimeriaBank.admin.mixins.BankUser', {
        
        initComponent: function(grid)
        {
            grid.on('afterrender', function() {
                console.log('afterrender');
            });
        },
        
        onPanelReady: function()
        {
            
        },
        
        onFormReady: function()
        {
            
        }
        
    });
    
})();