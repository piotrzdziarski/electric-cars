<?php

namespace Tests\Feature;

use App\Advert;
use App\Http\Controllers\AdvertsController;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdvertsTest extends TestCase
{
    /**
     * @var AdvertsController
     */
    private $advertsController;

    /**
     * Set up AdvertController
     */
    public function setUp()
    {
        parent::setUp();
        $this->advertsController = new AdvertsController(new Advert());
    }

    /**
     * test index method from AdvertController
     */
    public function testIndexMethod()
    {
        //without any data
        $request = Request::create('', 'GET');
        $this->json('GET', '/api/announcements')
            ->assertStatus(200)
            ->assertSee(json_encode($this->advertsController->index($request)));

        //with data
        $data = [
            'per_page' => 20,
            'order_by' => 'lowest_price',
            'looking_for' => 'eA'
        ];
        $request = Request::create('', 'GET', $data);
        $this->json('GET', '/api/announcements', $data)
            ->assertStatus(200)
            ->assertSee(json_encode($this->advertsController->index($request)));
    }


    /**
     * Test advanced searching
     */
    public function testAdvancedSearching()
    {
        $data = [
            'per_page' => 20,
            'order_by' => 'lowest_price',
            'looking_for' => 'eA',
            'min_price' => '50000',
            'max_price' => '75000',
            'user_settings' => [["Any", "New"],["Any"],["Any"],["Any"],["Any"]]
        ];
        $request = Request::create('', 'POST', $data);
        $this->json('POST', '/api/advanced_search', $data)
            ->assertStatus(200)
            ->assertSee(json_encode($this->advertsController->advanced_search($request)));
    }
}
