(function() {
    Ext.define('HatimeriaBank.invoices.InvoicesPanel', {   
        extend: 'HatimeriaAdmin.core.grid.BaseGrid',
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
                },
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Rekordy {0} - {1} of {2}',
                    emptyMsg: "Brak rekordów"
                })
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
    
/** Year and month picker to download all invoices from current month
 *             {
                margin: 20,
                width: 300,
                border: 0,
                id: 'invoice-month-picker',
                xtype: 'monthpicker',
                showButtons: false
            },
            {
              xtype: 'button',
              text: 'Pobierz faktury z danego miesiąca',
              handler: function() {
                  var picker = Ext.getCmp("invoice-month-picker");
                  var month = picker.value[0];
                  var year  = picker.value[1];
                  
                  if(year == null || month == null) {
                      Ext.Msg.alert("Musisz wybrać miesiąc i rok");
                  }
                  
                  Routing.generate("invoices_package", {month: month, year: year})
              }
            }
 */    
})();
