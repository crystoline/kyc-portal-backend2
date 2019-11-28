<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Document;

class DocumentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document()
    {
        $document = factory(Document::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/documents', $document
        );

        $this->assertApiResponse($document);
    }

    /**
     * @test
     */
    public function test_read_document()
    {
        $document = factory(Document::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/documents/'.$document->id
        );

        $this->assertApiResponse($document->toArray());
    }

    /**
     * @test
     */
    public function test_update_document()
    {
        $document = factory(Document::class)->create();
        $editedDocument = factory(Document::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/documents/'.$document->id,
            $editedDocument
        );

        $this->assertApiResponse($editedDocument);
    }

    /**
     * @test
     */
    public function test_delete_document()
    {
        $document = factory(Document::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/documents/'.$document->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/documents/'.$document->id
        );

        $this->response->assertStatus(404);
    }
}
