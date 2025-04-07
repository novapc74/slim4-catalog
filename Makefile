init: down docker-pull docker-build docker-up

down: docker-down

create_network:
	docker network create slim-catalog

create_shared_network:
	docker network create --driver bridge shared-network

docker-down:
	docker-compose --env-file ./project/.env.local down --remove-orphans

docker-pull:
	docker-compose --env-file ./project/.env.local pull

docker-build:
	docker-compose --env-file ./project/.env.local build --pull

docker-up:
	docker-compose --env-file ./project/.env.local up -d

php-cli:
	docker-compose --env-file ./project/.env.local run --rm php-cli bash
