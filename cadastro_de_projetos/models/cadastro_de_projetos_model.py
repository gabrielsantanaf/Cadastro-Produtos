from datetime import datetime, timezone
from sqlalchemy import Column, Integer, String, Text, DateTime
from shared.database import Base


class CadastroDeProjetos(Base):
    __tablename__ = "cadastro_projetos"  # Nome mais descritivo

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    name = Column(String(255), nullable=False, index=True)  # Limite de caracteres + índice
    description = Column(Text, nullable=True)  # Explicitamente nullable
    status = Column(String(20), default="ativo", nullable=False)  # Limite de caracteres
    created_at = Column(DateTime, default=lambda: datetime.now(timezone.utc), nullable=False)
    updated_at = Column(DateTime, default=lambda: datetime.now(timezone.utc),
                        onupdate=lambda: datetime.now(timezone.utc), nullable=False)

    def __repr__(self):
        return f"<CadastroDeProjetos(id={self.id}, name='{self.name}', status='{self.status}')>"