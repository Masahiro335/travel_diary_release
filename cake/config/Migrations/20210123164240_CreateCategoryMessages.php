<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateCategoryMessages extends AbstractMigration
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
        $table = $this->table('category_messages');
        $table->addColumn('message_id', 'integer',['comment' => 'メッセージID'])
        ->addColumn('category_id', 'integer',['comment' => 'カテゴリーID'])
        ->addColumn('modified', 'timestamp')
        ->addColumn('created', 'timestamp')
        ->create();
    }
}
