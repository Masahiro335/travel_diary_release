<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateMessages extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('messages');
        $table->addColumn('name', 'text', ['null' => false,'comment' => 'タイトル'])
            ->addColumn('reputation', 'integer',['comment' => '評価'])
            ->addColumn('image_extension', 'text',['comment' => '画像拡張子'])
            ->addColumn('message', 'text', ['limit' => 140,'comment' => 'コメント'])
            ->addColumn('prefecture_id', 'integer',['comment' => '都道府県ID'])
            ->addColumn('user_id', 'integer',['comment' => 'ユーザーID'])
            ->addColumn('is_deleted', 'boolean', ['default' => false , 'null' => false, 'comment' => '論理削除'])
            ->addColumn('modified', 'timestamp')
            ->addColumn('created', 'timestamp')
            ->create();
    }
}
