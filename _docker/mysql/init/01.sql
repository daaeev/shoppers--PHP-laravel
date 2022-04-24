CREATE DATABASE shoppers_test;

CREATE USER 'shoppers_test'@'%';

GRANT ALL PRIVILEGES ON `shoppers_test`.* TO 'shoppers_test'@'%';
ALTER USER 'shoppers_test'@'%';
