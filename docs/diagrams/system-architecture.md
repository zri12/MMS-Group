---
title: "System Architecture Diagram"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Arsitektur sistem

```mermaid
flowchart LR
    F[Flutter Marketing App] -->|HTTPS REST API + Sanctum| L[Laravel 12]
    A[Admin Browser] -->|Session + Web Routes| L
    L --> B[Blade + Livewire Web Admin]
    L --> M[(MySQL)]
    L --> S[Laravel Storage]
    F --> Q[(SQLite Local)]
    Q -->|Offline Sync| L
    F -->|GPS batches| L
    L -->|Polling data| A
```

# Boundary

- Flutter dan Laravel adalah dua project.
- Backend, API, dan web admin adalah satu project Laravel.
- MySQL hanya diakses Laravel.
- SQLite hanya berada di perangkat Flutter.
