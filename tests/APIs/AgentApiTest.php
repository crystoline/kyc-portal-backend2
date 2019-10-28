<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Agent;

class AgentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_agent()
    {
        $agent = factory(Agent::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/agents', $agent
        );

        $this->assertApiResponse($agent);
    }

    /**
     * @test
     */
    public function test_read_agent()
    {
        $agent = factory(Agent::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/agents/'.$agent->id
        );

        $this->assertApiResponse($agent->toArray());
    }

    /**
     * @test
     */
    public function test_update_agent()
    {
        $agent = factory(Agent::class)->create();
        $editedAgent = factory(Agent::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/agents/'.$agent->id,
            $editedAgent
        );

        $this->assertApiResponse($editedAgent);
    }

    /**
     * @test
     */
    public function test_delete_agent()
    {
        $agent = factory(Agent::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/agents/'.$agent->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/agents/'.$agent->id
        );

        $this->response->assertStatus(404);
    }
}
