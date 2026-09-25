<?php

namespace Tests\Feature;

use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    /**
     * Test 404 custom error page.
     */
    public function test_404_error_page_renders_correctly(): void
    {
        $response = $this->get('/duong-dan-khong-ton-tai-' . uniqid());

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Không Tìm Thấy Trang');
        $response->assertSee('Về Trang Chủ');
    }

    /**
     * Test 403 view directly.
     */
    public function test_403_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.403');

        $view->assertSee('403');
        $view->assertSee('Khu Vực Bị Hạn Chế Truy Cập');
        $view->assertSee('Đăng Nhập Quản Trị');
    }

    /**
     * Test 419 view directly.
     */
    public function test_419_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.419');

        $view->assertSee('419');
        $view->assertSee('Phiên Làm Việc Đã Hết Hạn');
        $view->assertSee('Tải Lại Trang');
    }

    /**
     * Test 429 view directly.
     */
    public function test_429_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.429');

        $view->assertSee('429');
        $view->assertSee('Yêu Cầu Quá Dồn Dập');
    }

    /**
     * Test 500 view directly.
     */
    public function test_500_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.500');

        $view->assertSee('500');
        $view->assertSee('Đã Xảy Ra Sự Cố Kỹ Thuật');
        $view->assertSee('Tải Lại Trang');
    }

    /**
     * Test 503 view directly.
     */
    public function test_503_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.503');

        $view->assertSee('503');
        $view->assertSee('Không Gian Đang Được Nâng Cấp');
    }
}
