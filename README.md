# Compile

```bash
composer global require humbug/box
sudo ln -s ~/.config/composer/vendor/bin/box /usr/local/bin/box
box compile
```


# Install

- copy `index.php` and `homestead.phar` to production host
- create `config` directory with default `_config.yml`, `_layout.yml` and `_sections.yml` files (see below)
- 

# Required files in `config/`:

## `_config.yml` - DB & Redis credentials

```yaml
cache:
  redis:
    host: "192.168.111.80"
    port: 6379
    database: 8
    ttl: 1800
  sql:
    type: "sqlite"
    database: "homestead.sqlite"
```

## _layout.yml

```yaml
layout:
  favicon: "/icons/favicon.svg"
  title: "My Services Dashboard"
  header: "My Services Dashboard"
  font-family: "Segoe UI, sans-serif"
  max-width: "1400px"
  background-color: "#f5f5f5"
  text-color: "#222"
  section-title-color: "#444"
  section-border-color: "#ddd"
  card-bg-color: "#fff"
  card-border-color: "#eee"
  card-hover-border-color: "#999"
  card-title-color: "#0066cc"
  card-description-color: "#555"

opengraph:
  title: "My Services Dashboard"
  type: "website"
#  url: "https://example.com/my-dashboard"
#  image: "/images/social-preview.png"  # Изображение для превью 1200×630
#  image_width: 1200
#  image_height: 630
  description: "All my favorite web services in one place"
  site_name: "My Dashboard"
  locale: "ru_RU"
```

## _sections.yml

```yaml
sections:
  - global.yml
```

## global.yml (section example)

```yaml
title: "Internet"
# icon: иконка к секции (но это не очень красиво)
resources:
  - name: "Google"
    link: "https://google.com"
    description: "Поисковая система"
    icon: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAw1BMVEVHcEz////////9/f39/f79/f33+Pj////b3d74+fny8/P19vYAAAD////w8fH////////9/f3///8+fu7ZRCpBmEtNnVXaTDbbUz79/Pz0uCtIm1EqdO2FqPJplvDqpJ282MDifG87l0DW4fvXOBWqzrDvurbt8v55oPLkjIH00Mx0sHuRvpVpq3BrrHL2wEn51Y/4zHbw6eP4uBf86MP0sw6EpvJEhdRSie50prrFrCrsrqfqkSrssavxrDPP48yyx/Yvv4yqAAAAEnRSTlMAWZFT48ytmAumbmsCSV/Uv52LM4rJAAABN0lEQVQokY2SiXKCMBCGEUHBW5qQhGgRKopn7zq9ff+n6mZLNIAz7T/OZDdf8rO7xrL+p749Gg5Hdv8CsoOT7ApqBCU1TNYOKmrX2DpereJ1hf56LhhhjBHCFqazh0lCOOeMwW8e44Z3Nk0Y52TO+ZyQxDRGT7hH3kUQiDjRRSnWVMEH4+ShUnGzcH16/CKfl9pxYb2LomehNvLp9AWULyF2ATqw3kfRLZ6eXKEmNxA7f0Fti/BaCaC2HWNBUTjThRwBqnWsW3mTIS1aWYJtrlvBIWQ0DCne/T4Un8QhWL6K9hIo3e1pKg+TqdrxjcHv4G4o4YhMX43BW11MspRKKSlNM0y7+g9tYSpm281mO8NJBa3zU2hV52owy+oIE4lO+fl5/gkL36s93N7Adx3H9Qe9GioOlNMflcoty3IDwqwAAAAASUVORK5CYII="

  - name: "GitHub"
    link: "https://github.com"
    description: "Хостинг для IT-проектов"
    icon: "/icons/github.png"

```