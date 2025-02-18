<template>
    <div class="row">
        <div class="col-12 q-gutter-md">
            <q-btn color="primary" icon="filter_alt" label="Filtros" @click="showFiltros"></q-btn>
            <q-btn color="primary" icon="add" label="Novo Pedido de Viagem" @click="showNovoPedido"></q-btn>
        </div>
        <div class="col-12 q-mt-lg"> 
            <q-table
                :style="!$q.screen.lt.sm ? 'max-height:600px' : 'background-color: #f5f5f5'"
                separator="cell"
                :card-class="$q.screen.lt.sm ? '' : 'bg-blue-grey-1'"
                table-header-class="bg-info text-bold"
                :rows="dadosListagem"
                :columns="columns"
                row-key="id"
                wrap-cells
                :loading="carregando"
                v-model:pagination="paginacao"
                loading-label="Carregando..."
                no-data-label="Nenhum registro encontrado"   
            >
                <template v-slot:loading>
                    <q-inner-loading showing color="primary" />
                </template>

                <template v-slot:body-cell-opcoes="props">
                    <q-td style="width: 140px" class="q-gutter-sm text-center">   
                        <q-btn no-caps size="sm" color="primary" label="Ver" title="Ver Viagem" @click="verPedido(props.row.id)" push></q-btn>    
                        <q-btn no-caps size="sm" color="primary" label="Editar" title="Editar Pedido" @click="editarPedido(props.row)" push></q-btn>                                   
                        <q-btn no-caps size="sm" color="primary" label="Alterar Status" title="Alterar Status" @click="alterarStatus(props.row)" push></q-btn>   
                        <q-btn no-caps size="sm" color="negative" label="Cancelar" title="Cancelar Pedido" @click="confirmacao(props.row.id)" push></q-btn>                            
                    </q-td>
                </template>

                <template v-slot:body-cell-status="props">
                    <q-td class="text-right" :style="colorStyle(props.row.status)">
                        <b>{{ primeiraLetraMaiuscula(props.row.status) }}</b>
                    </q-td>
                </template>

            </q-table> 
        </div>

        <Filtro ref="filtros" />
        <CriarPedido ref="criarPedido" />    
        
        <q-dialog ref="modalStatus">
            <q-card class="bg-grey-4" style="width: 600px; max-width: 80vw">
                <q-card-section class="row bg-blue-grey-13 text-white">
                    <div class="text-h6 q-py-sm q-mx-md">Alterar Status</div>
                    <q-space />
                    <q-btn icon="close" flat dense v-close-popup />
                </q-card-section>
                
                <q-card-section class="q-my-sm q-mx-sm"> 
                    <div class="row q-col-gutter-md">  
                        <div class="col-12">
                            <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Status:</p>
                            <q-btn-toggle
                                v-model="dadosAlterar.status"
                                push
                                toggle-color="blue-grey-13"
                                toggle-text-color="white"
                                :options="[
                                    { label: 'Solicitado', value: 'solicitado' },
                                    { label: 'Aprovado', value: 'aprovado' },
                                    { label: 'Cancelado', value: 'cancelado' },
                                ]"
                            />
                        </div>
                    </div>
                </q-card-section>
                <q-card-actions class="bg-blue-grey-13 justify-end">
                    <q-btn no-caps label="Salvar" color="positive" push @click="salvarStatus" v-close-popup />                    
                    <q-btn no-caps label="Fechar" icon="close" flat push text-color="white" v-close-popup />
                </q-card-actions>

            </q-card>
        </q-dialog>

    </div>  

</template>
<script>
import { useOrderStore } from './../../stores/order';
import Filtro from './Filtro';
import CriarPedido from './CriarPedido';
import { ref } from 'vue';
export default {  
    components: {
        Filtro,
        CriarPedido
    },
    data() {
        return {
            carregando: false,
            paginacao: {},      
            dadosAlterar:{
                status: "",   
                id: "",
            },           
                          
        }
    },
    created() {
        this.listarPedidos();
    },
    computed: {   
        dadosListagem() {
            return useOrderStore().dadosListagem;
        },
        columns(){
            return [
                { name: "opcoes", field: "opcoes", label: " " },
                { name: "id", field: "id", label: "ID", sortable: true, sortDirection: "desc" },
                { name: "user_name", field: "user_name", label: "Nome do Solicitante", sortable: true },
                { name: "destino", field: "destino", label: "Destino", sortable: true },
                { name: "data_partida", field: "data_partida", label: "Data de Partida", sortable: true, format: value => this.formataDataBR(value) } ,
                { name: "data_retorno", field: "data_retorno", label: "Data de Retorno", sortable: true, format: value => this.formataDataBR(value) } ,
                { name: "status", field: "status", label: "Status", sortable: true, }
            ]
        },
        nivel_acesso() {
            return useOrderStore().nivelAcesso;
        }, 
        token() {
            return localStorage.getItem('token');
        },        
    },
    methods: {
        confirmacao(id){
            this.$q.dialog({
                title: "Confirmação",
                message: "Deseja realmente cancelar o pedido?",
                html: true,            
                class: "bg-blue-3 q-pa-xs dialog-message",
                persistent: true,
                ok: {
                    push: true,
                    color: "teal-9",
                    label: "Cancelar Pedido",
                },
                cancel: {
                    push: true,
                    color: "negative",
                    label: "Sair",
                },
              
            }).onOk(() => {
                this.cancelar(id);
            }).onCancel(() => {
               
            });
        },
        async cancelar(id){
            this.$q.loading.show({ message: "Cancelando pedido..." });
            try {
                const response = await this.$api.post(`pedidos/cancelarPedido`, {id}, {
                    headers: {
                        Authorization: 'Bearer ' + this.token
                    }
                });
                if(response.data.status){                 
                    useOrderStore().limparFiltros();    
                    useOrderStore().obterResultados();
                    this.$q.dialog({
                        title: "Confirmação",
                        message: response.data.message,
                        html: true,
                        class: "bg-positive text-white",
                        persistent: true,                           
                    })
                }   
            } catch (erro) {
                if(!erro.response.data.status){
                    this.$q.dialog({
                        title: "Erro",
                        message: erro.response.data.message,
                        html: true,
                        class: "bg-negative text-white",
                        ok: {
                            push: true,
                            color: "white",
                            textColor: "black"
                        },
                    });
                }          
            } finally {
                this.$q.loading.hide();
            }           
        },
        showNovoPedido(){
            useOrderStore().limparDados();
            this.$refs.criarPedido.$refs.modalCriarPedido.show();
        },     
        editarPedido(row){           
            if(row.status == 'cancelado' && this.nivel_acesso == 1){
                this.$q.dialog({
                    title: "Erro",
                    message: "Pedido cancelado não pode ser editado.",
                    html: true,
                    class: "bg-negative text-white",
                    ok: {
                        push: true,
                        color: "white",
                        textColor: "black"
                    },
                });
                return false;
            }
            this.$refs.criarPedido.$refs.modalCriarPedido.show();
            useOrderStore().setDados(row, 'editar'); 
        },
        async verPedido(id){
            this.$q.loading.show({ message: "Carregando detalhes do pedido..." });   
            try {
                const response = await this.$api.post(`pedidos/obterPedidoPorId`, {id}, {
                    headers: {
                        Authorization: 'Bearer ' + this.token
                    }
                });
                if(response.data.status){                 
                    this.$refs.criarPedido.$refs.modalCriarPedido.show();
                    useOrderStore().setDados(response.data.pedidos, 'visualizar'); 
                }   
            } catch (erro) {
                if(!erro.response.data.status){
                    this.$q.dialog({
                        title: "Erro",
                        message: erro.response.data.message,
                        html: true,
                        class: "bg-negative text-white",
                        ok: {
                            push: true,
                            color: "white",
                            textColor: "black"
                        },
                    });
                }          
            } finally {
                this.$q.loading.hide();
            }
        },
        alterarStatus(row){
            this.dadosAlterar.status = row.status;        
            this.dadosAlterar.id = row.id; 
            this.$refs.modalStatus.show();            
        },
        async salvarStatus(){
            this.$q.loading.show({ message: "Salvando Status..." });
            try {
                const response = await  this.$api.post(`pedidos/alterarStatus`, this.dadosAlterar, {
                    headers: {
                    Authorization: 'Bearer ' + this.token
                    }
                });
                if(response.data.status){
                    useOrderStore().limparFiltros();    
                    useOrderStore().obterResultados();
                    this.$q.dialog({
                        title: "Confirmação",
                        message: response.data.message,
                        html: true,
                        class: "bg-positive text-white",
                        persistent: true,                           
                    })
                }  
            } catch (erro) {
                if(!erro.response.data.status){
                    this.$q.dialog({
                        title: "Erro",
                        message: erro.response.data.message,
                        html: true,
                        class: "bg-negative text-white",
                        ok: {
                            push: true,
                            color: "white",
                            textColor: "black"
                        },
                    });
                }          
            } finally {
                this.$q.loading.hide();
            }
        },
        showFiltros(){
            this.$refs.filtros.$refs.modalFiltro.show();
            useOrderStore().limparFiltros(); 
        },
        async listarPedidos() {
            this.$q.loading.show({ message: "Listando pedidos..." });
            try {
                const response = await  this.$api.post(`pedidos/obterPedidos`, {}, {
                    headers: {
                    Authorization: 'Bearer ' + this.token
                    }
                });
                if(response.data.status){
                    const filterStore = useOrderStore();
                    filterStore.setDadosListagem(response.data.pedidos);  
                }  
            } catch (erro) {
                if(!erro.response.data.status){
                    this.$q.dialog({
                        title: "Erro",
                        message: erro.response.data.message,
                        html: true,
                        class: "bg-negative text-white",
                        ok: {
                            push: true,
                            color: "white",
                            textColor: "black"
                        },
                    });
                }          
            } finally {
                this.$q.loading.hide();
            }
        },  
        formataDataBR(data) { 
            return new Date(data+"T00:00:00.0").toLocaleDateString("pt-BR");            
        },
        primeiraLetraMaiuscula(str) {
            if (!str) {
                return "";
            }
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        colorStyle(status){
            if(status == 'cancelado'){
                return 'color:red;';
            }else if(status == 'aprovado'){
                return 'color:green;';
            }
        },
    }
}
</script>
