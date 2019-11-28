<?php namespace Tests\Repositories;

use App\Models\BankType;
use App\Repositories\BankTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankTypeRepository
     */
    protected $bankTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankTypeRepo = \App::make(BankTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank_type()
    {
        $bankType = factory(BankType::class)->make()->toArray();

        $createdBankType = $this->bankTypeRepo->create($bankType);

        $createdBankType = $createdBankType->toArray();
        $this->assertArrayHasKey('id', $createdBankType);
        $this->assertNotNull($createdBankType['id'], 'Created BankType must have id specified');
        $this->assertNotNull(BankType::find($createdBankType['id']), 'BankType with given id must be in DB');
        $this->assertModelData($bankType, $createdBankType);
    }

    /**
     * @test read
     */
    public function test_read_bank_type()
    {
        $bankType = factory(BankType::class)->create();

        $dbBankType = $this->bankTypeRepo->find($bankType->id);

        $dbBankType = $dbBankType->toArray();
        $this->assertModelData($bankType->toArray(), $dbBankType);
    }

    /**
     * @test update
     */
    public function test_update_bank_type()
    {
        $bankType = factory(BankType::class)->create();
        $fakeBankType = factory(BankType::class)->make()->toArray();

        $updatedBankType = $this->bankTypeRepo->update($fakeBankType, $bankType->id);

        $this->assertModelData($fakeBankType, $updatedBankType->toArray());
        $dbBankType = $this->bankTypeRepo->find($bankType->id);
        $this->assertModelData($fakeBankType, $dbBankType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank_type()
    {
        $bankType = factory(BankType::class)->create();

        $resp = $this->bankTypeRepo->delete($bankType->id);

        $this->assertTrue($resp);
        $this->assertNull(BankType::find($bankType->id), 'BankType should not exist in DB');
    }
}
