<?php namespace Tests\Repositories;

use App\Models\DeviceOwner;
use App\Repositories\DeviceOwnerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DeviceOwnerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeviceOwnerRepository
     */
    protected $deviceOwnerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->deviceOwnerRepo = \App::make(DeviceOwnerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->make()->toArray();

        $createdDeviceOwner = $this->deviceOwnerRepo->create($deviceOwner);

        $createdDeviceOwner = $createdDeviceOwner->toArray();
        $this->assertArrayHasKey('id', $createdDeviceOwner);
        $this->assertNotNull($createdDeviceOwner['id'], 'Created DeviceOwner must have id specified');
        $this->assertNotNull(DeviceOwner::find($createdDeviceOwner['id']), 'DeviceOwner with given id must be in DB');
        $this->assertModelData($deviceOwner, $createdDeviceOwner);
    }

    /**
     * @test read
     */
    public function test_read_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->create();

        $dbDeviceOwner = $this->deviceOwnerRepo->find($deviceOwner->id);

        $dbDeviceOwner = $dbDeviceOwner->toArray();
        $this->assertModelData($deviceOwner->toArray(), $dbDeviceOwner);
    }

    /**
     * @test update
     */
    public function test_update_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->create();
        $fakeDeviceOwner = factory(DeviceOwner::class)->make()->toArray();

        $updatedDeviceOwner = $this->deviceOwnerRepo->update($fakeDeviceOwner, $deviceOwner->id);

        $this->assertModelData($fakeDeviceOwner, $updatedDeviceOwner->toArray());
        $dbDeviceOwner = $this->deviceOwnerRepo->find($deviceOwner->id);
        $this->assertModelData($fakeDeviceOwner, $dbDeviceOwner->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_device_owner()
    {
        $deviceOwner = factory(DeviceOwner::class)->create();

        $resp = $this->deviceOwnerRepo->delete($deviceOwner->id);

        $this->assertTrue($resp);
        $this->assertNull(DeviceOwner::find($deviceOwner->id), 'DeviceOwner should not exist in DB');
    }
}
