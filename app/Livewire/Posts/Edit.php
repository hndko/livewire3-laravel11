<?php

namespace App\Livewire\Posts;

use Storage;
use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    use WithFileUploads;

    //id post
    public $postID;

    //images
    public $images;

    #[Rule('required', message: 'Masukkan Judul Post')]
    public $title;

    #[Rule('required', message: 'Masukkan Isi Post')]
    #[Rule('min:3', message: 'Isi Post Minimal 3 Karakter')]
    public $content;

    public function render()
    {
        return view('livewire.posts.edit');
    }

    public function mount($id)
    {
        //get post
        $post = Post::find($id);

        //assign
        $this->postID   = $post->id;
        $this->title    = $post->title;
        $this->content  = $post->content;
    }

    /**
     * update
     *
     * @return void
     */
    public function update()
    {
        $this->validate();

        //get post
        $post = Post::find($this->postID);

        //check if images
        if ($this->images) {

            // Debugging: Cetak informasi file
            \Log::info('Uploaded file info:', [
                'name' => $this->images->getClientOriginalName(),
                'size' => $this->images->getSize(),
                'mime' => $this->images->getMimeType(),
                'extension' => $this->images->getClientOriginalExtension(),
            ]);

            // Simpan gambar ke folder `storage/app/public/posts`
            $path = $this->images->store('posts', 'public');
            // Hapus gambar lama jika ada
            if ($post->images) {
                Storage::disk('public')->delete('posts/' . $post->images);
            }

            // Debugging: Cetak path penyimpanan
            \Log::info('File stored at: ' . $path);

            // Periksa apakah file benar-benar ada
            if (\Storage::disk('public')->exists($path)) {
                \Log::info('File exists at: ' . $path);
            } else {
                \Log::error('File does not exist at: ' . $path);
            }

            //update post
            $post->update([
                'images' => $this->images->hashName(),
                'title' => $this->title,
                'content' => $this->content,
            ]);
        } else {

            //update post
            $post->update([
                'title' => $this->title,
                'content' => $this->content,
            ]);
        }

        //flash message
        session()->flash('message', 'Data Berhasil Diupdate.');

        //redirect
        return redirect()->route('posts.index');
    }
}
