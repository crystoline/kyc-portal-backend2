<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AgentType;

class AgentTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_agent_type()
    {
        $agentType = factory(AgentType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/agent_types', $agentType
        );

        $this->assertApiResponse($agentType);
    }

    /**
     * @test
     */
    public function test_read_agent_type()
    {
        $agentType = factory(AgentType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/agent_types/'.$agentType->id
        );

        $this->assertApiResponse($agentType->toArray());
    }

    /**
     * @test
     */
    public function test_update_agent_type()
    {
        $agentType = factory(AgentType::class)->create();
        $editedAgentType = factory(AgentType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/agent_types/'.$agentType->id,
            $editedAgentType
        );

        $this->assertApiResponse($editedAgentType);
    }

    /**
     * @test
     */
    public function test_delete_agent_type()
    {
        $agentType = factory(AgentType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/agent_types/'.$agentType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/agent_types/'.$agentType->id
        );

        $this->response->assertStatus(404);
    }
}
