<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Prefectures seed.
 */
class PrefecturesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $prefectures = [
            '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県'
            ,'栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県'
            ,'石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県'
            ,'京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県'
            ,'山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県'
            ,'大分県','宮崎県','鹿児島県','沖縄県'
            ];
        $data = [];

        for ($i = 0; $i < 47; $i++) {
            $data[] = 
                [
                    'name' => $prefectures[$i],
                    'modified' => date('Y-m-d H:i:s'),
                    'created' => date('Y-m-d H:i:s'),
                ];
        }
        var_dump($data);
        $table = $this->table('prefectures');
        $table->insert($data)->save();
    }
}
