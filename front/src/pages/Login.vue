<template>
    <q-layout >
        <q-page-container>
            <q-page class="bg-light-blue-6 q-px-lg" padding>           
               
                <form-login />
                   
            </q-page>
        </q-page-container>
        
    </q-layout>   
</template>

<script>
import FormLogin from 'components/Login/FormLogin';
export default{
    components: {
        FormLogin
    },
    created(){
      
    }
}
</script>