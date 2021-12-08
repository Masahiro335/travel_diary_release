-- ダミーデータ作成

-- ユーザーデータ作成
INSERT INTO users(name, email, password, status_id, unique_id, prefecture_id, profile, is_icon, is_home, is_deleted) 
VALUES 
  ('田中太郎', 'test1@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 20, 'csefsrgvf', 23, 'おはよう', false, false, false),
  ('上山雄介', 'test2@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 20, 'fcefofeec', 27, '今日はいい天気', false, false, false),
  ('田中由美', 'test3@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 20, 'ewfuoeelk', 13, 'なんでもない', false, false, false),
  ('植田炭治郎', 'test4@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 20, 'ewfuoeelk', 9, 'それは大変', false, false, false),
  ('井上なぎ', 'test5@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 20, 'ewfuoeelk', 31, 'なぎだ！', false, false, false),
  ('佐々木健(退会)', 'test6@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 20, 'fhefheieh', 36, '初参戦', false, true, false),
  ('中山士郎(行動制限)', 'test7@test.com', '$2y$10$K12wq4vUCwPJynwrBI6APOD69weVPyejum87G0FSYRz2X/jlAdOKO', 30, 'feihfeivt', 39, '今日も参加します。', false, false, false)
  ;

-- 投稿のデータ作成
INSERT INTO messages(message, user_id ,is_edit, is_deleted) 
VALUES 
  ('最高！', 1, false, false),
  ('暑い', 1, false, false),
  ('綺麗', 1, false, false),
  ('青い', 1, false, false),
  ('寒い', 1, false, false),
  ('良いね！', 2, false, false),
  ('何で', 2, true, false),
  ('ん？', 2, false, false),
  ('あああ', 2, false, false),
  ('寒くなってきた', 3, false, true),
  ('おかしいな', 3, false, false),
  ('おはよう！', 4, false, false),
  ('123', 4, false, false),
  ('綺麗だ', 5, false, false),
  ('abc', 5, false, false),
  ('<h1>aa!**</h1>,', 5, false, false),
  ('本文', 1, false, false),
  ('本文2', 1, false, false),
  ('本文3', 1, false, false),
  ('本文4', 1, false, false),
  ('本文5', 1, false, false),
  ('本文6', 1, false, false),
  ('本文7', 1, false, false),
  ('本文8', 1, false, false),
  ('本文9', 1, false, false),
  ('本文10', 2, false, false),
  ('本文11', 2, false, false),
  ('本文12', 2, false, false),
  ('本文13', 2, false, false)
  ;

-- 投稿画像データ作成
INSERT INTO message_images(message_id, user_id, sort, is_deleted) 
VALUES 
  (1, 1, 1, false),
  (1, 1, 2, false),
  (3, 1, 1, false)
  ;

-- いいねデータ作成
INSERT INTO goods(message_id, user_id, good) 
VALUES 
  (1, 1, true),
  (2, 1, true),
  (1, 2, true),
  (2, 2, true),
  (3, 2, true)
  ;

-- コメントデータ作成
INSERT INTO comments(message_id, user_id, comment, is_deleted) 
VALUES 
  (1, 1, 'コメント1', false),
  (1, 1, 'コメント2', false),
  (3, 1, 'コメント3', false),
  (1, 2, 'コメント4', false),
  (2, 2, 'コメント5', false),
  (3, 2, 'コメント6', false),
  (3, 2, 'コメント7', false)
  ;

-- 投稿と都道府県の中間テーブルデータ作成
INSERT INTO prefecture_messages(message_id, prefecture_id) 
VALUES 
  (1, 23),
  (1, 1),
  (1, 43),
  (2, 23),
  (2, 25),
  (3, 25),
  (3, 23),
  (4, 2),
  (4, 23),
  (5, 1),
  (5, 23),
  (5, 25),
  (6, 12),
  (6, 11),
  (6, 1),
  (7, 2),
  (7, 3),
  (8, 5),
  (8, 8),
  (8, 21),
  (9, 34),
  (9, 38),
  (10, 10),
  (10, 25),
  (10, 42),
  (11, 45),
  (11, 15),
  (12, 35),
  (12, 29),
  (12, 17),
  (13, 11),
  (13, 23),
  (14, 47),
  (15, 23),
  (15, 10),
  (16, 20),
  (16, 30),
  (17, 5),
  (17, 21),
  (17, 17),
  (18, 4),
  (18, 18),
  (19, 19),
  (19, 20),
  (20, 20),
  (20, 41),
  (21, 14),
  (22, 38),
  (22, 39),
  (22, 38),
  (23, 39),
  (23, 29),
  (24, 9),
  (24, 10),
  (24, 24),
  (25, 25),
  (25, 19),
  (25, 36),
  (26, 23),
  (26, 26),
  (27, 11),
  (27, 22),
  (27, 27),
  (28, 31),
  (28, 16),
  (29, 13),
  (29, 33),
  (29, 41)
  ;

-- フォローデータ作成
INSERT INTO follow_users(follow_user_id, user_id) 
VALUES 
  (1, 2),
  (1, 3),
  (2, 1),
  (2, 3),
  (3, 1)
  ;