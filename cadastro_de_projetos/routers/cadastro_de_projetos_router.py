from decimal import Decimal
from enum import Enum
from typing import List, Optional
from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel, Field
from sqlalchemy.orm import Session
from cadastro_de_projetos.models.cadastro_de_projetos_model import CadastroDeProjetos
from shared.dependencies import get_db

router = APIRouter(prefix="/cadastro-de-projetos")


class StatusEnum(str, Enum):
    ativo = 'ativo'
    pausado = 'pausado'
    finalizado = 'finalizado'


class CadastrarProjetoResponse(BaseModel):
    id: int
    name: str  # Corrigido para coincidir com o model
    description: str  # Corrigido para coincidir com o model
    status: StatusEnum

    class Config:
        from_attributes = True  # Substitui orm_mode no Pydantic v2
        json_encoders = {
            Decimal: float
        }


class CadastrarProjetoRequest(BaseModel):
    name: str = Field(min_length=1)  # Corrigido
    description: str = Field(min_length=3)  # Corrigido
    status: StatusEnum = StatusEnum.ativo  # Valor padrão


# GET - Buscar projeto por ID
@router.get("/{id}", response_model=CadastrarProjetoResponse)
def pegar_projeto(id: int, db: Session = Depends(get_db)) -> CadastrarProjetoResponse:
    projeto = db.query(CadastroDeProjetos).filter(CadastroDeProjetos.id == id).first()
    if not projeto:
        raise HTTPException(status_code=404, detail="Projeto não encontrado")
    return projeto


# GET - Listar todos os projetos
@router.get("/", response_model=List[CadastrarProjetoResponse])
def listar_projetos(db: Session = Depends(get_db)) -> List[CadastrarProjetoResponse]:
    projetos = db.query(CadastroDeProjetos).all()
    return projetos


# POST - Criar novo projeto
@router.post("/", response_model=CadastrarProjetoResponse)
def criar_projeto(projeto: CadastrarProjetoRequest, db: Session = Depends(get_db)) -> CadastrarProjetoResponse:
    novo_projeto = CadastroDeProjetos(
        name=projeto.name,
        description=projeto.description,
        status=projeto.status
    )
    db.add(novo_projeto)
    db.commit()
    db.refresh(novo_projeto)
    return novo_projeto


# PUT - Atualizar projeto
@router.put("/{id}", response_model=CadastrarProjetoResponse)
def atualizar_projeto(id: int, projeto: CadastrarProjetoRequest,
                      db: Session = Depends(get_db)) -> CadastrarProjetoResponse:
    projeto_existente = db.query(CadastroDeProjetos).filter(CadastroDeProjetos.id == id).first()
    if not projeto_existente:
        raise HTTPException(status_code=404, detail="Projeto não encontrado")

    projeto_existente.name = projeto.name
    projeto_existente.description = projeto.description
    projeto_existente.status = projeto.status

    db.commit()
    db.refresh(projeto_existente)
    return projeto_existente


# DELETE - Deletar projeto
@router.delete("/{id}")
def deletar_projeto(id: int, db: Session = Depends(get_db)):
    projeto = db.query(CadastroDeProjetos).filter(CadastroDeProjetos.id == id).first()
    if not projeto:
        raise HTTPException(status_code=404, detail="Projeto não encontrado")

    db.delete(projeto)
    db.commit()
    return {"message": "Projeto deletado com sucesso"}
