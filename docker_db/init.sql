DROP DATABASE IF EXISTS hello_print;

CREATE DATABASE hello_print;

CREATE TABLE requests (
   id bigserial,
   message text NOT NULL,
   created_at timestamp DEFAULT current_timestamp,
   updated_at timestamp
);