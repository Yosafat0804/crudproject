<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use illuminate\View\View;

class ProductController extends Controller
{
    // 
    public function index(): View
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    // buat method create
    public function create(): View
    {
        return view('products.create');
    }

    // method untuk simpan data ke tabvel database
    public function store(Request $request): RedirectResponse
    {
        // kode untuk validasi inputan
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('products', $image->hashName());

        // kirimkan data input ke tabel database3
        Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan']);
    }

    // method untuk detail produk
    public function show(string $id): View
    {
        // ambil id produk
        $product = Product::findOrFail($id);
        // render view
        return view('products.show', compact('product'));
    }

    // buat method untuk view data yang mau diubah
    public function edit(string $id): View
    {
        // ambil id produk
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }
    // method untuk ubah data di database
    public function update(Request $request, $id): RedirectResponse
    {
        // kode untuk validasi inputan
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        // get data by id produk
        $product = Product::findOrFail($id);
        // logika IF
        if ($request->hasFile('image')) {
            // hapus gambar yang lama
            Storage::delete('products/' . $product->image);

            // gantikan dengan gambar yang baru
            $image = $request->file('image');
            $image->storeAs('products', $image->hashName());

            // ubah data sesuai inputan
            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

        } else {
            // ubah data sesuai inputan
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);
        }
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah']);
    }

    // method hapus data
    public function destroy($id): RedirectResponse
    {

        // get data by id produk
        $product = Product::findOrFail($id);
        // hapus gambar
        Storage::delete('products/' . $product->image);

        // hapus data produk
        $product->delete();

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus']);
    }
}
