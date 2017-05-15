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
             , 'choseExam'
             , 'examTypes'
            ],
    views: ['login',
            'choose',
            'tabPanel',
            'totalChoise',
             'examUser'
           ],
      apiKey: '',     
//    userId: 0,
//    password: '',
    init: function () {
        
        
      var self=this;  
      
         self.listen({
            store: {
                '#choiceStore': {
                      exception: self.handleException, 
                      load: self.setData,
                      write: self.refreshTotalChoice
                 },
                 '#examStore':{
                     load: self.setComboStore
                 }
            }
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
       self.choseExamStore=Ext.StoreManager.lookup('choseExamStore');
       self.examTypesStore=Ext.StoreManager.lookup('examTypesStore');
        
       self.choiceStore
           .getProxy()
           .on('exception', self.handleException);
       
       self.getAuthData();
       
       if(self.apiKey){
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
                apiKey: localStorage.getItem('apiKey')
//                userId: localStorage.getItem('userId'),
//              password: localStorage.getItem('password')
        
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
        self.examTypesStore
            .getProxy()
            .setExtraParams(auth);
        
        
        self.examStore.load({
            callback:function(){
             self.choiceStore.load();
             self.examTypesStore.load({
                 callback: function(){
                      self.totalChoiceStore.load();      
                 }
             });
            
            }
        });
             
    }
    ,  showAuth: function(){
        var self=this;
        Ext.create('dopForma.view.login', {
            method: 'post', 
            url: 'http://127.0.0.5/users/auth', 
            authSuccess: function (form, action) {
                   // console.log('--->',arguments);
             //self.userId=Ext.ComponentQuery.query('form [name=userId]')[0].value;
             //self.password=Ext.ComponentQuery.query('form [name=password]')[0].value;   
             self.apiKey=action.result.data;
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
        localStorage.setItem('apiKey',this.apiKey);
//        dopForma.getApplication().userId=this.userId;
//        dopForma.getApplication().password=this.password;
    }
    , getAuthData: function(){
        this.apiKey=localStorage.getItem('apiKey');
//        this.userId=localStorage.getItem('userId');
//        this.password=localStorage.getItem('password');
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
        forma.excludeSelected(forma.exam1);
        forma.excludeSelected(forma.exam2);
        forma.excludeSelected(forma.exam3);
        this.colorForm();
        
        }
        
    }
    ,showSpec: function(cont, rec ){
        
        this.examUser
            .getProxy()
            .setExtraParams({
                exam : rec.get('exam'),
                apiKey: localStorage.getItem('apiKey')
//                userId: localStorage.getItem('userId'),
//                password: localStorage.getItem('password')
            }) ;
        this.examUser.load();         
    }
    , refreshTotalChoice: function(){
        this.totalChoiceStore.load();
        this.colorForm();
    }
    , logOut: function(){
         localStorage.removeItem('apiKey');
//       localStorage.removeItem(userId);
//       localStorage.removeItem(password);  
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
  , setComboStore: function(){
      var forma=Ext.getCmp('choiceForma');
      if(forma){
          forma.per1.loadData(this.getPerel(3));
          forma.per2.loadData(this.getPerel(4));
      }
        ;
  }  
  , getPerel: function(id){
      var res=[],
          store=Ext.data.StoreManager.lookup('examStore')    ;  
             
        store.filter('exam_type', +id  );
        this.examStore.each(function(rec){
              res.push({
                  'id':rec.get('id'),
                  'name':rec.get('name'), 
                  'exam_type':rec.get('exam_type')
              });  
            })  ;
        store.clearFilter();
        
        return res;
    }
   , colorForm: function(){
       var self=this,
           forma=Ext.getCmp('choiceForma');    
   
       self.choseExamStore
           .getProxy()
           .setExtraParams({
               exam1: forma.exam1.getValue(),
               exam2: forma.exam2.getValue(), 
               exam3: forma.exam3.getValue(),
               apiKey: localStorage.getItem('apiKey')
           }); 
           
       self.choseExamStore.load(function(){
           var res={};
           self.choseExamStore.each(function(rec){
               res[rec.get('exam')]=self.getColor( rec.get('num')
                                                 , rec.get('minNum')
                                                 )
           });
           
           
           forma.exam1.setStyle('background-color', (forma.exam1.getValue() in res ? res[forma.exam1.getValue()]:''  )  );
           forma.exam2.setStyle('background-color', (forma.exam2.getValue() in res ? res[forma.exam2.getValue()]:''  )  );
           forma.exam3.setStyle('background-color', (forma.exam3.getValue() in res ? res[forma.exam3.getValue()]:''  )  );
           
           
           //console.log('------>', res);
       });
   }
   ,getColor:function(val, minVal){
       return ( val<minVal ? ( (!val)?'none':'yellow')   : 'lightgreen');
   }
  }
);
