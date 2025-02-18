import { defineBoot } from '#q-app/wrappers'


export default defineBoot(({ router, store }) => {

  router.beforeEach((to) => {
    const records = to.matched.filter(record => record.meta.auth)
    let expires_at = localStorage.getItem('expires_at');
    let token = localStorage.getItem('token');
    let dataAtual = new Date().toJSON().slice(0,19);
    if(records.length > 0){
       for (const record of records) {        
        if(expires_at == null || expires_at < dataAtual){      
          return {
            path: '/login',        
          };       
        }      
       }
    }
  })


})

