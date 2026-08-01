# Migrador

Sistema para verificação da integridade, qualidade e consistência de dados durante processos de migração entre bases de dados.

O projeto permite cadastrar verificações SQL, executá-las em bancos de dados externos e registrar os problemas encontrados para auxiliar na análise da qualidade dos dados antes da migração.

---

# Funcionalidades

- Cadastro de clientes
- Conexão dinâmica com bancos de dados externos
- Execução de verificações SQL
- Registro detalhado dos problemas encontrados
- Validação de segurança das consultas SQL
- Execução de múltiplas verificações em um único processo

---

# Tecnologias

## Backend

- PHP 8.3
- Laravel 13

## Frontend

- JavaScript
- Vue 3
- Axios

## Banco de Dados

- PostgreSQL 18

## Servidor Web

- Nginx 1.30

## Ambiente

- Docker
- Docker Compose

---

# Instalação

Consulte o arquivo [INSTALL.md](INSTALL.md).

---

# Estrutura das respostas da API

Todas as respostas da API seguem o mesmo padrão.

```json
{
    "success": true,
    "message": "Processo finalizado.",
    "data": {
        "clients": [
            {
                "id": 1,
                "name": "Cliente A"
            }
        ]
    },
    "errors": [
        {
            "code": "CHECK_EXECUTION_FAILED",
            "message": "Erro ao executar a consulta SQL.",
            "field": null,
            "details": null
        }
    ]
}
```

## Campos

| Campo | Tipo | Descrição |
|--------|------|-----------|
| success | boolean | Indica se a operação foi concluída com sucesso. |
| message | string | Mensagem resumida sobre o resultado da operação. |
| data | object | Dados retornados pela operação. |
| errors | array | Lista de erros ocorridos durante a execução. |

### data

Objeto que contém os dados retornados pela operação.

Exemplos:

```json
{
    "data": {
        "clients": []
    }
}
```

```json
{
    "data": {
        "checks": []
    }
}
```

```json
{
    "data": {
        "verifyErrors": []
    }
}
```

Cada endpoint retorna apenas os recursos relacionados à operação realizada.

### errors

Lista de erros.

Quando não houver erros, será retornado:

```json
[]
```

Cada erro possui a seguinte estrutura:

```json
{
    "code": "CHECK_EXECUTION_FAILED",
    "message": "Erro ao executar a consulta SQL.",
    "field": null,
    "details": null
}
```

| Campo | Descrição |
|--------|-----------|
| code | Código interno do erro. |
| message | Mensagem descritiva. |
| field | Campo relacionado ao erro, quando aplicável. |
| details | Informações adicionais sobre o erro. |

---

# Segurança

As consultas SQL executadas em bancos externos passam por validações antes da execução.

O usuário utilizado para conexão com o banco externo DEVE possuir apenas permissões de leitura.

---
