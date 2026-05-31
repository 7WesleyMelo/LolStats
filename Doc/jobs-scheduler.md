# Jobs e Scheduler (Laravel + Docker)

Este documento reúne os comandos do projeto relacionados a filas (`queue`) e agendamento (`scheduler`).

## Serviços envolvidos

- `app`: container principal da aplicação Laravel (`lol_backend`)
- `worker`: processador de jobs da fila (`lol_worker`)
- `scheduler`: executor do scheduler Laravel (`lol_scheduler`)
- `redis`: backend de fila/cache (`lol_redis`)

## Estado dos containers

```powershell
docker compose ps

docker compose ps -a
```

## Subir/parar serviços de jobs e scheduler

```powershell
# Subir apenas app + worker + scheduler

docker compose up -d app worker scheduler

# Parar apenas processamento assíncrono

docker compose stop worker scheduler

# Reiniciar worker/scheduler

docker compose restart worker scheduler
```

## Comandos de fila (queue)

Executar no serviço `app`:

```powershell
# Reinício gracioso dos workers (finaliza o job atual e reinicia)
docker compose exec app php artisan queue:restart

# Limpar jobs pendentes da fila padrão
docker compose exec app php artisan queue:clear

# Rodar worker manualmente no app (debug/local)
docker compose exec app php artisan queue:work

# Rodar worker em modo listen (menos performático, útil em dev)
docker compose exec app php artisan queue:listen
```

## Comandos de scheduler

Executar no serviço `app`:

```powershell
# Executa uma passada do scheduler (cron-style)
docker compose exec app php artisan schedule:run

# Mantém scheduler em execução contínua

docker compose exec app php artisan schedule:work

# Lista eventos agendados

docker compose exec app php artisan schedule:list
```

## Horizon (somente se estiver habilitado no projeto)

```powershell
# Encerra processos do Horizon de forma controlada
docker compose exec app php artisan horizon:terminate
```

## Emergência: parar jobs imediatamente

```powershell
# Para containers que processam jobs/schedule agora
docker compose stop worker scheduler
```

Se necessário, depois de subir novamente:

```powershell
docker compose exec app php artisan queue:restart
docker compose exec app php artisan queue:clear
```

## Logs úteis

```powershell
# Log principal do Laravel
Get-Content .\backend\storage\logs\laravel.log -Tail 200

# Seguir log em tempo real
Get-Content .\backend\storage\logs\laravel.log -Wait

# Logs dos containers

docker compose logs -f worker

docker compose logs -f scheduler

docker compose logs -f app
```

## Jobs agendados atualmente no projeto

Definidos em `backend/routes/console.php`:

- `lol:sincronizar-estaticos` -> `hourly()`
- `lol:seed-ranked-players --pages=2` -> `everyThirtyMinutes()`
- `lol:fetch-player-matches --jogadores=100 --partidas=20` -> `everyTenMinutes()`
- `lol:agregar-estatisticas-campeoes` -> `hourlyAt(5)`

Para executar manualmente os comandos agendados:

```powershell
docker compose exec app php artisan lol:sincronizar-estaticos
docker compose exec app php artisan lol:seed-ranked-players --pages=2
docker compose exec app php artisan lol:fetch-player-matches --jogadores=100 --partidas=20
docker compose exec app php artisan lol:agregar-estatisticas-campeoes
```

Variação comum para reduzir carga em execução manual:

```powershell
docker compose exec app php artisan lol:fetch-player-matches --jogadores=10 --partidas=20
```

## Diagnóstico rápido

```powershell
# Verificar containers ativos

docker compose ps

# Verificar scheduler configurado

docker compose exec app php artisan schedule:list

# Reiniciar workers de forma segura

docker compose exec app php artisan queue:restart

# Checar logs

docker compose logs --tail=100 worker scheduler app
```
