<div class="modal fade" id="message_tags_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-full">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-tag"></i> <?php echo $this->lang->line('Message Tags'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
      	<?php if(strtotime(date("Y-m-d")) <= strtotime("2020-3-4")) : ?> <h5 class="text-center text-primary">NEW TAGS</h5><br> <?php endif; ?>
      	<div class="table-responsive2">
	        <table class="table table-bordered table-striped">
	      	    <thead>
	      	        <tr>
	      	            <th style="min-width: 200px;" class="text-center">
	      	                TAG
	      	            </th>
	      	            <th class="text-center">
	      	                DESCRIPTION
	      	            </th>
	      	            <th class="text-center">
	      	                ALLOWED
	      	            </th>
	      	            <th class="text-center">
	      	                DISALLOWED (NON-EXHAUSTIVE)
	      	            </th>
	      	        </tr>
	      	    </thead>
	      	    <tbody class="_5m37" id="u_0_1">
	      	        <tr class="row_0">
	      	            <td>
	      	                <p><code>CONFIRMED_EVENT_UPDATE</code></p>
	      	            </td>
	      	            <td>
	      	                <p>Send the user reminders or updates for an event they have registered for (e.g., RSVP'ed, purchased tickets). This tag may be used for upcoming events and events in progress.</p>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Reminder of upcoming classes, appointments, or events that the user has scheduled</li>
	      	                    <li>Confirmation of user's reservation or attendance to an accepted event or appointment</li>
	      	                    <li>Notification of user's transportation or trip scheduled, such as arrival, cancellation, baggage delay, or other status changes</li>
	      	                </ul>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Promotional content, including but not limited to deals, offers, coupons, and discounts</li>
	      	                    <li>Content related to an event the user has not signed up for (e.g., reminders to purchase event tickets, cross-sell of other events, tour schedules, etc) </li>
	      	                    <li>Messages related to past events</li>
	      	                    <li>Prompts to any survey, poll, or reviews</li>
	      	                </ul>
	      	            </td>
	      	        </tr>
	      	        <tr class="row_1 _5m29">
	      	            <td>
	      	                <p><code>POST_PURCHASE_UPDATE</code></p>
	      	            </td>
	      	            <td>
	      	                <p>Notify the user of an update on a recent purchase.</p>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Confirmation of transaction, such as invoices or receipts</li>
	      	                    <li>Notifications of shipment status, such as product in-transit, shipped, delivered, or delayed</li>
	      	                    <li>Changes related to an order that the user placed, such credit card has declined, backorder items, or other order updates that require user action</li>
	      	                </ul>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Promotional content, including but not limited to deals, promotions, coupons, and discounts</li>
	      	                    <li>Messages that cross-sell or upsell products or services</li>
	      	                    <li>Prompts to any survey, poll, or reviews</li>
	      	                </ul>
	      	            </td>
	      	        </tr>
	      	        <tr class="row_2">
	      	            <td>
	      	                <p><code>ACCOUNT_UPDATE</code></p>
	      	            </td>
	      	            <td>
	      	                <p>Notify the user of a non-recurring change to their application or account.</p>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>A change in application status (e.g., credit card, job)</li>
	      	                    <li>Notification of suspicious activity, such as fraud alerts</li>
	      	                </ul>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Promotional content, including but not limited to deals, promotions, coupons, and discounts</li>
	      	                    <li>Recurring content (e.g., statement is ready, bill is due, new job listings)</li>
	      	                    <li>Prompts to any survey, poll, or reviews</li>
	      	                </ul>
	      	            </td>
	      	        </tr>
	      	        <tr class="row_3 _5m29">
	      	            <td>
	      	                <p><code>HUMAN_AGENT</code></p>

	      	                <p>(Closed BETA)</p>
	      	            </td>
	      	            <td>
	      	                <p>Allows human agents to respond to user inquiries. Messages can be sent within 7 days after a user message.</p>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Human agent support for issues that cannot be resolved within the standard messaging window (e.g., business is closed for the weekend, issue requires &gt;24 hours to resolve)</li>
	      	                </ul>
	      	            </td>
	      	            <td>
	      	                <ul>
	      	                    <li>Automated messages </li>
	      	                    <li>Content unrelated to user inquiry</li>
	      	                </ul>
	      	            </td>
	      	        </tr>
	      	    </tbody>
	      	</table>
	     </div>

      	<?php if(strtotime(date("Y-m-d")) <= strtotime("2020-3-4")) : ?>
      	<br><br><br><br>
      	<h5 class="text-center text-danger">SUPPORTED UNTIL MAR 4, 2020</h5><br>
      	<div class="table-responsive2">
	        <table class="table table-bordered table-striped">
	           <thead>
	              <tr>
	                 <th style="min-width: 240px;" class="text-center">
	                    TAG 
	                 </th>
	                 <th class="text-center">
	                    ALLOWED USE CASES
	                 </th>
	                 <th class="text-center">
	                    EXAMPLES
	                 </th>
	                 <th class="text-center">
	                    MIGRATION NOTES
	                 </th>
	              </tr>
	           </thead>
	           <tbody class="_5m37" id="u_0_2">
	              <tr class="row_0">
	                 <td>
	                    <p><code>BUSINESS_PRODUCTIVITY</code></p>
	                 </td>
	                 <td>
	                    <p>Send non-promotional messages to help people manage the productivity of their businesses or related activities.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Notifications on services or products that a business has subscribed to or purchased from a service provider</li>
	                       <li>Reminders or alerts on upcoming invoices or service maintenance</li>
	                       <li>Reports on performance, metrics, or recommended actions for the business</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as a the new tag <code>POST_PURCHASE_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_1 _5m29">
	                 <td>
	                    <p><code>COMMUNITY_ALERT</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of emergency or utility alerts, or issue a safety check in your community.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Request a safety check</li>
	                       <li>Notify of an emergency or utility alerts</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as a the new tag <code>NON_PROMOTIONAL_SUBSCRIPTION</code></p>
	                 </td>
	              </tr>
	              <tr class="row_2">
	                 <td>
	                    <p><code>CONFIRMED_EVENT_REMINDER</code></p>
	                 </td>
	                 <td>
	                    <p>Send the message recipient reminders of a scheduled event which a person is going to attend.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Upcoming classes or events that a person has signed up for</li>
	                       <li>Confirmation of attendance to an accepted event or appointment</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as a the new tag <code>CONFIRMED_EVENT_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_3 _5m29">
	                 <td>
	                    <p><code>NON_PROMOTIONAL_SUBSCRIPTION</code></p>
	                 </td>
	                 <td>
	                    <p>Send non-promotional messages under the News, Productivity, and Personal Trackers categories described in the Messenger Platform's subscription messaging policy.  You can apply for access to use this tag under the Page Settings &gt; Messenger Platform.</p>
	                 </td>
	                 <td>
	                    <p>See <a href="/docs/messenger-platform/policy/policy-overview#subscription_messaging">Platform Policy Overview - Subscription Messaging</a></p>
	                 </td>
	                 <td>
	                    <p>After MAR 4, 2020 will only be available to Pages on the <a href="https://www.facebook.com/help/publisher/316333835842972">Facebook News Page Index (NPI)</a>,</p>
	                 </td>
	              </tr>
	              <tr class="row_4">
	                 <td>
	                    <p><code>PAIRING_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient that a pairing has been identified based on a prior request.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Match identified in dating app</li>
	                       <li>Parking spot available</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>No longer supported after MAR 4, 2020</p>
	                 </td>
	              </tr>
	              <tr class="row_5 _5m29">
	                 <td>
	                    <p><code>APPLICATION_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of an update on the status of their application.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Application is being reviewed</li>
	                       <li>Application has been approved</li>
	                       <li>Job application status</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the tag <code>ACCOUNT_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_6">
	                 <td>
	                    <p><code>ACCOUNT_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of a change to their account settings.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Profile has changed</li>
	                       <li>Preferences are updated</li>
	                       <li>Settings have changed</li>
	                       <li>Membership has expired</li>
	                       <li>Password has changed</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Has a new expanded definition</p>
	                 </td>
	              </tr>
	              <tr class="row_7 _5m29">
	                 <td>
	                    <p><code>PAYMENT_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of a payment update for an existing transaction.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Send a receipt</li>
	                       <li>Send an out-of-stock notification</li>
	                       <li>Notify an auction has ended</li>
	                       <li>Status on a payment transaction has changed</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>POST_PURCHASE_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_8">
	                 <td>
	                    <p><code>PERSONAL_FINANCE_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Confirm a message recipient's financial activity.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Bill-pay reminders</li>
	                       <li>Scheduled payment reminder</li>
	                       <li>Payment receipt notification</li>
	                       <li>Funds transfer confirmation or update</li>
	                       <li>Other transactional activities in financial services</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>ACCOUNT_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_9 _5m29">
	                 <td>
	                    <p><code>SHIPPING_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of a change in shipping status for a product that has already been purchased.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Product is shipped</li>
	                       <li>Status changes to in-transit</li>
	                       <li>Product is delivered</li>
	                       <li>Shipment is delayed</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>POST_PURCHASE_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_10">
	                 <td>
	                    <p><code>RESERVATION_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of updates to an existing reservation.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Itinerary changes</li>
	                       <li>Location changes</li>
	                       <li>Cancellation is confirmed</li>
	                       <li>Hotel booking is cancelled</li>
	                       <li>Car rental pick-up time changes</li>
	                       <li>Room upgrade is confirmed</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>POST_PURCHASE_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_11 _5m29">
	                 <td>
	                    <p><code>ISSUE_RESOLUTION</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of an update to a customer service issue that was initiated in a Messenger conversation.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Issue is resolved</li>
	                       <li>Issue status is updated</li>
	                       <li>Issue requires a request for additional information</li>
	                       <li>Follow up on a customer inquiry or support ticket</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>HUMAN_AGENT</code></p>
	                 </td>
	              </tr>
	              <tr class="row_12">
	                 <td>
	                    <p><code>APPOINTMENT_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of a change to an existing appointment.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Appointment time changes</li>
	                       <li>Appointment location changes</li>
	                       <li>Appointment is cancelled</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>CONFIRMED_EVENT_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_13 _5m29">
	                 <td>
	                    <p><code>GAME_EVENT</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of a change in in-game user progression, global events, or a live sporting event.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Player's in-game crops are ready to be collected</li>
	                       <li>Player's daily tournament is about to start</li>
	                       <li>Person's favorite soccer team is about to begin a match           </li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>No longer supported after MAR 4, 2020</p>
	                 </td>
	              </tr>
	              <tr class="row_14">
	                 <td>
	                    <p><code>TRANSPORTATION_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of updates to an existing transportation reservation.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Flight status changes</li>
	                       <li>Ride is canceled</li>
	                       <li>Trip is started</li>
	                       <li>Ferry has arrived</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>CONFIRMED_EVENT_UPDATE</code></p>
	                 </td>
	              </tr>
	              <tr class="row_15 _5m29">
	                 <td>
	                    <p><code>FEATURE_FUNCTIONALITY_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Notify the message recipient of new features or functionality that become available in your bot.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Chat with a live agent is added to your bot</li>
	                       <li>A new skill is added to your bot</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>HUMAN_AGENT</code></p>
	                 </td>
	              </tr>
	              <tr class="row_16">
	                 <td>
	                    <p><code>TICKET_UPDATE</code></p>
	                 </td>
	                 <td>
	                    <p>Send the message recipient updates or reminders for an event for which a person already has a ticket.</p>
	                 </td>
	                 <td>
	                    <ul>
	                       <li>Concert start time changes</li>
	                       <li>Event location changes</li>
	                       <li>Show is cancelled</li>
	                       <li>A refund opportunity is made available</li>
	                    </ul>
	                 </td>
	                 <td>
	                    <p>Partially covered as the new tag <code>CONFIRMED_EVENT_UPDATE</code></p>
	                 </td>
	              </tr>
	           </tbody>
	        </table>
    	</div>
    	<?php endif; ?>
      </div>
    </div>
  </div>
</div>