Ext.define('dopForma.view.login', {
    extend: 'Ext.window.Window',
    xtype: 'login',
//    requires: [     
//        'Ext.form.Panel'
//    ],

    bodyPadding: 10,
    title: 'Авторизація',
    closable: false,
    autoShow: true,

    initComponent:function(){
      var self=this;
      self.items={
        xtype: 'form',
        reference: 'form',
        items: [{
            xtype: 'textfield',
            name: 'email',
            fieldLabel: 'Пошта',
            allowBlank: false
        }, {
            xtype: 'textfield',
            name: 'password_',
            inputType: 'password',
            fieldLabel: 'Пароль',
            allowBlank: false
        }, {
            xtype: 'displayfield',
            hideEmptyLabel: false,
            value: 'Оберіть свою поштову скриньку та введітьпароль'
        }],
        buttons: [{
            text: 'Вхід',
            formBind: true,
            id: 'login'
        }]
    };
      
      this.callParent(arguments);
    }


    
});