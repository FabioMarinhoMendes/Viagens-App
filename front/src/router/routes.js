const routes = [
  {
    path: '/',
    component: () => 
      import('layouts/MainLayout.vue'),
        children: [
          { path: '', component: () => import('pages/IndexPage.vue') }
        ],
        name: 'home',
        meta: {
          auth: true
        }
  },
  {
    path: '/pedidos',
    component: () => 
      import('layouts/MainLayout.vue'),
        children: [
          { path: '', component: () => import('pages/Pedido.vue') }
        ],
        name: 'pedido',
        meta: {
          auth: true
        }
  },
  {
    path: '/login',
    component: () => import('pages/Login.vue'), name: 'login', 
    meta: {
      auth: false
    },    
  },
  {
    path: '/cadastrar',
    component: () => import('pages/Cadastrar.vue'), name: 'new-user', 
    meta: {
      auth: false
    }
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
