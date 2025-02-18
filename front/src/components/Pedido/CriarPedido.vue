<template>
    <q-dialog ref="modalCriarPedido">
        <q-card class="bg-grey-4" style="width: 700px; max-width: 80vw">
            <q-card-section class="row bg-blue-grey-13 text-white">
                <div class="text-h6 q-py-sm q-mx-md">Pedido de Viagem</div>
                <q-space />
                <q-btn icon="close" flat dense v-close-popup />
            </q-card-section>
            
            <q-card-section class="q-my-sm q-mx-sm">               
                <div class="row q-col-gutter-md">                           
                    <div class="col-12 col-md-6">  
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Data de Partida:</p>
                        <q-input outlined dense :readonly="dados.visualizar" v-model="dados.data_partida" type="date" />
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Data de Retorno:</p>
                        <q-input outlined dense :readonly="dados.visualizar" v-model="dados.data_retorno" type="date" />
                    </div>
                    <div class="col-12">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Destino:</p>
                        <q-select outlined
                            :readonly="dados.visualizar"
                            dense
                            v-model="dados.destino"
                            use-input
                            map-options
                            option-value="nome"
                            option-label="nome"
                            emit-value
                            clearable
                            input-debounce="0"    
                            hint="Digite o nome da cidade"                
                            :options="cidades"
                            @filter="filterCidades"
                        >
                        
                            <template v-slot:no-option>
                                <q-item>
                                    <q-item-section class="text-grey">
                                        Sem resultados
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-select> 
                    </div>

                    <div class="col-12" v-if="dados.visualizar">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Status:</p>
                        <q-btn-toggle
                            v-model="dados.status"
                            push
                            :readonly="dados.visualizar"
                            toggle-color="blue-grey-13"
                            toggle-text-color="white"
                            :options="[
                                { label: 'Geral', value: 'geral' },
                                { label: 'Solicitado', value: 'solicitado' },
                                { label: 'Aprovado', value: 'aprovado' },
                                { label: 'Cancelado', value: 'cancelado' },                                
                            ]"
                        />
                    </div> 

                    <div class="col-12" v-if="nivel_acesso == 0">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Solicitante:</p>          
                        <q-select outlined
                            v-if="!dados.visualizar"                    
                            dense                            
                            v-model="dados.user_id"
                            use-input
                            map-options
                            option-value="id"
                            option-label="name"
                            emit-value
                            clearable
                            input-debounce="0"    
                            hint="Digite o nome do solicitante"                
                            :options="usuarios"
                            @filter="filterUser"
                        >
                        
                            <template v-slot:no-option>
                                <q-item>
                                    <q-item-section class="text-grey">
                                        Sem resultados
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-select> 
                        <q-input v-else outlined dense :readonly="dados.visualizar" v-model="dados.user_name"/>
                    </div>

                                   

                </div>

            </q-card-section>
            <q-card-actions class="bg-blue-grey-13 justify-end"  v-if="!dados.visualizar">
                <q-btn no-caps label="Salvar" icon="save" color="positive" push @click="salvar" v-close-popup />               
                <q-btn no-caps label="Fechar" icon="close" flat push text-color="white" v-close-popup />
            </q-card-actions>

        </q-card>
    </q-dialog>
</template>


<script>
import { useOrderStore } from './../../stores/order';
import { ref } from 'vue';
export default {  
    data() {
        return {            
            cidades: [],
            usuarios: [],  
        }
    },
    created() {       
        this.listarCidades();  
        this.obterUsuarios();
    },
    computed: {   
        dados() {
            return useOrderStore().dados;
        }, 
        nivel_acesso() {
            return useOrderStore().nivelAcesso;
        },    
        cidadesFilter() {           
            return useOrderStore().cidadesFilter;
        },
        usuariosFilter() {           
            return useOrderStore().usuariosFilter;
        },
        erro() {           
            return useOrderStore().erro;
        },
        showErros() {           
            return useOrderStore().showErros;
        },
        token() {
            return localStorage.getItem('token');
        },
    },
    methods: {
        async salvar() {
            this.$q.loading.show({ message: "Salvando pedidos..." });       
            if(this.nivel_acesso == 1 || (this.nivel_acesso == 0 && this.dados.user_id == '')){
                delete this.dados.user_id;
            }if(this.dados.id == ''){
                delete this.dados.id;
            }
            try {
                const response = await  this.$api.post(`pedidos/criarPedido`, this.dados, {
                    headers: {
                        Authorization: 'Bearer ' + this.token
                    }
                });

                if(response.data.status){                      
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
                useOrderStore().limparDados();
                this.$q.loading.hide();
            }
        }, 
        obterUsuarios(){
            useOrderStore().obterUsuarios();             
            if(this.showErros){
                this.$q.dialog({
                    title: "Erro",
                    message: this.erro,
                    html: true,
                    class: "bg-negative text-white",
                    ok: {
                        push: true,
                        color: "white",
                        textColor: "black"
                    },
                });
            }    
        },
       
        listarCidades() {
            useOrderStore().listarCidades();
            if(this.showErros){
                this.$q.dialog({
                    title: "Erro",
                    message: this.erro,
                    html: true,
                    class: "bg-negative text-white",
                    ok: {
                        push: true,
                        color: "white",
                        textColor: "black"
                    },
                });
            }   
        },
        filterUser(val, update){
            if (val === '') {
                update(() => {
                    this.usuarios = this.usuariosFilter;
                })
                return
            }            
            update(() => {
                const needle = val.toString().toLowerCase()
                this.usuarios = this.usuariosFilter.filter(v => v.name.toString().toLowerCase().indexOf(needle) > -1)            
            })  
        },
        filterCidades(val, update) {
            if (val === '') {
                update(() => {
                    this.cidades = this.cidadesFilter;
                })
                return
            }            
            update(() => {
                const needle = val.toString().toLowerCase()
                this.cidades = this.cidadesFilter.filter(v => v.nome.toString().toLowerCase().indexOf(needle) > -1)            
            })              
        },
    }
}
</script>
