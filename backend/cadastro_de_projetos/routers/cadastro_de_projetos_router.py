from datetime import datetime
from decimal import Decimal
from enum import Enum
from typing import List
from fastapi import APIRouter, HTTPException
from pydantic import BaseModel, Field
from tortoise.exceptions import IntegrityError
from ..models.cadastro_de_projetos_model import CadastroDeProjetos

router = APIRouter(prefix="/cadastro-de-projetos", tags=["Operações com o cadastro"])


class StatusEnum(str, Enum):
    ativo       = 'ativo'
    pausado     = 'pausado'
    finalizado  = 'finalizado'


class CadastrarProjetoResponse(BaseModel):
    id:          int
    name:        str
    description: str
    status:      StatusEnum
    created_at:  datetime

    class Config:
        from_attributes = True
        json_encoders = {
            Decimal: float
        }

class ApiResponse(BaseModel):
    status: int
    data: List[CadastrarProjetoResponse] | CadastrarProjetoResponse | dict | None = None
    message: str = ""

class CadastrarProjetoRequest(BaseModel):
    name: str = Field(min_length=1)
    description: str = Field(min_length=3)
    status: StatusEnum = StatusEnum.ativo


@router.get("/")
async def listar_projetos():
    try:
        projetos = await CadastroDeProjetos.all()
        data = [CadastrarProjetoResponse.model_validate(p) for p in projetos]
        return {
            "status": 200,
            "data": data,
            "message": "Projetos listados com sucesso"
        }
    except Exception as e:
        return {
            "status": 500,
            "data": {"error": str(e)},
            "message": "Erro interno do servidor"
        }


@router.get("/{projeto_id}")
async def pegar_projeto(projeto_id: int):
    try:
        projeto = await CadastroDeProjetos.get_or_none(id=projeto_id)
        if not projeto:
            return {
                "status": 404,
                "data": {"error": "Projeto não encontrado"},
                "message": "Projeto não encontrado"
            }

        return {
            "status": 200,
            "data": CadastrarProjetoResponse.model_validate(projeto),
            "message": "Projeto encontrado"
        }
    except Exception as e:
        return {
            "status": 500,
            "data": {"error": str(e)},
            "message": "Erro interno do servidor"
        }


@router.post("/")
async def criar_projeto(projeto: CadastrarProjetoRequest):
    try:
        novo_projeto = await CadastroDeProjetos.create(
            name=projeto.name,
            description=projeto.description,
            status=projeto.status
        )
        return {
            "status": 201,
            "data": CadastrarProjetoResponse.model_validate(novo_projeto),
            "message": "Projeto criado com sucesso"
        }
    except IntegrityError:
        return {
            "status": 400,
            "data": {"error": "Já existe um projeto como esse"},
            "message": "Erro de validação"
        }
    except Exception as e:
        return {
            "status": 500,
            "data": {"error": str(e)},
            "message": "Erro interno do servidor"
        }


@router.put("/{projeto_id}")
async def atualizar_projeto(projeto_id: int, body: CadastrarProjetoRequest):
    try:
        projeto = await CadastroDeProjetos.get_or_none(id=projeto_id)

        if not projeto:
            return {
                "status": 404,
                "data": {"error": "Projeto não encontrado"},
                "message": "Projeto não encontrado"
            }

        update_projeto = body.model_dump(exclude_unset=True)
        await projeto.update_from_dict(update_projeto)
        await projeto.save()

        return {
            "status": 200,
            "data": CadastrarProjetoResponse.model_validate(projeto, from_attributes=True),
            "message": "Projeto atualizado com sucesso"
        }
    except Exception as e:
        return {
            "status": 500,
            "data": {"error": str(e)},
            "message": "Erro interno do servidor"
        }


@router.delete("/{projeto_id}")
async def deletar_projeto(projeto_id: int):
    try:
        projeto = await CadastroDeProjetos.get_or_none(id=projeto_id)

        if not projeto:
            return {
                "status": 404,
                "data": {"error": "Projeto não encontrado"},
                "message": "Projeto não encontrado"
            }

        await projeto.delete()

        return {
            "status": 200,
            "data": None,
            "message": "Projeto deletado com sucesso"
        }
    except Exception as e:
        return {
            "status": 500,
            "data": {"error": str(e)},
            "message": "Erro interno do servidor"
        }