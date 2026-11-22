# Countdown Widget

<!-- prettier-ignore-start -->

Six weeks before the wedding, guests start asking how long is left. Put a countdown on the invitation page and they'll see the days, hours, minutes and seconds ticking down below the date written out in full. The same works for an open day, a launch or an entry deadline.

Each countdown has a title, a target time, an end message and an optional button with a link. Its colours follow the rest of your site. At zero the digits stay and your message appears beneath them. It needs Layout Builder, the page builder, to go on a page. The live ticking and end message need a small script in the visitor's browser. Without it, they see the written date and the time left when the page was built.

Type the time zone first, such as Europe/London, then the time the event starts there. Summer time is handled for you, so a 6pm wedding in London in July is entered as 6pm, and the written date reads 6:00 PM BST. The time zone box starts as UTC.

Visitors using a screen reader hear the time left once when the page loads and again when it ends.

## What it does

Countdown gives launches, events, offers, and deadlines a timezone-aware timer. Editors choose a timezone name (for example `Europe/London`) and type the local event time in it; summer time is applied and the stored value is a UTC instant.

## Do I need to do anything?

Add the widget to a page in Layout Builder, complete its fields, and preview the public page before publishing.

## Where it shows up

In Layout Builder, as a Countdown widget. Public output includes a useful absolute time before JavaScript enhances it into a live countdown.

## Good to know

- This free beta extension belongs to Capell Essentials and requires Layout Builder.
- Live announcements use the active page locale. Translate `remaining` and the `day`, `hour`, and `minute` unit keys with CLDR suffixes (`zero`, `one`, `two`, `few`, `many`, `other`); unavailable local categories use the local `other` form first, then the application fallback language with its own plural rules. Unit messages accept `:count`; the remaining-time message accepts `:days`, `:hours`, and `:minutes`.
- Visual seconds stay hidden from assistive technology and the status is announced once on load and once at expiry.

Widgets added through deferred content or inline reveal initialise when the content arrives. Repeated content-ready events do not duplicate listeners or countdown timers.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
