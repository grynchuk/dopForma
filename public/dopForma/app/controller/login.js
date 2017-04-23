Ext.define('dopForma.controller.login', {
    extend: 'Ext.app.Controller',
    models: [ 'users'
              ,'exam'
             , 'choice' 
             , 'totalChoice'
             , 'examUser'
            ],
    stores: ['users'
             ,'exam'  
             , 'choice'
             , 'totalChoice'
             , 'examUser'
            ],
    views: ['login',
            'choose',
            'tabPanel',
            'totalChoise',
             'examUser'
           ],
    userId: 0,
    password: '',
    init: function () {
        
        
      var self=this;  
      
         self.listen({
            store: {
                '#choiceStore': {
                      exception: self.handleException, 
                      load: self.setData,
                      write: self.refreshTotalChoice
                 }
                 
            },
//            proxy : {
//                '*':{
//                    exception: function(){ alert('ddddd'); }
//                }
//            }
        });
        
        self.control({
            '#choose':{
                'click': self.makeChoice
            },
            '#totalChoise':{
                 'rowclick': self.showSpec     
             }
             ,
             '#dopFormaMainPanel':{
                  'close': self.logOut
             }
             ,'#reset':{
                 'click': function(){
                     self.choiceStore.load();
                     self.examStore.clearFilter();
                 }
             },
             '#passRestore':{
                 'click': self.passRestore
             }
            
        });
       
       self.examUser= Ext.StoreManager.lookup('examUser');
       self.examStore=Ext.StoreManager.lookup('examStore');
       self.choiceStore=Ext.StoreManager.lookup('choiceStore');
       self.totalChoiceStore=Ext.StoreManager.lookup('totalChoiceStore');
       
       self.choiceStore
           .getProxy()
           .on('exception', self.handleException);
       
       self.getAuthData();
       
       if(self.userId){
             self.loadData();
             self.showTabs();
       }else{
             self.showAuth();            
       }
        
        
        
    }
    
    
    ,  showTabs: function(){
        Ext.create('dopForma.view.tabPanel', {
                successSub: function (){alert('ok');  },             
                failSub: function (){alert('error');  }
        }).show() ;
        
        
        
    }
    
    
    , setSecret: function(){
         
         
    }
    , loadData :function(){
        var self=this,
            auth={
         
                userId: localStorage.getItem('userId'),
              password: localStorage.getItem('password')
        
            };
        
        self.examStore
            .getProxy()
            .setExtraParams(auth); 
        self.choiceStore
            .getProxy()
            .setExtraParams(auth);
    
        self.totalChoiceStore
            .getProxy()
            .setExtraParams(auth);
    
        self.examStore.load({
            callback:function(){
             self.choiceStore.load();                
             self.totalChoiceStore.load();
            }
        });
             
    }
    ,  showAuth: function(){
        var self=this;
        Ext.create('dopForma.view.login', {
            method: 'post', 
            url: 'http://127.0.0.5/users/auth', 
            authSuccess: function (form, action) {
                
             self.userId=Ext.ComponentQuery.query('form [name=userId]')[0].value;
             self.password=Ext.ComponentQuery.query('form [name=password]')[0].value;   
             self.setAuthData();
             self.loadData();             
             Ext.getCmp('loginWin').destroy();   
             self.showTabs();
             
            },
            authFail:function(form, action){
                  Ext.getCmp('authRes').setValue(action.result.message);               
            }
        });
    }
    
    , setAuthData: function(){
        localStorage.setItem('userId'  ,this.userId);
        localStorage.setItem('password',this.password);
        dopForma.getApplication().userId=this.userId;
        dopForma.getApplication().password=this.password;
    }
    , getAuthData: function(){
        this.userId=localStorage.getItem('userId');
        this.password=localStorage.getItem('password');
     } 
    , logOut: function(){
       localStorage.removeItem(userId);
       localStorage.removeItem(password);
    }
    , makeChoice: function(){
       var   form=Ext.getCmp('choiceForma')
           , record=form.getRecord();
           form.updateRecord(record);
    }    
    , handleException: function(cont, req, op){
        var resp=JSON.parse(req.responseText);
        Ext.Msg.alert('Повідомлення',  resp.message);
        //alert(resp.message);       
        //console.log(arguments);
    }
    , setData:function(cont, records){
        
        var forma=Ext.getCmp('choiceForma');
        if(forma && records[0]){
        forma.loadRecord(records[0]);
        forma.filterExam([
            records[0].get('exam1'),
            records[0].get('exam2'),
            records[0].get('exam3')
        ]);
        }
        
    }
    ,showSpec: function(cont, rec ){
        
        this.examUser
            .getProxy()
            .setExtraParams({
                exam : rec.get('exam'),
                userId: localStorage.getItem('userId'),
                password: localStorage.getItem('password')
            }) ;
        this.examUser.load();         
    }
    , refreshTotalChoice: function(){
        this.totalChoiceStore.load();
    }
    , logOut: function(){
        
        localStorage.removeItem('userId');
        localStorage.removeItem('password');
        this.showAuth(); 
    }
    , passRestore: function(){
     
     var user= Ext.ComponentQuery.query('combo[name="userId"]')[0].getValue();
     
     if(!(user>0)) {
             Ext.Msg.alert('Повідомлення',  ' Оберіть електронну пошту ');
             return;      
     }
     
     Ext.getCmp('loginWin').mask(' Відправляю листа з паролем... ');

     
     Ext.Ajax.request({
            url: window.myHost+'/users/setNewPass',
            method: 'POST', 
            //extraParams:
            params:
                    {
                 userId: user
                },
            success: function(response, opts) {
                       Ext.getCmp('loginWin').unmask();           
                       Ext.Msg.alert('Повідомлення',  ' Вам відправлено листа з новим  паролем. ');
                      },

            failure: function(response, opts) {
                console.log('server-side failure with status code ' + response.status);
            }
     });
    }
}
);
