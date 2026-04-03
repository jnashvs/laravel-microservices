# Laravel Microservices

Microservices architecture built with Laravel, Docker, Redis Pub/Sub and Domain-Driven Design (DDD).

---

## Architecture

```
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

---

## Tech Stack

- PHP 8.4 + Laravel
- MySQL 8.0 — Ticket Service database
- Redis — Pub/Sub messaging between services
- Docker + Docker Compose
- Nginx + PHP-FPM
- Spatie Laravel Data — DTOs with validation
- DDD (Domain-Driven Design)

---

## Project Structure

```bash
laravel-microservices/
├── docker-compose.yml
├── .env
│
├── api-gateway/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── HealthController.php
│   │   │   │   ├── TicketProxyController.php
│   │   │   │   └── NotificationProxyController.php
│   │   │   └── Middleware/
│   │   │       ├── AuthenticateApiKey.php
│   │   │       └── RateLimitMiddleware.php
│   │   ├── Services/
│   │   │   ├── BaseServiceProxy.php
│   │   │   ├── TicketServiceProxy.php
│   │   │   └── NotificationServiceProxy.php
│   │   └── Exceptions/
│   │       └── ServiceUnavailableException.php
│   ├── resources/views/dashboard.blade.php
│   ├── routes/api.php
│   └── Dockerfile
│
├── ticket-service/
│   ├── app/
│   │   ├── Domain/
│   │   │   └── Ticket/
│   │   │       ├── Entities/Ticket.php
│   │   │       ├── ValueObjects/Priority.php
│   │   │       ├── ValueObjects/TicketStatus.php
│   │   │       ├── Events/TicketCreated.php
│   │   │       ├── Events/EventDispatcherInterface.php
│   │   │       └── Repositories/TicketRepositoryInterface.php
│   │   ├── Application/
│   │   │   └── Ticket/
│   │   │       ├── DTOs/CreateTicketData.php
│   │   │       ├── DTOs/TicketResponseData.php
│   │   │       ├── UseCases/CreateTicketUseCase.php
│   │   │       ├── UseCases/ListTicketsUseCase.php
│   │   │       ├── UseCases/GetTicketUseCase.php
│   │   │       └── Exceptions/
│   │   │           ├── TicketCreationException.php
│   │   │           └── TicketNotFoundException.php
│   │   └── Infrastructure/
│   │       ├── Http/Controllers/TicketController.php
│   │       ├── Repositories/EloquentTicketRepository.php
│   │       ├── Events/LaravelEventDispatcher.php
│   │       └── Listeners/LogTicketCreated.php
│   ├── app/Models/Ticket.php
│   ├── database/migrations/
│   ├── routes/api.php
│   └── Dockerfile
│
├── notification-service/
│   ├── app/
│   │   ├── Domain/
│   │   │   └── Notification/
│   │   │       ├── Entities/Notification.php
│   │   │       └── Repositories/NotificationRepositoryInterface.php
│   │   ├── Application/
│   │   │   └── Notification/
│   │   │       └── UseCases/CreateNotificationUseCase.php
│   │   ├── Infrastructure/
│   │   │   ├── Http/Controllers/NotificationController.php
│   │   │   └── Repositories/FileNotificationRepository.php
│   │   └── Console/Commands/
│   │       └── SubscribeTicketEvents.php
│   ├── routes/api.php
│   └── Dockerfile
```

---

## ⚙️ Setup

### 📋 Prerequisites

- Docker Desktop
- Git

---

### 1️⃣ Clone the repository

```bash
git clone https://github.com/jnashvs/laravel-microservices.git
cd laravel-microservices
```

---

### 2️⃣ Configure environment variables

```bash
cp .env.example .env
cp ticket-service/.env.example ticket-service/.env
cp api-gateway/.env.example api-gateway/.env
cp notification-service/.env.example notification-service/.env
```

---

### 3️⃣ Generate APP_KEYs

```bash
cd ticket-service && php artisan key:generate --show
cd ../api-gateway && php artisan key:generate --show
cd ../notification-service && php artisan key:generate --show
cd ..
```

---

### 4️⃣ Start all services

```bash
docker-compose up -d --build
```

---

### 5️⃣ Verify services are running

```bash
docker-compose ps
```

---

## Security

X-RateLimit-Limit: 60
X-RateLimit-Remaining: 57

## API Testing

1. Health check (public — no auth required)

```bash
curl http://localhost:8000/api/health
```

2. Without API key (401 Unauthorized)

```bash
curl -s http://localhost:8000/api/tickets | jq
```

3. Wrong API key (403 Forbidden)

```bash
curl -s http://localhost:8000/api/tickets \
  -H "X-API-Key: wrong-key" | jq
```

4. Create a ticket (201 Created)

```bash
curl -s -X POST http://localhost:8000/api/tickets \
  -H "Content-Type: application/json" \
  -H "X-API-Key: ms-key-2026-prod" \
  -d '{"title": "Login bug", "description": "User cannot login", "priority": "high"}' | jq
```

5. List all tickets (200 OK)

```bash
curl -s http://localhost:8000/api/tickets \
  -H "X-API-Key: wrong-key" | jq
```

6. List notifications (200 OK)

```bash
sleep 2
curl -s http://localhost:8000/api/notifications \
  -H "X-API-Key: ms-key-2026-prod" | jq
```

---

## 🔍 Monitoring

```bash
docker exec -it redis redis-cli MONITOR
```

---

## 🐳 Docker Commands

```bash
docker-compose up -d --build
docker-compose down
docker-compose down -v
docker-compose ps
docker-compose logs -f
```

---

## Event Flow

1. Client sends POST /api/tickets to API Gateway (:8000)
2. AuthenticateApiKey middleware validates X-API-Key header
3. RateLimitMiddleware checks request quota
4. API Gateway proxies request to Ticket Service (:8100)
5. Ticket Service:
   a. CreateTicketData (Spatie Data) validates the request automatically
   b. CreateTicketUseCase executes inside a DB transaction
   c. Ticket::create() factory method builds the domain entity with UUID
   d. EloquentTicketRepository persists to MySQL
   e. EventDispatcherInterface dispatches TicketCreated domain event
   f. LogTicketCreated listener publishes to Redis channel "ticket.created"
   g. Returns TicketResponseData DTO
6. Notification Service:
   a. SubscribeTicketEvents (Artisan Command) listens on "ticket.created"
   b. Receives the JSON payload via Predis (read_write_timeout: 0)
   c. Creates Notification entity
   d. FileNotificationRepository persists to JSON file
7. Client queries GET /api/notifications via API Gateway

---

## Conclusion

This project demonstrates a microservices architecture using Laravel, Docker and Redis Pub/Sub, following DDD principles to keep the codebase organized and scalable. The API Gateway centralizes all requests with authentication and rate limiting, while independent services communicate through events, enabling decoupled evolution and independent deployment of each service.
