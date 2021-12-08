-- 外部キー　作成

ALTER TABLE users
  ADD CONSTRAINT users_FK1 FOREIGN KEY (status_id) REFERENCES user_statuses(id);
ALTER TABLE users
  ADD CONSTRAINT users_FK2 FOREIGN KEY (prefecture_id) REFERENCES prefectures(id);

ALTER TABLE follow_users
  ADD CONSTRAINT follow_users_FK1 FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE follow_users
  ADD CONSTRAINT follow_users_FK2 FOREIGN KEY (follow_user_id) REFERENCES users(id);

ALTER TABLE operation_logs
  ADD CONSTRAINT operation_logs_FK1 FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE operation_logs
  ADD CONSTRAINT operation_logs_FK2 FOREIGN KEY (type_id) REFERENCES operation_types(id);

ALTER TABLE sns_tokens
  ADD CONSTRAINT sns_tokens_FK1 FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE sns_tokens
  ADD CONSTRAINT sns_tokens_FK2 FOREIGN KEY (sns_provider_id) REFERENCES sns_providers(id);

ALTER TABLE messages
  ADD CONSTRAINT messages_FK1 FOREIGN KEY (user_id) REFERENCES users(id);

ALTER TABLE message_images
  ADD CONSTRAINT message_images_FK1 FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE message_images
  ADD CONSTRAINT message_images_FK2 FOREIGN KEY (message_id) REFERENCES messages(id);

ALTER TABLE goods
  ADD CONSTRAINT goods_FK1 FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE goods
  ADD CONSTRAINT goods_FK2 FOREIGN KEY (message_id) REFERENCES messages(id);

ALTER TABLE comments
  ADD CONSTRAINT comments_FK1 FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE comments
  ADD CONSTRAINT comments_FK2 FOREIGN KEY (message_id) REFERENCES messages(id);

ALTER TABLE prefecture_messages
  ADD CONSTRAINT prefecture_messages_FK1 FOREIGN KEY (prefecture_id) REFERENCES prefectures(id);
ALTER TABLE prefecture_messages
  ADD CONSTRAINT prefecture_messages_FK2 FOREIGN KEY (message_id) REFERENCES messages(id);