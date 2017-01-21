Ext.define('dopForma.view.login', {
    extend: 'Ext.window.Window',
    xtype: 'login',
    id: "loginWin",
    requires: [     
        'Ext.form.Panel'
    ],
    width : 400,
   // height: 250,
    bodyPadding: 10,
    title: 'Авторизація',
    closable: false,
    autoShow: true,
    authSuccess: '',
    authFail: '',
    myUrl: 'http://127.0.0.5/users/auth' ,
    constructor: function(o){
      this.authSuccess=o.authSuccess;
      this.authFail=o.authFail;      
      this.method=o.method;
      this.myUrl=o.url;
      this.callParent(arguments);  
    },
    initComponent:function(){
      var self=this;
      self.URL='http://127.0.0.5/users/auth';
      self.items={
        xtype: 'form',
        reference: 'form',
        url: self.myUrl,
        items: [{
            xtype:     'combo',
            store:   'users',
            queryMode: 'local',
            name: 'id',
            displayField: 'email',
            valueField: 'id', 
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
            id: 'authRes',
            value: 'Оберіть свою поштову скриньку та введітьпароль'
        },
        
        ],
        buttons: [{
            text: 'Вхід',
            formBind: true,
            id: 'login',
            handler:  function() {
                var form = this.up('form'); // get the form panel
                    
                if (form.isValid()) { // make sure the form contains valid data before submitting
                    form.submit({
                        success: self.authSuccess,
                        failure: self.authFail,
                    });
                } 
            }
        }]
    };
      
      this.callParent(arguments);
    }


    
});