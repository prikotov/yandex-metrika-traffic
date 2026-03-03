# Yandex Metrika Traffic

> Источники трафика из Яндекс.Метрики

## Зачем это нужно

Этот skill анализирует откуда приходят посетители. Данные помогают:

- **Понять каналы трафика** — какие источники приносят больше посетителей
- **Оценить поисковый трафик** — какие поисковые системы работают лучше
- **Проанализировать соцсети** — откуда приходят из социальных сетей
- **Найти рефералов** — какие сайты ссылаются на ваш

## Что вы получите

Отчёт в форматах CSV и Markdown с группировкой по источникам:

| Файл | Содержание |
|------|------------|
| `traffic_*.*` | Источники с метриками: визиты, посетители, отказы, время на сайте |

## Зависимости

Требует установленный [yandex-metrika-core](https://github.com/prikotov/yandex-metrika-core)

## Установка

Skill совместим с различными AI-агентами. Примеры ниже даны для OpenCode — для других инструментов смотрите их документацию по установке skills.

```bash
# Сначала установите core
git clone https://github.com/prikotov/yandex-metrika-core.git .opencode/skills/yandex-metrika-core

# Затем этот skill
git clone https://github.com/prikotov/yandex-metrika-traffic.git .opencode/skills/yandex-metrika-traffic
```

## Использование

### Напрямую через PHP

```bash
# Распределение по типам источников
php .opencode/skills/yandex-metrika-traffic/traffic.php

# Топ поисковых систем
php .opencode/skills/yandex-metrika-traffic/traffic.php -b search_engine

# Топ-10 соцсетей
php .opencode/skills/yandex-metrika-traffic/traffic.php --by social_network -l 10

# Реферальные домены
php .opencode/skills/yandex-metrika-traffic/traffic.php -b referral_domain

# Источники для конкретной страницы
php .opencode/skills/yandex-metrika-traffic/traffic.php -p "blog/rag-s-nulya"
```

### Параметры

| Параметр | Сокращение | Описание | Пример |
|----------|------------|----------|--------|
| `--by` | `-b` | Группировка | `-b search_engine` |
| `--sort` | `-s` | Поле сортировки | `-s bounce_rate` |
| `--order` | `-o` | Порядок: asc/desc | `-o asc` |
| `--limit` | `-l` | Лимит записей | `-l 10` |
| `--page` | `-p` | Фильтр по странице (часть URL) | `-p "rag-s-nulya"` |

### Типы группировки (`--by` / `-b`)

| Параметр | Описание |
|----------|----------|
| `source` | Тип источника (по умолчанию) |
| `search_engine` | Поисковые системы |
| `social_network` | Социальные сети |
| `referral_domain` | Реферальные домены |

### Через агента

После установки skill агент автоматически узнаёт о нём. Примеры запросов:

```
Покажи распределение трафика по источникам
```

```
Сделай топ-10 поисковых систем по визитам
```

```
Проанализируй какие соцсети приносят трафик
```

## Результаты

Отчёты сохраняются в папку с датой:

```
metrika_reports/
└── 2026-03-03/
    ├── traffic_2026-03-03_10-30-15.csv
    └── traffic_2026-03-03_10-30-15.md
```

CSV открывается в Excel/LibreOffice, Markdown — в любом текстовом редакторе или напрямую в Obsidian.

---

> Постановка задач, архитектура, ревью — [Dmitry Prikotov](https://prikotov.pro/), реализация — GLM-5 в [OpenCode](https://opencode.ai)
