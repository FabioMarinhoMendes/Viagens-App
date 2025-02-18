# Use a imagem base do Node.js
FROM node:20-alpine

# Defina o diretório de trabalho dentro do contêiner
WORKDIR /app

# Copie os arquivos package.json e package-lock.json (se existir) para o diretório de trabalho
COPY package*.json ./

RUN rm -rf node_modules

# Instale as dependências
RUN npm install -g npm@11.1.0 --save-dev @quasar/cli

RUN npm install --save-dev esbuild

# Copie o restante do código do aplicativo para o diretório de trabalho
COPY . .

# Construa o aplicativo Quasar para produção
RUN npx quasar build

# Use uma imagem Nginx para servir o conteúdo estático
FROM nginx:alpine

# Copie o conteúdo construído do aplicativo Quasar para o diretório de arquivos estáticos do Nginx
COPY --from=0 /app/dist/spa /usr/share/nginx/html

# Exponha a porta 8000 para acessar o aplicativo
EXPOSE 9000

# Comando para iniciar o Nginx
CMD ["nginx", "-g", "daemon off;"]