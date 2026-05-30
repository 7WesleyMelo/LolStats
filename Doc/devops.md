# DevOps

## Stack

- Docker
- Docker Compose
- Nginx

## Containers

- app (Laravel)
- nginx
- postgres
- redis
- frontend

## Comandos

docker-compose up -d
docker-compose exec app php artisan migrate

## Produção

- Docker Swarm (ideal no seu caso)
- Logs centralizados
- Backup DB'

```md
## Scheduler Container

Recomendado rodar o scheduler em um container separado:

scheduler:
  build: .
  command: php artisan schedule:work
  depends_on:
    - redis
    - postgres

## Workers

workers:
  build: .
  command: php artisan queue:work
  depends_on:
    - redis