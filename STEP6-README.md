# PaBarang – Step 6 (documents, announcements, officials, resident pages)

Copy each folder over your project (same paths). Nothing from Step 5 is overwritten
except the 9 Blade views listed below.

## Install (PowerShell, in D:\Laravel\pabarang)

```powershell
Expand-Archive -Path "$env:USERPROFILE\Downloads\step6.zip" -DestinationPath "D:\Laravel\pabarang" -Force
cd D:\Laravel\pabarang
if (-not (Select-String -Path routes\web.php -Pattern "step6.php" -Quiet)) { Add-Content routes\web.php "`nrequire __DIR__.'/step6.php';" }
Set-Alias php84 "$env:USERPROFILE\.config\herd\bin\php84\php.exe"
php84 artisan migrate
php84 artisan db:seed --class=Step6Seeder
php84 artisan storage:link
php84 artisan optimize:clear
php84 artisan route:list --path=admin/documents
```

`routes/step6.php` is loaded last from `routes/web.php`, so its routes replace the old
placeholder routes with the same URL. If your middleware aliases aren't named `admin`,
`resident` and `role`, rename them in that file.

## What's new

| Area | Files |
|---|---|
| Shared lists | `config/pabarang.php` (doc types, purposes, announcement categories, the 4 official groups) |
| Tables | `document_requests`, `document_request_logs` (status history), `announcements`, `resident_popups` (one row), `officials` |
| Models | `DocumentRequest`, `DocumentRequestLog`, `Announcement`, `ResidentPopup`, `Official` |
| Admin | `Admin\DocumentRequestController` (list, status change AJAX, private file viewer), `Admin\AnnouncementController` (CRUD + popup save/toggle/"use as popup"), `Admin\OfficialController` (CRUD + photo) |
| Resident | `Resident\DocumentRequestController` (6 forms → one route), `Resident\PortalController` (home, announcements, officials, My Reports, transparency) |
| Helpers | `App\Support\Current` (who is logged in), `App\Support\Audit` (writes `audit_logs`) |

Every admin write (status change, announcement CRUD, popup, officials, opening an uploaded ID) is logged to `audit_logs`.

## Rules the server now enforces

- Document status can only move forward: pending → processing/rejected, processing → ready/rejected, ready → completed.
- Reject needs a note. Indigency can't be marked ready until "social worker assessment done" is checked.
- Uploaded IDs / medical certificates go to the **private** disk; only admins can open them via `/admin/documents/{id}/files/{n}`.
- Each resident sees only their own requests and concerns. Transparency only uses counts/averages.
- Resident popup: shown once per login session; an edited popup counts as new. The localStorage bridge is gone.
- Roles: documents = captain, secretary, staff. Announcements and officials = captain, secretary.

## Assumptions to check (each lives in ONE place)

1. **Logged-in admin**: `Current::admin()` reads the `$me` that EnsureAdmin shares with views, else `session('admin.id')`.
2. **Logged-in resident**: `Current::residentId()` reads `session('resident.id')`.
3. **Resident columns** used for the admin drawer: `first_name`/`last_name` (or `name`), `zone`/`purok`, `contact_number` — see `DocumentRequest::toAdminArray()`.
4. **Concerns** (`PortalController::myConcerns()` and `transparency()`): uses `resident_id`, `ref`, `title`, `category`, `zone`, `status`, timestamps; optional `resolved_at` and `admin_note`. If the table has no `resident_id`, My Reports simply shows no concerns.
5. **audit_logs**: `Audit::log()` only fills columns that exist (`actor`, `action`, `target`, `details`, `admin_id`, `ip_address`, timestamps).
6. Middleware names in the routes snippet: `admin`, `resident`, `role:...`.
7. FKs point at the `residents` and `admins` tables.

## Changed views

- `admin/documents` – now uses `partials.admin-sidebar` / `partials.admin-user` (its own sidebar and the hardcoded official name are gone); uploaded files open for real; buttons lock while saving.
- `admin/announcements` – real list/forms; popup saved to the DB with an uploaded picture; the hardcoded "Posted by Hon. Ricardo…" is gone ("Posted by" defaults to the logged-in admin).
- `admin/officials` – reads the DB; **Lupong Tagapamayapa** added to the group dropdown (it was missing); optional photo.
- `resident/request-document` – fields have names, forms post with CSRF, errors reopen the right modal with the typed values kept.
- `resident/home`, `my-reports`, `announcements`, `officials`, `transparency` – all from the DB.

## Removed on purpose

- **Follow-up comment boxes** on My Reports: the Send buttons didn't save anything. They're replaced by a line telling the resident to mention their reference at the hall. Add a comments table later if you want them back.
- **`pb-doc-forms.js`** on Request a Document: the forms now submit to the server. If that script does more than the old demo submit, add it back, but make sure it doesn't call `preventDefault()` on submit.
- **Barangay Health Worker card** on the resident directory: FR-33 says the roster has exactly four groups and BHW isn't one of them. Add a group in `config/pabarang.php` if the barangay wants BHWs listed.

## Still to do

- Admin dashboard (its file wasn't included in this batch).
- Blotter "Mediator" dropdown → `Official::lupon()->ordered()->get()`.
- Landing page officials list → read from `Official` too.
- "Print" on document requests still only shows a toast (no certificate template yet).
