import re
from datetime import datetime
from decimal import Decimal
from enum import Enum
from typing import List
from wsgiref.validate import validator

from pydantic import BaseModel, Field


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

class User(BaseModel):
    username: str
    password: str

    @validator('username')
    def validate_username(cls, value):
        if not re.match('^([a-z]|[0-9]|@)+$', value):
            raise ValueError('Invalid username')
        return value

