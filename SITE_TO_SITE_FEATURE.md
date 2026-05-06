# Site-to-Site Event Linking Feature

## Overview
The site-to-site linking feature allows you to transfer items from one event directly to another event at a different location, without returning items to the warehouse. This is useful for consecutive events or when equipment needs to move between sites.

## How It Works

### 1. **When to Use Site-to-Site Linking**
- After an event reaches "Set Down" status
- When you have items that haven't been received yet
- When items need to go to another event instead of returning to warehouse

### 2. **User Flow**

#### Step 1: Initiate Link
1. Navigate to an event in "Set Down" status with pending items
2. Click the **"Site-to-Site Link"** button in the top action bar (blue button)
3. This opens the Site-to-Site Linking Wizard

#### Step 2: Configure New Event
Fill in the details for the destination event:
- Event name, client, venue, location
- Loading, setup, event, and set-down dates
- Optional: cost and notes

#### Step 3: Select Items to Transfer
**Important: Simple checkbox logic**
- ✅ **Ticked items** = Transfer to new site → Receive at new event
- ☐ **Unticked items** = Return to warehouse → Receive at current event

**Default behavior:**
- All items from the current event are **pre-ticked** (ready to transfer)
- Simply **untick** any item you don't want to transfer
- You can also add additional items from the warehouse

#### Step 4: Complete Setup
- Click "Create Linked Event & Continue"
- Items are automatically processed:
  - Unticked items are marked as returned to warehouse
  - Ticked items are assigned to the new event
- You're redirected to assign a team to the new event

### 3. **Visual Indicators**

#### Event List (events/index)
- Linked events show a blue "S2S" badge next to the status

#### Event Details (events/show)
- Blue "Site-to-Site" pill badge next to event status
- Link to parent event (if applicable)
- **Event Chain Visualization** - horizontal flowchart showing all linked events
- Sidebar shows all linked child events

#### Event Chain
When viewing an event that's part of a site-to-site chain, you'll see:
- A visual flowchart showing all events in the chain
- Current event highlighted with "You are here" badge
- Click any event in the chain to navigate to it
- Tip message for chains with 3+ events

### 4. **Database Structure**

#### New Fields in `events` table:
```sql
linked_from_event_id (FK to events.id) - Parent event
link_type (enum: 'site-to-site', 'follow-up') - Type of link
```

#### Relationships:
- `linkedFromEvent()` - BelongsTo parent event
- `linkedEvents()` - HasMany child events

## Technical Implementation

### Routes
```
GET  /events/{event}/site-to-site    - Show wizard
POST /events/{event}/site-to-site    - Create linked event
```

### Controller Methods
- `EventController@siteToSiteWizard` - Display wizard form
- `EventController@createSiteToSite` - Process and create linked event

### Key Features
1. **Duplicate Prevention**: Items can't be added to the same event twice
2. **Automatic Item Processing**:
   - Unticked items auto-returned to warehouse with status "Available"
   - Activity logs created for all item movements
3. **Chain Building**: Events can form chains (Event A → Event B → Event C)
4. **Parent Event Completion**: If all items are transferred/returned, parent event becomes "Completed"

## Business Logic

### Item Flow
```
Current Event (Set Down)
    ├─→ Ticked Items → New Event (Draft) → Receive at new event
    └─→ Unticked Items → Warehouse (Available) → Receive at current event
```

### Validation
- Only events in "Set Down" status can create links
- Must have items pending return
- All standard event validation applies to new event
- Items must be available for dispatch

### Activity Logging
Every item movement is logged:
- Returns to warehouse: "Returned to warehouse from {event} (excluded from site-to-site link to {new_event})"
- Site-to-site link creation: "Site-to-site link created: {event} → {new_event}. X items transferred."

## Example Scenario

**Scenario:** Concert equipment needs to move to another venue

1. **Event "Rock Festival - Day 1"** (Set Down)
   - 50 items dispatched
   - Event ends, items need to go to Day 2 venue

2. **Create Site-to-Site Link**
   - Click "Site-to-Site Link" button
   - Create "Rock Festival - Day 2" event
   - Keep 45 items ticked (transfer)
   - Untick 5 items not needed (return to warehouse)

3. **Result:**
   - 5 items returned to warehouse → Receive at Day 1 event
   - 45 items assigned to Day 2 event → Receive at new venue
   - Day 1 event marked "Completed"
   - Day 2 event status "Draft" → ready for team assignment → dispatch

4. **Visual Chain:**
   ```
   [Rock Festival Day 1] ──→ [Rock Festival Day 2] ──→ [Rock Festival Day 3]
        (Completed)              (Active)                  (Scheduled)
   ```

## Benefits

✅ **Efficiency**: No need to return items to warehouse just to dispatch them again
✅ **Tracking**: Full audit trail of item movement between sites
✅ **Flexibility**: Can add warehouse items or return unwanted items
✅ **Visibility**: Clear visualization of event chains and item flow
✅ **Simplicity**: Easy-to-understand checkbox logic (tick = transfer, untick = return)

## Files Modified

### Migrations
- `2026_04_14_100000_add_site_to_site_linking_to_events.php`

### Models
- `app/Models/Event.php` - Added relationships and fillable fields

### Controllers
- `app/Http/Controllers/EventController.php` - Added siteToSiteWizard and createSiteToSite methods

### Views
- `resources/views/events/site_to_site_wizard.blade.php` - New wizard interface
- `resources/views/events/show.blade.php` - Added chain visualization, badges, linked events
- `resources/views/events/index.blade.php` - Added S2S badge

### Routes
- `routes/web.php` - Added site-to-site routes

## Future Enhancements

Potential improvements:
- Bulk edit item conditions during transfer
- Transfer notes/photos between events
- Email notifications for linked events
- Calendar integration showing event chains
- Analytics: average chain length, items per transfer
- Different link types: "follow-up", "backup", "overflow"
