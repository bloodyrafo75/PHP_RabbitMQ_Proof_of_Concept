# PHP_RabbitMQ_Proof_of_Concept
Simple code for connecting PHP and RabbitMQ

Requirements:
- Docker-composer
- composer
- free port 80 in localhost (you can change it in docker-composer.yml file)


Install (Linux):
- cd producer-php-code/web
- composer install
- cd ../..

- cd consumer-php-code/cmd
- composer install
- cd ../..

- docker-compose up -d

Access to RabbitMQ web UI:
http://localhost:15672

Credentials:
user:bitnami


Demonstration:

You can check 'fanout' or 'direct' modes
If you want to broadcast to every client, use 'fanout' mode.
Use 'direct' mode to deploy only to certain consumers (setting routing).

Fanout mode:
- Start one or more consumers.
  php ./php-consumer-code/cmd/fanout_consumer.php
  
- Send message to queue:
  http://localhost/fanout_producer_sending.php
  
- Check consumer console.


Direct mode:
- Start one or more consumers using an specific token:
  php ./php-consumer-code/cmd/direct_consumer_with_routing.php <route_token>
  
- Send message to queue:
  http://localhost/direct_producer_sending.php?client_token=<route_token>
  
- Check consumer console.






