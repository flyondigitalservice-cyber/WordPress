=== Lawyers Foundation Membership ===
Requires at least: 6.2
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Membership platform for a foundation whose members are lawyers doing pro bono and public-interest work.

== Features ==

* Membership applications for lawyers (bar / licence number, jurisdiction, practice areas) with a declaration of good standing.
* Admin approval: Foundation → Overview, a row action and a bulk action on the Users screen. Applicants and members are emailed.
* Member dashboard: profile editing, the member's event registrations, and pro bono hour logging.
* Member directory, filterable by name, practice area and city; members-only or public, contact details shown to members only.
* Events with a yearly flagship cycle (two events a year by default; add a third line in Settings for three). RSVPs with capacity, confirmation email and CSV attendee export.
* Initiatives (the foundation's programmes of work) and verified service hours, with headline impact numbers.

== Setup ==

1. Activate the plugin. It creates the pages Become a Member, Member Dashboard, Member Directory, Events and Our Work, two starter flagship events for next year and four starter initiatives.
2. Go to Foundation → Settings to set the foundation's name, the event cycle and directory visibility.
3. Activate the "Lawyers Foundation" theme for the matching design, or place the shortcodes in any theme.

== Shortcodes ==

[lf_register] [lf_dashboard] [lf_directory] [lf_event_cycle year="2027"] [lf_events show="upcoming|past|all" limit="6"] [lf_initiatives limit="6"] [lf_impact]
