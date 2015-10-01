(function() {
    Ext.require("HatimeriaBank.invoices.model.InvoiceModel");

    Ext.define("HatimeriaBank.invoices.store.AllStore", {
         extend: "Hatimeria.core.store.BaseStore",
         
         /**
          * Constructor
          * 
          * @param {} cfg
          */
         constructor: function(cfg) {
            var config = {
                id: 'invoice-store',
                model: "HatimeriaBank.invoices.model.InvoiceModel",
                directFn: Actions.HatimeriaBank_Invoice.all,
                pageSize: 10     
            };

            Ext.apply(config, cfg || {});

            this.callParent([config]);
        }
    });
})();
