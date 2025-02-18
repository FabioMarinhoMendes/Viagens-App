import { defineBoot, defineStore } from '#q-app/wrappers'
import axios from 'axios'

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ baseURL: 'http://localhost:8000/api/' })
//const teste = useAppStore;

export default defineBoot(({ app, store, router }) => {
  api.interceptors.response.use(resp => {
		let erroRetorno = resp.data.error || null;  
		if (![undefined, null].includes(erroRetorno) && erroRetorno == "tokenError"){      
			localStorage.removeItem("token");
      localStorage.removeItem("expires_at");
      localStorage.removeItem("userName");      
      router.push({ name: 'login' });
		}

		return resp
	})

  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

window.axios = api

export { api }
