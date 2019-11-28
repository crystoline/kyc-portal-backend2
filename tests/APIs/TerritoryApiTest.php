<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Territory;

class TerritoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_territory()
    {
        $territory = factory(Territory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/territories', $territory
        );

        $this->assertApiResponse($territory);
    }

    /**
     * @test
     */
    public function test_read_territory()
    {
        $territory = factory(Territory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/territories/'.$territory->id
        );

        $this->assertApiResponse($territory->toArray());
    }

    /**
     * @test
     */
    public function test_update_territory()
    {
        $territory = factory(Territory::class)->create();
        $editedTerritory = factory(Territory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/territories/'.$territory->id,
            $editedTerritory
        );

        $this->assertApiResponse($editedTerritory);
    }

    /**
     * @test
     */
    public function test_delete_territory()
    {
        $territory = factory(Territory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/territories/'.$territory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/territories/'.$territory->id
        );

        $this->response->assertStatus(404);
    }
}
