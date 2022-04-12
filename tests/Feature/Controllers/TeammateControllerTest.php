<?php

namespace Tests\Feature\Controllers;

use App\Models\Teammate;
use App\Models\User;
use App\Services\ImageProfiler;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\TeammatesRepositoryInterface;
use App\Services\Repositories\TeammatesRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TeammateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected UploadedFile $image;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $localfile = dirname(__DIR__) . '/test_files/image.png';

        $this->image = new UploadedFile(
            $localfile,
            'image.png',
            'image/*',
            null,
            true
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testCreateSuccess()
    {
        $data = [
            'full_name' => 'Name Name',
            'position' => 'some',
            'description' => 'some',
            'image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['image'])
            ->willReturn('image_hash.png');

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'full_name' => 'Name Name',
                'position' => 'some',
                'description' => 'some',
                'image' => 'image_hash.png',
            ]);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            Teammate::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.create'), $data)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_success');
    }

    public function testCreateImageSaveFailed()
    {
        $data = [
            'full_name' => 'Name Name',
            'position' => 'some',
            'description' => 'some',
            'image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['image'])
            ->willReturn(false);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.create'), $data)
            ->assertRedirect(route('admin.team.create.form'));

        $response->assertSessionHas('status_failed');
    }

    public function testCreateModelSaveFailed()
    {
        $data = [
            'full_name' => 'Name Name',
            'position' => 'some',
            'description' => 'some',
            'image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['image'])
            ->willReturn('image_hash.png');

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'full_name' => 'Name Name',
                'position' => 'some',
                'description' => 'some',
                'image' => 'image_hash.png',
            ]);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            Teammate::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.create'), $data)
            ->assertRedirect(route('admin.team.create.form'));

        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $data = [
            'id' => $teammate->id
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('image_hash.png')
            ->willReturn(true);

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.delete'), $data)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_success');
    }

    public function testDeleteModelDeleteFailed()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $data = [
            'id' => $teammate->id
        ];

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.delete'), $data)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_failed');
    }

    public function testDeleteOldImageDeleteFailed()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $data = [
            'id' => $teammate->id
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('image_hash.png')
            ->willReturn(false);

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.delete'), $data)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_warning');
    }

    public function testEditWithoutImageSuccess()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $edited = [
            'id' => $teammate->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited'
        ];

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'id' => $teammate->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
            ]);

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.edit'), $edited)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_success');
    }

    public function testEditWithImageSuccess()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $edited = [
            'id' => $teammate->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
            'image' => $this->image
        ];

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'id' => $teammate->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
                'image' => 'new_image_hash.png'
            ]);

        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['disk', 'directory', 'saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($edited['image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($model_mock->image)
            ->willReturn(true);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.edit'), $edited)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_success');
    }

    public function testEditWithImageNewImageSaveFailed()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $edited = [
            'id' => $teammate->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
            'image' => $this->image
        ];

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($teammate);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['disk', 'directory', 'saveImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($edited['image'])
            ->willReturn(false);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.edit'), $edited)
            ->assertRedirect(route('admin.team.edit.form', ['id' => $teammate->id]));

        $response->assertSessionHas('status_failed');
    }

    public function testEditWithImageModelSaveAndNewImageDeleteFailed()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $edited = [
            'id' => $teammate->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
            'image' => $this->image
        ];

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'id' => $teammate->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
                'image' => 'new_image_hash.png'
            ]);

        $model_mock->id = $teammate->id;
        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['disk', 'directory', 'saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($edited['image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('new_image_hash.png')
            ->willReturn(false);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.edit'), $edited)
            ->assertRedirect(route('admin.team.edit.form', ['id' => $teammate->id]));

        $response->assertSessionHas('status_failed');
    }

    public function testEditWithImageModelSaveFailedButNewImageDeleteSuccess()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $edited = [
            'id' => $teammate->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
            'image' => $this->image
        ];

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'id' => $teammate->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
                'image' => 'new_image_hash.png'
            ]);

        $model_mock->id = $teammate->id;
        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['disk', 'directory', 'saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($edited['image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('new_image_hash.png')
            ->willReturn(true);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.edit'), $edited)
            ->assertRedirect(route('admin.team.edit.form', ['id' => $teammate->id]));

        $response->assertSessionHas('status_failed');
    }

    public function testEditModelSaveSuccessWithImageButOldImageDeleteFailed()
    {
        $teammate = Teammate::factory()->createOne(['image' => 'image_hash.png']);

        $edited = [
            'id' => $teammate->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
            'image' => $this->image
        ];

        $model_mock = $this->getMockBuilder(Teammate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'id' => $teammate->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
                'image' => 'new_image_hash.png'
            ]);

        $model_mock->image = $teammate->image;

        $rep_mock = $this->getMockBuilder(TeammatesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($teammate->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['disk', 'directory', 'saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($edited['image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($model_mock->image)
            ->willReturn(false);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->instance(
            TeammatesRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.team.edit'), $edited)
            ->assertRedirect(route('admin.team'));

        $response->assertSessionHas('status_warning');
    }
}
