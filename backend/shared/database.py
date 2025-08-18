from tortoise import Tortoise

DATABASE_CONFIG = {
    "connections": {

        "default": {
            "engine": "tortoise.backends.asyncpg",
            "credentials": {
                "host": "postgres",
                "port": 5432,
                "user": "postgres",
                "password": "postgres123",
                "database": "projects_db",
            }
        }
    },
    "apps": {
        "models": {
            "models": ["cadastro_de_projetos.models.cadastro_de_projetos_model", "aerich.models"],
            "default_connection": "default",
        }
    },
}

async def init_database():
    await Tortoise.init(config=DATABASE_CONFIG)
    await Tortoise.generate_schemas()

async def close_database():
    await Tortoise.close_connections()