# Phase 5 — Receiving Workflow Testing Checklist

**Test Date:** _______________
**Tested By:** _______________

---

## Pre-Test Setup

- [ ] Database seeded with test events, items, and item pieces
- [ ] At least one event in "Scheduled" status with items dispatched
- [ ] Test user logged in with appropriate permissions
- [ ] Browser cache cleared
- [ ] Frontend assets built (`npm run build`)
- [ ] All caches cleared (`php artisan optimize:clear`)

---

## PART 1: Event Status Lifecycle

### 1.1 Draft → Scheduled (Wizard Step 4)
- [ ] Create new event via wizard
- [ ] Complete Step 1 (Event Details)
- [ ] Complete Step 2 (Checklist — add items)
- [ ] Complete Step 3 (Team assignment)
- [ ] Click "Confirm Event" on Step 4 (Review)
- [ ] Verify event status changes to "Scheduled"
- [ ] Verify success toast appears
- [ ] Verify redirect to event show page

### 1.2 Scheduled → Active (Dispatch)
- [ ] Open a Scheduled event
- [ ] Click "Start Dispatch Session" button
- [ ] OR use Manual Dispatch fallback
- [ ] Complete dispatch (scan pieces or manual entry)
- [ ] Submit dispatch session
- [ ] Verify event status changes to "Active"
- [ ] Verify dispatch packing list PDF generated
- [ ] Verify session QR code displayed

### 1.3 Active → Set Down (Status Button)
- [ ] Open an Active event
- [ ] Click status badge/dropdown in event header
- [ ] Select "Set Down" from dropdown
- [ ] Confirm status change
- [ ] Verify event status changes to "Set Down"
- [ ] Verify "Start Receive Session" button now enabled

### 1.4 Set Down → Completed (Receive)
- [ ] Open a Set Down event
- [ ] Start receive session (scan or manual)
- [ ] Receive all/most dispatched pieces
- [ ] Submit receive session
- [ ] Verify event status changes to "Completed"
- [ ] Verify redirect to completion page
- [ ] Verify receiving report PDF accessible

---

## PART 2: Receive Session (Mobile Scan Flow)

### 2.1 Session Initiation
- [ ] Event must be in "Set Down" status
- [ ] Click "Start Receive Session" button
- [ ] Verify modal opens with session details
- [ ] Verify session token generated (format: RCV-YYYYMMDD-XXXX)
- [ ] Verify QR code displayed
- [ ] Verify session expiry time shown (48 hours)
- [ ] Copy session URL or scan QR code

### 2.2 Mobile Scan Interface
- [ ] Open session URL on mobile device
- [ ] Verify mobile-optimized layout loads
- [ ] Verify event name and receive ref displayed
- [ ] Verify progress counter shows 0 received
- [ ] Verify expected returns list displayed
- [ ] Verify camera permissions requested
- [ ] Verify scan input field active

### 2.3 Scanning Pieces
- [ ] Scan a valid piece QR code
- [ ] Verify piece recognized (success feedback)
- [ ] Verify condition modal appears
- [ ] Select condition score (1-5)
- [ ] Select destination (Warehouse/Cleaning/Repair)
- [ ] Add damage note if condition < 4
- [ ] Submit condition details
- [ ] Verify piece added to "Received Today" list
- [ ] Verify progress counter increments
- [ ] Verify next item suggestion appears

### 2.4 Validation Scenarios
- [ ] Scan piece NOT dispatched for this event → verify error message
- [ ] Scan already-received piece → verify "already received" warning
- [ ] Scan invalid/unknown code → verify "not found" error
- [ ] Scan piece from different event → verify rejection

### 2.5 Session Completion
- [ ] Receive all expected pieces
- [ ] Click "Complete Receiving" button
- [ ] Verify confirmation prompt appears
- [ ] Confirm completion
- [ ] Verify session marked as completed
- [ ] Verify redirect to completion page
- [ ] Verify missing items logged (if any)
- [ ] Verify event status → Completed

---

## PART 3: Manual Receive Fallback

### 3.1 Access Manual Receive
- [ ] Open Set Down event
- [ ] Click "Manual Receive" link
- [ ] Verify form loads with piece input field
- [ ] Verify borrowed items section visible
- [ ] Verify operational items section visible

### 3.2 Manual Piece Entry
- [ ] Type piece unique code manually
- [ ] Click "Receive Piece" button
- [ ] Verify piece validated
- [ ] Select condition and destination
- [ ] Submit details
- [ ] Verify piece recorded
- [ ] Repeat for multiple pieces

### 3.3 Borrowed Items Return
- [ ] Locate borrowed items table
- [ ] Enter quantity returned for each item
- [ ] Verify shortfall calculated automatically
- [ ] Note: no piece-level tracking for borrowed items

### 3.4 Operational Items Return
- [ ] Locate operational items table
- [ ] Enter quantity returned for each item
- [ ] Verify shortfall calculated
- [ ] Submit form
- [ ] Verify event marked as Completed

---

## PART 4: Receive Monitor (Laptop Polling View)

### 4.1 Monitor Page Load
- [ ] Start receive session from event show page
- [ ] Click "Monitor on Laptop" button
- [ ] Verify monitor page loads
- [ ] Verify event details displayed
- [ ] Verify real-time progress shown
- [ ] Verify auto-refresh every 2 seconds

### 4.2 Real-Time Updates
- [ ] Scan piece on mobile while monitor open
- [ ] Verify monitor updates automatically
- [ ] Verify "Received Today" list updates
- [ ] Verify progress bar animates
- [ ] Verify destination counts update
- [ ] Verify item images appear

### 4.3 Monitor Controls
- [ ] Click "Extend Session" button → verify expiry extended +2 hours
- [ ] Click "Cancel Session" → verify confirmation prompt
- [ ] Confirm cancellation → verify session cancelled
- [ ] Verify redirect back to event show page

---

## PART 5: Missing Items Tracking

### 5.1 Automatic Missing Detection
- [ ] Complete receive session without scanning all pieces
- [ ] Submit session
- [ ] Verify missing items auto-created in database
- [ ] Check `missing_items` table for new records
- [ ] Verify missing piece status = "missing"
- [ ] Verify piece item status remains "Assigned"

### 5.2 Missing Items Panel (Event Show Page)
- [ ] Open event with missing items
- [ ] Scroll to missing items panel (before gallery)
- [ ] Verify red-bordered warning panel displayed
- [ ] Verify missing count badge shows correct number
- [ ] Verify "View Full Report" link present
- [ ] Verify each missing item listed with:
  - [ ] Piece unique code
  - [ ] Item name
  - [ ] "Mark Found" button

### 5.3 Mark Missing Item as Found
- [ ] Click "Mark Found" button
- [ ] Verify confirmation (optional)
- [ ] Verify missing item status changes to "found"
- [ ] Verify piece status changes to "Available"
- [ ] Verify piece current_event_id cleared
- [ ] Verify activity log entry created
- [ ] Verify item removed from missing panel
- [ ] Verify success message displayed

---

## PART 6: Receiving Report PDF

### 6.1 PDF Generation
- [ ] Open completed event
- [ ] Click "Receiving Report" button/link
- [ ] Verify PDF generates without errors
- [ ] Verify PDF downloads or opens in new tab
- [ ] Verify filename: `receiving-report-RCV-YYYYMMDD-XXXX.pdf`

### 6.2 PDF Content - Header
- [ ] Grey Apple Events logo displayed
- [ ] "RECEIVING REPORT" title visible
- [ ] Receive reference (RCV-YYYYMMDD-XXXX) shown
- [ ] Generation timestamp in EAT timezone

### 6.3 PDF Content - Event Info Grid
- [ ] Event name
- [ ] Client name
- [ ] Venue
- [ ] Set down date
- [ ] Dispatch ref (plan_ref)
- [ ] Receive ref

### 6.4 PDF Content - Summary Cards
- [ ] Blue card: Pieces Dispatched (count)
- [ ] Green card: To Warehouse (count)
- [ ] Amber card: To Cleaning (count)
- [ ] Red card: To Repair (count)
- [ ] Amber card: Missing (count)

### 6.5 PDF Content - Section A: Received Items
- [ ] Table header: Item, Category, Piece Code, Condition, Destination, Damage Note
- [ ] All received pieces listed
- [ ] Condition scores shown (X/5)
- [ ] Destination badges colored correctly:
  - [ ] Warehouse = blue
  - [ ] Cleaning = amber
  - [ ] Repair = red
- [ ] Damage notes displayed where applicable
- [ ] Monospace font for piece codes

### 6.6 PDF Content - Section B: Borrowed Items
- [ ] Only shown if event has borrowed items
- [ ] Table: Item, Source Company, Qty Dispatched, Qty Returned, Shortfall
- [ ] Shortfall calculation correct
- [ ] "X missing" shown in red if shortfall > 0
- [ ] Green checkmark if complete

### 6.7 PDF Content - Section C: Operational Items
- [ ] Only shown if event has operational items
- [ ] Table: Item, Qty Dispatched, Qty Returned, Shortfall
- [ ] Shortfall calculation correct
- [ ] Red/green status indicators

### 6.8 PDF Content - Section D: Missing Items
- [ ] Only shown if missing items exist
- [ ] Red-bordered section with warning icon
- [ ] Count shown: "Missing Items (X)"
- [ ] Table: Item, Piece Code, Category, Notes, Status
- [ ] Red table header background
- [ ] Missing status badge displayed

### 6.9 PDF Content - Footer
- [ ] "Generated by GAIMS" text
- [ ] "joseasoftwares.co.ke" domain
- [ ] Receive reference repeated
- [ ] Footer on every page

---

## PART 7: Activity Logging

### 7.1 Receive Actions Logged
- [ ] Open activity_logs table
- [ ] Filter by recently received items
- [ ] Verify each received piece has log entry:
  - [ ] Action = "Returned"
  - [ ] Description includes piece code, event name, destination
  - [ ] user_id = receiver's ID
  - [ ] item_id matches piece's item_id
  - [ ] Timestamp accurate

### 7.2 Missing Item Resolution Logged
- [ ] Mark a missing item as found
- [ ] Check activity_logs table
- [ ] Verify new entry created:
  - [ ] Action = "Found"
  - [ ] Description includes piece code and event name
  - [ ] user_id = current user
  - [ ] Timestamp accurate

### 7.3 Status Transition Logged
- [ ] Verify event status changes logged
- [ ] Check for entries when:
  - [ ] Event set to "Set Down"
  - [ ] Event marked "Completed"
  - [ ] Receive session completed

---

## PART 8: Database Integrity

### 8.1 ReceiveSession Table
- [ ] Session record created with correct event_id
- [ ] session_token unique and formatted correctly
- [ ] receive_ref matches event.receive_ref
- [ ] expires_at = created_at + 48 hours
- [ ] status = "active" initially, "completed" after submission
- [ ] received_count increments correctly
- [ ] completed_at set on submission

### 8.2 ReceiveSessionPiece Table
- [ ] One record per scanned piece
- [ ] receive_session_id references correct session
- [ ] item_piece_id references correct piece
- [ ] unique_code stored correctly
- [ ] item_id matches piece's item
- [ ] condition_score between 1-5
- [ ] destination in (warehouse, cleaning, repair)
- [ ] damage_note stored if provided
- [ ] received_by = user_id
- [ ] received_at timestamp accurate

### 8.3 EventPieceDispatch Table
- [ ] condition_on_return updated after receive
- [ ] return_destination updated
- [ ] return_notes stored
- [ ] returned_at timestamp set
- [ ] returned_by = user_id

### 8.4 ItemPiece Table
- [ ] Piece status updated based on destination:
  - [ ] warehouse → "Available"
  - [ ] cleaning → "Cleaning"
  - [ ] repair → "Under Repair"
- [ ] current_event_id cleared if status = "Available"
- [ ] current_event_id retained if Cleaning or Repair

### 8.5 MissingItem Table
- [ ] Auto-created for unreceived pieces
- [ ] event_id correct
- [ ] item_piece_id correct
- [ ] unique_code stored
- [ ] item_id correct
- [ ] status = "missing" initially
- [ ] status = "found" after resolution
- [ ] marked_by = user_id
- [ ] marked_at timestamp
- [ ] notes populated

### 8.6 Event Table
- [ ] receive_ref generated on first receive session
- [ ] Format: RCV-YYYYMMDD-XXXX
- [ ] status updated to "Completed" after receive
- [ ] Timestamps updated correctly

---

## PART 9: Edge Cases & Error Handling

### 9.1 Session Expiry
- [ ] Create receive session
- [ ] Manually set expires_at to past time in DB
- [ ] Try to access session URL
- [ ] Verify "Session Expired" error shown
- [ ] Verify cannot scan pieces

### 9.2 Concurrent Sessions
- [ ] Start receive session
- [ ] Try to start another session for same event
- [ ] Verify error: "Active session already exists"
- [ ] Verify forced to use existing session

### 9.3 Partial Completion
- [ ] Start receive session
- [ ] Scan only 50% of pieces
- [ ] Click "Save Progress" (mobile)
- [ ] Close browser
- [ ] Re-scan QR code
- [ ] Verify session resumes
- [ ] Verify already-received pieces shown
- [ ] Continue scanning remaining pieces

### 9.4 All Items Missing
- [ ] Complete receive session with 0 pieces scanned
- [ ] Verify all items flagged as missing
- [ ] Verify event still marked "Completed"
- [ ] Verify receiving report shows all as missing

### 9.5 Duplicate Scans
- [ ] Scan same piece twice in one session
- [ ] Verify second scan shows "already received" warning
- [ ] Verify piece not duplicated in database
- [ ] Verify count doesn't increment twice

### 9.6 Wrong Event Piece
- [ ] Create Event A and Event B
- [ ] Dispatch pieces to both events
- [ ] Start receive session for Event A
- [ ] Scan piece from Event B
- [ ] Verify error: "Not dispatched for this event"
- [ ] Verify piece not recorded

### 9.7 Network Interruption (Mobile)
- [ ] Start scanning on mobile
- [ ] Disable WiFi/data mid-scan
- [ ] Try to submit piece
- [ ] Verify error handling (timeout or retry)
- [ ] Re-enable network
- [ ] Verify can continue

### 9.8 Cancel Session
- [ ] Start receive session
- [ ] Scan 3-5 pieces
- [ ] Click "Cancel Session" on monitor
- [ ] Confirm cancellation
- [ ] Verify session status = "cancelled"
- [ ] Verify pieces already scanned are NOT deleted
- [ ] Verify can start new session
- [ ] Verify new session does not include cancelled pieces

---

## PART 10: UI/UX Verification

### 10.1 Responsive Design
- [ ] Test on mobile (portrait)
- [ ] Test on mobile (landscape)
- [ ] Test on tablet
- [ ] Test on desktop (1080p)
- [ ] Test on desktop (4K)
- [ ] Verify no horizontal scroll
- [ ] Verify touch targets adequate size (44x44px minimum)

### 10.2 Accessibility
- [ ] Tab through receive form with keyboard
- [ ] Verify focus indicators visible
- [ ] Verify labels associated with inputs
- [ ] Verify color contrast meets WCAG AA
- [ ] Test with screen reader (optional)

### 10.3 Visual Consistency
- [ ] Blue theme (#185FA5) used throughout receive flow
- [ ] Consistent with dispatch red (#CC0000) separation
- [ ] Cards have proper borders and shadows
- [ ] Badges colored correctly per destination
- [ ] Typography consistent (Inter font)

### 10.4 Success/Error Feedback
- [ ] Success scans show green toast
- [ ] Error scans show red toast
- [ ] Sound feedback plays (if enabled)
- [ ] Loading spinners shown during processing
- [ ] Disabled buttons have opacity/cursor changes

---

## PART 11: Performance

### 11.1 Load Times
- [ ] Receive session page loads < 2 seconds
- [ ] Monitor page loads < 2 seconds
- [ ] PDF generates < 5 seconds (for 100+ items)
- [ ] Mobile scan interface responsive (< 500ms per scan)

### 11.2 Database Queries
- [ ] Check query count on receive session show page
- [ ] Verify eager loading used (no N+1 queries)
- [ ] Check query count on monitor page refresh
- [ ] Verify indexes exist on foreign keys

### 11.3 Concurrent Users
- [ ] Multiple users receiving different events
- [ ] Verify no data cross-contamination
- [ ] Verify no session conflicts
- [ ] Verify database transactions handle concurrency

---

## PART 12: Integration Points

### 12.1 Item Status Updates
- [ ] Receive piece to warehouse
- [ ] Check inventory index page
- [ ] Verify item status badge shows "Available"
- [ ] Receive piece to cleaning
- [ ] Check cleaning page
- [ ] Verify item appears in cleaning queue

### 12.2 Repair Module Integration
- [ ] Receive piece with destination = "repair"
- [ ] Navigate to repairs index
- [ ] Verify repair record auto-created
- [ ] Verify status = "Pending"
- [ ] Verify description includes event name

### 12.3 Dashboard KPIs
- [ ] Complete receive session
- [ ] Navigate to dashboard
- [ ] Verify item counts updated
- [ ] Verify event status counts updated
- [ ] Verify recent activity shows receive actions

---

## PASS/FAIL SUMMARY

**Total Tests:** _______
**Passed:** _______
**Failed:** _______
**Blocked:** _______

**Critical Issues:**
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

**Minor Issues:**
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

**Recommendations:**
- _______________________________________________
- _______________________________________________
- _______________________________________________

---

## Sign-Off

**Tested By:** _____________________  **Date:** _______________
**Approved By:** ___________________  **Date:** _______________

**Notes:**
_______________________________________________________________
_______________________________________________________________
_______________________________________________________________
