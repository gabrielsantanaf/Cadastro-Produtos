from enum import Enum

from tortoise.models import Model
from tortoise import fields

class Status(Enum):
    ATIVO = "ativo"
    PAUSADO = "pausado"
    FINALIZADO = "finalizado"

class CadastroDeProjetos(Model):

    id = fields.IntField(pk=True)
    name = fields.CharField(max_length=100)
    description = fields.TextField()
    status = fields.CharEnumField(Status, default=Status.ATIVO)
    created_at = fields.DatetimeField(auto_now_add=True)
    updated_at = fields.DatetimeField(auto_now=True)

    class Meta:
        table = "cadastro_de_projetos"

    def __str__(self):
        return f"{self.name}"
