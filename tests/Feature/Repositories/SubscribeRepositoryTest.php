<?php

namespace Tests\Feature\Repositories;

use App\Models\Subscribe;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use App\Services\Repositories\SubscribeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SubscribeRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(SubscribeRepository::class);
    }

    public function testGetEmailsIfNotHave()
    {
        $result = $this->repository->getEmails();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    public function testGetEmailsIfHave()
    {
        $subs = Subscribe::factory(2)->create();

        $result = $this->repository->getEmails();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('email', $result[0]);
        $this->assertArrayNotHasKey('user_id', $result[0]);
        $this->assertArrayHasKey('email', $result[1]);
        $this->assertArrayNotHasKey('user_id', $result[1]);
    }
}
