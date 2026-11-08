<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaduanSaranaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $siswa;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->siswa = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa@test.com',
            'password' => 'password',
            'role' => 'siswa',
            'nis' => '99999',
            'kelas' => 'XII RPL',
        ]);

        $this->category = Category::create([
            'nama' => 'Fasilitas Test',
        ]);
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_dashboard_and_resources(): void
    {
        $this->actingAs($this->admin);

        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/categories')->assertStatus(200);
        $this->get('/admin/users')->assertStatus(200);
        $this->get('/admin/pengaduans')->assertStatus(200);
    }

    public function test_siswa_can_access_pengaduans_but_not_categories_or_users(): void
    {
        $this->actingAs($this->siswa);

        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/pengaduans')->assertStatus(200);
        $this->get('/admin/pengaduans/create')->assertStatus(200);

        // Categories and Users should be forbidden
        $this->get('/admin/categories')->assertStatus(403);
        $this->get('/admin/categories/create')->assertStatus(403);
        $this->get('/admin/users')->assertStatus(403);
        $this->get('/admin/users/create')->assertStatus(403);
    }

    public function test_siswa_only_sees_own_pengaduan(): void
    {
        $pengaduanSiswa = Pengaduan::create([
            'user_id' => $this->siswa->id,
            'category_id' => $this->category->id,
            'lokasi' => 'Ruang 1',
            'deskripsi' => 'AC Rusak',
            'status' => 'Menunggu',
        ]);

        $pengaduanAdmin = Pengaduan::create([
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'lokasi' => 'Kantor',
            'deskripsi' => 'Meja Rusak',
            'status' => 'Menunggu',
        ]);

        $this->actingAs($this->siswa);
        $querySiswa = \App\Filament\Resources\Pengaduans\PengaduanResource::getEloquentQuery()->get();
        $this->assertTrue($querySiswa->contains($pengaduanSiswa));
        $this->assertFalse($querySiswa->contains($pengaduanAdmin));

        $this->actingAs($this->admin);
        $queryAdmin = \App\Filament\Resources\Pengaduans\PengaduanResource::getEloquentQuery()->get();
        $this->assertTrue($queryAdmin->contains($pengaduanSiswa));
        $this->assertTrue($queryAdmin->contains($pengaduanAdmin));
    }
}

