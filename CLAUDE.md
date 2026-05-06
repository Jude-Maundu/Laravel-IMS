# CLAU.md — Grey Apple IMS Project Context

Read this entire file before executing any task. This is the single source of truth for the project. Do not ask clarifying questions that are already answered here.

---

## What This Project Is

Grey Apple Events Limited is an event planning and equipment hire company based in Ruaraka, Nairobi, Kenya. This system tracks their physical inventory — tents, furniture, AV equipment, flooring, fabric — through its full lifecycle from warehouse to events and back.

**The system is called Grey Apple IMS (Inventory Management System).**

---

## Environment — Critical

- **OS:** Windows 10/11
- **Dev server:** Laragon — the app runs at `http://localhost` or `http://laravel-inventory.test`
- **Database:** MySQL via Laragon, database name `laravel-invetory` (note the typo — this is the actual database name)
- **Terminal:** Always use the **Laragon terminal** for PHP and Node commands — never PowerShell, never Git Bash
- **PHP path in Laragon terminal:** `php` command works directly in the Laragon terminal
- **Project folder:** `C:\Users\user\Desktop\Laravel-Inventory`

---

## Tech Stack
Backend:   Laravel 12, PHP 8.3
Frontend:  Blade templates, Tailwind CSS, Vite
Database:  MySQL
Auth:      Laravel built-in sessions
Perms:     Spatie Laravel Permission
PDF:       DomPDF (barryvdh/laravel-dompdf)
Fonts:     Inter via Google Fonts

---

## After Every Change

Always run these in the Laragon terminal:
```bash
npm run build
php artisan view:clear
php artisan config:clear
```

---

## Repository
GitHub:   Private repo, Jomo's account
main:     Production only — never commit directly
develop:  All active development
feature:  feature/name branched off develop

---

## File Structure
app/
Http/Controllers/
DashboardController.php
InventoryController.php
EventController.php
CleaningController.php      ← to be built
RepairsController.php
ReportsController.php
Models/
User.php
Item.php
ItemImage.php
Event.php
EventItem.php
EventItemImage.php
EventStaff.php              ← to be built
Repair.php
ActivityLog.php
Checklist.php
Assignment.php
resources/
css/
app.css                     ← ALL styles go here, never in Blade style blocks
views/
layouts/app.blade.php
auth/login.blade.php
components/
sidebar.blade.php
topbar.blade.php
toast.blade.php
ui/status-badge.blade.php
dashboard/index.blade.php
inventory/
index.blade.php           ← grid view only
show.blade.php            ← item detail with tabs
create.blade.php
edit.blade.php
events/
index.blade.php
create.blade.php          ← wizard step 1
checklist.blade.php       ← wizard step 2
dispatch.blade.php        ← wizard step 3
show.blade.php
edit.blade.php
receive.blade.php         ← post-event item triage
requests.blade.php
team.blade.php            ← to be built
cleaning/
index.blade.php           ← to be built
reports/
index.blade.php           ← to be built
repair-report.blade.php   ← to be built
item-report.blade.php     ← to be built
routes/web.php
database/migrations/
database/seeders/InventorySeeder.php
public/
images/grey-apple-events-logo.png
sounds/success.mp3

---

## Critical Architecture Rules

### CSS — Most Important Rule
**ALL styles go in `resources/css/app.css` only.**
Never add `<style>` blocks inside Blade files. Never use inline styles except for dynamic PHP values like `style="width:{{ $pct }}%"`.

CSS class naming uses module prefixes:
db-     dashboard
inv-    inventory list and grid
itd-    item detail page
ev-     events list
evsh-   event show page
wiz-    create event wizard
rcv-    receive items page
evt-    event team (new)
cln-    cleaning page (new)
rpt-    reports page (new)

### Images
```php
// CORRECT — always use this
asset('storage/' . $model->image_path)

// WRONG — never use this
Storage::url($model->image_path)
```

### Session
SESSION_DRIVER=file
SESSION_LIFETIME=480
Never change these.

### Pagination
```php
->paginate(15)->withQueryString()
```

### Activity Logging
Every item status change must create an ActivityLog:
```php
ActivityLog::create([
    'item_id'     => $item->id,
    'action'      => 'status_changed',
    'description' => "Status changed to {$newStatus}.",
    'user_id'     => auth()->id(),
]);
```

### On Dispatch (item sent to event)
```php
$item->update([
    'status'          => 'Assigned',
    'location'        => $event->venue,  // venue name, not "Warehouse"
    'last_updated_at' => now(),
    'last_updated_by' => auth()->user()->name,
]);
```

### On Return to Warehouse
```php
$item->update([
    'status'          => 'Available',
    'location'        => 'Warehouse',
    'assigned_to'     => null,
    'last_updated_at' => now(),
    'last_updated_by' => auth()->user()->name,
]);
```

### Middleware
```php
// CORRECT
hasRole('Admin')

// WRONG
hasPermissionTo('admin')
```

---

## Brand Colors
Red primary:      #CC0000   main brand, buttons, active states
Dark:             #0f0f0f   headings, confirm buttons
Page background:  #f8f7f5
Card background:  #ffffff
Card border:      #ece8e3
Text primary:     #0f0f0f
Text secondary:   #5c5550
Text muted:       #a09890
Text hint:        #b0a8a0
Green available:  text #3B6D11  bg #eaf3de
Blue info:        text #185FA5  bg #e6f1fb
Amber warning:    text #854F0B  bg #faeeda
Red danger:       text #A32D2D  bg #fcebeb
Purple:           text #534AB7  bg #eeedfe
Teal cleaning:    text #0F6E56  bg #E1F5EE

---

## UI Component Patterns

### Page Layout
Every page inside the app:
```blade
@extends('layouts.app')
@section('title', 'Page Title')
@section('page-title', 'Section Name')
@section('content')
  {{-- breadcrumb --}}
  {{-- flash messages --}}
  {{-- page header with title + action buttons --}}
  {{-- content --}}
@endsection
```

### Breadcrumb Pattern
```blade
<div class="itd-breadcrumb">
  <a href="{{ route('section.index') }}" class="itd-bc-link">
    <svg ...back arrow...></svg>
    Section Name
  </a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">Current Page</span>
</div>
```

### Flash Messages
```blade
@if(session('success'))
<div class="ev-flash ev-flash-success">
  <svg ...check icon...></svg>
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="ev-flash ev-flash-error">
  <svg ...alert icon...></svg>
  {{ session('error') }}
</div>
@endif
```

### Cards
background: #fff
border: 1px solid #ece8e3
border-radius: 10px
overflow: hidden

Card head pattern:
padding: 14-16px
border-bottom: 1px solid #f5f1ed
display: flex, align items center, justify space-between

### Buttons
Primary:   bg #CC0000, white text, radius 8px, padding 8px 14-16px
hover: bg #aa0000
Dark:      bg #0f0f0f, white text, radius 8px
hover: bg #2a2a2a
Outline:   bg white, border 1px solid #ece8e3, text #3a3530
hover: border #CC0000, text #CC0000
Danger:    bg #fff0f0, border #f5c0c0, text #CC0000

### Tables
Wrapper:    bg white, border 1px #ece8e3, radius 10px, overflow hidden
TH:         9px, font-weight 700, uppercase, letter-spacing 0.1em
color #a09890, bg #faf8f6, border-bottom 1px #f0ece8
TD:         12px, color #3a3530, border-bottom 1px #f8f6f3
Row hover:  bg #fdf9f8

### Status Badges (use existing component)
```blade
<x-ui.status-badge :status="$item->status" />
```

### Two-column detail layout
```css
display: grid;
grid-template-columns: 1fr 240-260px;
gap: 12-14px;
align-items: start;
```

### Sidebar two-column layout (wizard/forms)
```css
display: grid;
grid-template-columns: 220px 1fr;
gap: 14px;
align-items: start;
```

### KPI row
```css
display: grid;
grid-template-columns: repeat(5, minmax(0,1fr));
gap: 8px;
margin-bottom: 12px;
```

### Empty state
Center aligned, padding 40-48px
Muted SVG icon 28-36px
Heading: 13px font-weight 600 color #5c5550
Subtext: 11px color #b0a8a0
Optional action link in #CC0000

---

## Database Schema

### items
id, name, category, status, location,
assigned_to, assigned_by, last_updated_by, last_updated_at,
notes, image_path, created_at, updated_at

### item_images
id, item_id FK, image_path,
is_primary bool default false,
caption nullable, uploaded_by FK users,
timestamps

### events
id, name, client_name, venue,
location_name nullable, latitude nullable, longitude nullable,
loading_date, setup_date, event_date, setdown_date,
status ENUM(Draft,Scheduled,Active,Set Down,Completed,Cancelled) default Draft,
cost nullable, notes nullable,
created_by FK users, timestamps

### event_items
id, event_id FK cascade, item_id FK cascade,
condition_on_dispatch tinyint nullable,
condition_on_return tinyint nullable,
dispatch_notes nullable, return_notes nullable,
return_destination ENUM(warehouse,cleaning,repair) nullable,
return_processed bool default false,
dispatched_at timestamp nullable, returned_at timestamp nullable,
dispatched_by FK users nullable, returned_by FK users nullable,
timestamps
UNIQUE(event_id, item_id)

### event_item_images
id, event_item_id FK cascade,
image_path, type ENUM(dispatch,return) default dispatch,
uploaded_by FK users nullable, timestamps

### event_staff (to be created)
id, event_id FK cascade, user_id FK cascade,
role ENUM(member,leader) default member,
timestamps
UNIQUE(event_id, user_id)

### repairs
id, item_id FK,
repair_type nullable, description nullable,
estimated_cost decimal nullable, actual_cost decimal nullable,
status (Pending,In Progress,Completed,Cancelled),
started_at date nullable, completed_at date nullable,
technician_name nullable, notes nullable,
timestamps

### activity_logs
id, item_id FK, action, description nullable,
user_id nullable, timestamps

---

## Item Lifecycle
Available
→ dispatch to event → Assigned (location = event venue)
→ on site → In Use
→ event ends, packed → Set Down
→ Receive Items page triage:
Good     → Available  / Warehouse
Dirty    → Cleaning   / Cleaning Bay  → Cleaning page → Available / Warehouse
Damaged  → Under Repair / Repair Workshop → Repairs module
→ Repaired → Available / Warehouse
→ Irreparable (written off)

Valid statuses: `Available, Assigned, In Use, Cleaning, Under Inspection, Under Repair, Repaired, Damaged, Irreparable`

Valid locations: `Warehouse, Site A, Site B, Cleaning Bay, Repair Workshop, [event venue name]`

---

## Item Categories
Tents - 30 Span, Tents - 20 Span, Tents - 15 Span, Tents - 10 Span,
Tent - G25, Tent - 6x6, Furniture, Flooring,
AV Equipment, Fabric - Table Cloths

---

## Routes Reference
Auth:        login, logout
Dashboard:   GET /  → dashboard.index
Inventory:   /inventory  CRUD → inventory.*
GET /inventory/available → inventory.available
POST /inventory/{item}/change-status → inventory.changeStatus
POST /inventory/{item}/images        → inventory.image.upload
POST /inventory/{item}/images/{img}/primary → inventory.image.primary
DELETE /inventory/{item}/images/{img} → inventory.image.delete
GET /inventory/{item}/report         → inventory.report
Events:      /events  CRUD → events.*
GET/POST /events/{event}/checklist   → events.checklist / events.checklist.save
GET/POST /events/{event}/dispatch    → events.dispatch / events.dispatch.confirm
POST     /events/{event}/dispatch/image → events.dispatch.image
GET/POST /events/{event}/receive     → events.receive / events.receive.process
POST     /events/{event}/receive/image → events.receive.image
GET      /events/requests            → events.requests
GET/POST /events/{event}/team        → events.team / events.team.save
GET      /events/{event}/team/search → events.team.search
Cleaning:    GET /cleaning              → cleaning.index
POST /cleaning/{item}/complete → cleaning.complete
Repairs:     /repairs  CRUD → repairs.*
GET /repairs/{repair}/report → repairs.report
Reports:     GET /reports → reports.index
GET /reports/inventory  → reports.inventory
GET /reports/damage     → reports.damage
GET /reports/event/{event} → reports.event
GET /reports/activity   → reports.activity
GET /reports/cleaning   → reports.cleaning

---

## Sidebar Navigation
Overview
Dashboard
Inventory
All Items
Available Items [badge: count of items in Available status]
Add Item
Cleaning [badge: count of items in Cleaning status]
Event Management
Events
Create Event
Event Requests [badge: count of Draft events]
Analytics
Reports
Administration [Admin role only]
Users
Settings

---

## Module Status

### Complete and working
- Auth, session, login toast with sound
- Sidebar (collapsible, light/dark, localStorage)
- Topbar (profile dropdown, notifications)
- Dashboard (KPIs, charts, activity feed)
- All Items page (grid only, filters, status tabs, pagination)
- Item Detail (hero, KPIs, 5 tabs: Media/Events/Repairs/Health/Activity/Edit)
- Item Images (multi-upload, primary star, delete)
- Events List (search, status tabs, sort, paginate)
- Create Event Wizard (Details → Checklist → Dispatch)
- Event Show (hero, KPIs, items table, gallery, activity, status advance)
- Event Edit
- Event Requests (approve/decline inline)
- Receive Items (triage per item: warehouse/cleaning/repair, conditions, photos)

### To build next
1. Fix Receive Items saving (return_processed column + EventItem fillable)
2. Phase A — Team Assignment (event_staff table, team page, leader star)
3. Phase C — Cleaning Page (list cleaning items, mark as cleaned)
4. Repair Report PDF (DomPDF branded template)
5. Item PDF Report (full audit per item)
6. Reports Index Page (grid of report cards)

---

## How to Work on This Project

### Starting any task
1. Read this file first
2. Check which module you are touching
3. Use the correct CSS prefix for any new classes
4. Follow the exact UI patterns described above
5. Always update CLAUDE.md and GEMINI.md if you add new routes, tables, or modules

### Blade conventions
- No `<style>` blocks ever
- Use `asset('storage/' . $path)` for images
- Use `$item->last_updated_at->diffForHumans()` for relative times
- Use `str_pad($item->id, 3, '0', STR_PAD_LEFT)` for item IDs like #ITM-001
- Use `number_format($cost, 0)` for KES amounts

### Controller conventions
- Eager load all relationships needed by the view
- Always return redirect with `->with('success', ...)` or `->with('error', ...)`
- Always log status changes to ActivityLog
- Use `->withQueryString()` on all paginate calls

### JavaScript conventions
- Vanilla JS only — no jQuery, no Alpine, no Livewire
- Use `fetch()` for AJAX with CSRF from `meta[name="csrf-token"]`
- Always disable submit buttons on form submit to prevent double-posting

### PDF reports (DomPDF)
- View files go in `resources/views/reports/`
- Do NOT extend layouts/app
- Use inline CSS only — DomPDF does not support external stylesheets
- Logo: `public_path('images/grey-apple-events-logo.png')`
- Always include Grey Apple header, report title, generated date, signature line

---

## Common Mistakes to Avoid

- Do not use `Storage::url()` — use `asset('storage/' . $path)`
- Do not add styles in Blade files — use app.css only
- Do not use PowerShell for PHP commands — use Laragon terminal
- Do not forget `->withQueryString()` on paginate
- Do not forget ActivityLog on every status change
- Do not change SESSION_DRIVER from file
- Do not commit directly to main branch
- Do not create new CSS without the module prefix
- Do not use jQuery — vanilla JS only
- Do not forget `npm run build` after CSS changes