# Laravel Microservices

Microservices architecture built with Laravel, Docker, Redis Pub/Sub and Domain-Driven Design (DDD).

## 📐 Architecture

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
MySQL 8.0 — Ticket Service database
Redis — Pub/Sub messaging between services
Docker + Docker Compose
Nginx + PHP-FPM
Spatie Laravel Data — DTOs with validation
DDD (Domain-Driven Design)

# 📁 Project Structure

laravel-microservices/
├── docker-compose.yml
├── .env
│
├── api-gateway/
│ ├── app/
│ │ └── Http/Controllers/
│ │ ├── TicketProxyController.php # Proxy requests to Ticket Service
│ │ └── NotificationProxyController.php # Proxy requests to Notification Service
│ ├── resources/views/
│ │ └── dashboard.blade.php # Visual dashboard
│ ├── routes/
│ │ ├── api.php
│ │ └── web.php
│ └── Dockerfile
│
├── ticket-service/
│ ├── app/
│ │ ├── Domain/
│ │ │ └── Ticket/
│ │ │ ├── Entities/
│ │ │ │ └── Ticket.php # Aggregate root with factory methods
│ │ │ ├── ValueObjects/
│ │ │ │ ├── Priority.php # low, medium, high
│ │ │ │ └── TicketStatus.php # open, in_progress, closed
│ │ │ ├── Events/
│ │ │ │ ├── TicketCreated.php # Domain event
│ │ │ │ └── EventDispatcherInterface.php
│ │ │ └── Repositories/
│ │ │ └── TicketRepositoryInterface.php
│ │ │
│ │ ├── Application/
│ │ │ └── Ticket/
│ │ │ ├── DTOs/
│ │ │ │ ├── CreateTicketData.php # Spatie Data — request validation
│ │ │ │ └── TicketResponseData.php # Spatie Data — response mapping
│ │ │ ├── UseCases/
│ │ │ │ ├── CreateTicketUseCase.php # DB transaction + event dispatch
│ │ │ │ ├── ListTicketsUseCase.php
│ │ │ │ └── GetTicketUseCase.php
│ │ │ └── Exceptions/
│ │ │ ├── TicketCreationException.php
│ │ │ └── TicketNotFoundException.php
│ │ │
│ │ └── Infrastructure/
│ │ ├── Http/Controllers/
│ │ │ └── TicketController.php # Thin controller — delegates to use cases
│ │ ├── Repositories/
│ │ │ └── EloquentTicketRepository.php # Eloquent implementation
│ │ ├── Events/
│ │ │ └── LaravelEventDispatcher.php # Laravel Event facade wrapper
│ │ └── Listeners/
│ │ └── LogTicketCreated.php # Publishes to Redis
│ │
│ ├── app/Models/
│ │ └── Ticket.php # Eloquent model
│ ├── database/migrations/
│ ├── routes/api.php
│ ├── docker/
│ │ ├── nginx.conf
│ │ └── start.sh
│ └── Dockerfile
│
├── notification-service/
│ ├── app/
│ │ ├── Domain/
│ │ │ └── Notification/
│ │ │ ├── Entities/
│ │ │ │ └── Notification.php # Notification entity
│ │ │ └── Repositories/
│ │ │ └── NotificationRepositoryInterface.php
│ │ │
│ │ ├── Application/
│ │ │ └── Notification/
│ │ │ └── UseCases/
│ │ │ └── CreateNotificationUseCase.php
│ │ │
│ │ ├── Infrastructure/
│ │ │ ├── Http/Controllers/
│ │ │ │ └── NotificationController.php
│ │ │ └── Repositories/
│ │ │ └── FileNotificationRepository.php # JSON file storage
│ │ │
│ │ └── Console/Commands/
│ │ └── SubscribeTicketEvents.php # Redis Pub/Sub subscriber
│ │
│ ├── routes/api.php
│ ├── docker/
│ │ ├── nginx.conf
│ │ └── start.sh
│ └── Dockerfile

``

# Setup

Prerequisites
Docker Desktop
Git

1. Clone the repository

git clone https://github.com/jnashvs/laravel-microservices.git
cd laravel-microservices

2. Configure environment variables

cp .env.example .env
cp ticket-service/.env.example ticket-service/.env
cp api-gateway/.env.example api-gateway/.env
cp notification-service/.env.example notification-service/.env

3. Generate APP_KEYs

cd ticket-service && php artisan key:generate --show
cd ../api-gateway && php artisan key:generate --show
cd ../notification-service && php artisan key:generate --show
cd ..

4. Start all services

docker-compose up -d --build

5. Verify all services are running (All services should show Up or healthy)

docker-compose ps

# API Testing

1. API Gateway
   curl http://localhost:8000/api/health

2. Ticket Service (direct)
   curl http://localhost:8100/api/health

3. Notification Service (direct)
   curl http://localhost:8200/api/health

4. Create a ticket via API Gateway

curl -X POST http://localhost:8000/api/tickets \
 -H "Content-Type: application/json" \
 -d '{"title": "Login bug", "description": "User cannot login with valid credentials", "priority": "high"}'

5. List tickets via API Gateway
   curl http://localhost:8000/api/tickets

6. Get a specific Ticket by ID via API Gateway
   curl http://localhost:8000/api/tickets/1

7. Check notifications (after creating a ticket, a notification should be created)

curl http://localhost:8000/api/notifications

# 🔍 Monitoring

1. Real-time Redis Monitor

docker exec -it redis redis-cli MONITOR

2. RedisInsight (UI)

http://localhost:5540

3. Service Logs

docker logs api-gateway -f
docker logs ticket-service -f
docker logs notification-service -f
docker-compose logs -f

# Docker Commands

1. Start all services

docker-compose up -d --build

2. Stop all services

docker-compose down

3. Stop and remove volumes (reset database)

docker-compose down -v

4. Rebuild a specific service

docker-compose up -d --build ticket-service

5. Check container status

docker-compose ps

6. Enter a container shell

docker exec -it ticket-service sh
docker exec -it notification-service sh

7. View real-time logs

docker-compose logs -f

# Event Flow

1. Client sends POST /api/tickets to the API Gateway (:8000)
2. API Gateway proxies the request to the Ticket Service (:8100)
3. Ticket Service:
   a. CreateTicketData (Spatie Data) validates the request automatically
   b. CreateTicketUseCase executes inside a DB transaction
   c. Ticket::create() factory method builds the domain entity with UUID
   d. EloquentTicketRepository persists to MySQL
   e. EventDispatcherInterface dispatches TicketCreated domain event
   f. LogTicketCreated listener publishes to Redis channel "ticket.created"
   g. Returns TicketResponseData DTO
4. Notification Service:
   a. SubscribeTicketEvents (Artisan Command) listens on "ticket.created" channel
   b. Receives the JSON payload via Predis
   c. CreateNotificationUseCase builds the Notification entity
   d. FileNotificationRepository persists to JSON file
5. Client queries GET /api/notifications via API Gateway

# Conclusion

This project demonstrates a microservices architecture using Laravel, Docker and Redis Pub/Sub, following DDD principles to keep the codebase organized and scalable. The API Gateway centralizes all requests while independent services communicate through events, enabling decoupled evolution and independent deployment of each service.
