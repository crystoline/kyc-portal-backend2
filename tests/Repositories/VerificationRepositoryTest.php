<?php namespace Tests\Repositories;

use App\Models\Verification;
use App\Repositories\VerificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VerificationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VerificationRepository
     */
    protected $verificationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->verificationRepo = \App::make(VerificationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_verification()
    {
        $verification = factory(Verification::class)->make()->toArray();

        $createdVerification = $this->verificationRepo->create($verification);

        $createdVerification = $createdVerification->toArray();
        $this->assertArrayHasKey('id', $createdVerification);
        $this->assertNotNull($createdVerification['id'], 'Created Verification must have id specified');
        $this->assertNotNull(Verification::find($createdVerification['id']), 'Verification with given id must be in DB');
        $this->assertModelData($verification, $createdVerification);
    }

    /**
     * @test read
     */
    public function test_read_verification()
    {
        $verification = factory(Verification::class)->create();

        $dbVerification = $this->verificationRepo->find($verification->id);

        $dbVerification = $dbVerification->toArray();
        $this->assertModelData($verification->toArray(), $dbVerification);
    }

    /**
     * @test update
     */
    public function test_update_verification()
    {
        $verification = factory(Verification::class)->create();
        $fakeVerification = factory(Verification::class)->make()->toArray();

        $updatedVerification = $this->verificationRepo->update($fakeVerification, $verification->id);

        $this->assertModelData($fakeVerification, $updatedVerification->toArray());
        $dbVerification = $this->verificationRepo->find($verification->id);
        $this->assertModelData($fakeVerification, $dbVerification->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_verification()
    {
        $verification = factory(Verification::class)->create();

        $resp = $this->verificationRepo->delete($verification->id);

        $this->assertTrue($resp);
        $this->assertNull(Verification::find($verification->id), 'Verification should not exist in DB');
    }
}
