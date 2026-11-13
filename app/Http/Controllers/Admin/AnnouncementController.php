<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ResidentPopup;
use App\Support\Audit;
use App\Support\Current;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements', [
            'announcements' => Announcement::forDisplay()->get(),
            'popup'         => ResidentPopup::current(),
            'categories'    => config('pabarang.announcement_categories'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['admin_id'] = Current::adminId();
        $data['posted_by'] = $data['posted_by'] ?: Current::adminName();

        $a = Announcement::create($data);
        Audit::log('Created announcement', $a->title, ['id' => $a->id, 'category' => $a->category]);

        return back()->with('status', 'Announcement posted.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $this->validated($request);
        $data['posted_by'] = $data['posted_by'] ?: $announcement->posted_by;

        $announcement->update($data);
        Audit::log('Updated announcement', $announcement->title, ['id' => $announcement->id]);

        return back()->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        Audit::log('Deleted announcement', $announcement->title, [
            'id' => $announcement->id, 'category' => $announcement->category, 'posted_by' => $announcement->posted_by,
        ]);
        $announcement->delete();

        return back()->with('status', 'Announcement deleted.');
    }

    // ---------------- Resident popup ----------------

    public function savePopup(Request $request)
    {
        $data = $request->validate([
            'enabled'      => 'nullable|boolean',
            'category'     => 'required|in:' . implode(',', array_keys(config('pabarang.announcement_categories'))),
            'title'        => 'required|string|max:80',
            'body'         => 'required|string|max:280',
            'image'        => 'nullable|image|max:4096',
            'remove_image' => 'nullable|boolean',
        ]);

        $popup = ResidentPopup::current();
        $oldImage = $popup->image_path;

        $popup->fill([
            'enabled'    => $request->boolean('enabled'),
            'category'   => $data['category'],
            'title'      => $data['title'],
            'body'       => $data['body'],
            'updated_by' => Current::adminId(),
        ]);
        if ($request->hasFile('image')) {
            $popup->image_path = $request->file('image')->store('popups', 'public');
        } elseif ($request->boolean('remove_image')) {
            $popup->image_path = null;
        }
        $popup->touch();   // new updated_at = new version, so residents see it again
        $popup->save();

        if ($oldImage && $oldImage !== $popup->image_path) {
            Storage::disk('public')->delete($oldImage);
        }

        Audit::log('Updated resident popup', $popup->title, ['enabled' => $popup->enabled]);

        return back()->with('status', $popup->enabled
            ? 'Popup saved. Residents will see it next time they log in.'
            : 'Popup saved (currently off).');
    }

    public function togglePopup()
    {
        $popup = ResidentPopup::current();
        $popup->enabled = ! $popup->enabled;
        $popup->updated_by = Current::adminId();
        $popup->save();

        Audit::log($popup->enabled ? 'Turned on resident popup' : 'Turned off resident popup', $popup->title);

        return back()->with('status', $popup->enabled ? 'Popup is now live for residents.' : 'Popup turned off.');
    }

    /** "Use as popup" on an existing announcement. */
    public function useAsPopup(Announcement $announcement)
    {
        $popup = ResidentPopup::current();
        $oldImage = $popup->image_path;

        $popup->fill([
            'enabled'    => true,
            'category'   => $announcement->category,
            'title'      => Str::limit($announcement->title, 77),
            'body'       => Str::limit($announcement->body, 277),
            'image_path' => null,
            'updated_by' => Current::adminId(),
        ]);
        $popup->touch();
        $popup->save();

        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        Audit::log('Set announcement as resident popup', $announcement->title, ['announcement_id' => $announcement->id]);

        return back()->with('status', 'Popup now shows this announcement.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category'  => 'required|in:' . implode(',', array_keys(config('pabarang.announcement_categories'))),
            'title'     => 'required|string|max:160',
            'body'      => 'required|string|max:5000',
            'posted_by' => 'nullable|string|max:120',
            'pinned'    => 'nullable|boolean',
        ]);
        $data['pinned'] = $request->boolean('pinned');
        $data['posted_by'] = trim((string) ($data['posted_by'] ?? ''));

        return $data;
    }
}
