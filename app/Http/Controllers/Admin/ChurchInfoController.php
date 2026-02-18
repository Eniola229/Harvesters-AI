<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchInfo;
use Illuminate\Http\Request;

class ChurchInfoController extends Controller
{
    public const CATEGORIES = [
        'about'    => 'About the Church',
        'values'   => 'Our Values',
        'faq'      => 'FAQ',
        'services' => 'Services / Programs',
        'giving'   => 'Giving & Tithes',
        'contact'  => 'Contact Info',
        'nlp'      => 'Next Level Prayers',
        'other'    => 'Other',
    ];

    public function index()
    {
        $infos = ChurchInfo::orderBy('category')->orderBy('order')->get(); // no groupBy here
        $categories = self::CATEGORIES;
        return view('admin.church-info.index', compact('infos', 'categories'));
    }

    public function create()
    {
        $categories = self::CATEGORIES;
        return view('admin.church-info.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category'  => 'required|string',
            'title'     => 'required|string|max:200',
            'content'   => 'required|string',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        ChurchInfo::create($data);
        return redirect()->route('admin.church-info.index')->with('success', 'Info added!');
    }

    public function edit(ChurchInfo $churchInfo)
    {
        $categories = self::CATEGORIES;
        return view('admin.church-info.edit', compact('churchInfo', 'categories'));
    }

    public function update(Request $request, ChurchInfo $churchInfo)
    {
        $data = $request->validate([
            'category'  => 'required|string',
            'title'     => 'required|string|max:200',
            'content'   => 'required|string',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $churchInfo->update($data);
        return redirect()->route('admin.church-info.index')->with('success', 'Info updated!');
    }

    public function destroy(ChurchInfo $churchInfo)
    {
        $churchInfo->delete();
        return redirect()->route('admin.church-info.index')->with('success', 'Info deleted.');
    }
}