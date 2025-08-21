from tortoise import fields
from tortoise.models import Model

class Usuario(Model):
    id = fields.IntField(pk=True)
    nome = fields.CharField(max_length=100)
    email = fields.CharField(max_length=150, unique=True)
    senha = fields.CharField(max_length=200)  # senha hash
    criado_em = fields.DatetimeField(auto_now_add=True)

    class Meta:
        table = "usuarios"

    def __str__(self):
        return self.nome
