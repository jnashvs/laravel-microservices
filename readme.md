# Laravel Microservices

Arquitetura de microserviços com Laravel, Docker, Redis Pub/Sub e DDD.

## Arquitetura

```text
┌──────────────┐     ┌──────────────────┐     ┌────────────────────────┐
│  API Gateway │────▶│  Ticket Service  │────▶│  Notification Service  │
│  :8000       │     │  :8100           │     │  :8200                 │
└──────────────┘     └───────┬──────────┘     └────────────┬───────────┘
                             │                             │
                             ▼                             ▼
                     ┌──────────────┐             ┌──────────────┐
                     │   MySQL      │             │    Redis     │
                     │   :3307      │             │    :6379     │
                     └──────────────┘             └──────────────┘

```

## Tech Stack

PHP 8.4 + Laravel
MySQL 8.0 — Base de dados do Ticket Service
Redis — Pub/Sub entre serviços
Docker + Docker Compose
Nginx + PHP-FPM
DDD (Domain-Driven Design)

## 📁 Estrutura do Projeto

laravel-microservices/
├── api-gateway/ # Proxy para os microserviços
├── ticket-service/ # CRUD de tickets (DDD)
│ ├── app/
│ │ ├── Domain/Ticket/ # Entities, ValueObjects, Events, Repositories
│ │ ├── Application/Ticket/ # DTOs, UseCases
│ │ └── Infrastructure/ # Eloquent Repositories, Controllers, Listeners
│ └── ...
├── notification-service/ # Consome eventos e guarda notificações (DDD)
│ ├── app/
│ │ ├── Domain/Notification/ # Entities, Repositories
│ │ ├── Application/ # UseCases
│ │ ├── Infrastructure/ # File Repository, Controllers
│ │ └── Console/Commands/ # Redis Subscriber
│ └── ...
├── docker-compose.yml
└── .env

## Setup

Pré-requisitos
Docker Desktop
Git

1. Clone o repositório
   git clone https://github.com/jnashvs/laravel-microservices.git
   cd laravel-microservices

2. Configure variáveis de ambiente
   cp .env.example .env
   cp ticket-service/.env.example ticket-service/.env
   cp api-gateway/.env.example api-gateway/.env
   cp notification-service/.env.example notification-service/.env

3. Gerar APP_KEYs

cd ticket-service && php artisan key:generate --show
cd ../api-gateway && php artisan key:generate --show
cd ../notification-service && php artisan key:generate --show
cd ..

4. Subir os serviços
   docker-compose up -d --build

## Testar a API - Health Checks

- API Gateway: http://localhost:8000/api/health
- Ticket Service: http://localhost:8100/api/health
- Notification Service: http://localhost:8200/api/health
- Criar um ticket
  curl -X POST http://localhost:8000/api/tickets \
   -H "Content-Type: application/json" \
   -d '{"title": "Bug no login", "description": "Utilizador não consegue fazer login", "priority": "high"}'

- Listar tickets
  curl http://localhost:8000/api/tickets

- Ver um Ticket específico
  curl http://localhost:8000/api/tickets/1

- Listar notificações
  curl http://localhost:8000/api/notifications

## Monitor Redis em tempo real

- docker exec -it redis redis-cli MONITOR

## RedisInsight (UI)

- http://localhost:5540

## Logs dos serviços

- docker logs api-gateway -f
- docker logs ticket-service -f
- docker logs notification-service -f
- docker-compose logs -f

## Fluxo de Eventos

1. Cliente envia POST /api/tickets ao API Gateway (:8000)
2. API Gateway faz proxy para o Ticket Service (:8100)
3. Ticket Service:
   a. Valida os dados (Controller)
   b. Executa CreateTicketUseCase (Application)
   c. Cria a entidade Ticket (Domain)
   d. Guarda no MySQL via EloquentTicketRepository (Infrastructure)
   e. Dispara evento TicketCreated (Domain Event)
   f. Listener publica no Redis canal "ticket.created"
4. Notification Service:
   a. Subscriber (Artisan Command) ouve o canal "ticket.created"
   b. Recebe o payload JSON
   c. Executa CreateNotificationUseCase (Application)
   d. Cria entidade Notification (Domain)
   e. Guarda em ficheiro JSON via FileNotificationRepository (Infrastructure)
5. Cliente consulta GET /api/notifications via API Gateway

## Padrões Utilizados

1. DDD (Domain-Driven Design) — Separação em Domain, Application e Infrastructure
2. Repository Pattern — Abstração do acesso a dados
3. Use Case Pattern — Lógica de negócio isolada
4. Value Objects — Priority e TicketStatus
5. Domain Events — TicketCreated
6. Proxy Pattern — API Gateway como ponto de entrada único
7. Event-Driven Architecture — Redis Pub/Sub entre serviços

## Conclusão

Este projeto demonstra uma arquitetura de microserviços utilizando Laravel, Docker e Redis Pub/Sub, seguindo os princípios de DDD para manter o código organizado e escalável. O API Gateway centraliza as requisições, enquanto os serviços independentes comunicam-se via eventos, permitindo uma evolução desacoplada.
