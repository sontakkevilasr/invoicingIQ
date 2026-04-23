<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->when($request->q, fn($q, $term) => $q->search($term))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'hsn_sac'     => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'type'        => 'required|in:goods,service',
            'rate'        => 'required|numeric|min:0',
            'gst_rate'    => 'required|numeric|in:0,5,12,18,28',
            'unit'        => 'required|string|max:20',
        ]);

        $item = Item::create($data);

        if ($request->wantsJson()) {
            return response()->json($item, 201);
        }

        return redirect()->route('items.index')->with('success', 'Item added successfully.');
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'hsn_sac'     => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'type'        => 'required|in:goods,service',
            'rate'        => 'required|numeric|min:0',
            'gst_rate'    => 'required|numeric|in:0,5,12,18,28',
            'unit'        => 'required|string|max:20',
        ]);

        $item->update($data);

        if ($request->wantsJson()) {
            return response()->json($item);
        }

        return redirect()->route('items.index')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted.');
    }

    public function search(Request $request)
    {
        $items = Item::active()
            ->search($request->q ?? '')
            ->select('id', 'name', 'hsn_sac', 'description', 'rate', 'gst_rate', 'unit', 'type')
            ->limit(10)
            ->get();

        return response()->json($items);
    }
}
