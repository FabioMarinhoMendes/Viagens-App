<template>
    <div :class="$q.screen.lt.sm ? 'row' : 'row q-pt-xl'">
        <div :class="$q.screen.lt.sm ? 'col-12 col-md-4 offset-md-4' : 'col-12 col-md-4 offset-md-4 q-pt-xl'">
            <q-card class="q-mt-xl">
                <q-card-section>
                    <div class="text-h6">
                        Cadatrar novo usuário
                    </div>
                </q-card-section>
                <q-separator/>
                <q-card-section>
                    <form>       
                        <q-input outlined v-model="dados.name" label="Nome:" color="dark"
                            :dense="!$q.screen.lt.sm" :rules="[val => !!val || 'O Nome é obrigatorio!!']">                           
                        </q-input>

                        <q-input outlined v-model="dados.email" label="Email:" color="dark"
                            :dense="!$q.screen.lt.sm" :rules="[val => !!val || 'O Email é obrigatorio!!', 'email']">                           
                        </q-input>
                    
                        <q-select outlined v-model="dados.nivel_acesso"  label="Nivel de Acesso" :options="nivel_acesso_options"
                            emit-value map-options :dense="!$q.screen.lt.sm" :rules="[val => !!val || 'O Nivel de Acesso é obrigatorio!!']"
                        />

                        <q-input outlined v-model="dados.password" label="Senha:" color="dark" type="password"
                            hint="Precisa ter 6 ou mais caracteres"
                            :dense="!$q.screen.lt.sm" :rules="[val => !!val || 'A senha é obrigatoria!!', val => val.length >= 6 || 'A senha é muito curta!!']">                        
                        </q-input>

                        <q-input outlined class="q-mt-md" v-model="dados.cPassword" label="Confirmar senha:" color="dark" type="password"
                            hint="Precisa ter 6 ou mais caracteres"
                            :dense="!$q.screen.lt.sm" :rules="[val => val == dados.password || 'A senha esta diferente!!', val => !!val || 'A senha é obrigatoria!!', val => val.length >= 6 || 'A senha é muito curta!!']">                        
                        </q-input>
                    </form>
                </q-card-section>  
                <q-separator/>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-btn class="col-5" label="Voltar" size="md" @click="$router.push({ name: 'login' })" />
                        </div>
                        <div class="col-12 col-md-6">
                            <q-btn class="col-5 full-width" color="positive" size="md" label="Cadastrar" @click="cadastrar" />
                        </div>
                    </div>                    
                </q-card-section>                  
            </q-card>  

        </div>
    </div>
    
    
 

</template>
<script>
export default {   
    data() {
        return {
            dados: {
                name: "",
                email: "",
                password: "",
                cPassword: "",
                nivel_acesso: "1",                
            },  
            nivel_acesso_options: [
                { value: '0', label: '0 - Administrador' },
                { value: '1', label: '1 - Cliente' }
            ],      
        }
    },
    created() {
     
    },
    computed: {    
     
    },
    methods: {
        cadastrar() {   
            if(this.dados.password != this.dados.cPassword){
                this.$q.dialog({
                    title: "Erro",
                    message: "A confirmação de senha não confere com a senha digitada. Verifique se ambas as senhas são iguais e tente novamente.",
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
            this.$q.loading.show({ message: "Cadastrando usuário..." });
            this.$api.post("auth/cadastrar", { ...this.dados })
                .then(response => {
                    this.$q.loading.hide();                     
                    if (response.data.status == false) {
                        this.$q.dialog({
                            title: "Erro!",
                            message: response.data.message,
                            html: true,
                            class: "bg-negative text-white",
                            ok: {
                                push: true,
                                color: "white",
                                textColor: "black"
                            },
                        });
                        return false;
                    }else {
                        this.$q.loading.hide();
                        this.$q.dialog({
                            title: "Confirmação",
                            message: "Usuário cadastrado com sucesso!!!",
                            html: true,
                            class: "bg-positive text-white",
                            persistent: true,                           
                        })
                        this.$router.push({ name: 'login' });
                    }
                })
                .catch(error => {   
                    this.$q.loading.hide();    
                    // Erros de validação
                    if (error.response.status === 422) {
                        this.$q.dialog({
                            title: "Erro!",
                            message: error.response.data.message,
                            html: true,
                            class: "bg-negative text-white",
                            ok: {
                                push: true,
                                color: "white",
                                textColor: "black"
                            },
                        });
                    }                     
                })
                
        },        
    }
}
</script>
