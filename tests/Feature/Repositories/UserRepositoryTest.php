<?php

namespace Repositories;

use App\Models\User;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(UserRepository::class);
    }

    public function testFirstOrNullIfNotExist()
    {
        $user = $this->repository->getFirstOrNull(1);

        $this->assertNull($user);
    }

    public function testFirstOrNullIfExist()
    {
        $user_created = User::factory()->createOne();
        $user_found = $this->repository->getFirstOrNull($user_created->id);

        $this->assertNotNull($user_found);
        $this->assertInstanceOf(User::class, $user_found);
        $this->assertEquals($user_created->id, $user_found->id);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }

    public function testGetAuthenticatedIfAuth()
    {
        $user_created = User::factory()->createOne();
        $this->actingAs($user_created);

        $user_logged = $this->repository->getAuthenticated();

        $this->assertNotNull($user_logged);
        $this->assertInstanceOf(User::class, $user_logged);
        $this->assertEquals($user_created->id, $user_logged->id);
    }

    public function testGetAuthenticatedIfNot()
    {
        $user_logged = $this->repository->getAuthenticated();

        $this->assertNull($user_logged);
    }
}
