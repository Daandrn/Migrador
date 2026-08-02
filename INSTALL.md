# Instalação

## Pré-requisitos

Antes de iniciar, certifique-se de possuir:

- Git
- Docker
- Docker Compose

> Todo o ambiente da aplicação foi desenvolvido utilizando Docker. Não é necessário instalar PHP, Composer, PostgreSQL ou Node.js na máquina hospedeira.

---

# Obtendo o código-fonte

Clone o repositório:

```bash
git clone https://github.com/Daandrn/Migrador.git
```

Acesse a pasta do projeto:

```bash
cd Migrador
```

---

# Configurando o ambiente

Crie o arquivo `.env` a partir do arquivo de exemplo.

### Linux

```bash
cp .env-example .env
```

Abra o arquivo `.env` e altere as configurações necessárias.

Na configuração padrão, o banco PostgreSQL é criado automaticamente pelo Docker Compose, portanto normalmente não é necessário alterar as configurações de conexão.

---

# Criando os containers

Execute:

```bash
docker compose up -d
```

Na primeira inicialização serão criados os containers da aplicação.

O entrypoint executará automaticamente:

- `composer install`

> A instalação das dependências pode levar alguns minutos na primeira execução.

---

# Instalando as dependências do Front-end

Acesse o container da aplicação:

```bash
docker compose exec app bash
```

Instale as dependências do Node:

```bash
npm install
```

Compile os arquivos do Vue:

```bash
npm run build
```

---

# Criando o banco da aplicação

Ainda dentro do container da aplicação, execute:

```bash
php artisan migrate
php artisan db:seed
```

---

# Estrutura do ambiente

Após a instalação, os seguintes serviços estarão disponíveis:

- **Aplicação Laravel**
- **PostgreSQL**
- **Nginx**

Todos executando em containers Docker independentes.

---

# Primeira utilização

Após a instalação:

Faça Login com o usuário admin
email: admin@gmail.com
senha: admin

1. Cadastre um cliente.
2. Informe as credenciais do banco de dados do cliente.
3. Execute as verificações desejadas.
