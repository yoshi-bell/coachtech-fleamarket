<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('index', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load(['seller', 'condition', 'categories']);
        return view('item.show', compact('item'));
    }
}