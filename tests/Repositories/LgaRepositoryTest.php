<?php namespace Tests\Repositories;

use App\Models\Lga;
use App\Repositories\LgaRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LgaRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LgaRepository
     */
    protected $lgaRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->lgaRepo = \App::make(LgaRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_lga()
    {
        $lga = factory(Lga::class)->make()->toArray();

        $createdLga = $this->lgaRepo->create($lga);

        $createdLga = $createdLga->toArray();
        $this->assertArrayHasKey('id', $createdLga);
        $this->assertNotNull($createdLga['id'], 'Created Lga must have id specified');
        $this->assertNotNull(Lga::find($createdLga['id']), 'Lga with given id must be in DB');
        $this->assertModelData($lga, $createdLga);
    }

    /**
     * @test read
     */
    public function test_read_lga()
    {
        $lga = factory(Lga::class)->create();

        $dbLga = $this->lgaRepo->find($lga->id);

        $dbLga = $dbLga->toArray();
        $this->assertModelData($lga->toArray(), $dbLga);
    }

    /**
     * @test update
     */
    public function test_update_lga()
    {
        $lga = factory(Lga::class)->create();
        $fakeLga = factory(Lga::class)->make()->toArray();

        $updatedLga = $this->lgaRepo->update($fakeLga, $lga->id);

        $this->assertModelData($fakeLga, $updatedLga->toArray());
        $dbLga = $this->lgaRepo->find($lga->id);
        $this->assertModelData($fakeLga, $dbLga->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_lga()
    {
        $lga = factory(Lga::class)->create();

        $resp = $this->lgaRepo->delete($lga->id);

        $this->assertTrue($resp);
        $this->assertNull(Lga::find($lga->id), 'Lga should not exist in DB');
    }
}
