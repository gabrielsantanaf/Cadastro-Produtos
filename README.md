📌 Cadastro de Projetos

Este é um sistema de cadastro de projetos com backend em FastAPI (Python) e frontend em PHP (Apache).
O objetivo é permitir que usuários façam o gerenciamento de projetos de forma organizada, com operações de cadastro, edição, listagem e exclusão.

⚙️ Tecnologias Utilizadas
🔹 Backend

Python 3.11+

FastAPI
 – Framework web rápido e moderno

Tortoise ORM
 – ORM para manipulação do banco

Uvicorn
 – Servidor ASGI para rodar o FastAPI

🔹 Frontend

PHP 8+

Apache

🔹 Infraestrutura

Docker

Docker Compose

Banco de dados configurável (SQLite, PostgreSQL ou MySQL)

📂 Estrutura do Projeto
CadastroProjetos/
│── backend/
│   ├── cadastro_de_projetos/
│   │   ├── models/                  # Modelos (Tortoise ORM)
│   │   │   └── cadastro_de_projetos_model.py
│   │   ├── routers/                 # Rotas do FastAPI
│   │   │   └── cadastro_de_projetos_router.py
│   ├── shared/                      # Configurações compartilhadas
│   │   └── database.py
│   ├── Dockerfile                   # Dockerfile do backend
│   ├── main.py                      # Ponto de entrada FastAPI
│   └── requirements.txt             # Dependências Python
│
│── frontend/
│   ├── apache-config.conf           # Configuração do Apache
│   ├── *.php                        # Arquivos do frontend
│   └── Dockerfile                   # Dockerfile do frontend
│
│── docker-compose.yml               # Orquestração de containers
│── .gitignore
│── README.md

🚀 Como Rodar o Projeto
1. Clonar o repositório
git clone https://github.com/seu-usuario/cadastro-projetos.git
cd cadastro-projetos

2. Rodar com Docker
docker-compose up --build

3. Acessar

Frontend (PHP/Apache): 👉 http://localhost:8080

Backend (FastAPI): 👉 http://localhost:8000

Docs interativas do FastAPI: 👉 http://localhost:8000/docs

📖 Funcionalidades

✅ Listagem de projetos
✅ Criação de projetos
✅ Edição de projetos
✅ Exclusão de projetos
✅ Integração do PHP (frontend) com o FastAPI (backend) via API REST

👨‍💻 Contribuição

Faça um fork do projeto

Crie uma branch com a feature (git checkout -b minha-feature)

Commit suas alterações (git commit -m 'Adiciona minha feature')

Faça push para a branch (git push origin minha-feature)

Abra um Pull Request 🚀

📜 Licença

Este projeto está sob a licença MIT.