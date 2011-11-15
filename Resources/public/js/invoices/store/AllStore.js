(function() {
    Ext.require("HatimeriaBank.invoices.model.InvoiceModel");

    Ext.define("HatimeriaBank.invoices.store.AllStore", {
         extend: "HatimeriaAdmin.core.store.BaseStore",
         
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
                pageSize: 25     
            };

            Ext.apply(config, cfg || {});

            this.callParent([config]);
        }
    });
})();