-- テーブル作成

-- ユーザーテーブル
CREATE TABLE users (
  id bigserial unique not null
  , name text not null
  , email text not null
  , password text not null
  , status_id integer not null
  , prefecture_id integer
  , profile text
  , unique_id text not null
  , is_icon boolean default false not null
  , icon_upload_datetime timestamp
  , icon_extension text
  , is_home boolean default false not null
  , home_upload_datetime timestamp
  , home_extension text
  , token_limit_time timestamp
  , register_token text
  , tmp_mail_token text
  , auth_code text
  , is_deleted boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT users_pkey PRIMARY KEY (id)
) ;

-- ユーザーステータステーブル
CREATE TABLE user_statuses (
  id serial unique not null
  , name text not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT user_statuses_pkey PRIMARY KEY (id)
) ;

-- フォローユーザーテーブル
CREATE TABLE follow_users (
  id bigserial unique not null
  , follow_user_id integer not null
  , user_id integer not null
  , is_deleted boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT follow_users_pkey PRIMARY KEY (id)
) ;

-- 行動履歴テーブル
CREATE TABLE operation_logs (
  id bigserial unique not null
  , type_id integer not null
  , user_id integer not null
  , ip_address text not null
  , ua text not null
  , cookie text not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT operation_logs_pkey PRIMARY KEY (id)
) ;

-- 行動タイプテーブル
CREATE TABLE operation_types (
  id bigserial unique not null
  , name text not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT operation_types_pkey PRIMARY KEY (id)
) ;

-- ログイン用SNSトークン
CREATE TABLE sns_tokens (
    id bigserial unique not null
  , sns_token text not null
  , user_id integer not null
  , sns_provider_id integer not null
  , email text not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT sns_tokens_pkey PRIMARY KEY (id)
) ;

-- ログイン用SNSプロバイダー
CREATE TABLE sns_providers (
  id serial unique not null
  , name text not null
  , is_deleted boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT sns_providers_pkey PRIMARY KEY (id)
) ;

-- 投稿テーブル
CREATE TABLE messages (
  id bigserial unique not null
  , message text not null
  , user_id integer not null
  , is_edit boolean default false not null
  , is_deleted boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT messages_pkey PRIMARY KEY (id)
) ;

-- 投稿画像テーブル
CREATE TABLE message_images (
  id bigserial unique not null
  , message_id integer not null
  , user_id integer not null
  , sort integer not null
  , image_upload_datetime timestamp
  , image_extension text
  , is_deleted boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT message_images_pkey PRIMARY KEY (id)
) ;

-- いいねテーブル
CREATE TABLE goods (
  id bigserial unique not null
  , message_id integer not null
  , user_id integer not null
  , good boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT goods_pkey PRIMARY KEY (id)
) ;

-- コメントテーブル
CREATE TABLE comments (
  id bigserial unique not null
  , message_id integer not null
  , user_id integer not null
  , comment text not null
  , is_deleted boolean default false not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT comments_pkey PRIMARY KEY (id)
) ;


-- 投稿と都道府県の中間テーブル
CREATE TABLE prefecture_messages (
  id bigserial unique not null
  , message_id integer not null
  , prefecture_id integer not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT prefecture_messages_pkey PRIMARY KEY (id)
) ;

-- 都道府県
CREATE TABLE prefectures (
  id serial unique not null
  , name text not null
  , modified timestamp default now()
  , created timestamp default now()
  , CONSTRAINT prefectures_pkey PRIMARY KEY (id)
) ;


-- コメント

-- ユーザーテーブルのコメント
comment on table users is 'ユーザー';
comment on column users.id is 'ID';
comment on column users.name is 'ユーザー名';
comment on column users.email is 'メールアドレス';
comment on column users.password is 'パスワード';
comment on column users.status_id is 'ステータスID';
comment on column users.prefecture_id is '出身地';
comment on column users.profile is 'プロフィール';
comment on column users.unique_id is 'ユーザー固有ID';
comment on column users.is_icon is 'アイコン画像';
comment on column users.icon_upload_datetime is 'アイコン画像をアップした時刻';
comment on column users.icon_extension is 'アイコン画像の拡張子';
comment on column users.is_home is 'ホーム画像';
comment on column users.home_extension is 'ホーム画像の拡張子';
comment on column users.home_upload_datetime is 'ホーム画像をアップした時刻';
comment on column users.token_limit_time is 'トークンの有効期限';
comment on column users.register_token is '本登録用トークン';
comment on column users.tmp_mail_token is 'メールアドレス変更トークン:メールアドレス変更に使用';
comment on column users.auth_code is '認証コード';
comment on column users.is_deleted is '論理削除(凍結されたユーザー)';
comment on column users.modified is '更新日時';
comment on column users.created is '作成日時';

-- ユーザーステータステーブルのコメント
comment on table user_statuses is 'ユーザーステータス';
comment on column user_statuses.id is 'ID';
comment on column user_statuses.name is 'ユーザーステータス名';
comment on column user_statuses.modified is '更新日時';
comment on column user_statuses.created is '作成日時';

-- フォローユーザーテーブルのコメント
comment on table follow_users is 'フォローユーザー';
comment on column follow_users.id is 'ID';
comment on column follow_users.follow_user_id is 'フォローユーザーID';
comment on column follow_users.user_id is 'ユーザーID';
comment on column follow_users.is_deleted is '論理削除';
comment on column follow_users.modified is '更新日時';
comment on column follow_users.created is '作成日時';

-- 行動履歴テーブルのコメント
comment on table operation_logs is ' 行動履歴';
comment on column operation_logs.id is 'ID';
comment on column operation_logs.type_id is '操作種別';
comment on column operation_logs.user_id is 'ユーザーID';
comment on column operation_logs.ip_address is 'IPアドレス';
comment on column operation_logs.ua is 'ユーザーエージェント';
comment on column operation_logs.cookie is 'cookie';
comment on column operation_logs.modified is '更新日時';
comment on column operation_logs.created is '作成日時';

-- 行動タイプテーブルのコメント
comment on table operation_types is '行動タイプ';
comment on column operation_types.id is 'ID';
comment on column operation_types.name is '行動名';
comment on column operation_types.modified is '更新日時';
comment on column operation_types.created is '作成日時';


-- ログイン用SNSトークン
comment on table sns_tokens is 'ログイン用SNSトークン';
comment on column sns_tokens.id is 'ID';
comment on column sns_tokens.sns_token is 'SNSトークン';
comment on column sns_tokens.user_id is 'ユーザーID';
comment on column sns_tokens.sns_provider_id is 'SNSプロバイダーID';
comment on column sns_tokens.email is 'メールアドレス';
comment on column sns_tokens.modified is '更新日時';
comment on column sns_tokens.created is '作成日時';


-- ログイン用SNSプロバイダー
comment on table sns_providers is 'ログイン用SNSプロバイダー';
comment on column sns_providers.id is 'ID';
comment on column sns_providers.name is '名前';
comment on column sns_providers.is_deleted is '論理削除';
comment on column sns_providers.modified is '更新日時';
comment on column sns_providers.created is '作成日時';

-- 投稿テーブルのコメント
comment on table messages is '投稿';
comment on column messages.id is 'ID';
comment on column messages.message is 'メッセージ';
comment on column messages.user_id is 'ユーザーID';
comment on column messages.is_edit is '編集済み';
comment on column messages.is_deleted is '論理削除';
comment on column messages.modified is '更新日時';
comment on column messages.created is '作成日時';

-- 投稿画像テーブルのコメント
comment on table message_images is '投稿画像';
comment on column message_images.id is 'ID';
comment on column message_images.message_id is '投稿ID';
comment on column message_images.user_id is 'ユーザーID';
comment on column message_images.sort is 'ソート順位';
comment on column message_images.image_upload_datetime is '画像をアップした時刻';
comment on column message_images.image_extension is '画像の拡張子';
comment on column message_images.is_deleted is '論理削除';
comment on column message_images.modified is '更新日時';
comment on column message_images.created is '作成日時';

-- いいねテーブルのコメント
comment on table goods is 'いいね';
comment on column goods.id is 'ID';
comment on column goods.message_id is '投稿ID';
comment on column goods.user_id is 'ユーザーID';
comment on column goods.good is 'いいね';
comment on column goods.modified is '更新日時';
comment on column goods.created is '作成日時';

-- コメントテーブルのコメント
comment on table comments is 'コメント';
comment on column comments.id is 'ID';
comment on column comments.message_id is '投稿ID';
comment on column comments.user_id is 'ユーザーID';
comment on column comments.comment is 'コメント';
comment on column comments.is_deleted is '論理削除';
comment on column comments.modified is '更新日時';
comment on column comments.created is '作成日時';

-- 投稿と都道府県の中間テーブルのコメント
comment on table prefecture_messages is '投稿と都道府県の中間テーブル';
comment on column prefecture_messages.id is 'ID';
comment on column prefecture_messages.message_id is '投稿ID';
comment on column prefecture_messages.prefecture_id is '都道府県ID';
comment on column prefecture_messages.modified is '更新日時';
comment on column prefecture_messages.created is '作成日時';

-- 都道府県テーブルのコメント
comment on table prefectures is '都道府県';
comment on column prefectures.id is 'ID';
comment on column prefectures.name is '都道府県名';
comment on column prefectures.modified is '更新日時';
comment on column prefectures.created is '作成日時';

