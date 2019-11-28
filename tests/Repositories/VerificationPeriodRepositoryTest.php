<?php namespace Tests\Repositories;

use App\Models\VerificationPeriod;
use App\Repositories\VerificationPeriodRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VerificationPeriodRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VerificationPeriodRepository
     */
    protected $verificationPeriodRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->verificationPeriodRepo = \App::make(VerificationPeriodRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->make()->toArray();

        $createdVerificationPeriod = $this->verificationPeriodRepo->create($verificationPeriod);

        $createdVerificationPeriod = $createdVerificationPeriod->toArray();
        $this->assertArrayHasKey('id', $createdVerificationPeriod);
        $this->assertNotNull($createdVerificationPeriod['id'], 'Created VerificationPeriod must have id specified');
        $this->assertNotNull(VerificationPeriod::find($createdVerificationPeriod['id']), 'VerificationPeriod with given id must be in DB');
        $this->assertModelData($verificationPeriod, $createdVerificationPeriod);
    }

    /**
     * @test read
     */
    public function test_read_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->create();

        $dbVerificationPeriod = $this->verificationPeriodRepo->find($verificationPeriod->id);

        $dbVerificationPeriod = $dbVerificationPeriod->toArray();
        $this->assertModelData($verificationPeriod->toArray(), $dbVerificationPeriod);
    }

    /**
     * @test update
     */
    public function test_update_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->create();
        $fakeVerificationPeriod = factory(VerificationPeriod::class)->make()->toArray();

        $updatedVerificationPeriod = $this->verificationPeriodRepo->update($fakeVerificationPeriod, $verificationPeriod->id);

        $this->assertModelData($fakeVerificationPeriod, $updatedVerificationPeriod->toArray());
        $dbVerificationPeriod = $this->verificationPeriodRepo->find($verificationPeriod->id);
        $this->assertModelData($fakeVerificationPeriod, $dbVerificationPeriod->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->create();

        $resp = $this->verificationPeriodRepo->delete($verificationPeriod->id);

        $this->assertTrue($resp);
        $this->assertNull(VerificationPeriod::find($verificationPeriod->id), 'VerificationPeriod should not exist in DB');
    }
}
