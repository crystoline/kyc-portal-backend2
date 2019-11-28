<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VerificationPeriod;

class VerificationPeriodApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/verification_periods', $verificationPeriod
        );

        $this->assertApiResponse($verificationPeriod);
    }

    /**
     * @test
     */
    public function test_read_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/verification_periods/'.$verificationPeriod->id
        );

        $this->assertApiResponse($verificationPeriod->toArray());
    }

    /**
     * @test
     */
    public function test_update_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->create();
        $editedVerificationPeriod = factory(VerificationPeriod::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/verification_periods/'.$verificationPeriod->id,
            $editedVerificationPeriod
        );

        $this->assertApiResponse($editedVerificationPeriod);
    }

    /**
     * @test
     */
    public function test_delete_verification_period()
    {
        $verificationPeriod = factory(VerificationPeriod::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/verification_periods/'.$verificationPeriod->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/verification_periods/'.$verificationPeriod->id
        );

        $this->response->assertStatus(404);
    }
}
