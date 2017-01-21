Ext.define('dopForma.controller.login', {
    extend: 'Ext.app.Controller',
    models: [ 'users'
              ,'exam' 
            ],
    stores: ['users'
             ,'exam'   
            ],
    views: ['login',
            'choose',
            'tabPanel'
           ],
    userId: 0,
    password: '',
    init: function () {
      var self=this;  
         this.listen({
            store: {
                '#dicSetStore': {
                    load: function(context, records){
                              this.makeElList(records);                        
                      }
                }
            }
        });
        
       self.showAuth(); 
        
        
    }
    
    ,  showTabs: function(){
        Ext.create('dopForma.view.tabPanel', {
                successSub: function (){alert('ok');  },             
                failSub: function (){alert('error');  }
        }).show() ;
        
        
        
    }
    
    
    , setSecret: function(){
         
    }
    ,  showAuth: function(){
        var self=this;
        Ext.create('dopForma.view.login', {
            method: 'post', 
            url: 'http://127.0.0.5/users/auth', 
            authSuccess: function (form, action) {
             self.userId=Ext.ComponentQuery.query('form [name=id]')[0].value;
             self.password=Ext.ComponentQuery.query('form [name=password_]')[0].value;   
             Ext.getCmp('loginWin').destroy();   
             self.showTabs();
            },
            authFail:function(form, action){
                  Ext.getCmp('authRes').setValue(action.result.mess);               
            }
        });
    }
    
    
}
);
