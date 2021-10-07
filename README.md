# PHP_RabbitMQ_Proof_of_Concept
Simple code for connecting PHP and RabbitMQ
<br/><small>(for further information please visit https://rabbitmq.com)</small>

Requirements:
- Docker-composer
- composer
- port 81 in localhost (you can change it in docker-composer.yml file)


# Install (Linux):
- cd web-php-code/web
- composer install
- cd ../..

- docker-compose up -d

# Access to RabbitMQ web UI:
http://localhost:15672

Credentials:
root:root (set in docker-compose.yml as well)






