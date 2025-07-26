build:
	docker build -t mschaffr/fruits-and-vegetables -f docker/Dockerfile .

run:
	docker run -it -w/app -v$(PWD):/app mschaffr/fruits-and-vegetables sh

test:
	docker run -it -w/app -v$(PWD):/app mschaffr/fruits-and-vegetables bin/phpunit

serve:
	docker run -it -w/app -v$(PWD):/app -p 8080:8080 mschaffr/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public