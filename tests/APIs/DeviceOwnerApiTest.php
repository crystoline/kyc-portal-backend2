<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DeviceOwner;

class DeviceOwnerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/device_owners', $deviceOwner
        );

        $this->assertApiResponse($deviceOwner);
    }

    /**
     * @test
     */
    public function test_read_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/device_owners/'.$deviceOwner->id
        );

        $this->assertApiResponse($deviceOwner->toArray());
    }

    /**
     * @test
     */
    public function test_update_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->create();
        $editedDeviceOwner = factory(DeviceOwner::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/device_owners/'.$deviceOwner->id,
            $editedDeviceOwner
        );

        $this->assertApiResponse($editedDeviceOwner);
    }

    /**
     * @test
     */
    public function test_delete_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/device_owners/'.$deviceOwner->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/device_owners/'.$deviceOwner->id
        );

        $this->response->assertStatus(404);
    }
}
