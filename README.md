# Como iniciar sua aplicação Vue - Quasar e Laravel
### Este guia rápido irá te mostrar como iniciar sua aplicação Quasar e Laravel, detalhando os passos para rodar o frontend e o backend.

### Pré-requisitos
### Antes de começar, certifique-se de que você tem instalado em sua máquina:

```bash
Node.js: Necessário para o frontend Quasar.
Composer: Necessário para o backend Laravel.
Docker
```

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



