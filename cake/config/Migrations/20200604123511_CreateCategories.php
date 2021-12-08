<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateCategories extends AbstractMigration
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
      $table = $this->table('categories');
      $table->addColumn('name', 'text',['comment' => 'カテゴリー名'])
          ->addColumn('is_deleted', 'boolean', ['default' => false , 'null' => false, 'comment' => '論理削除'])
          ->addColumn('modified', 'timestamp')
          ->addColumn('created', 'timestamp')
          ->create();
    }
}
