<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Region;

class RegionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_region()
    {
        $region = factory(Region::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/regions', $region
        );

        $this->assertApiResponse($region);
    }

    /**
     * @test
     */
    public function test_read_region()
    {
        $region = factory(Region::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/regions/'.$region->id
        );

        $this->assertApiResponse($region->toArray());
    }

    /**
     * @test
     */
    public function test_update_region()
    {
        $region = factory(Region::class)->create();
        $editedRegion = factory(Region::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/regions/'.$region->id,
            $editedRegion
        );

        $this->assertApiResponse($editedRegion);
    }

    /**
     * @test
     */
    public function test_delete_region()
    {
        $region = factory(Region::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/regions/'.$region->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/regions/'.$region->id
        );

        $this->response->assertStatus(404);
    }
}
