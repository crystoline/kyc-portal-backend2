<?php namespace Tests\Repositories;

use App\Models\Bank;
use App\Repositories\BankRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankRepository
     */
    protected $bankRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankRepo = \App::make(BankRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank()
    {
        $bank = factory(Bank::class)->make()->toArray();

        $createdBank = $this->bankRepo->create($bank);

        $createdBank = $createdBank->toArray();
        $this->assertArrayHasKey('id', $createdBank);
        $this->assertNotNull($createdBank['id'], 'Created Bank must have id specified');
        $this->assertNotNull(Bank::find($createdBank['id']), 'Bank with given id must be in DB');
        $this->assertModelData($bank, $createdBank);
    }

    /**
     * @test read
     */
    public function test_read_bank()
    {
        $bank = factory(Bank::class)->create();

        $dbBank = $this->bankRepo->find($bank->id);

        $dbBank = $dbBank->toArray();
        $this->assertModelData($bank->toArray(), $dbBank);
    }

    /**
     * @test update
     */
    public function test_update_bank()
    {
        $bank = factory(Bank::class)->create();
        $fakeBank = factory(Bank::class)->make()->toArray();

        $updatedBank = $this->bankRepo->update($fakeBank, $bank->id);

        $this->assertModelData($fakeBank, $updatedBank->toArray());
        $dbBank = $this->bankRepo->find($bank->id);
        $this->assertModelData($fakeBank, $dbBank->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank()
    {
        $bank = factory(Bank::class)->create();

        $resp = $this->bankRepo->delete($bank->id);

        $this->assertTrue($resp);
        $this->assertNull(Bank::find($bank->id), 'Bank should not exist in DB');
    }
}
