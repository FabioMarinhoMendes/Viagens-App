<template>
    <q-dialog ref="modalFiltro">
        <q-card class="bg-grey-4" style="width: 600px; max-width: 80vw">
            <q-card-section class="row bg-blue-grey-13 text-white">
                <div class="text-h6 q-py-sm q-mx-md">Filtros</div>
                <q-space />
                <q-btn icon="close" flat dense v-close-popup />
            </q-card-section>
            
            <q-card-section class="q-my-sm q-mx-sm"> 
                <div class="row q-col-gutter-md">     
                    <div class="col-12">  
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Pesquisa por ID do Pedido:</p>                        
                        <q-input outlined dense v-model="filtros.id"/>
                    </div>                      
                    <div class="col-12 col-md-6">  
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Data de Partida:</p>                        
                        <q-input outlined clearable dense v-model="filtros.data_partida" type="date" />
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Data de Retorno:</p>
                        <q-input outlined clearable dense v-model="filtros.data_retorno" type="date" />
                    </div>
                    <div class="col-12">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Destino:</p>
                        <q-select outlined
                            dense
                            v-model="filtros.destino"
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

                    <div class="col-12">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Status:</p>
                        <q-btn-toggle
                            v-model="filtros.status"
                            push
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

                    <div class="col-12">
                        <p class="q-field__label" style="margin-bottom: 5px; font-size: 0.95rem">Solicitante:</p>          
                        <q-select outlined
                            dense
                            v-model="filtros.user_id"
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
                    </div>

                </div>
            </q-card-section>
            <q-card-actions class="bg-blue-grey-13 justify-end">
                <q-btn no-caps label="Filtrar" icon="filter_alt" color="positive" push @click="filtrar" v-close-popup />
                <q-btn no-caps label="Limpar" color="warning" text-color="dark" push @click="limparFiltros" v-close-popup />
                <q-btn no-caps label="Fechar" icon="close" flat push text-color="white" v-close-popup />
            </q-card-actions>

        </q-card>
    </q-dialog>
</template>


<script>
import { ref } from 'vue';
import { useOrderStore } from './../../stores/order';
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
        filtros() {     
            const orderStore = useOrderStore();      
            return orderStore.filtros;
        },
        dadosListagem() {         
            return useOrderStore().dadosListagem;
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
        filtrar(){
            this.$q.loading.show({ message: "Listando pedidos..." });
            useOrderStore().obterResultados();
            this.$q.loading.hide();    
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
        limparFiltros(){
            useOrderStore().limparFiltros();           
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
    }
}
</script>
