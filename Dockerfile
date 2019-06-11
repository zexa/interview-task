FROM composer:latest
RUN git clone https://github.com/zexa/interview-task.git /usr/src/comission-calculator
WORKDIR /usr/src/comission-calculator
RUN composer install
RUN vendor/bin/phpunit --testdox tests/
RUN php index.php tests/inputs/input.csv
