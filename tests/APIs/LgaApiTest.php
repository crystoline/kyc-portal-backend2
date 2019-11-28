<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Lga;

class LgaApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_lga()
    {
        $lga = factory(Lga::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/lgas', $lga
        );

        $this->assertApiResponse($lga);
    }

    /**
     * @test
     */
    public function test_read_lga()
    {
        $lga = factory(Lga::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/lgas/'.$lga->id
        );

        $this->assertApiResponse($lga->toArray());
    }

    /**
     * @test
     */
    public function test_update_lga()
    {
        $lga = factory(Lga::class)->create();
        $editedLga = factory(Lga::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/lgas/'.$lga->id,
            $editedLga
        );

        $this->assertApiResponse($editedLga);
    }

    /**
     * @test
     */
    public function test_delete_lga()
    {
        $lga = factory(Lga::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/lgas/'.$lga->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/lgas/'.$lga->id
        );

        $this->response->assertStatus(404);
    }
}
