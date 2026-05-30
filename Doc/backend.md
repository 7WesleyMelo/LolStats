# Backend (Laravel)

## Responsabilidades

- Integração com Riot API
- Processamento de dados
- Exposição de endpoints

## Estrutura sugerida

app/
├── Services/
├── Jobs/
├── Actions/
├── DTOs/

## Exemplo de fluxo

Controller → Service → Job → Worker → DB

## Filas

- Redis driver
- Jobs:
    - FetchMatchesJob
    - ProcessMatchJob
    - CalculateStatsJob

## Scheduler (Agendador)

O Laravel Scheduler é responsável por **orquestrar a coleta e atualização de dados** da Riot API.

Ele NÃO processa dados diretamente — apenas dispara Jobs para a fila.

### Princípio

Scheduler → cria Jobs → Workers executam

### Exemplo de configuração

app/Console/Kernel.php:

```php
protected function schedule(Schedule $schedule)
{
    // Coleta novas partidas a cada 5 minutos
    $schedule->job(new FetchMatchesJob)->everyFiveMinutes();

    // Processa partidas pendentes
    $schedule->job(new ProcessMatchesJob)->everyMinute();

    // Recalcula estatísticas
    $schedule->job(new CalculateStatsJob)->hourly();

    // Atualiza dados estáticos por patch
    $schedule->job(new SyncStaticDataJob)->daily();
}