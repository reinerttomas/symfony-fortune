database=symfony-doctrine-queries-database-1

### DOCKER ###
build:
	@docker compose build

up:
	@docker compose up -d

down:
	@docker compose down

db:
	@docker exec -it $(database) bash

### ANALYSIS ###

phpstan:
	composer phpstan

ccs:
	composer ccs

fcs:
	composer fcs

ci:
	composer ci
