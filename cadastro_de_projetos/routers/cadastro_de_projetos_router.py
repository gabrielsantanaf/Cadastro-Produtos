from decimal import Decimal
from enum import Enum
from typing import List
from fastapi import APIRouter, HTTPException
from pydantic import BaseModel, Field
from tortoise.exceptions import IntegrityError
from cadastro_de_projetos.models.cadastro_de_projetos_model import CadastroDeProjetos

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

    class Config:
        from_attributes = True
        json_encoders = {
            Decimal: float
        }


class CadastrarProjetoRequest(BaseModel):
    name: str = Field(min_length=1)
    description: str = Field(min_length=3)
    status: StatusEnum = StatusEnum.ativo


@router.get("/", response_model=List[CadastrarProjetoResponse])
async def listar_projetos() -> List[CadastrarProjetoResponse]:
    projeto = await CadastroDeProjetos().all()
    return [CadastrarProjetoResponse.model_validate(p) for p in projeto]

@router.get("/{projeto_id}", response_model=CadastrarProjetoResponse)
async def pegar_projeto(projeto_id: int):
    projeto = await CadastroDeProjetos.get_or_none(id=projeto_id)
    if not projeto:
        raise HTTPException(status_code=404, detail="Projeto não encontrado")
    return projeto


@router.post("/", response_model=CadastrarProjetoResponse)
async def criar_projeto(projeto: CadastrarProjetoRequest) -> CadastrarProjetoResponse:
    try:
        novo_projeto = await CadastroDeProjetos.create(
            name=projeto.name,
            description=projeto.description,
            status=projeto.status
        )
        return CadastrarProjetoResponse.model_validate(novo_projeto)
    except IntegrityError:
        raise HTTPException(status_code=400, detail="Já existe um projeto como esse")
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.put("/{projeto_id}", response_model=CadastrarProjetoResponse)
async def atualizar_projeto(projeto_id: int, body: CadastrarProjetoRequest) -> CadastrarProjetoResponse:
    projeto = await CadastroDeProjetos.get_or_none(id=projeto_id)

    if not projeto:
        raise HTTPException(status_code=404, detail="Projeto não encontrado")

    update_projeto = body.model_dump(exclude_unset=True)

    await projeto.update_from_dict(update_projeto)
    await projeto.save()

    return CadastrarProjetoResponse.model_validate(projeto, from_attributes=True)

@router.delete("/{projeto_id}")
async def deletar_projeto(projeto_id: int):
    projeto = await CadastroDeProjetos.get_or_none(id=projeto_id)

    if not projeto:
        raise HTTPException(status_code=404, detail="Projeto não encontrado")

    await projeto.delete()

    return {"message": "Projeto deletado com sucesso"}
