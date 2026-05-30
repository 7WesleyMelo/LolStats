# LOL Analytics Platform

## 🧠 Visão Geral
Projeto para análise de dados do League of Legends utilizando a API oficial da Riot.

## 🏗️ Arquitetura

- Frontend: Next.js
- Backend: Laravel (API)
- Banco: PostgreSQL
- Cache/Fila: Redis
- Workers: Laravel Queue
- Scheduler: Laravel Scheduler
- Infra: Docker

## 🔄 Fluxo de Dados

1. Scheduler agenda tarefas
2. Jobs são enviados para fila
3. Workers processam:
   - Buscar partidas
   - Processar dados
   - Calcular estatísticas
4. API entrega dados prontos para o frontend

## 🐳 Docker (estrutura)

- nginx
- php-fpm (Laravel)
- postgres
- redis
- node (Next.js)

## 📊 Banco de Dados (principais tabelas)

- champions
- items
- matches
- match_participants
- builds
- runes
- stats
- matchups

## ⚙️ Filas / Workers / Scheduler

### Scheduler
Executa tarefas programadas:
- Buscar novas partidas
- Atualizar dados por patch

### Workers
Processam:
- Partidas em lote
- Estatísticas
- Builds e runas

## 🚀 Roadmap

### Fase 1 (MVP)
- Listagem de campeões
- Builds mais usadas
- Win rate

### Fase 2
- Matchups
- Filtros por elo/região

### Fase 3
- Machine Learning
- Recomendações inteligentes

## 🧩 Agentes Especialistas

### Backend (Laravel)
Responsável por:
- Integração com API Riot
- Processamento de dados
- Segurança

### Frontend (Next.js)
Responsável por:
- UI/UX
- SEO
- Consumo da API

### Data Engineer
Responsável por:
- Modelagem de dados
- Performance
- Estatísticas

### DevOps
Responsável por:
- Docker
- Deploy
- Escalabilidade

### Riot API Specialist
Responsável por:
- Limites de API
- Estratégias de coleta
- Otimização de requisições

## 🔐 Segurança

- API Key da Riot apenas no backend
- Uso de cache (Redis)
- Rate limiting interno

## 📌 Observações

- Dados devem ser versionados por patch
- Evitar cálculos em tempo real
- Sempre usar dados pré-processados
