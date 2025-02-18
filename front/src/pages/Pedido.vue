<template>
    <q-banner dense class="bg-blue-grey-2 q-mb-md text-center">
        <div class="text-h5">Listagem de Pedidos de Viagem</div>
    </q-banner>
    <q-page class="q-pa-md">          
        <pedido />                   
    </q-page>          
</template>

<script>
import Pedido from 'components/Pedido/Pedido';
export default{
    components: {
        Pedido
    },
    created(){
      
    }
}
</script>