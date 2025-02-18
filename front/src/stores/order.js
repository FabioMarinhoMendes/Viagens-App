import { defineStore } from 'pinia';
import axios from "axios";

const api = axios.create({
 baseURL: "http://localhost:8000/api",
});

export default api;

export const useOrderStore = defineStore('order', {
  state: () => ({
    filtros: {
      id: '',
      data_partida: '',
      data_retorno: '',
      status: 'geral',
      destino: '',
      user_id: '',
      user_name: '',
    },
    dadosListagem: [],
    dados: {
      id: '',
      data_partida: new Date().toJSON().slice(0,10),
      data_retorno: new Date().toJSON().slice(0,10),
      status: 'solicitado',
      destino: '',
      user_id: '',
      user_name: '',
      visualizar: false,
      editar: false,
    },
    cidadesFilter: [],
    usuariosFilter: [],
    nivelAcesso: 1,
    erro: "",
    showErros: false,
  }),
  actions: {
    async obterResultados() {     
        let token = localStorage.getItem('token');  
        let filtros = this.filtros;     
        try {          
          const response = await  api.post(`pedidos/obterPedidos`, {filtros}, {
              headers: {
                Authorization: 'Bearer ' + token
              }
          });
         
          if(response.data.status){
              this.dadosListagem = response.data.pedidos;
              this.showErros = false;
          }               
            
        } catch (erro) {         
          if(!erro.response.data.status){
            this.erro = erro.response.data.message;
            this.showErros = true;
          }          
        } 
    },
    async obterUsuarios() {     
      let token = localStorage.getItem('token');        
      try {          
        const response = await  api.post(`pedidos/obterUsuarios`, {}, {
            headers: {
              Authorization: 'Bearer ' + token
            }
        });
       
        if(response.data.status){
          this.usuariosFilter = response.data.users;
          this.nivelAcesso = response.data.nivelAcesso;
          this.showErros = false;
        }               
          
      } catch (erro) {         
        if(!erro.response.data.status){
          this.erro = erro.response.data.message;
          this.showErros = true;
        }          
      } 
  },
    async listarCidades() {
      try {
          const response = await api.get('https://servicodados.ibge.gov.br/api/v1/localidades/municipios');
          this.cidadesFilter = response.data;
          this.showErros = false;
      } catch (erro) {
        this.erro = "Erro ao listar cidades";  
        this.showErros = true;    
      } 
    },  
    limparFiltros(){
      this.filtros = {
        id: '',
        data_partida: new Date().toJSON().slice(0,10),
        data_retorno: new Date().toJSON().slice(0,10),
        status: 'geral',
        destino: '',
        user_id: '',
        user_name: '',
      };
    },
    limparDados(){
      this.dados = {
        id: '',
        data_partida: new Date().toJSON().slice(0,10),
        data_retorno: new Date().toJSON().slice(0,10),
        status: 'solicitado',
        destino: '',
        user_id: '',
        user_name: '',
        visualizar: false,
        editar: false,
      };
    },
    setDadosListagem(dadosListagem){
      this.dadosListagem = dadosListagem;
    },
    setDados(dados, tipo){
      this.dados = dados;
      if(tipo == 'editar'){
        this.dados.editar = true;
      }else{
        this.dados.visualizar = true;
      }
      
    }  
  },
});