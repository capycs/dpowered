/* Team Work Area — lead manager interactions */
(function () {
    'use strict';

    var cfg = window.dpoweredLeads || {};
    var table = document.getElementById('waTable');
    if (!table || !cfg.ajaxurl) return;

    var body = document.getElementById('waBody');
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.wa-tab'));
    var searchInput = document.getElementById('waSearch');
    var emptyMsg = document.getElementById('waEmpty');
    var dateInput = document.getElementById('waDate');
    var dateLabel = document.getElementById('waDateLabel');
    var peopleSelect = document.getElementById('waPeople');
    var showAllBtn = document.getElementById('waShowAll');

    var activeFilter = 'all';
    var activeDate = dateInput ? dateInput.value : '';
    var activePerson = peopleSelect ? peopleSelect.value : 'everyone';
    var allDates = false;

    /* ── Date helpers ─────────────────────────────────────── */
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function shiftDate(str, days) {
        var p = (str || todayStr()).split('-');
        var d = new Date(Date.UTC(+p[0], +p[1] - 1, +p[2]));
        d.setUTCDate(d.getUTCDate() + days);
        return d.toISOString().slice(0, 10);
    }
    function formatLabel(str) {
        if (!str) return '';
        var p = str.split('-');
        var d = new Date(Date.UTC(+p[0], +p[1] - 1, +p[2]));
        var label = d.toLocaleDateString(undefined, { weekday: 'short', day: 'numeric', month: 'long', timeZone: 'UTC' });
        return str === todayStr() ? label + ' · Today' : label;
    }
    function setDate(str) {
        allDates = false;
        if (showAllBtn) showAllBtn.classList.remove('is-active');
        activeDate = str;
        if (dateInput) dateInput.value = str;
        if (dateLabel) dateLabel.textContent = formatLabel(str);
        applyFilter();
        recomputeCounts();
        loadPadForDate(str);
    }

    /* ── AJAX helper ──────────────────────────────────────── */
    function post(fields) {
        var data = new FormData();
        data.append('nonce', cfg.nonce);
        Object.keys(fields).forEach(function (k) { data.append(k, fields[k]); });
        return fetch(cfg.ajaxurl, { method: 'POST', credentials: 'same-origin', body: data })
            .then(function (r) { return r.json(); });
    }

    function flash(el) {
        if (!el) return;
        el.classList.remove('wa-saved');
        void el.offsetWidth;
        el.classList.add('wa-saved');
    }

    /* ── Filtering ───────────────────────────────────────── */
    function rowMatches(row) {
        if (!allDates && activeDate) {
            var onDate     = row.getAttribute('data-date') === activeDate;
            var onCallback = row.getAttribute('data-callback') === activeDate;
            if (!onDate && !onCallback) return false;
        }

        var status = row.getAttribute('data-status');
        if (activeFilter === 'tocall' && status !== 'new') return false;
        if (activeFilter === 'won' && status !== 'won') return false;

        if (activePerson !== 'everyone' && row.getAttribute('data-assigned') !== String(activePerson)) return false;

        var term = (searchInput && searchInput.value || '').trim().toLowerCase();
        if (term) {
            var hay = '';
            row.querySelectorAll('input[type="text"], input[type="tel"]').forEach(function (i) {
                hay += ' ' + i.value.toLowerCase();
            });
            if (hay.indexOf(term) === -1) return false;
        }
        return true;
    }

    function applyFilter() {
        var visible = 0;
        Array.prototype.forEach.call(body.querySelectorAll('.wa-row'), function (row) {
            var show = rowMatches(row);
            row.style.display = show ? '' : 'none';

            // Show callback badge only when row appears via its callback date (not its original date).
            var badge = row.querySelector('.wa-callback-badge');
            if (badge && !allDates && activeDate) {
                var isCallbackRow = row.getAttribute('data-callback') === activeDate && row.getAttribute('data-date') !== activeDate;
                badge.hidden = !isCallbackRow;
            }

            var detail = body.querySelector('.wa-detail[data-id="' + row.getAttribute('data-id') + '"]');
            if (detail && !show) { detail.hidden = true; detail.style.display = 'none'; }
            else if (detail && show && detail.style.display === 'none') { detail.style.display = ''; }
            if (show) visible++;
        });
        if (emptyMsg) emptyMsg.hidden = visible !== 0;
    }

    function recomputeCounts() {
        var c = { all: 0, tocall: 0, won: 0 };
        Array.prototype.forEach.call(body.querySelectorAll('.wa-row'), function (row) {
            if (!allDates && activeDate && row.getAttribute('data-date') !== activeDate) return;
            if (activePerson !== 'everyone' && row.getAttribute('data-assigned') !== String(activePerson)) return;
            var status = row.getAttribute('data-status');
            c.all++;
            if (status === 'new') c.tocall++;
            if (status === 'won') c.won++;
        });
        tabs.forEach(function (tab) {
            var span = tab.querySelector('span');
            var key = tab.getAttribute('data-filter');
            if (span && c[key] !== undefined) span.textContent = c[key];
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            activeFilter = tab.getAttribute('data-filter');
            applyFilter();
        });
    });
    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (peopleSelect) peopleSelect.addEventListener('change', function () {
        activePerson = peopleSelect.value;
        applyFilter();
        recomputeCounts();
    });

    /* ── Date navigation ─────────────────────────────────── */
    if (dateInput) dateInput.addEventListener('change', function () { setDate(dateInput.value || todayStr()); });
    var prevBtn = document.getElementById('waDatePrev');
    var nextBtn = document.getElementById('waDateNext');
    var todayBtn = document.getElementById('waToday');
    if (prevBtn) prevBtn.addEventListener('click', function () { setDate(shiftDate(activeDate, -1)); });
    if (nextBtn) nextBtn.addEventListener('click', function () { setDate(shiftDate(activeDate, 1)); });
    if (todayBtn) todayBtn.addEventListener('click', function () { setDate(todayStr()); });
    if (showAllBtn) showAllBtn.addEventListener('click', function () {
        allDates = !allDates;
        showAllBtn.classList.toggle('is-active', allDates);
        if (dateLabel) dateLabel.textContent = allDates ? 'All dates' : formatLabel(activeDate);
        applyFilter();
        recomputeCounts();
    });

    /* ── Expand / collapse detail ────────────────────────── */
    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.wa-expand');
        if (!btn) return;
        var row = btn.closest('.wa-row');
        var detail = body.querySelector('.wa-detail[data-id="' + row.getAttribute('data-id') + '"]');
        if (!detail) return;
        var open = detail.hidden;
        detail.hidden = !open;
        detail.style.display = '';
        btn.classList.toggle('is-open', open);
    });

    /* ── Inline saves ────────────────────────────────────── */
    function saveField(control) {
        var row = control.closest('.wa-row') || control.closest('.wa-detail');
        var id = row.getAttribute('data-id');
        var field = control.getAttribute('data-field');
        if (!field) return;

        var value = control.type === 'checkbox' ? (control.checked ? '1' : '0') : control.value;

        post({ action: 'dpowered_update_lead', lead_id: id, field: field, value: value })
            .then(function (res) {
                if (!res || !res.success) {
                    alert((res && res.data && res.data.msg) || 'Could not save. Try again.');
                    return;
                }
                var lead = res.data.lead;
                var mainRow = body.querySelector('.wa-row[data-id="' + id + '"]');
                flash(control.closest('td'));

                if (field === 'status') {
                    mainRow.setAttribute('data-status', lead.status);
                    var statusSel = mainRow.querySelector('select[data-field="status"]');
                    if (statusSel) statusSel.className = 'wa-status status-' + lead.status;
                    var calledBox = mainRow.querySelector('input[data-field="called"]');
                    if (calledBox) calledBox.checked = lead.called;
                    recomputeCounts();
                    applyFilter();
                }
                if (field === 'assigned') {
                    mainRow.setAttribute('data-assigned', lead.assigned);
                    recomputeCounts();
                    applyFilter();
                }
                if (field === 'date') {
                    mainRow.setAttribute('data-date', lead.date);
                    recomputeCounts();
                    applyFilter();
                }
                if (field === 'callback') {
                    mainRow.setAttribute('data-callback', lead.callback || '');
                    recomputeCounts();
                    applyFilter();
                }
            })
            .catch(function () { alert('Network error — change not saved.'); });
    }

    body.addEventListener('change', function (e) {
        var c = e.target;
        if (c.matches('select[data-field], input[type="checkbox"][data-field]')) saveField(c);
    });
    body.addEventListener('blur', function (e) {
        var c = e.target;
        if (c.matches('input[data-field]:not([type="checkbox"]), textarea[data-field]')) saveField(c);
    }, true);

    /* ── Private toggle ─────────────────────────────────── */
    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.wa-private-btn');
        if (!btn) return;
        var row        = btn.closest('.wa-row');
        var id         = row.getAttribute('data-id');
        var isPrivate  = row.getAttribute('data-private') === '1';
        var newPrivate = isPrivate ? 0 : 1;

        post({ action: 'dpowered_update_lead', lead_id: id, field: 'private', value: newPrivate })
            .then(function (res) {
                if (!res || !res.success) { alert('Could not update.'); return; }
                row.setAttribute('data-private', newPrivate);
                row.classList.toggle('is-private-lead', !!newPrivate);
                btn.classList.toggle('is-private', !!newPrivate);
                btn.setAttribute('title', newPrivate ? 'Private — click to share with team' : 'Shared — click to make private');
            });
    });

    /* ── Delete ──────────────────────────────────────────── */
    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.wa-delete');
        if (!btn) return;
        var row = btn.closest('.wa-row');
        var id = row.getAttribute('data-id');
        var name = (row.querySelector('.wa-business') || {}).value || 'this lead';
        if (!window.confirm('Delete "' + name + '"? This moves it to the trash.')) return;

        post({ action: 'dpowered_delete_lead', lead_id: id }).then(function (res) {
            if (!res || !res.success) { alert('Could not delete.'); return; }
            var detail = body.querySelector('.wa-detail[data-id="' + id + '"]');
            row.remove();
            if (detail) detail.remove();
            recomputeCounts();
            applyFilter();
        });
    });

    /* ── Add Lead modal ──────────────────────────────────── */
    var modal = document.getElementById('waModal');
    var addBtn = document.getElementById('waAddBtn');
    var closeBtn = document.getElementById('waModalClose');
    var addForm = document.getElementById('waAddForm');
    var addError = document.getElementById('waAddError');
    var addDate = document.getElementById('add-date');

    function openModal() {
        if (!modal) return;
        if (addDate && activeDate) addDate.value = activeDate; // new lead lands on the sheet you're viewing
        modal.hidden = false;
    }
    function closeModal() { if (modal) { modal.hidden = true; if (addError) addError.hidden = true; } }

    if (addBtn) addBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal && !modal.hidden) closeModal(); });

    if (addForm) addForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var fields = { action: 'dpowered_add_lead' };
        new FormData(addForm).forEach(function (v, k) { fields[k] = v; });
        var submit = addForm.querySelector('button[type="submit"]');
        if (submit) { submit.disabled = true; submit.textContent = 'Saving…'; }

        post(fields).then(function (res) {
            if (!res || !res.success) {
                if (addError) { addError.textContent = (res && res.data && res.data.msg) || 'Could not save.'; addError.hidden = false; }
                if (submit) { submit.disabled = false; submit.textContent = 'Save Lead'; }
                return;
            }
            // Preserve the viewed date across the reload so the new lead is in view.
            window.location.hash = 'd=' + (fields.lead_date || activeDate || todayStr());
            window.location.reload();
        }).catch(function () {
            if (addError) { addError.textContent = 'Network error — try again.'; addError.hidden = false; }
            if (submit) { submit.disabled = false; submit.textContent = 'Save Lead'; }
        });
    });

    /* ── Meetings ────────────────────────────────────────── */
    var meetingsView      = document.getElementById('waMeetingsView');
    var meetingsList      = document.getElementById('waMeetingsList');
    var addMeetingBtn     = document.getElementById('waAddMeetingBtn');
    var meetingModal      = document.getElementById('waMeetingModal');
    var meetingModalClose = document.getElementById('waMeetingModalClose');
    var addMeetingForm    = document.getElementById('waAddMeetingForm');
    var meetingAddError   = document.getElementById('waMeetingAddError');

    function openMeetingModal() {
        if (meetingModal) meetingModal.hidden = false;
    }
    function closeMeetingModal() {
        if (meetingModal) { meetingModal.hidden = true; if (meetingAddError) meetingAddError.hidden = true; }
    }
    if (addMeetingBtn)     addMeetingBtn.addEventListener('click', openMeetingModal);
    if (meetingModalClose) meetingModalClose.addEventListener('click', closeMeetingModal);
    if (meetingModal)      meetingModal.addEventListener('click', function(e) { if (e.target === meetingModal) closeMeetingModal(); });

    if (addMeetingForm) addMeetingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var fields = { action: 'dpowered_add_meeting' };
        new FormData(addMeetingForm).forEach(function(v, k) { fields[k] = v; });
        var submit = addMeetingForm.querySelector('button[type="submit"]');
        if (submit) { submit.disabled = true; submit.textContent = 'Saving…'; }

        post(fields).then(function(res) {
            if (!res || !res.success) {
                if (meetingAddError) { meetingAddError.textContent = (res && res.data && res.data.msg) || 'Could not save.'; meetingAddError.hidden = false; }
                if (submit) { submit.disabled = false; submit.textContent = 'Save Meeting'; }
                return;
            }
            closeMeetingModal();
            window.location.reload();
        }).catch(function() {
            if (meetingAddError) { meetingAddError.textContent = 'Network error.'; meetingAddError.hidden = false; }
            if (submit) { submit.disabled = false; submit.textContent = 'Save Meeting'; }
        });
    });

    /* Meeting expand / collapse */
    document.addEventListener('click', function(e) {
        var expandBtn = e.target.closest('.wa-meeting-expand-btn');
        if (!expandBtn) return;
        var card   = expandBtn.closest('.wa-meeting-card');
        var detail = card && card.querySelector('.wa-meeting-detail');
        if (!detail) return;
        var open = detail.hidden;
        detail.hidden = !open;
        expandBtn.classList.toggle('is-open', open);
    });

    /* Meeting inline save */
    function saveMeetingField(el) {
        var card = el.closest('.wa-meeting-card') || el.closest('.wa-meeting-detail');
        if (!card) return;
        var meetingCard = el.closest('.wa-meeting-card') || card.previousElementSibling;
        var id = meetingCard ? meetingCard.getAttribute('data-id') : null;
        if (!id) { card = el.closest('[data-id]'); id = card ? card.getAttribute('data-id') : null; }
        if (!id) return;
        var field = el.getAttribute('data-field');
        if (!field) return;
        var value = el.tagName === 'SELECT' ? el.value : el.value;
        post({ action: 'dpowered_update_meeting', meeting_id: id, field: field, value: value })
            .then(function(res) {
                if (!res || !res.success) { alert('Could not save.'); return; }
                flash(el.closest('td') || el.parentNode);
                var mc = document.querySelector('.wa-meeting-card[data-id="' + id + '"]');
                if (mc && field === 'status') mc.setAttribute('data-status', res.data.meeting.status);
                if (mc && field === 'date')   mc.setAttribute('data-date',   res.data.meeting.date);
            });
    }

    document.addEventListener('change', function(e) {
        if (e.target.closest('.wa-meeting-card') && e.target.matches('select[data-field]')) saveMeetingField(e.target);
    });
    document.addEventListener('blur', function(e) {
        if (e.target.closest('.wa-meeting-card') && e.target.matches('input[data-field], textarea[data-field]')) saveMeetingField(e.target);
    }, true);

    /* Mark as done */
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.wa-meeting-done-btn');
        if (!btn) return;
        var card = btn.closest('.wa-meeting-card');
        var id   = card && card.getAttribute('data-id');
        if (!id) return;
        post({ action: 'dpowered_update_meeting', meeting_id: id, field: 'status', value: 'done' })
            .then(function(res) {
                if (!res || !res.success) { alert('Could not update.'); return; }
                window.location.reload();
            });
    });

    /* Delete meeting */
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.wa-meeting-delete-btn');
        if (!btn) return;
        var card = btn.closest('.wa-meeting-card');
        var id   = card && card.getAttribute('data-id');
        var name = card && card.querySelector('.wa-meeting-business') ? card.querySelector('.wa-meeting-business').value : 'this meeting';
        if (!id) return;
        if (!window.confirm('Delete "' + name + '"?')) return;
        post({ action: 'dpowered_delete_meeting', meeting_id: id }).then(function(res) {
            if (!res || !res.success) { alert('Could not delete.'); return; }
            card.remove();
        });
    });

    /* ── Workspace navigation ────────────────────────────── */
    var viewLeadsBtn    = document.getElementById('waViewLeads');
    var viewMeetingsBtn = document.getElementById('waViewMeetings');
    var viewPagesBtn    = document.getElementById('waViewPages');
    var leadsView       = document.getElementById('waLeadsView');
    var pagesView       = document.getElementById('waPagesView');

    function showView(view) {
        var views   = { leads: leadsView, meetings: meetingsView, pages: pagesView };
        var buttons = { leads: viewLeadsBtn, meetings: viewMeetingsBtn, pages: viewPagesBtn };
        Object.keys(views).forEach(function(v) {
            if (views[v])   views[v].hidden = (v !== view);
            if (buttons[v]) { buttons[v].classList.toggle('is-active', v === view); buttons[v].setAttribute('aria-selected', String(v === view)); }
        });
        if (view === 'pages' && !pagesLoaded) initPages();
    }
    if (viewLeadsBtn)    viewLeadsBtn.addEventListener('click',    function () { showView('leads'); });
    if (viewMeetingsBtn) viewMeetingsBtn.addEventListener('click', function () { showView('meetings'); });
    if (viewPagesBtn)    viewPagesBtn.addEventListener('click',    function () { showView('pages'); });

    /* ── Scratchpad ──────────────────────────────────────── */
    var padToggle = document.getElementById('waPadToggle');
    var padBody   = document.getElementById('waPadBody');
    var pad       = document.getElementById('waPad');
    var padStatus = document.getElementById('waPadStatus');
    var padTimer  = null;

    if (padToggle) padToggle.addEventListener('click', function () {
        var opening = padBody.hidden;
        padBody.hidden = !opening;
        padToggle.classList.toggle('is-open', opening);
    });

    function savePad() {
        if (!pad) return;
        post({ action: 'dpowered_save_pad', date: activeDate, content: pad.value })
            .then(function (res) {
                if (padStatus) padStatus.textContent = res && res.success ? 'Saved' : 'Error saving';
            });
    }
    if (pad) pad.addEventListener('input', function () {
        if (padStatus) padStatus.textContent = 'Saving…';
        clearTimeout(padTimer);
        padTimer = setTimeout(savePad, 800);
    });

    function loadPadForDate(date) {
        if (!pad) return;
        if (date === todayStr() && cfg.todayPad !== undefined) {
            pad.value = cfg.todayPad;
            return;
        }
        post({ action: 'dpowered_load_pad', date: date })
            .then(function (res) { if (res && res.success) pad.value = res.data.content || ''; });
    }

    /* ── Pages ───────────────────────────────────────────── */
    var pagesData        = Array.isArray(cfg.pages) ? cfg.pages : [];
    var activePage       = null;
    var pagesLoaded      = false;
    var pageTimer        = null;
    var iconPickerEl     = null;

    var newPageBtn       = document.getElementById('waNewPage');
    var newPageEmptyBtn  = document.getElementById('waNewPageEmpty');
    var pagesList        = document.getElementById('waPagesList');
    var pageEmptyState   = document.getElementById('waPageEmptyState');
    var pageEditorWrap   = document.getElementById('waPageEditorWrap');
    var pageIconBtn      = document.getElementById('waPageIconBtn');
    var pageTitleInput   = document.getElementById('waPageTitleInput');
    var pagePrivacyBtn   = document.getElementById('waPagePrivacyBtn');
    var privacyLabel     = document.getElementById('waPrivacyLabel');
    var pageSavedStatus  = document.getElementById('waPageSavedStatus');
    var pageDeleteBtn    = document.getElementById('waPageDeleteBtn');
    var editorContent    = document.getElementById('waEditorContent');
    var editorToolbar    = document.getElementById('waEditorToolbar');

    var PAGE_ICONS = ['📄','📝','🎯','💡','📊','✅','🔖','📌','⭐','🔥','💼','📱','🗂','📋','🧠','🚀'];

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function initPages() {
        pagesLoaded = true;
        renderPageList();
        if (pagesData.length) openPage(pagesData[0]);
    }

    function renderPageList() {
        if (!pagesList) return;
        pagesList.innerHTML = '';
        if (!pagesData.length) {
            pagesList.innerHTML = '<p class="wa-pages-list-empty">No pages yet.<br>Create your first one above.</p>';
            return;
        }
        pagesData.forEach(function (p) {
            var btn = document.createElement('button');
            btn.className = 'wa-page-item' + (activePage && activePage.id === p.id ? ' is-active' : '');
            btn.setAttribute('data-page-id', p.id);
            btn.innerHTML = '<span class="wa-page-item-icon">' + escHtml(p.icon || '📄') + '</span>'
                + '<span class="wa-page-item-title">' + escHtml(p.title || 'Untitled') + '</span>'
                + (p.private ? '<span class="wa-page-item-badge">🔒</span>' : '');
            btn.addEventListener('click', function () { openPage(p); });
            pagesList.appendChild(btn);
        });
    }

    function openPage(page) {
        activePage = page;
        if (pageEmptyState)  pageEmptyState.hidden  = true;
        if (pageEditorWrap)  pageEditorWrap.hidden  = false;
        if (pageIconBtn)     pageIconBtn.textContent = page.icon || '📄';
        if (pageTitleInput)  pageTitleInput.value   = page.title || '';
        if (editorContent)   editorContent.innerHTML = page.content || '';
        var priv = page.private ? '1' : '0';
        if (pagePrivacyBtn)  { pagePrivacyBtn.setAttribute('data-private', priv); pagePrivacyBtn.classList.toggle('is-private', !!page.private); }
        if (privacyLabel)    privacyLabel.textContent = page.private ? 'Private' : 'Shared with team';
        if (pageSavedStatus) pageSavedStatus.textContent = '';
        document.querySelectorAll('.wa-page-item').forEach(function (el) {
            el.classList.toggle('is-active', parseInt(el.getAttribute('data-page-id'), 10) === page.id);
        });
    }

    function newPage() {
        activePage = null;
        if (pageEmptyState)  pageEmptyState.hidden  = true;
        if (pageEditorWrap)  pageEditorWrap.hidden  = false;
        if (pageIconBtn)     pageIconBtn.textContent = '📄';
        if (pageTitleInput)  { pageTitleInput.value = ''; pageTitleInput.focus(); }
        if (editorContent)   editorContent.innerHTML = '';
        if (pagePrivacyBtn)  { pagePrivacyBtn.setAttribute('data-private', '0'); pagePrivacyBtn.classList.remove('is-private'); }
        if (privacyLabel)    privacyLabel.textContent = 'Shared with team';
        if (pageSavedStatus) pageSavedStatus.textContent = '';
        document.querySelectorAll('.wa-page-item').forEach(function (el) { el.classList.remove('is-active'); });
    }

    function scheduleSavePage() {
        if (pageSavedStatus) pageSavedStatus.textContent = 'Saving…';
        clearTimeout(pageTimer);
        pageTimer = setTimeout(savePage, 1000);
    }

    function savePage() {
        var title    = pageTitleInput  ? pageTitleInput.value.trim()         : '';
        var content  = editorContent   ? editorContent.innerHTML             : '';
        var icon     = pageIconBtn     ? pageIconBtn.textContent.trim()      : '📄';
        var isPriv   = pagePrivacyBtn  ? pagePrivacyBtn.getAttribute('data-private') === '1' : false;

        post({
            action:   'dpowered_save_page',
            page_id:  activePage ? activePage.id : 0,
            title:    title,
            content:  content,
            icon:     icon,
            private:  isPriv ? 1 : 0,
        }).then(function (res) {
            if (!res || !res.success) { if (pageSavedStatus) pageSavedStatus.textContent = 'Error saving'; return; }
            var page = res.data.page;
            activePage = page;
            var idx = -1;
            pagesData.forEach(function (p, i) { if (p.id === page.id) idx = i; });
            if (idx >= 0) pagesData[idx] = page;
            else pagesData.unshift(page);
            renderPageList();
            document.querySelectorAll('.wa-page-item').forEach(function (el) {
                el.classList.toggle('is-active', parseInt(el.getAttribute('data-page-id'), 10) === page.id);
            });
            if (pageSavedStatus) pageSavedStatus.textContent = 'Saved';
        }).catch(function () { if (pageSavedStatus) pageSavedStatus.textContent = 'Error saving'; });
    }

    if (newPageBtn)      newPageBtn.addEventListener('click', newPage);
    if (newPageEmptyBtn) newPageEmptyBtn.addEventListener('click', newPage);

    if (pageTitleInput) pageTitleInput.addEventListener('input', scheduleSavePage);
    if (editorContent)  editorContent.addEventListener('input', scheduleSavePage);

    if (pagePrivacyBtn) pagePrivacyBtn.addEventListener('click', function () {
        var nowPrivate = pagePrivacyBtn.getAttribute('data-private') === '1';
        var next = nowPrivate ? '0' : '1';
        pagePrivacyBtn.setAttribute('data-private', next);
        pagePrivacyBtn.classList.toggle('is-private', next === '1');
        if (privacyLabel) privacyLabel.textContent = next === '1' ? 'Private' : 'Shared with team';
        scheduleSavePage();
    });

    if (pageDeleteBtn) pageDeleteBtn.addEventListener('click', function () {
        if (!activePage) return;
        if (!window.confirm('Delete "' + (activePage.title || 'Untitled') + '"? This cannot be undone.')) return;
        post({ action: 'dpowered_delete_page', page_id: activePage.id }).then(function (res) {
            if (!res || !res.success) { alert('Could not delete.'); return; }
            var idx = -1;
            pagesData.forEach(function (p, i) { if (p.id === activePage.id) idx = i; });
            if (idx >= 0) pagesData.splice(idx, 1);
            activePage = null;
            renderPageList();
            if (pagesData.length) {
                openPage(pagesData[0]);
            } else {
                if (pageEditorWrap) pageEditorWrap.hidden = true;
                if (pageEmptyState) pageEmptyState.hidden = false;
            }
        });
    });

    // Formatting toolbar
    if (editorToolbar) editorToolbar.addEventListener('mousedown', function (e) {
        var btn = e.target.closest('.wa-fmt');
        if (!btn) return;
        e.preventDefault();
        var cmd = btn.getAttribute('data-cmd');
        var val = btn.getAttribute('data-val') || null;
        document.execCommand(cmd, false, val);
        if (editorContent) editorContent.focus();
        scheduleSavePage();
    });

    // Icon picker
    if (pageIconBtn) pageIconBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (iconPickerEl) { iconPickerEl.remove(); iconPickerEl = null; return; }
        iconPickerEl = document.createElement('div');
        iconPickerEl.className = 'wa-icon-picker';
        PAGE_ICONS.forEach(function (emoji) {
            var opt = document.createElement('button');
            opt.className = 'wa-icon-opt';
            opt.textContent = emoji;
            opt.addEventListener('click', function () {
                pageIconBtn.textContent = emoji;
                iconPickerEl.remove(); iconPickerEl = null;
                scheduleSavePage();
            });
            iconPickerEl.appendChild(opt);
        });
        var rect = pageIconBtn.getBoundingClientRect();
        iconPickerEl.style.top  = (rect.bottom + window.scrollY + 4) + 'px';
        iconPickerEl.style.left = rect.left + 'px';
        iconPickerEl.style.position = 'absolute';
        document.body.appendChild(iconPickerEl);
    });
    document.addEventListener('click', function () {
        if (iconPickerEl) { iconPickerEl.remove(); iconPickerEl = null; }
    });

    // Clicking "Add Lead" from pages view switches back to leads first
    var addBtn = document.getElementById('waAddBtn');
    if (addBtn) addBtn.addEventListener('click', function () {
        showView('leads');
        var modal = document.getElementById('waModal');
        var addDate = document.getElementById('add-date');
        if (addDate && activeDate) addDate.value = activeDate;
        if (modal) modal.hidden = false;
    }, true); // capture so this fires before the existing listener

    /* ── Init ────────────────────────────────────────────── */
    var hashMatch = (window.location.hash || '').match(/d=(\d{4}-\d{2}-\d{2})/);
    setDate(hashMatch ? hashMatch[1] : (activeDate || todayStr()));
})();
