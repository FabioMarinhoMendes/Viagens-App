<template>
  <q-page>
    <div class="text-h5 q-pa-md text-center">
        Olá, {{ userName }}
    </div>
    <div class="grid-atalhoshome q-pb-md q-mt-xl">       
        <RouterLink style="height: 170px;border:1px solid;" 
            class="grid-atalhoshome-item" to="/">
            <div style="height:60px;position:relative;overflow:hidden;">
                <q-icon size="60px" name="home" />
            </div>
            <div>home</div>
        </RouterLink>  
        
        <RouterLink style="height: 170px;border:1px solid;" 
            class="grid-atalhoshome-item" to="/pedidos">
            <div style="height:53px;position:relative;" class="q-ml-xl">
                <q-icon size="53px" name="explorer" />
            </div>
            <div>Viagens</div>
        </RouterLink>  
    </div>
  </q-page>
</template>

<script>
export default {   
    data() {
        return {
                  
        }
    },    
    computed: {    
        userName(){
            return localStorage.getItem('userName');
        }
    },
    
       
}
</script>

<style>
.grid-atalhoshome {
    display: grid;
    grid-template-columns: repeat(2, 200px);
    grid-template-rows: repeat(2, 200px);
    gap: 2px;
    row-gap: 5px;
    column-gap: 5px;
    justify-content: center;
}

.grid-atalhoshome-item {
    cursor: pointer;
    box-sizing: border-box;
    justify-content: center;
    text-align: center;
    background: #EEE;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    align-content: center;
    align-items: center;
    justify-content: center;
    -webkit-touch-callout: none;
    /* iPhone OS, Safari */
    -webkit-user-select: none;
    /* Chrome, Safari 3 */
    -khtml-user-select: none;
    /* Safari 2 */
    -moz-user-select: none;
    /* Firefox */
    -ms-user-select: none;
    /* IE10+ */
    user-select: none;
    /* Possível implementação no futuro */
    text-decoration: none;
    color: var(--q-primary)
}

.grid-atalhoshome-item:hover {
    background: var(--q-primary);
    color: white;
}
</style>