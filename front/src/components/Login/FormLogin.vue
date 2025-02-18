<template>
    <div :class="$q.screen.lt.sm ? 'row' : 'row q-pt-xl'">
        <div :class="$q.screen.lt.sm ? 'col-12 col-md-4 offset-md-4' : 'col-12 col-md-4 offset-md-4 q-pt-xl'">
            <q-card class="q-mt-xl">
                <q-card-section>
                    <div class="text-h6">
                        Login
                    </div>
                </q-card-section>
                <q-separator/>
                <q-card-section>
                    <form>                    
                        <q-input outlined v-model="dados.email" label="Email:" color="dark"
                            @keyup.enter="fazerLogin" :dense="!$q.screen.lt.sm" :rules="[val => !!val || 'O Email é obrigatorio!!', 'email']">                           
                        </q-input>
                            <q-input outlined  v-model="dados.password" label="Senha:" color="dark" type="password"
                            @keyup.enter="fazerLogin" :dense="!$q.screen.lt.sm" :rules="[val => !!val || 'A senha é obrigatoria!!', val => val.length >= 6 || 'A senha é muito curta!!']">                        
                        </q-input>
                    </form>
                </q-card-section>  
                <q-separator/>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-btn class="col-5" label="Cadastrar Usuário" size="md" @click="$router.push({ name: 'new-user' })" />
                        </div>
                        <div class="col-12 col-md-6">
                            <q-btn class="col-5 full-width" color="positive" size="md" label="Entrar" @click="fazerLogin" />
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
                email: "",
                password: "",
            },        
        }
    },
    created() {
     
    },
    computed: {    
     
    },
    methods: {
        fazerLogin() {   
            this.$q.loading.show({ message: "Verificando usuário..." });
            this.$api.post("auth/login", { ...this.dados })
                .then(resposta => {
                    let dadosResposta = resposta.data;
                    let dataAtual = new Date();
                    dataAtual.setMinutes(dataAtual.getMinutes() + 100); 
                    let expires_at = dataAtual.toJSON().slice(0,19);                   
                    if (dadosResposta.status) {
                        this.$q.loading.hide();
                        localStorage.setItem('token', dadosResposta.token);
                        localStorage.setItem('userName', dadosResposta.userName);
                        localStorage.setItem('expires_at', expires_at);                        
                        this.$router.push('/'); 
                    } else {
                        this.$q.loading.hide();
                        this.$q.dialog({
                            title: "Erro",
                            message: dadosResposta.message,
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
