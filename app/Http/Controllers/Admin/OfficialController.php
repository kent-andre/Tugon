<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Official;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfficialController extends Controller
{
    public function index()
    {
        return view('admin.officials', [
            'officials'   => Official::ordered()->get()->groupBy('group'),
            'groupLabels' => config('pabarang.official_groups'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) Official::max('sort_order') + 1;
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('officials', 'public');
        }

        $o = Official::create($data);
        Audit::log('Added official', $o->name, ['position' => $o->position, 'group' => $o->group]);

        return back()->with('status', $o->name . ' added.');
    }

    public function update(Request $request, Official $official)
    {
        $data = $this->validated($request);
        $old = $official->photo_path;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('officials', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $data['photo_path'] = null;
        }
        $official->update($data);

        if ($old && $old !== $official->photo_path && ! str_starts_with($old, 'images/')) {
            Storage::disk('public')->delete($old);
        }

        Audit::log('Updated official', $official->name, ['position' => $official->position, 'group' => $official->group]);

        return back()->with('status', $official->name . ' updated.');
    }

    public function destroy(Official $official)
    {
        Audit::log('Removed official', $official->name, ['position' => $official->position, 'group' => $official->group]);

        if ($official->photo_path && ! str_starts_with($official->photo_path, 'images/')) {
            Storage::disk('public')->delete($official->photo_path);
        }
        $official->delete();

        return back()->with('status', 'Official removed from the roster.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'group'     => 'required|in:' . implode(',', array_keys(config('pabarang.official_groups'))),
            'name'      => 'required|string|max:120',
            'position'  => 'required|string|max:80',
            'committee' => 'nullable|string|max:120',
            'contact'   => ['nullable', 'string', 'max:30', 'regex:/^[0-9 +()-]*$/'],
            'term'      => 'nullable|string|max:40',
            'photo'     => 'nullable|image|max:2048',
        ], [
            'contact.regex' => 'Contact number can only contain digits, spaces, +, - and parentheses.',
        ]);
        unset($data['photo']);   // the file is handled separately

        return $data;
    }
}
