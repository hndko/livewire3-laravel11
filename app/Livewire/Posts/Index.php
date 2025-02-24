<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class Index extends Component
{
    #[On('post-updated ')]
    public function refreshPosts()
    {
        // Method ini akan dipanggil ketika event 'post-updated' diterima
        // Tidak perlu melakukan apa-apa karena Livewire otomatis akan me-render ulang
    }

    public function render()
    {
        $data = [
            'posts' => Post::latest()->paginate(5)
        ];

        return view('livewire.posts.index', $data);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        // Find the post
        $post = Post::findOrFail($id);

        // Delete the images if it exists
        if ($post->images) {
            Storage::disk('public')->delete('posts/' . $post->images);
        }

        // Destroy the post
        $post->delete();

        // Flash message
        session()->flash('message', 'Data Berhasil Dihapus.');

        // Redirect
        return redirect()->route('posts.index');
    }
}
