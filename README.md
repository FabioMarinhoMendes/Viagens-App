# Como iniciar sua aplicação Vue - Quasar e Laravel
### Este guia rápido irá te mostrar como iniciar sua aplicação Quasar e Laravel, detalhando os passos para rodar o frontend e o backend.

### Pré-requisitos
### Antes de começar, certifique-se de que você tem instalado em sua máquina:

```bash
Node.js: Necessário para o frontend Quasar.
Composer: Necessário para o backend Laravel.
Docker
```

### Iniciando o projeto com o Docker
### Navegue ate o frontend: cd front
###### execute o comando
```bash
docker-compose -f docker-compose.frontend.yml up -d
```

### Navegue ate o backend: cd api
###### execute o comando
```bash
docker-compose -f docker-compose.backend.yml up -d
```
### Depois rode as migrates, para criar as tabelas no banco de dados

```bash
docker-compose -f docker-compose.backend.yml exec app php artisan migrate
```

#### Se não quiser usar o Docker ou em caso de erro, siga os passos:

### Iniciando o Frontend Quasar
###### Navegue até o diretório do frontend: cd front

###### Instale suas dependências
```bash
npm install
```

### Iniciando o Backend Laravel
###### Navegue até o diretório do backend: cd api

###### Instale suas dependências
```bash
composer install
```

### Configurações
```bash
Configure seu banco de dados e e-mail no arquivo .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projeto_viagem
DB_USERNAME=
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
```

### Execute suas migrations
```bash
php artisan migrate
```

### Inicie a aplicação em ambiente de desenvolvimento
```bash
php artisan serve
```

#### Para usar o sistema, recomendo que você cadastre novos usuarios, 1 com o nivel de acesso (0 - administrador) e 2 ou mais com nivel de acesso (1 - cliente)
#### O usuario administrador vai conseguir criar, editar, alterar status, cancelar e ver todos os pedidos sem restriçoes
#### O usuário cliente pode criar, editar, cancelar e ver apenas os proprios pedidos com algumas regras inplementadas


## Testes

#### Para executar os testes unitarios, você pode executar um por um ou todos de uma vez
#### Para executar todos de uma vez, navegue ate o diretorio do backend: 
```bash
cd api
```
### Execute o comando
```bash
php artisan test
```
#### Para executar um por um, use os comandos
```bash
php artisan test --filter=PedidoControllerTest::test_login_com_jwt
php artisan test --filter=PedidoControllerTest::test_criar_pedido
php artisan test --filter=PedidoControllerTest::test_criar_pedido_verificar_exception
php artisan test --filter=PedidoControllerTest::test_alterar_status_falha_nivel_acesso
php artisan test --filter=PedidoControllerTest::test_alterar_status_falha_administrador
php artisan test --filter=PedidoControllerTest::test_obter_pedido_por_id
php artisan test --filter=PedidoControllerTest::test_obter_pedido_por_id_falha
php artisan test --filter=PedidoControllerTest::test_cancelar_pedido_tempo_para_cancelar_excedido_falha
php artisan test --filter=PedidoControllerTest::test_cancelar_pedido_status_cancelado_falha
```





