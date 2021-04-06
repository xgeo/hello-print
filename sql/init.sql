CREATE TABLE IF NOT EXISTS requests (
   id bigserial PRIMARY KEY,
   message text,
   created_at timestamp with time zone,
   updated_at timestamp with time zone DEFAULT NULL
);