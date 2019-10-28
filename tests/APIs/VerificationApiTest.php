<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Verification;

class VerificationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_verification()
    {
        $verification = factory(Verification::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/verifications', $verification
        );

        $this->assertApiResponse($verification);
    }

    /**
     * @test
     */
    public function test_read_verification()
    {
        $verification = factory(Verification::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/verifications/'.$verification->id
        );

        $this->assertApiResponse($verification->toArray());
    }

    /**
     * @test
     */
    public function test_update_verification()
    {
        $verification = factory(Verification::class)->create();
        $editedVerification = factory(Verification::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/verifications/'.$verification->id,
            $editedVerification
        );

        $this->assertApiResponse($editedVerification);
    }

    /**
     * @test
     */
    public function test_delete_verification()
    {
        $verification = factory(Verification::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/verifications/'.$verification->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/verifications/'.$verification->id
        );

        $this->response->assertStatus(404);
    }
}
