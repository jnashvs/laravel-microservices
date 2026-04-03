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

├── api-gateway/
│   ├── app/Http/Controllers/
│   │   ├── TicketProxyController.php
│   │   └── NotificationProxyController.php
│   ├── resources/views/dashboard.blade.php
│   ├── routes/api.php
│   ├── routes/web.php
│   └── Dockerfile

├── ticket-service/
│   ├── app/
│   │   ├── Domain/Ticket/
│   │   │   ├── Entities/Ticket.php
│   │   │   ├── ValueObjects/Priority.php
│   │   │   ├── ValueObjects/TicketStatus.php
│   │   │   ├── Events/TicketCreated.php
│   │   │   ├── Events/EventDispatcherInterface.php
│   │   │   └── Repositories/TicketRepositoryInterface.php
│   │   ├── Application/Ticket/
│   │   │   ├── DTOs/CreateTicketData.php
│   │   │   ├── DTOs/TicketResponseData.php
│   │   │   ├── UseCases/CreateTicketUseCase.php
│   │   │   ├── UseCases/ListTicketsUseCase.php
│   │   │   ├── UseCases/GetTicketUseCase.php
│   │   │   ├── Exceptions/TicketCreationException.php
│   │   │   └── Exceptions/TicketNotFoundException.php
│   │   └── Infrastructure/
│   │       ├── Http/Controllers/TicketController.php
│   │       ├── Repositories/EloquentTicketRepository.php
│   │       ├── Events/LaravelEventDispatcher.php
│   │       └── Listeners/LogTicketCreated.php
│   ├── app/Models/Ticket.php
│   ├── database/migrations/
│   ├── routes/api.php
│   ├── docker/nginx.conf
│   ├── docker/start.sh
│   └── Dockerfile

├── notification-service/
│   ├── app/
│   │   ├── Domain/Notification/
│   │   │   ├── Entities/Notification.php
│   │   │   └── Repositories/NotificationRepositoryInterface.php
│   │   ├── Application/Notification/UseCases/CreateNotificationUseCase.php
│   │   ├── Infrastructure/Http/Controllers/NotificationController.php
│   │   ├── Infrastructure/Repositories/FileNotificationRepository.php
│   │   └── Console/Commands/SubscribeTicketEvents.php
│   ├── routes/api.php
│   ├── docker/nginx.conf
│   ├── docker/start.sh
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

## API Testing

```bash
curl http://localhost:8000/api/health
curl http://localhost:8100/api/health
curl http://localhost:8200/api/health
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

1. Client sends POST /api/tickets to API Gateway
2. API Gateway proxies request to Ticket Service
3. Ticket Service processes and publishes event
4. Notification Service consumes event and stores notification

---

## Conclusion

This project demonstrates a microservices architecture using Laravel, Docker and Redis Pub/Sub, following DDD principles to keep the codebase organized and scalable.
