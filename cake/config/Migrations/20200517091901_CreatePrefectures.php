<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreatePrefectures extends AbstractMigration
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
        $table = $this->table('prefectures');
        $table->addColumn('name', 'text',['comment' => '都道府県名'])
              ->addColumn('modified', 'timestamp')
              ->addColumn('created', 'timestamp')
              ->create();
    }
}
