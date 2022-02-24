<?php

namespace Tests\Feature\Support\Model;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function testListerWhenPageable()
    {
        request()->merge([
            'pageable' => true,
            'size' => 1,
        ]);

        $lister = (new Admin())->lister();

        self::assertIsArray($lister);
        self::assertArrayHasKey('pageable', $lister);
        self::assertArrayHasKey('data', $lister);
        self::assertArrayHasKey('total', $lister);
        self::assertArrayHasKey('page', $lister);
        self::assertArrayHasKey('size', $lister);
        self::assertArrayHasKey('from', $lister);
        self::assertArrayHasKey('to', $lister);

        self::assertTrue($lister['pageable']);
        self::assertEquals(2, $lister['total']);
        self::assertEquals(1, $lister['size']);
        self::assertTrue($lister['more']);
    }

    public function testListerWhenNotPageable()
    {
        request()->merge([
            'pageable' => false,
        ]);

        $lister = (new Admin())->lister();

        self::assertIsArray($lister);
        self::assertArrayHasKey('pageable', $lister);
        self::assertArrayHasKey('data', $lister);
        self::assertArrayHasKey('total', $lister);

        self::assertFalse($lister['pageable']);
        self::assertEquals(2, $lister['total']);
    }
}
