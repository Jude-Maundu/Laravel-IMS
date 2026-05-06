The Current Situation

Grey Apple IMS is already a fully functional inventory management system for your event equipment rental business. It tracks tents, chairs, tables, AV gear, and other items from your warehouse to event venues and back. You have a complete admin system where your staff creates events, assigns equipment, manages triage, handles repairs, and generates dispatch checklists.

What the system does NOT have is a way for customers to pay for their event bookings. Right now, you likely handle payments manually through bank transfers, cash, or M-Pesa sent directly to your business number. This creates extra work for your staff and delays event confirmation while you manually verify who has paid.
What Adding M-Pesa Achieves

Integrating M-Pesa allows customers to pay for their event instantly, directly from their phone. When a customer pays, your system knows immediately. The event automatically becomes confirmed. Your staff does not need to chase payments or manually update booking statuses. The customer leaves happy because their event is locked in within seconds.

The integration adds a self-service payment layer on top of your existing system. Your inventory tracking, QR scanning, triage workflows, and admin dashboards remain exactly the same. Only the payment process changes.
What You Need to Build
Customer Portal

This is the only major new feature you must create. Your current system has no customer-facing interface. Staff use it internally. Customers cannot log in and see anything. You need to build a simple customer portal where clients can view their events and make payments.

The customer portal requires four pages.

The login page lets customers access their account. They can log in using their email address and a password, or you can keep it even simpler by allowing them to log in using their booking number and phone number. The simpler approach requires no password management and is easier for customers who only book once or twice per year.

After logging in, customers see their dashboard. This page lists all events they have booked with Grey Apple. For each event, they see the event date, venue, items requested, total amount due, and payment status. Events that are unpaid show a prominent Pay Now button. Events that are paid show a green Paid badge and a button to download a receipt.

When a customer clicks Pay Now, they go to the payment page. This page shows a summary of what they are paying for, asks for their M-Pesa phone number, and has a button that says Send Payment Prompt. After they enter their number and click the button, the system sends an STK Push to their phone.

The final page is the receipt page. After payment succeeds, customers see a confirmation message, the transaction ID from M-Pesa, and a button to download a PDF receipt. Your system already uses DomPDF for generating documents, so you can reuse that same library to create professional receipts with your Grey Apple branding.
Database Changes

Your existing events table needs a few new columns. You need to track whether payment is pending, paid, failed, or refunded. You need to store the amount the customer owes for that event. You need the customer phone number so the system knows where to send the M-Pesa prompt. You also need to store the transaction ID that M-Pesa returns so you can match payments to events and look up receipts later.

You also need a separate payments table. This table logs every single payment attempt. When a payment fails, you record why it failed. When a payment succeeds, you record the full response from M-Pesa. This table gives you a complete audit trail. If a customer ever disputes a payment, you can look up exactly what happened. Your existing activity logs already track system actions, but payment logs need their own table because they contain sensitive financial data.
M-Pesa Gateway Connection

You need to choose a gateway to handle the communication between your system and M-Pesa. The gateway receives your request to send a payment prompt, forwards it to Safaricom, and then tells your system what happened.

Tuma is the fastest option. You can sign up for an account, verify your business, and receive API keys within one hour. Tuma handles all the complex parts of M-Pesa integration. Your system only needs to send a simple request with the amount and phone number. Tuma sends the STK Push and tells you whether it succeeded or failed.

Jenga is another Kenyan option that offers similar simplicity. Both Tuma and Jenga are designed for businesses that want to add M-Pesa without hiring a developer to figure out Safaricom's direct Daraja API.

Direct Daraja integration gives you more control but requires more work. You need to generate authentication tokens, manage expiration times, build your own callback handling, and troubleshoot cryptic error messages. For most event rental businesses, Tuma or Jenga is the better choice because you can be live in days instead of weeks.

Regardless of which gateway you choose, you receive API keys that you store in your Laravel environment configuration. Your system uses these keys every time a customer requests to pay.
Callback Handler

When a customer enters their PIN on their phone, M-Pesa processes the payment and sends a result to your gateway. The gateway then forwards that result to your system by calling a specific URL on your server called a webhook.

Your system needs one new URL that exists only to receive these payment confirmations. This URL is publicly accessible so M-Pesa can reach it. When a payment succeeds, this callback handler updates the corresponding event payment status to paid, records the transaction ID, logs the success in the payments table, and adds an entry to your activity log saying that payment was received.

When a payment fails, the callback handler updates the payment status to failed, records the failure reason, and logs the attempt. The customer can then try again from their dashboard.

This callback handler is critical. Without it, your system never knows whether a customer paid. You must ensure the URL is correctly configured in your gateway dashboard and that your server is configured to accept incoming webhook requests.
Business Logic Changes

Your existing system has rules about when an event can move from one status to another. You need to add payment as a condition.

When your staff creates an event using the event wizard, the event should be created with payment status pending. The event status should be something like Awaiting Payment rather than Scheduled. This makes it clear to both staff and customers that the event is not confirmed until payment arrives.

When the customer pays successfully, your callback handler automatically changes the event payment status to paid and changes the event status to Scheduled. No staff action required.

You need to modify your dispatch checklist generation. The system should check whether payment status is paid before allowing a PDF to be generated. If a staff member tries to print a checklist for an unpaid event, the system shows a warning message saying payment not received.

Your admin dashboard needs a new card or widget showing pending payments. Your staff should see at a glance how many events are booked but unpaid. You might also add a report that shows payment collections by day, week, or month so you can track revenue.
How the Customer Experience Works

A customer calls your office or fills out a contact form on your website. They need a tent, fifty chairs, and ten tables for a wedding on June fifteenth.

Your staff opens the Grey Apple IMS admin system and creates a new event using the existing event wizard. They enter the customer name, email address, phone number, venue, and all the dates. They select the required items from the inventory checklist. They assign a team leader. On the final review screen, they see the total amount due.

Before saving the event, your staff enters the payment amount in a new field called Amount Due. They also enter the customer phone number if it is not already in the system. They save the event. The system automatically generates a customer account if one does not exist and sends a welcome email to the customer with a link to log in.

The customer receives the email. They click the link, go to your customer portal, and log in using their phone number or email. They see their upcoming wedding event with a Pay Now button.

They click Pay Now, enter their M-Pesa phone number, and click Send Payment Prompt. Within five seconds, their phone receives an M-Pesa pop-up asking for their PIN. They enter their PIN. Their phone shows payment successful.

They look back at their browser. The page automatically refreshes or they see a success message. Their dashboard now shows the event as Paid and Confirmed. They click Download Receipt and save a PDF for their records.

Your staff logs into the admin system. They see the event now shows Paid status in the events list. They generate the dispatch checklist. They load the truck. The wedding happens on schedule.

Everyone is happy because the process took less than two minutes from the customer perspective and required zero staff time to verify payment.
What You Do NOT Need to Change

Your inventory tracking remains completely unchanged. Items are still assigned, used, triaged, cleaned, and repaired exactly as they are today. Payment status has no relationship to how items move through the warehouse.

Your QR code scanning engine stays the same. Staff scans items during dispatch and receiving regardless of whether payment has been made. The only change is that dispatch checklists should not generate for unpaid events, but the scanning process itself does not change.

Your site-to-site linking feature works exactly as it does today. Equipment can move from event to event without returning to the warehouse. Payment status is per event and does not affect equipment movement.

Your photo documentation and triage workflows remain identical. Staff still takes photos of damaged items, generates comparison reports, and creates repair tickets. Payment status does not affect damage responsibility or repair tracking.

Your admin interface except for the new payment status column and reports remains identical. Your staff does not need retraining on inventory management. Only the event creation and dispatch checklist workflows gain the new payment check.
Estimated Work Effort

Building the customer portal requires about two to three days of development time. This includes the login page, dashboard, payment page, receipt page, and all the navigation between them.

Adding the database tables and columns takes less than one day. This includes writing the migration, updating your event model to include the new relationships, and ensuring your existing reports do not break.

Integrating Tuma or another gateway takes about one to two days. This includes signing up for an account, getting your API keys, building the service class that sends payment requests, building the callback handler that receives results, and testing everything in sandbox mode.

Modifying your business logic to check payment status before dispatch checklist generation takes a few hours. Adding the payment status widget to your admin dashboard takes another few hours.

Total development time is roughly five to seven days for an experienced Laravel developer. If you have a developer on staff or contract with a Laravel specialist, you can expect delivery within two weeks including testing.

If you use Tuma instead of direct Daraja, you save significant time because you do not need to implement authentication token generation, token refresh logic, or complex error handling. Tuma also provides a test environment that works immediately without waiting for Safaricom approval.
Recommended Order of Work

Start by adding the database tables and columns. This gives you a place to store payment data and does not affect any existing functionality.

Next, build the customer portal login and dashboard. Customers need a place to see their events before you add the payment button.

Next, integrate Tuma and build the payment button functionality. Test everything in Tuma sandbox mode using test phone numbers that never actually deduct money.

Next, build the callback handler and test the full flow from payment button to callback to event status update.

Next, add the business logic changes to block dispatch checklist generation for unpaid events and add payment reports to the admin dashboard.

Finally, switch Tuma from sandbox to live mode, run a few real transactions with small amounts to verify everything works, and then announce to customers that online payment is available.
Ongoing Considerations

Transaction fees apply for every M-Pesa payment. Tuma charges a small percentage plus a fixed fee per transaction. You need to decide whether to absorb these fees or add them to the customer total. Most event rental businesses add a small processing fee or simply build the cost into their pricing.

Failed payments happen occasionally. Customers may have insufficient balance, enter the wrong PIN, or have network issues. Your system should allow customers to retry payment as many times as needed. Each failed attempt is logged in the payments table so you can identify customers who are having trouble.

Refunds are a separate workflow. If a customer cancels an event and you need to refund their payment, you must handle this outside the automatic system. Most gateways offer a refund API, but you may choose to process refunds manually through your M-Pesa business account to avoid accidental double refunds.

Security is important because you are handling money. Your customer portal should use HTTPS only. Your callback URL should verify that incoming requests actually come from your payment gateway using a signature or secret key. Your database should store only the transaction ID and amount, not sensitive customer financial information.

This integration adds a complete self-service payment layer to Grey Apple IMS while leaving your proven inventory management workflows untouched. Customers gain convenience and instant confirmation. Your staff gains time previously spent chasing payments and manual data entry. The system becomes more professional and easier to scale as you add more customers and events.
