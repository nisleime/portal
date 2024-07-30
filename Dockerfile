# Use uma imagem oficial do Nginx
FROM nginx:alpine

# Copie o arquivo de configuração do Nginx
COPY default.conf /etc/nginx/conf.d/default.conf

# Exponha a porta 80 e 443
EXPOSE 80 443
