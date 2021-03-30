DROP DATABASE IF EXISTS hello_print;

CREATE DATABASE hello_print;

CREATE TABLE `requests` (
   id bigserial PRIMARY KEY,
   message VARCHAR(125) NOT NULL ,
   created_at TIMESTAMP NOT NULL,
   updated_at TIMESTAMP NULL
);