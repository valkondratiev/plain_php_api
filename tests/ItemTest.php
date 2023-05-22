<?php

use PHPUnit\Framework\TestCase;


class ItemTest extends TestCase
{

    private Item $itemModel;

    protected function setUp(): void
    {
        define('DB_HOST', '');
        define('DB_USER', '');
        define('DB_PASS', '');
        define('DB_NAME', '');

        $this->itemModel = new Item();
    }

    public function testAddItem()
    {

        $result = $this->itemModel->addItem([
            'name' => 'CreatedbyUnitTest',
            'phone' => '123456987',
            'key' => 'secretkey'
        ]);
        $this->assertTrue($result);

    }
}
