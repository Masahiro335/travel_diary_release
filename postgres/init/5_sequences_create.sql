-- シーケンスの作成

SELECT setval('users_id_seq', max(id)) FROM users;
SELECT setval('user_statuses_id_seq', max(id)) FROM user_statuses;
SELECT setval('follow_users_id_seq', max(id)) FROM follow_users;
SELECT setval('operation_logs_id_seq', max(id)) FROM operation_logs;
SELECT setval('operation_types_id_seq', max(id)) FROM operation_types;
SELECT setval('sns_tokens_id_seq', max(id)) FROM sns_tokens;
SELECT setval('sns_providers_id_seq', max(id)) FROM sns_providers;
SELECT setval('messages_id_seq', max(id)) FROM messages;
SELECT setval('message_images_id_seq', max(id)) FROM message_images;
SELECT setval('goods_id_seq', max(id)) FROM goods;
SELECT setval('comments_id_seq', max(id)) FROM comments;
SELECT setval('prefectures_id_seq', max(id)) FROM prefectures;
SELECT setval('prefecture_messages_id_seq', max(id)) FROM prefecture_messages;