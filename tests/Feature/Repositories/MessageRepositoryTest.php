<?php

namespace Tests\Feature\Repositories;

use App\Models\Message;
use App\Services\Interfaces\MessageRepositoryInterface;
use App\Services\Repositories\MessageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class MessageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected MessageRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(MessageRepository::class);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }

    public function testFirstOrNullIfNotExist()
    {
        $mess = $this->repository->getFirstOrNull(1);

        $this->assertNull($mess);
    }

    public function testFirstOrNullIfExist()
    {
        $mess_created = Message::factory()->createOne();
        $mess_found = $this->repository->getFirstOrNull($mess_created->id);

        $this->assertNotNull($mess_found);
        $this->assertEquals($mess_created->id, $mess_found->id);
        $this->assertInstanceOf(Message::class, $mess_found);
    }
}
