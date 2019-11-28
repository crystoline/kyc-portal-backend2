<?php namespace Tests\Repositories;

use App\Models\Territory;
use App\Repositories\TerritoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TerritoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TerritoryRepository
     */
    protected $territoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->territoryRepo = \App::make(TerritoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_territory()
    {
        $territory = factory(Territory::class)->make()->toArray();

        $createdTerritory = $this->territoryRepo->create($territory);

        $createdTerritory = $createdTerritory->toArray();
        $this->assertArrayHasKey('id', $createdTerritory);
        $this->assertNotNull($createdTerritory['id'], 'Created Territory must have id specified');
        $this->assertNotNull(Territory::find($createdTerritory['id']), 'Territory with given id must be in DB');
        $this->assertModelData($territory, $createdTerritory);
    }

    /**
     * @test read
     */
    public function test_read_territory()
    {
        $territory = factory(Territory::class)->create();

        $dbTerritory = $this->territoryRepo->find($territory->id);

        $dbTerritory = $dbTerritory->toArray();
        $this->assertModelData($territory->toArray(), $dbTerritory);
    }

    /**
     * @test update
     */
    public function test_update_territory()
    {
        $territory = factory(Territory::class)->create();
        $fakeTerritory = factory(Territory::class)->make()->toArray();

        $updatedTerritory = $this->territoryRepo->update($fakeTerritory, $territory->id);

        $this->assertModelData($fakeTerritory, $updatedTerritory->toArray());
        $dbTerritory = $this->territoryRepo->find($territory->id);
        $this->assertModelData($fakeTerritory, $dbTerritory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_territory()
    {
        $territory = factory(Territory::class)->create();

        $resp = $this->territoryRepo->delete($territory->id);

        $this->assertTrue($resp);
        $this->assertNull(Territory::find($territory->id), 'Territory should not exist in DB');
    }
}
