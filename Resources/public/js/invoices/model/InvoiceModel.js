Ext.define('HatimeriaBank.invoices.model.InvoiceModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id',  type: 'int'},
        {name: 'fullNumber',  type: 'string'},
        {name: 'title',  type: 'string'},
        {name: 'amount',  type: 'auto'},
        {name: 'createdAt',  type: 'auto'},
    ]
});