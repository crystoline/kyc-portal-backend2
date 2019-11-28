<?php namespace Tests\Repositories;

use App\Models\AgentType;
use App\Repositories\AgentTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AgentTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AgentTypeRepository
     */
    protected $agentTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->agentTypeRepo = \App::make(AgentTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_agent_type()
    {
        $agentType = factory(AgentType::class)->make()->toArray();

        $createdAgentType = $this->agentTypeRepo->create($agentType);

        $createdAgentType = $createdAgentType->toArray();
        $this->assertArrayHasKey('id', $createdAgentType);
        $this->assertNotNull($createdAgentType['id'], 'Created AgentType must have id specified');
        $this->assertNotNull(AgentType::find($createdAgentType['id']), 'AgentType with given id must be in DB');
        $this->assertModelData($agentType, $createdAgentType);
    }

    /**
     * @test read
     */
    public function test_read_agent_type()
    {
        $agentType = factory(AgentType::class)->create();

        $dbAgentType = $this->agentTypeRepo->find($agentType->id);

        $dbAgentType = $dbAgentType->toArray();
        $this->assertModelData($agentType->toArray(), $dbAgentType);
    }

    /**
     * @test update
     */
    public function test_update_agent_type()
    {
        $agentType = factory(AgentType::class)->create();
        $fakeAgentType = factory(AgentType::class)->make()->toArray();

        $updatedAgentType = $this->agentTypeRepo->update($fakeAgentType, $agentType->id);

        $this->assertModelData($fakeAgentType, $updatedAgentType->toArray());
        $dbAgentType = $this->agentTypeRepo->find($agentType->id);
        $this->assertModelData($fakeAgentType, $dbAgentType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_agent_type()
    {
        $agentType = factory(AgentType::class)->create();

        $resp = $this->agentTypeRepo->delete($agentType->id);

        $this->assertTrue($resp);
        $this->assertNull(AgentType::find($agentType->id), 'AgentType should not exist in DB');
    }
}
