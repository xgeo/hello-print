DROP DATABASE IF EXISTS hello_print;
CREATE DATABASE hello_print;

CREATE TABLE requests (
   id bigserial PRIMARY KEY,
   message text,
   created_at timestamp DEFAULT current_timestamp,
   updated_at timestamp
);