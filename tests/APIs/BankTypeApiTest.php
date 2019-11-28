<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankType;

class BankTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bank_type()
    {
        $bankType = factory(BankType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bank_types', $bankType
        );

        $this->assertApiResponse($bankType);
    }

    /**
     * @test
     */
    public function test_read_bank_type()
    {
        $bankType = factory(BankType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bank_types/'.$bankType->id
        );

        $this->assertApiResponse($bankType->toArray());
    }

    /**
     * @test
     */
    public function test_update_bank_type()
    {
        $bankType = factory(BankType::class)->create();
        $editedBankType = factory(BankType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bank_types/'.$bankType->id,
            $editedBankType
        );

        $this->assertApiResponse($editedBankType);
    }

    /**
     * @test
     */
    public function test_delete_bank_type()
    {
        $bankType = factory(BankType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bank_types/'.$bankType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bank_types/'.$bankType->id
        );

        $this->response->assertStatus(404);
    }
}
