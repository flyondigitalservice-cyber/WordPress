<?php
/**
 * One-time starter content: portal pages plus sample events, press releases, news and office bearers.
 * Everything created here is ordinary content that can be edited or deleted in wp-admin.
 */
if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (get_option('lf_seed_version') === '1') return;
    if (get_transient('lf_seeding')) return;
    set_transient('lf_seeding', 1, 60);

    /* Pages */
    $pages = [
        'about' => 'About', 'leadership' => 'Office Bearers & Committees', 'membership' => 'Membership', 'join' => 'Apply for Membership',
        'member-login' => 'Member Sign In', 'member-dashboard' => 'My Dashboard', 'members' => 'Member Directory', 'legal-aid' => 'Legal Aid & Pro Bono',
        'resources' => 'Resources', 'news' => 'News & Articles', 'gallery' => 'Gallery', 'contact' => 'Contact',
        'code-of-conduct' => 'Code of Conduct', 'privacy' => 'Privacy Policy', 'terms' => 'Terms of Use',
    ];
    $text = [
        'code-of-conduct' => '<p>Every member of the Foundation agrees to the following.</p><h2>Towards the court</h2><ul><li>To act with honesty and respect before every court and tribunal.</li><li>To follow the standards of professional conduct laid down under the Advocates Act, 1961 and the Bar Council of India Rules.</li></ul><h2>Towards colleagues</h2><ul><li>To treat fellow advocates, juniors, clerks and court staff with courtesy.</li><li>Not to solicit the clients of another member through the directory or at Foundation events.</li><li>To keep the contact details of other members private and to use them only for professional purposes.</li></ul><h2>Towards the public</h2><ul><li>To give legal aid advice with the same care as paid work.</li><li>Not to advertise or solicit work in breach of the Bar Council of India Rules.</li></ul><h2>Towards the Foundation</h2><ul><li>To give true information in the membership application and keep the profile up to date.</li><li>Not to use the name or emblem of the Foundation for personal or political purposes.</li></ul><p>The managing committee may suspend or cancel a membership after giving the member a fair hearing.</p>',
        'privacy' => '<p>This policy explains what information the Foundation collects through this website and how it is used.</p><h2>What we collect</h2><ul><li><strong>Membership applications:</strong> name, email address, mobile number, Bar Council enrolment number and year, court of practice, practice areas, locality, and firm or college.</li><li><strong>Event registrations:</strong> name, email address and mobile number.</li><li><strong>Contact and legal aid requests:</strong> the details you type into the form.</li></ul><h2>How we use it</h2><ul><li>To verify and administer your membership and issue your membership card.</li><li>To send you notices about your membership and the events you register for.</li><li>To respond to your enquiry or legal aid request.</li></ul><h2>Member directory</h2><p>You choose whether to appear in the directory. Your name, court, locality and practice areas are shown publicly. Your phone number and email address are shown only to signed-in, approved members. You can remove your listing at any time from your dashboard.</p><h2>Sharing</h2><p>We do not sell personal information. Legal aid requests are shared only with the volunteer advocates who handle them.</p><h2>Your choices</h2><p>You can correct your details from your dashboard, or write to the office to have your account and information deleted.</p>',
        'terms' => '<p>By using this website you agree to these terms.</p><h2>Information, not legal advice</h2><p>Articles, press releases and resources on this website are for general information. They are not legal advice and do not create an advocate-client relationship.</p><h2>No solicitation</h2><p>In keeping with the Bar Council of India Rules, this website does not advertise or solicit work for any advocate. The member directory is a professional reference for members and the public.</p><h2>Member accounts</h2><p>Keep your password private. You are responsible for activity on your account. The Foundation may suspend an account that gives false information or misuses the directory.</p><h2>Events</h2><p>Event details can change. Registered participants are told by email if an event is moved or cancelled.</p><h2>External links</h2><p>Links to court and government websites are provided for convenience. The Foundation is not responsible for their content.</p>',
    ];
    foreach ($pages as $slug => $title) {
        if (get_page_by_path($slug, OBJECT, 'page')) continue;
        wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $title, 'post_content' => isset($text[$slug]) ? $text[$slug] : '', 'comment_status' => 'closed']);
    }

    /* Sample events: dates are set relative to the day the theme is first loaded. */
    if (!get_posts(['post_type' => 'lf_event', 'numberposts' => 1, 'post_status' => 'any'])) {
        $events = [
            [10, 'Workshop: Practice under the New Criminal Laws', 'Workshop', '5:30 pm to 7:30 pm', 'Foundation Hall, Fort', 'Mumbai', 'Free for members, ₹300 for guests', 80, 0,
                'A practical session on the Bharatiya Nyaya Sanhita, the Bharatiya Nagarik Suraksha Sanhita and the Bharatiya Sakshya Adhiniyam: what has changed in bail, remand, charge and electronic evidence, with drafting pointers for trial court practice.'],
            [17, 'Free Legal Aid Camp for Senior Citizens and Tenants', 'Legal Aid Camp', '10:00 am to 2:00 pm', 'Community Hall, Sector 15, CBD Belapur', 'Navi Mumbai', 'Free and open to the public', 0, 0,
                'Volunteer advocates will give free first advice on maintenance, tenancy, housing society and consumer matters. Bring copies of the papers you have. Members who want to volunteer can register here.'],
            [24, 'MahaRERA and Redevelopment: A Practitioner\'s Seminar', 'Seminar', '4:00 pm to 6:30 pm', 'Conference Room, Vashi', 'Navi Mumbai', 'Free for members, ₹500 for guests', 120, 0,
                'Speakers from the real estate bar discuss complaints before MahaRERA, society redevelopment agreements, and recent rulings that matter to flat purchasers and societies.'],
            [38, 'Junior Advocates\' Mentoring Circle: Drafting and Court Craft', 'Mentoring', '5:00 pm to 6:30 pm', 'Foundation Library, Fort', 'Mumbai', 'Free, members only', 30, 1,
                'A small-group session for advocates in their first five years. Senior members review sample pleadings and discuss how to prepare for and conduct a hearing.'],
            [52, 'Annual Members\' Meet and Felicitation Evening', 'Annual Meet', '6:30 pm onwards', 'Banquet Hall, Nerul', 'Navi Mumbai', '₹750 per member, includes dinner', 250, 1,
                'The yearly gathering of the Foundation: the annual report, felicitation of senior members and pro bono volunteers, and dinner.'],
            [-21, 'Mediation Awareness Evening', 'Seminar', '5:30 pm to 7:00 pm', 'Foundation Hall, Fort', 'Mumbai', 'Free', 0, 0,
                'An introduction to the Mediation Act, 2023 and court-annexed mediation, with accredited mediators sharing their experience.'],
        ];
        foreach ($events as $e) {
            $id = wp_insert_post(['post_type' => 'lf_event', 'post_status' => 'publish', 'post_title' => $e[1], 'post_excerpt' => $e[9],
                'post_content' => '<p>' . $e[9] . '</p><p><em>This is a sample event. Edit or delete it under Events in the site admin.</em></p>']);
            if (!$id || is_wp_error($id)) continue;
            update_post_meta($id, '_lf_date', wp_date('Y-m-d', strtotime(($e[0] >= 0 ? '+' : '') . $e[0] . ' days')));
            update_post_meta($id, '_lf_type', $e[2]); update_post_meta($id, '_lf_time', $e[3]); update_post_meta($id, '_lf_venue', $e[4]);
            update_post_meta($id, '_lf_city', $e[5]); update_post_meta($id, '_lf_fee', $e[6]); update_post_meta($id, '_lf_capacity', $e[7]);
            update_post_meta($id, '_lf_members_only', $e[8] ? 1 : '');
        }
    }

    /* Sample press releases */
    if (!get_posts(['post_type' => 'lf_press', 'numberposts' => 1, 'post_status' => 'any'])) {
        $press = [
            ['Lawyers Foundation opens online membership for advocates of Mumbai and Navi Mumbai', 'Advocates can now apply for membership, receive a digital membership card and register for events through the new member portal.'],
            ['Foundation announces monthly free legal aid camps across Navi Mumbai', 'Volunteer advocates will hold a free legal aid camp every month, starting with CBD Belapur, followed by Vashi, Nerul and Panvel.'],
            ['Mentoring programme for junior advocates begins this term', 'Senior members will mentor advocates in their first five years of practice through monthly drafting and court-craft circles.'],
        ];
        foreach ($press as $i => $p) {
            wp_insert_post(['post_type' => 'lf_press', 'post_status' => 'publish', 'post_title' => $p[0], 'post_excerpt' => $p[1], 'post_date' => wp_date('Y-m-d H:i:s', strtotime('-' . ($i * 9 + 2) . ' days')),
                'post_content' => '<p><strong>Mumbai:</strong> ' . $p[1] . '</p><p>For details, journalists may write to the media committee through the contact page.</p><p><em>This is a sample press release. Edit or delete it under Press Releases in the site admin.</em></p>']);
        }
    }

    /* Sample news: replaces the default "Hello world!" post */
    $hello = get_page_by_path('hello-world', OBJECT, 'post');
    if ($hello) wp_trash_post($hello->ID);
    if (!get_posts(['post_type' => 'post', 'numberposts' => 1, 'post_status' => 'publish'])) {
        $news = [
            ['How to check your case status on eCourts: a short guide for litigants', 'A step-by-step note on finding your case by CNR number, party name or advocate name on the eCourts services portal.'],
            ['Five drafting habits every junior advocate should build early', 'Notes from the mentoring circle: chronology first, one fact per paragraph, exact prayers, paginated compilations and a list of dates.'],
            ['What tenants and housing societies ask most at our legal aid desk', 'Deposits, repairs, transfer fees and redevelopment consent: the questions volunteers hear most often, and where to find the answers.'],
        ];
        foreach ($news as $i => $n) {
            wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => $n[0], 'post_excerpt' => $n[1], 'post_date' => wp_date('Y-m-d H:i:s', strtotime('-' . ($i * 6 + 1) . ' days')),
                'post_content' => '<p>' . $n[1] . '</p><p><em>This is a sample article. Edit or delete it under Posts in the site admin.</em></p>']);
        }
    }

    /* Office bearer placeholders */
    if (!get_posts(['post_type' => 'lf_team', 'numberposts' => 1, 'post_status' => 'any'])) {
        foreach (['President', 'Vice President (Mumbai)', 'Vice President (Navi Mumbai)', 'Honorary Secretary', 'Joint Secretary', 'Treasurer', 'Convenor, Legal Aid', 'Convenor, Women Advocates'] as $i => $role) {
            wp_insert_post(['post_type' => 'lf_team', 'post_status' => 'publish', 'post_title' => 'Name to be added', 'post_excerpt' => $role, 'menu_order' => $i]);
        }
    }

    /* A welcome notice for members */
    if (!get_posts(['post_type' => 'lf_notice', 'numberposts' => 1, 'post_status' => 'any'])) {
        wp_insert_post(['post_type' => 'lf_notice', 'post_status' => 'publish', 'post_title' => 'Welcome to the member portal',
            'post_content' => 'Your digital membership card is at the top of your dashboard. Keep your profile up to date so colleagues can find you in the directory. Notices and circulars from the office will appear here.']);
    }

    if (!get_option('blogdescription') || get_option('blogdescription') === 'Just another WordPress site') update_option('blogdescription', 'The association of advocates of Mumbai and Navi Mumbai');
    if (strpos((string) get_option('blogname'), 'hostingersite.com') !== false) update_option('blogname', 'Lawyers Foundation, Mumbai & Navi Mumbai');

    update_option('lf_seed_version', '1');
    delete_transient('lf_seeding');
}, 20);

/** Make sure the /events/ and /press/ addresses are registered, including after this theme is published over an older one. */
add_action('init', function () {
    $rules = get_option('rewrite_rules');
    if (is_array($rules) && !isset($rules['events/?$'])) flush_rewrite_rules(false);
}, 99);
