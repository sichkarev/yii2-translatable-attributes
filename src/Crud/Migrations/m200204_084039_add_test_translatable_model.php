<?php

use yii\db\Migration;

/**
 * Class m200204_084039_add_test_translatable_model
 */
class m200204_084039_add_test_translatable_model extends Migration
{
    const TABLE = 'test_translatable_model';

    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey()->comment('ID'),
            'name' => $this->string()->comment('Имя'),
            'description' => $this->string()->comment('Описание'),
            'text' => $this->string()->comment('Текст'),
            'translations' => $this->json()->comment('Переводы')
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE);
    }
}
