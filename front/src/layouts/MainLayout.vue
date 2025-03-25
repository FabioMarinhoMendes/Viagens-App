<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Viagem
        </q-toolbar-title>

        <div>
          <q-btn color="negative" @click="logout" label="Sair"></q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"   
      bordered
    >
      <q-list bordered class="rounded-borders">
        <q-item-label header class="text-center">
            <h1 style="margin: 5px 0; font-size: 26px; line-height: 2rem;">Viagem</h1>
        </q-item-label>

        <q-item clickable to="/" @click="leftDrawerOpen = !leftDrawerOpen">
          <q-item-section avatar>
            <q-icon name="home" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Home</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable to="/pedidos" @click="leftDrawerOpen = !leftDrawerOpen">
          <q-item-section avatar>
            <q-icon name="explore" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Viagens</q-item-label>
          </q-item-section>
        </q-item>
        
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue'
export default defineComponent({
  name: 'MainLayout',
  components: {
    
  },
  data () {
    return {
      leftDrawerOpen: false
    }
  },
  created(){
    
  },
  methods: {
    logout() {
      this.$q.dialog({
        title: "Sair do Sistema",
        message: "Deseja realmente sair do sistema?",
        html: true,
        class: "bg-blue-3 q-pa-xs dialog-message",
        persistent: true,        
        cancel: {
          push: true,
          color: "negative",
          label: "Cancelar",
        },
        ok: {
          push: true,
          color: "positive",
          label: "Sair do Sistema",
        },
      }).onOk(() => {
        this.$q.loading.show({ message: "Saindo do sistema..." })
          let token = localStorage.getItem('token');
          this.$api.post(`auth/logout`, {}, {
            headers: {
              Authorization: 'Bearer ' + token
            }
          })
          .then(response => {
            this.$q.loading.hide();
            let dadosResposta = response.data;
            if (dadosResposta.status) {
              localStorage.removeItem("token");
              localStorage.removeItem("expires_at");
              this.$router.push({ name: 'login' });
            }
          }).catch(error => {
            this.$q.loading.hide();
            this.$q.dialog({
                title: "Erro",
                message: "Erro ao sair do sistema!!",
                html: true,
                class: "bg-negative text-white",
                ok: {
                    push: true,
                    color: "white",
                    textColor: "black"
                },
            });
          })
      }).onCancel(() => { });
    },
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    }
  }
})
</script>
<style>
.dialog-message .q-dialog__message { 
  font-size: 1.2em; 
}
</style>