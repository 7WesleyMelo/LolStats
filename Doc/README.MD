# LOL Analytics Platform

Plataforma para analise de dados do League of Legends com backend Laravel, frontend Next.js, PostgreSQL e Redis.

## Estado atual

- Controllers API organizados:
  - `CampeaoController`
  - `ItemController`
  - `FeiticoInvocadorController`
  - `RunaController`
  - `PatchController`
- Command geral de sincronizacao criado: `lol:sincronizar-estaticos`
- Scheduler registrado para executar sincronizacao de hora em hora

## Subida do ambiente (Docker)

Na raiz do projeto:

```bash
docker compose up -d --build
```

## Setup backend

```bash
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

## Sincronizacao de dados estaticos

```bash
docker compose exec app php artisan lol:sincronizar-estaticos
```

O comando sincroniza:
- Campeoes
- Itens
- Feiticos de invocador
- Runas
- Patch ativo na tabela `patches`

## Scheduler

Container dedicado ja previsto no `docker-compose.yml`:

```bash
docker compose up -d scheduler
```

Verificacao manual:

```bash
docker compose exec app php artisan schedule:list
```

## Observacoes de ambiente local (fora do Docker)

Se rodar comandos direto no host, ajuste `.env` para hosts reais de banco/redis.  
Com `DB_HOST=postgres` e `REDIS_HOST=redis`, os comandos esperam a rede Docker.
