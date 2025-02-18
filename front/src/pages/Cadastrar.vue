<template>
    <q-layout >
        <q-page-container>
            <q-page class="bg-light-blue-6 q-px-lg" padding>           
               
                <form-cadastro />
                   
            </q-page>
        </q-page-container>
        
    </q-layout>   
</template>

<script>
import FormCadastro from 'components/Login/FormCadastro';
export default{
    components: {
        FormCadastro
    },
    created(){
      
    }
}
</script>