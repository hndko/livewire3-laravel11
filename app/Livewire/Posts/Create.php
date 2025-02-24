<?php

namespace App\Livewire\Posts;

use Log;
use Storage;
use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    //images
    #[Rule('required', message: 'Masukkan Gambar Post')]
    #[Rule('image', message: 'File Harus Gambar')]
    #[Rule('max:2048', message: 'Ukuran File Maksimal 1MB')]
    public $images;

    //title
    #[Rule('required', message: 'Masukkan Judul Post')]
    public $title;

    //content
    #[Rule('required', message: 'Masukkan Isi Post')]
    #[Rule('min:3', message: 'Isi Post Minimal 3 Karakter')]
    public $content;

    /**
     * render
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.posts.create');
    }

    /**
     * store
     *
     * @return void
     */
    public function store()
    {
        $this->validate();

        // Debugging: Cetak informasi file
        \Log::info('Uploaded file info:', [
            'name' => $this->images->getClientOriginalName(),
            'size' => $this->images->getSize(),
            'mime' => $this->images->getMimeType(),
            'extension' => $this->images->getClientOriginalExtension(),
        ]);

        // Simpan gambar ke folder `storage/app/public/posts`
        $path = $this->images->store('posts', 'public');

        // Debugging: Cetak path penyimpanan
        \Log::info('File stored at: ' . $path);

        // Periksa apakah file benar-benar ada
        if (\Storage::disk('public')->exists($path)) {
            \Log::info('File exists at: ' . $path);
        } else {
            \Log::error('File does not exist at: ' . $path);
        }

        // Buat post
        Post::create([
            'images' => $this->images->hashName(), // Simpan path relatif ke `storage/app/public`
            'title' => $this->title,
            'content' => $this->content,
        ]);

        // Kirim event ke semua komponen yang sedang aktif
        $this->dispatch('post-updated');

        // Flash message
        session()->flash('message', 'Data Berhasil Disimpan.');

        // Redirect
        return redirect()->route('posts.index');
    }
}
