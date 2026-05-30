# Arquitetura

## Visão geral

[Next.js] → [Laravel API] → [PostgreSQL]
→ [Redis]
→ [Workers]

## Fluxo

1. Scheduler agenda coleta
2. Jobs são enviados para fila
3. Workers:
    - Buscam partidas
    - Processam dados
    - Calculam estatísticas
4. API entrega dados prontos

## Fluxo completo

Scheduler → Jobs → Queue (Redis) → Workers → Banco

Usuário → API → Dados já processados

## Princípios

- Event-driven (fila)
- Cache-first
- Separação clara de responsabilidades

Idioma a ser priorizado pt-br