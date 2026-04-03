# 🚀 Laravel Microservices

Microservices architecture built with Laravel, Docker, Redis Pub/Sub and Domain-Driven Design (DDD).

---

## 📐 Architecture

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

## 🧰 Tech Stack

- PHP 8.4 + Laravel
- MySQL 8.0 — Ticket Service database
- Redis — Pub/Sub messaging between services
- Docker + Docker Compose
- Nginx + PHP-FPM
- Spatie Laravel Data — DTOs with validation
- DDD (Domain-Driven Design)

---

## 📁 Project Structure

```bash
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
```

### 2️⃣ Configure environment variables

```bash
cp .env.example .env
cp ticket-service/.env.example ticket-service/.env
cp api-gateway/.env.example api-gateway/.env
cp notification-service/.env.example notification-service/.env
```

### 3️⃣ Generate APP_KEYs

```bash
cd ticket-service && php artisan key:generate --show
cd ../api-gateway && php artisan key:generate --show
cd ../notification-service && php artisan key:generate --show
cd ..
```

### 4️⃣ Start all services

```bash
docker-compose up -d --build
```

### 5️⃣ Verify services

```bash
docker-compose ps
```

---

## 🧪 API Testing

```bash
curl http://localhost:8000/api/health
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
```

---

## 🧾 Conclusion

This project demonstrates a microservices architecture using Laravel, Docker and Redis Pub/Sub, following DDD principles to keep the codebase organized and scalable.
