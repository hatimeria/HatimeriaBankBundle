(function() {
    Ext.define('HatimeriaBank.invoices.InvoicesPanel', {   
        extend: 'Hatimeria.core.grid.BaseGrid',
        actionColumn: false,
        initComponent: function()
        {
            var store = Ext.create('HatimeriaBank.invoices.store.AllStore');

            var config = {
                id: 'invoices-panel',
                title: 'Faktury',
                store: store,
                columns: [
                    {header: "Nr", dataIndex: 'fullNumber', sortable: false},
                    {header: "Kwota", dataIndex: 'amount'},
                    {header: "Data", dataIndex: 'createdAt'},
                    {header: "Tytuł", dataIndex: 'title', flex: 1}
                ],
                viewConfig: {
                    forceFit: true
                }
            };

            this.listeners = {
                itemclick: {
                    fn: function(view, record){ 
                        window.location = Routing.generate("invoice_download", {id: record.get('id')});
                    }
            }};

            Ext.apply(this, Ext.apply(config, this.initialConfig));
            this.callParent();
        }

    });
    
})();
