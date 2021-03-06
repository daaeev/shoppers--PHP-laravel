<?php

namespace Services;

use App\Services\ImageProfiler;
use App\Services\Interfaces\ImageProfilerInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use function app;

class ImageProfillerTest extends TestCase
{
    protected ImageProfilerInterface $profiler;

    protected string $imager_dir = 'products_images';
    protected string $disk = 'public';

    public function setUp(): void
    {
        parent::setUp();

        $this->profiler = (app(ImageProfiler::class))
            ->disk($this->disk)
            ->directory($this->imager_dir);
    }

    public function testSaveImageSuccess()
    {
        $localfile = dirname(__DIR__) . '/test_files/image.png';

        $image = new UploadedFile(
            $localfile,
            'image.png',
            'image/*',
            null,
            true
        );

        Storage::fake($this->disk);

        $image_name = $this->profiler->saveImage($image);

        $this->assertNotFalse($image_name);
        Storage::disk($this->disk)->assertExists($this->imager_dir . '/' . $image_name);
    }

    public function testSaveImageFailed()
    {
        $file_mock = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['storeAs', 'getClientOriginalExtension'])
            ->getMock();

        $file_mock->expects($this->once())
            ->method('getClientOriginalExtension')
            ->willReturn('png');

        $file_mock->expects($this->once())
            ->method('storeAs')
            ->willReturn(false);

        $result = $this->profiler->saveImage($file_mock);
        $this->assertFalse($result);
    }

    public function testDeleteImageSuccess()
    {
        $localfile = dirname(__DIR__) . '/test_files/image.png';

        $image = new UploadedFile(
            $localfile,
            'image.png',
            'image/*',
            null,
            true
        );

        Storage::fake($this->disk);

        $image_name = $this->profiler->saveImage($image);
        Storage::disk($this->disk)->assertExists($this->imager_dir . '/' . $image_name);

        $result = $this->profiler->deleteImage($image_name);

        $this->assertTrue($result);
        Storage::disk($this->disk)->assertMissing($this->imager_dir . '/' . $image_name);
    }

    public function testDeleteImageFailed()
    {
        Storage::fake($this->disk);
        $result_wit_empty = $this->profiler->deleteImage('');
        $result_with_undefined_file = $this->profiler->deleteImage('undefined_file.error');

        $this->assertFalse($result_wit_empty);
        $this->assertTrue($result_with_undefined_file);
    }

    public function testSaveTwoImageSuccess()
    {
        $localfile = dirname(__DIR__) . '/test_files/image.png';

        $image = new UploadedFile(
            $localfile,
            'image.png',
            'image/*',
            null,
            true
        );

        Storage::fake($this->disk);

        $images = $this->profiler->saveTwoImages($image, $image);

        $this->assertIsArray($images);
        $this->assertCount(2, $images);
        Storage::disk($this->disk)->assertExists($this->imager_dir . '/' . $images[0]);
        Storage::disk($this->disk)->assertExists($this->imager_dir . '/' . $images[1]);
    }

    public function testSaveTwoImagesOneFailed()
    {
        $file_mock = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['storeAs', 'getClientOriginalExtension'])
            ->getMock();

        $file_mock->expects($this->exactly(2))
            ->method('getClientOriginalExtension')
            ->willReturn('png');

        $file_mock->expects($this->exactly(2))
            ->method('storeAs')
            ->willReturn(false);

        $localfile = dirname(__DIR__) . '/test_files/image.png';

        $image = new UploadedFile(
            $localfile,
            'image.png',
            'image/*',
            null,
            true
        );

        Storage::fake($this->disk);

        $result_first_failed = $this->profiler->saveTwoImages($file_mock, $image);
        $result_second_failed = $this->profiler->saveTwoImages($image, $file_mock);

        $this->assertFalse($result_first_failed);
        $this->assertFalse($result_second_failed);
    }
}
