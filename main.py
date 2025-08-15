import uvicorn
from fastapi import FastAPI
from shared.database import Base, engine

# ⚠️ IMPORTANTE: Importar o modelo ANTES de criar as tabelas
from cadastro_de_projetos.models.cadastro_de_projetos_model import CadastroDeProjetos
from cadastro_de_projetos.routers import cadastro_de_projetos_router

app = FastAPI(title="API de Cadastro de Projetos")


@app.on_event("startup")
async def startup_event():
    try:
        print("🔄 Criando tabelas no banco de dados...")

        # Verificar se o modelo foi importado
        print(f"📋 Modelos encontrados: {Base.metadata.tables.keys()}")

        # Criar todas as tabelas
        Base.metadata.create_all(bind=engine)

        print("✅ Banco de dados configurado com sucesso!")

        # Verificar se a tabela foi criada (PostgreSQL)
        with engine.connect() as conn:
            from sqlalchemy import text
            result = conn.execute(text("SELECT tablename FROM pg_tables WHERE schemaname = 'public';"))
            tables = [row[0] for row in result]
            print(f"📊 Tabelas criadas: {tables}")

    except Exception as e:
        print(f"❌ Erro ao configurar banco: {e}")


@app.get("/")
def hello() -> str:
    return "Olá Bahia!"


@app.get("/health")
def health_check():
    try:
        with engine.connect() as conn:
            # Para PostgreSQL
            from sqlalchemy import text
            result = conn.execute(text("SELECT tablename FROM pg_tables WHERE schemaname = 'public';"))
            tables = [row[0] for row in result]
            return {"status": "ok", "database": "connected", "tables": tables}
    except Exception as e:
        return {"status": "error", "database": str(e)}


app.include_router(cadastro_de_projetos_router.router, tags=["Projetos"])

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8003)