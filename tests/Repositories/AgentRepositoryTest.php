<?php namespace Tests\Repositories;

use App\Models\Agent;
use App\Repositories\AgentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AgentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AgentRepository
     */
    protected $agentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->agentRepo = \App::make(AgentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_agent()
    {
        $agent = factory(Agent::class)->make()->toArray();

        $createdAgent = $this->agentRepo->create($agent);

        $createdAgent = $createdAgent->toArray();
        $this->assertArrayHasKey('id', $createdAgent);
        $this->assertNotNull($createdAgent['id'], 'Created Agent must have id specified');
        $this->assertNotNull(Agent::find($createdAgent['id']), 'Agent with given id must be in DB');
        $this->assertModelData($agent, $createdAgent);
    }

    /**
     * @test read
     */
    public function test_read_agent()
    {
        $agent = factory(Agent::class)->create();

        $dbAgent = $this->agentRepo->find($agent->id);

        $dbAgent = $dbAgent->toArray();
        $this->assertModelData($agent->toArray(), $dbAgent);
    }

    /**
     * @test update
     */
    public function test_update_agent()
    {
        $agent = factory(Agent::class)->create();
        $fakeAgent = factory(Agent::class)->make()->toArray();

        $updatedAgent = $this->agentRepo->update($fakeAgent, $agent->id);

        $this->assertModelData($fakeAgent, $updatedAgent->toArray());
        $dbAgent = $this->agentRepo->find($agent->id);
        $this->assertModelData($fakeAgent, $dbAgent->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_agent()
    {
        $agent = factory(Agent::class)->create();

        $resp = $this->agentRepo->delete($agent->id);

        $this->assertTrue($resp);
        $this->assertNull(Agent::find($agent->id), 'Agent should not exist in DB');
    }
}
