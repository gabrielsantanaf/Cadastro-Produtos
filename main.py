import uvicorn
from fastapi import FastAPI
from tortoise.contrib.fastapi import register_tortoise
from tortoise import connections
from shared.database import DATABASE_CONFIG
from cadastro_de_projetos.routers import cadastro_de_projetos_router

app = FastAPI(
    title="API de Cadastro de Projetos",
    description="API - Cadastro de Projetos",
    version="1.0.0"
)

register_tortoise(
    app,
    config=DATABASE_CONFIG,
    generate_schemas=True,
    add_exception_handlers=True,
)

@app.get("/health")
async def health_check():
    try:
        conn = connections.get("default")
        if conn:
            rows = await conn.execute_query("SELECT tablename FROM pg_tables WHERE schemaname = 'public';")
            tables = [row["tablename"] for row in rows[1]]
            return {"status": "ok", "database": "connected", "tables": tables}
        else:
            return {"status": "error", "database": "no connection"}
    except Exception as e:
        return {"status": "error", "database": str(e)}


app.include_router(cadastro_de_projetos_router.router)

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8003)