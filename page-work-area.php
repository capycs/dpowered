<?php
/* Template Name: Team Work Area */
get_header();

$logged_in   = is_user_logged_in();
$can_access  = $logged_in && dpowered_user_can_leads();
$statuses    = dpowered_lead_statuses();
$sources     = dpowered_lead_sources();
$team        = dpowered_lead_team_members();
$current_uid = get_current_user_id();
$today       = current_time('Y-m-d');
?>

<section class="inner-hero" id="main-content">
    <div class="container inner-hero-content">
        <span class="section-tag">Team Area</span>
        <?php if ($can_access): ?>
            <h1>Team <span class="gradient-text">Workspace</span></h1>
            <p>Leads, pages and daily notes — everything your team needs in one place.</p>
        <?php else: ?>
            <h1>Team <span class="gradient-text">Login</span></h1>
            <p>Sign in to access the DPowered work area.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section work-area-section">
    <div class="container">

    <?php if (!$can_access): ?>

        <?php $login_error = isset($_GET['login_error']) ? sanitize_key($_GET['login_error']) : ''; ?>
        <div class="wa-login">
            <?php if ($logged_in && !dpowered_user_can_leads()): ?>
                <div class="form-notice form-error" role="alert">
                    <p><strong>No access.</strong> This account isn't set up for the work area. Ask Josh to add you as a Sales Team member.</p>
                </div>
                <a href="<?php echo esc_url(wp_logout_url(dpowered_work_area_url())); ?>" class="btn btn-secondary btn-full">Log out</a>
            <?php else: ?>
                <?php if ($login_error): ?>
                <div class="form-notice form-error" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <p>
                        <?php
                        if ($login_error === 'noaccess') {
                            echo "<strong>No access.</strong> That account isn't set up for the work area.";
                        } elseif ($login_error === 'security') {
                            echo '<strong>Security check failed.</strong> Please refresh and try again.';
                        } else {
                            echo '<strong>Login failed.</strong> Check your username and password.';
                        }
                        ?>
                    </p>
                </div>
                <?php endif; ?>

                <form class="contact-form wa-login-form" method="post" action="<?php echo esc_url(dpowered_work_area_url()); ?>">
                    <?php wp_nonce_field('dpowered_team_login', 'dpowered_login_nonce'); ?>
                    <input type="hidden" name="dpowered_login_submit" value="1">
                    <div class="form-group">
                        <label for="wa-user">Username or Email</label>
                        <input type="text" id="wa-user" name="log" autocomplete="username" required>
                    </div>
                    <div class="form-group">
                        <label for="wa-pass">Password</label>
                        <input type="password" id="wa-pass" name="pwd" autocomplete="current-password" required>
                    </div>
                    <label class="wa-remember">
                        <input type="checkbox" name="rememberme" value="forever"> Keep me logged in
                    </label>
                    <button type="submit" class="btn btn-primary btn-lg btn-full">Log In &rarr;</button>
                </form>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <?php
        $is_admin     = current_user_can('manage_options');
        $current_user = wp_get_current_user();
        $leads        = get_posts([
            'post_type'      => 'lead',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        // Initial counts scoped to today (JS re-scopes on date change).
        $counts = ['all' => 0, 'tocall' => 0, 'won' => 0];
        foreach ($leads as $l) {
            $d_tmp = dpowered_get_lead_data($l->ID);
            if ($d_tmp['private'] && $d_tmp['assigned'] !== $current_uid && !$is_admin) continue;
            if ($d_tmp['date'] !== $today) continue;
            $s = $d_tmp['status'];
            $counts['all']++;
            if ($s === 'new')  $counts['tocall']++;
            if ($s === 'won')  $counts['won']++;
        }
        $pad_content = (string) get_user_meta($current_uid, '_dpowered_pad_' . $today, true);

        // Meetings — sorted by date ASC (upcoming first), then past
        $all_meetings = get_posts([
            'post_type'      => 'dpowered_meeting',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_meeting_date',
            'order'          => 'ASC',
        ]);
        $upcoming_meetings = [];
        $past_meetings     = [];
        foreach ($all_meetings as $m) {
            $md = dpowered_get_meeting_data($m->ID);
            if ($md['status'] === 'upcoming' && $md['date'] >= $today) {
                $upcoming_meetings[] = $md;
            } else {
                $past_meetings[] = $md;
            }
        }
        // Past meetings newest first
        usort($past_meetings, function($a, $b) { return strcmp($b['date'], $a['date']); });
        $all_meeting_data = array_merge($upcoming_meetings, $past_meetings);
        ?>

        <!-- Workspace navigation -->
        <nav class="wa-workspace-nav" role="tablist">
            <button class="wa-workspace-tab is-active" id="waViewLeads" role="tab" aria-selected="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M7 5H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                Leads
            </button>
            <button class="wa-workspace-tab" id="waViewMeetings" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Meetings
                <?php $upcoming_count = count($upcoming_meetings); if ($upcoming_count): ?><span class="wa-workspace-tab-badge"><?php echo $upcoming_count; ?></span><?php endif; ?>
            </button>
            <button class="wa-workspace-tab" id="waViewPages" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Pages
            </button>
        </nav>

        <!-- Top bar (persistent across both views) -->
        <div class="wa-bar">
            <div class="wa-bar-user">
                Signed in as <strong><?php echo esc_html($current_user->display_name); ?></strong>
            </div>
            <div class="wa-bar-actions">
                <button type="button" class="btn btn-primary" id="waAddBtn">+ Add Lead</button>
                <a href="<?php echo esc_url(wp_logout_url(dpowered_work_area_url())); ?>" class="btn btn-secondary">Log out</a>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════ LEADS VIEW -->
        <div id="waLeadsView">

            <!-- Daily scratchpad (private to logged-in user) -->
            <div class="wa-scratchpad" id="waScratchpad">
                <button type="button" class="wa-scratchpad-toggle" id="waPadToggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    My notes <span class="wa-scratchpad-date-hint">(private · today)</span>
                    <span class="wa-scratchpad-arrow">›</span>
                </button>
                <div class="wa-scratchpad-body" id="waPadBody" hidden>
                    <textarea class="wa-pad" id="waPad" placeholder="Jot anything here — private to you, auto-saves per date…"><?php echo esc_textarea($pad_content); ?></textarea>
                    <span class="wa-pad-status" id="waPadStatus"></span>
                </div>
            </div>

            <div class="wa-datebar">
                <button type="button" class="wa-date-nav" id="waDatePrev" aria-label="Previous day">&lsaquo;</button>
                <input type="date" class="wa-date" id="waDate" value="<?php echo esc_attr($today); ?>" aria-label="Sheet date">
                <button type="button" class="wa-date-nav" id="waDateNext" aria-label="Next day">&rsaquo;</button>
                <button type="button" class="wa-today" id="waToday">Today</button>
                <button type="button" class="wa-showall" id="waShowAll">Show all</button>
                <span class="wa-date-label" id="waDateLabel"></span>
                <button type="button" class="wa-export" id="waExportBtn" title="Download the leads in view as a CSV">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export
                </button>
            </div>

            <!-- Live stats — reflect the date + team filter in view -->
            <div class="wa-stats" id="waStats" aria-label="Lead summary">
                <div class="wa-stat-card" data-tone="new"><span class="wa-stat-num" data-stat="tocall">0</span><span class="wa-stat-lbl">To call</span></div>
                <div class="wa-stat-card" data-tone="interested"><span class="wa-stat-num" data-stat="interested">0</span><span class="wa-stat-lbl">Interested</span></div>
                <div class="wa-stat-card" data-tone="quoted"><span class="wa-stat-num" data-stat="quoted">0</span><span class="wa-stat-lbl">Quoted</span></div>
                <div class="wa-stat-card" data-tone="won"><span class="wa-stat-num" data-stat="won">0</span><span class="wa-stat-lbl">Won</span></div>
                <div class="wa-stat-card" data-tone="callback"><span class="wa-stat-num" data-stat="callbacks">0</span><span class="wa-stat-lbl">Callbacks due</span></div>
                <div class="wa-stat-card" data-tone="rate"><span class="wa-stat-num" data-stat="conversion">0%</span><span class="wa-stat-lbl">Win rate</span></div>
            </div>

            <div class="wa-tabs" role="tablist">
                <button class="wa-tab is-active" data-filter="all" role="tab">All <span><?php echo (int) $counts['all']; ?></span></button>
                <button class="wa-tab" data-filter="tocall" role="tab">To Call <span><?php echo (int) $counts['tocall']; ?></span></button>
                <button class="wa-tab" data-filter="won" role="tab">Won <span><?php echo (int) $counts['won']; ?></span></button>
                <select class="wa-people" id="waPeople" aria-label="Filter by team member">
                    <option value="everyone">Everyone's leads</option>
                    <?php foreach ($team as $member): ?>
                    <option value="<?php echo (int) $member->ID; ?>"><?php echo esc_html($member->display_name) . ($member->ID === $current_uid ? ' (me)' : ''); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="search" class="wa-search" id="waSearch" placeholder="Search business, contact, phone…" aria-label="Search leads">
            </div>

            <div class="wa-table-wrap">
                <table class="wa-table" id="waTable">
                    <thead>
                        <tr>
                            <th class="wa-col-toggle" aria-label="Expand"></th>
                            <th><button type="button" class="wa-th-sort" data-sort="business">Business <span class="wa-sort-caret" aria-hidden="true"></span></button></th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th><button type="button" class="wa-th-sort" data-sort="status">Status <span class="wa-sort-caret" aria-hidden="true"></span></button></th>
                            <th class="wa-col-center">Called</th>
                            <th class="wa-col-center">Offered</th>
                            <th><button type="button" class="wa-th-sort" data-sort="assigned">Assigned <span class="wa-sort-caret" aria-hidden="true"></span></button></th>
                            <th class="wa-col-center" aria-label="Actions"></th>
                        </tr>
                    </thead>
                    <tbody id="waBody">
                        <?php foreach ($leads as $l):
                            $d = dpowered_get_lead_data($l->ID);
                            // Skip private leads not assigned to current user (unless admin).
                            if ($d['private'] && $d['assigned'] !== $current_uid && !$is_admin) continue;
                        ?>
                        <tr class="wa-row<?php echo $d['private'] ? ' is-private-lead' : ''; ?>"
                            data-id="<?php echo (int) $d['id']; ?>"
                            data-status="<?php echo esc_attr($d['status']); ?>"
                            data-assigned="<?php echo (int) $d['assigned']; ?>"
                            data-date="<?php echo esc_attr($d['date']); ?>"
                            data-callback="<?php echo esc_attr($d['callback']); ?>"
                            data-private="<?php echo $d['private'] ? '1' : '0'; ?>">
                            <td class="wa-col-toggle"><button type="button" class="wa-expand<?php echo $d['notes'] ? ' has-notes' : ''; ?>" aria-label="Toggle details">&rsaquo;</button></td>
                            <td data-label="Business">
                                <input type="text" class="wa-input wa-business" data-field="business" value="<?php echo esc_attr($d['business']); ?>">
                                <span class="wa-meta">Added <?php echo esc_html($d['created']); ?> · <?php echo esc_html($sources[$d['source']] ?? $d['source']); ?></span>
                                <span class="wa-callback-badge" hidden>📞 Callback</span>
                            </td>
                            <td data-label="Contact"><input type="text" class="wa-input" data-field="contact" value="<?php echo esc_attr($d['contact']); ?>"></td>
                            <td data-label="Phone" class="wa-phone-cell">
                                <input type="tel" class="wa-input" data-field="phone" value="<?php echo esc_attr($d['phone']); ?>">
                                <button type="button" class="wa-call-btn" aria-label="Call this number" title="Call">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </button>
                            </td>
                            <td data-label="Status">
                                <select class="wa-status status-<?php echo esc_attr($d['status']); ?>" data-field="status">
                                    <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($d['status'], $key); ?>><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="wa-col-center" data-label="Called"><input type="checkbox" class="wa-check" data-field="called" <?php checked($d['called']); ?>></td>
                            <td class="wa-col-center" data-label="Offered"><input type="checkbox" class="wa-check" data-field="offered" <?php checked($d['offered']); ?>></td>
                            <td data-label="Assigned">
                                <select class="wa-input" data-field="assigned">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($team as $member): ?>
                                    <option value="<?php echo (int) $member->ID; ?>" <?php selected($d['assigned'], $member->ID); ?>><?php echo esc_html($member->display_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="wa-col-center wa-col-actions">
                                <button type="button"
                                    class="wa-private-btn<?php echo $d['private'] ? ' is-private' : ''; ?>"
                                    data-field="private"
                                    aria-label="Toggle privacy"
                                    title="<?php echo $d['private'] ? 'Private — click to share with team' : 'Shared — click to make private'; ?>"></button>
                                <button type="button" class="wa-delete" aria-label="Delete lead">&times;</button>
                            </td>
                        </tr>
                        <tr class="wa-detail" data-id="<?php echo (int) $d['id']; ?>" hidden>
                            <td colspan="9">
                                <div class="wa-detail-grid">
                                    <label>Email
                                        <input type="email" class="wa-input" data-field="email" value="<?php echo esc_attr($d['email']); ?>">
                                    </label>
                                    <label>Source
                                        <select class="wa-input" data-field="source">
                                            <?php foreach ($sources as $key => $label): ?>
                                            <option value="<?php echo esc_attr($key); ?>" <?php selected($d['source'], $key); ?>><?php echo esc_html($label); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label>Sheet date
                                        <input type="date" class="wa-input" data-field="date" value="<?php echo esc_attr($d['date']); ?>">
                                    </label>
                                    <label>Call back date
                                        <input type="date" class="wa-input" data-field="callback" value="<?php echo esc_attr($d['callback']); ?>">
                                    </label>
                                    <label class="wa-detail-notes">Notes
                                        <textarea class="wa-input" data-field="notes" rows="2"><?php echo esc_textarea($d['notes']); ?></textarea>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="wa-empty" id="waEmpty"<?php echo $leads ? ' hidden' : ''; ?>>No leads yet. Click <strong>+ Add Lead</strong> to add your first one.</p>
            </div>

            <!-- Add Lead modal -->
            <div class="wa-modal" id="waModal" hidden>
                <div class="wa-modal-card">
                    <button type="button" class="wa-modal-close" id="waModalClose" aria-label="Close">&times;</button>
                    <h3>Add a Lead</h3>
                    <form id="waAddForm" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="add-business">Business Name</label>
                                <input type="text" id="add-business" name="business" placeholder="Smith Plumbing Ltd">
                            </div>
                            <div class="form-group">
                                <label for="add-contact">Contact Name</label>
                                <input type="text" id="add-contact" name="contact" placeholder="John Smith">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="add-phone">Phone</label>
                                <input type="tel" id="add-phone" name="phone" placeholder="07123 456789">
                            </div>
                            <div class="form-group">
                                <label for="add-email">Email</label>
                                <input type="email" id="add-email" name="email" placeholder="john@business.co.uk">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="add-date">Sheet date</label>
                                <input type="date" id="add-date" name="lead_date" value="<?php echo esc_attr($today); ?>">
                            </div>
                            <div class="form-group">
                                <label for="add-callback">Call back date <small style="font-weight:400;text-transform:none">(optional)</small></label>
                                <input type="date" id="add-callback" name="callback">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="add-status">Status</label>
                                <select id="add-status" name="status">
                                    <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="add-source">Source</label>
                                <select id="add-source" name="source">
                                    <?php foreach ($sources as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($key, 'cold-call'); ?>><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="add-assigned">Assign to</label>
                                <select id="add-assigned" name="assigned">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($team as $member): ?>
                                    <option value="<?php echo (int) $member->ID; ?>" <?php selected($member->ID, $current_uid); ?>><?php echo esc_html($member->display_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group" style="display:flex;align-items:flex-end">
                                <label class="wa-remember" style="margin-bottom:0">
                                    <input type="checkbox" name="private" value="1"> Private lead (only you + admin)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add-notes">Notes</label>
                            <textarea id="add-notes" name="notes" rows="3" placeholder="Anything useful about this lead…"></textarea>
                        </div>
                        <p class="wa-form-error" id="waAddError" hidden></p>
                        <button type="submit" class="btn btn-primary btn-lg btn-full">Save Lead</button>
                    </form>
                </div>
            </div>

        </div><!-- /#waLeadsView -->

        <!-- ═══════════════════════════════════════════════ MEETINGS VIEW -->
        <div id="waMeetingsView" hidden>

            <div class="wa-meetings-bar">
                <div>
                    <h2 class="wa-meetings-title">Meetings</h2>
                    <p class="wa-meetings-subtitle"><?php echo count($upcoming_meetings); ?> upcoming &middot; <?php echo count($past_meetings); ?> past</p>
                </div>
                <button type="button" class="btn btn-primary" id="waAddMeetingBtn">+ Add Meeting</button>
            </div>

            <?php if (empty($all_meeting_data)): ?>
            <div class="wa-meetings-empty">
                <div class="wa-page-empty-icon">📅</div>
                <p>No meetings yet. Click <strong>+ Add Meeting</strong> to schedule your first one.</p>
            </div>
            <?php else: ?>

            <?php if ($upcoming_meetings): ?>
            <div class="wa-meetings-section-label">Upcoming</div>
            <?php endif; ?>

            <div class="wa-meetings-list" id="waMeetingsList">
            <?php foreach ($all_meeting_data as $md):
                $is_past = ($md['status'] !== 'upcoming' || $md['date'] < $today);
                $status_class = 'mstatus-' . $md['status'];
                $status_labels = ['upcoming' => 'Upcoming', 'done' => 'Done', 'cancelled' => 'Cancelled'];
            ?>
            <?php if ($is_past && $md === reset($past_meetings)): ?>
            </div><!-- /.wa-meetings-list -->
            <?php if ($upcoming_meetings): ?><div class="wa-meetings-section-label" style="margin-top:24px">Past</div><?php endif; ?>
            <div class="wa-meetings-list" id="waMeetingsListPast">
            <?php endif; ?>

            <div class="wa-meeting-card<?php echo $is_past ? ' is-past' : ''; ?>"
                 data-id="<?php echo (int) $md['id']; ?>"
                 data-status="<?php echo esc_attr($md['status']); ?>"
                 data-date="<?php echo esc_attr($md['date']); ?>">

                <div class="wa-meeting-card-main">
                    <div class="wa-meeting-date-col">
                        <?php
                        $d = $md['date'] ? new DateTime($md['date']) : null;
                        if ($d): ?>
                        <span class="wa-meeting-day"><?php echo $d->format('d'); ?></span>
                        <span class="wa-meeting-month"><?php echo $d->format('M'); ?></span>
                        <?php endif; ?>
                        <?php if ($md['time']): ?><span class="wa-meeting-time"><?php echo esc_html($md['time']); ?></span><?php endif; ?>
                    </div>

                    <div class="wa-meeting-info">
                        <input type="text" class="wa-meeting-input wa-meeting-business" data-field="business" value="<?php echo esc_attr($md['business']); ?>" placeholder="Business name">
                        <div class="wa-meeting-meta-row">
                            <?php if ($md['contact']): ?><span class="wa-meeting-meta-item">👤 <?php echo esc_html($md['contact']); ?></span><?php endif; ?>
                            <?php if ($md['location']): ?><span class="wa-meeting-meta-item">📍 <?php echo esc_html($md['location']); ?></span><?php endif; ?>
                            <?php if ($md['phone']): ?><span class="wa-meeting-meta-item">📞 <?php echo esc_html($md['phone']); ?></span><?php endif; ?>
                        </div>
                        <?php if ($md['notes']): ?><p class="wa-meeting-notes-preview"><?php echo esc_html(wp_trim_words($md['notes'], 12)); ?></p><?php endif; ?>
                    </div>

                    <div class="wa-meeting-actions">
                        <span class="wa-meeting-status-badge <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_labels[$md['status']] ?? $md['status']); ?></span>
                        <?php if ($md['status'] === 'upcoming'): ?>
                        <button type="button" class="wa-meeting-done-btn" title="Mark as done">✓ Done</button>
                        <?php endif; ?>
                        <button type="button" class="wa-meeting-expand-btn" title="Edit details">&#8250;</button>
                        <button type="button" class="wa-meeting-delete-btn" title="Delete">&times;</button>
                    </div>
                </div>

                <div class="wa-meeting-detail" hidden>
                    <div class="wa-meeting-detail-grid">
                        <label>Contact
                            <input type="text" class="wa-input" data-field="contact" value="<?php echo esc_attr($md['contact']); ?>" placeholder="Contact name">
                        </label>
                        <label>Phone
                            <input type="tel" class="wa-input" data-field="phone" value="<?php echo esc_attr($md['phone']); ?>" placeholder="Phone number">
                        </label>
                        <label>Date
                            <input type="date" class="wa-input" data-field="date" value="<?php echo esc_attr($md['date']); ?>">
                        </label>
                        <label>Time
                            <input type="time" class="wa-input" data-field="time" value="<?php echo esc_attr($md['time']); ?>">
                        </label>
                        <label>Location
                            <input type="text" class="wa-input" data-field="location" value="<?php echo esc_attr($md['location']); ?>" placeholder="Address or video link">
                        </label>
                        <label>Assigned to
                            <select class="wa-input" data-field="assigned">
                                <option value="0">Unassigned</option>
                                <?php foreach ($team as $member): ?>
                                <option value="<?php echo (int) $member->ID; ?>" <?php selected($md['assigned'], $member->ID); ?>><?php echo esc_html($member->display_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>Status
                            <select class="wa-input" data-field="status">
                                <option value="upcoming" <?php selected($md['status'], 'upcoming'); ?>>Upcoming</option>
                                <option value="done"     <?php selected($md['status'], 'done');     ?>>Done</option>
                                <option value="cancelled"<?php selected($md['status'], 'cancelled');?>>Cancelled</option>
                            </select>
                        </label>
                        <label class="wa-meeting-notes-label">Notes
                            <textarea class="wa-input" data-field="notes" rows="3" placeholder="What to discuss, prep notes…"><?php echo esc_textarea($md['notes']); ?></textarea>
                        </label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div><!-- /.wa-meetings-list -->

            <?php endif; ?>

            <!-- Add Meeting modal -->
            <div class="wa-modal" id="waMeetingModal" hidden>
                <div class="wa-modal-card">
                    <button type="button" class="wa-modal-close" id="waMeetingModalClose" aria-label="Close">&times;</button>
                    <h3>Add a Meeting</h3>
                    <form id="waAddMeetingForm" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="mtg-business">Business / Client <span aria-hidden="true">*</span></label>
                                <input type="text" id="mtg-business" name="business" placeholder="Smith Plumbing Ltd" required>
                            </div>
                            <div class="form-group">
                                <label for="mtg-contact">Contact Name</label>
                                <input type="text" id="mtg-contact" name="contact" placeholder="John Smith">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="mtg-date">Date <span aria-hidden="true">*</span></label>
                                <input type="date" id="mtg-date" name="date" value="<?php echo esc_attr($today); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="mtg-time">Time</label>
                                <input type="time" id="mtg-time" name="time">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="mtg-location">Location / Link</label>
                                <input type="text" id="mtg-location" name="location" placeholder="Address or Zoom/Meet link">
                            </div>
                            <div class="form-group">
                                <label for="mtg-phone">Phone</label>
                                <input type="tel" id="mtg-phone" name="phone" placeholder="07123 456789">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="mtg-assigned">Assign to</label>
                                <select id="mtg-assigned" name="assigned">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($team as $member): ?>
                                    <option value="<?php echo (int) $member->ID; ?>" <?php selected($member->ID, $current_uid); ?>><?php echo esc_html($member->display_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mtg-notes">Notes</label>
                            <textarea id="mtg-notes" name="notes" rows="3" placeholder="What to discuss, prep notes, objectives…"></textarea>
                        </div>
                        <p class="wa-form-error" id="waMeetingAddError" hidden></p>
                        <button type="submit" class="btn btn-primary btn-lg btn-full">Save Meeting</button>
                    </form>
                </div>
            </div>

        </div><!-- /#waMeetingsView -->

        <!-- ═══════════════════════════════════════════════ PAGES VIEW -->
        <div id="waPagesView" hidden>
            <div class="wa-pages-layout">

                <!-- Sidebar: page list -->
                <aside class="wa-pages-sidebar">
                    <button class="btn btn-primary wa-pages-new-btn" id="waNewPage">+ New page</button>
                    <div class="wa-pages-list" id="waPagesList">
                        <p class="wa-pages-list-empty">No pages yet.<br>Create your first one above.</p>
                    </div>
                </aside>

                <!-- Main: editor -->
                <main class="wa-pages-main">

                    <div class="wa-page-empty-state" id="waPageEmptyState">
                        <div class="wa-page-empty-icon">📄</div>
                        <p>Select a page or create a new one<br>to start writing.</p>
                        <button class="btn btn-primary" id="waNewPageEmpty">+ New page</button>
                    </div>

                    <div class="wa-page-editor-wrap" id="waPageEditorWrap" hidden>

                        <div class="wa-page-titlebar">
                            <button class="wa-page-icon-btn" id="waPageIconBtn" title="Click to change icon">📄</button>
                            <input type="text" class="wa-page-title-input" id="waPageTitleInput" placeholder="Untitled">
                        </div>

                        <div class="wa-page-metabar">
                            <button class="wa-page-privacy-btn" id="waPagePrivacyBtn" data-private="0">
                                <span class="wa-privacy-icon">🔓</span>
                                <span id="waPrivacyLabel">Shared with team</span>
                            </button>
                            <span class="wa-page-saved-status" id="waPageSavedStatus"></span>
                            <button class="wa-page-delete-btn" id="waPageDeleteBtn" aria-label="Delete page" title="Delete this page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                        </div>

                        <div class="wa-editor-toolbar" id="waEditorToolbar">
                            <button class="wa-fmt" data-cmd="bold" title="Bold"><b>B</b></button>
                            <button class="wa-fmt" data-cmd="italic" title="Italic"><i>I</i></button>
                            <button class="wa-fmt" data-cmd="underline" title="Underline"><u>U</u></button>
                            <span class="wa-fmt-sep"></span>
                            <button class="wa-fmt" data-cmd="formatBlock" data-val="h2" title="Heading">H2</button>
                            <button class="wa-fmt" data-cmd="formatBlock" data-val="h3" title="Sub-heading">H3</button>
                            <button class="wa-fmt" data-cmd="insertUnorderedList" title="Bullet list">• List</button>
                            <button class="wa-fmt" data-cmd="insertOrderedList" title="Numbered list">1. List</button>
                            <span class="wa-fmt-sep"></span>
                            <button class="wa-fmt" data-cmd="insertHorizontalRule" title="Divider">—</button>
                        </div>

                        <div class="wa-editor-content" id="waEditorContent" contenteditable="true" spellcheck="true" data-placeholder="Start writing — your content auto-saves as you type…"></div>

                    </div><!-- /.wa-page-editor-wrap -->

                </main>

            </div><!-- /.wa-pages-layout -->
        </div><!-- /#waPagesView -->

    <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
