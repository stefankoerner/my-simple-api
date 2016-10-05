
all: docker-build docker-run

docker-rm:
	docker ps -a -f name=my-simple-api -q | xargs -r docker rm -f

docker-build: docker-rm
	docker build -t my-simple-api .

docker-run: docker-rm
	docker run -it --name my-simple-api -p 4202:4202 my-simple-api

docker-shell:
	docker exec -it my-simple-api /bin/bash