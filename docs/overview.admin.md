## What it does

Countdown gives launches, events, offers, and deadlines a timezone-aware timer. Editors enter an IANA timezone and target; Capell stores the normalised UTC instant.

## Do I need to do anything?

Add the widget to a page in Layout Builder, complete its fields, and preview the public page before publishing.

## Where it shows up

In Layout Builder, as a Countdown widget. Public output includes a useful absolute time before JavaScript enhances it into a live countdown.

## Good to know

- This free beta extension belongs to Capell Essentials and requires Layout Builder.
- Live announcements use the active page locale. Translate `remaining` and the `day`, `hour`, and `minute` unit keys with CLDR suffixes (`zero`, `one`, `two`, `few`, `many`, `other`); unavailable local categories use the local `other` form first, then the application fallback language with its own plural rules. Unit messages accept `:count`; the remaining-time message accepts `:days`, `:hours`, and `:minutes`.
- Visual seconds stay hidden from assistive technology and announcements update no more than once per minute.

## Screenshot status

Capture the timezone and target fields plus a public timer showing days, hours, minutes, and the absolute-time fallback. The committed Marketplace card is interim beta artwork until these two fixture captures are recorded.

Widgets added through deferred content or inline reveal initialise when the content arrives. Repeated content-ready events do not duplicate listeners or countdown timers.
