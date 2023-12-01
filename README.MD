# DIGITAL-SECTOR CODESTYLE

Codestyle package
## Установка
`composer require --dev digital-sector/codestyle`

## Команды
1) PHP линтер для проверки синтаксических ошибок
```bash
composer code-style:phplint <file>
```
2) Исправление ошибок код-стайла в директории
```bash
composer code-style:fix <dir>
```
3) Проверка наличия ошибок  код-стайла в директории
```bash
composer code-style:check <dir>
```
4) Запуск aнализа phpstan для директории
```bash
composer code-style:analyze <dir>
```

## Phpstan

Конфигурация `phpstan.neon` находится в директорию проекта

## CS-Fixer

Все манипуляции для него задаются через composer.json

```json
...
"extra": {
	"code-style": {
		"finder": {
			"excludes": [] # Список директорий, которые будут игнорироваться
		},
		"analyze-path": "" # Базовая директория для работы
	}
}
```
