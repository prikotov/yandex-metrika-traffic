---
name: yandex-metrika-traffic
description: Источники трафика из Яндекс.Метрики
---

## Когда использовать

- Анализ источников трафика
- Оценка эффективности каналов
- Определение поисковых систем и соцсетей

## Запуск

```bash
php .opencode/skills/yandex-metrika-traffic/traffic.php [опции] [дата_от] [дата_до]
```

### Параметры дат

- `дата_от` — начало периода (формат: YYYY-MM-DD), по умолчанию 30 дней назад
- `дата_до` — конец периода (формат: YYYY-MM-DD), по умолчанию сегодня

### Опции

| Опция | Сокращение | Описание | Значения | По умолчанию |
|-------|------------|----------|----------|--------------|
| `--by` | `-b` | Группировка | `source`, `search_engine`, `social_network`, `referral_domain` | `source` |
| `--sort` | `-s` | Поле сортировки | `visits`, `visitors`, `bounce_rate`, `page_depth`, `avg_duration` | `visits` |
| `--order` | `-o` | Направление сортировки | `asc`, `desc` | `desc` |
| `--limit` | `-l` | Лимит записей | число (например: 10, 20, 50) | все записи |
| `--page` | `-p` | Фильтр по странице (часть URL) | строка | без фильтра |

### Типы группировки

| Параметр | Описание |
|----------|----------|
| `source` | Тип источника (по умолчанию): поиск, прямой, реферальный и т.д. |
| `search_engine` | Поисковые системы |
| `social_network` | Социальные сети |
| `referral_domain` | Реферальные домены |

### Примеры

```bash
# Распределение по типам источников
php .opencode/skills/yandex-metrika-traffic/traffic.php

# Топ поисковых систем
php .opencode/skills/yandex-metrika-traffic/traffic.php -b search_engine

# Топ-10 соцсетей по посетителям
php .opencode/skills/yandex-metrika-traffic/traffic.php --by social_network --sort visitors -l 10

# Реферальные домены
php .opencode/skills/yandex-metrika-traffic/traffic.php -b referral_domain -s bounce_rate -l 15

# Источники трафика для конкретной страницы
php .opencode/skills/yandex-metrika-traffic/traffic.php -p "rag-s-nulya"

# Поисковые системы для конкретной страницы
php .opencode/skills/yandex-metrika-traffic/traffic.php -b search_engine -p "blog/article"

# За определённый период
php .opencode/skills/yandex-metrika-traffic/traffic.php -b search_engine -l 10 2025-01-01 2025-01-31
```

## Результат

`yandex_metrika_reports/YYYY-MM-DD/`:
- `yandex_metrika_traffic_YYYY-MM-DD_HH-MM-SS.csv` / `.md` — данные о трафике

### Поля в отчете

| Поле | Описание |
|------|----------|
| `source` | Источник (поисковая система, соцсеть и т.д.) |
| `visits` | Визиты |
| `visitors` | Посетители |
| `pageviews` | Просмотры страниц |
| `bounce_rate` | Процент отказов |
| `page_depth` | Глубина просмотра |
| `avg_duration` | Среднее время на сайте (сек) |
