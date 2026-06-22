
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


    var toastWrap = null;
    function toast(msg, type) {
        if (!toastWrap) {
            toastWrap = document.createElement('div');
            toastWrap.className = 'wa-toast-wrap';
            document.body.appendChild(toastWrap);
        }
        var t = document.createElement('div');
        t.className = 'wa-toast' + (type === 'error' ? ' wa-toast-error' : '');
        t.textContent = msg;
        toastWrap.appendChild(t);
        setTimeout(function () {
            t.classList.add('is-out');
            setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 260);
        }, type === 'error' ? 4200 : 2400);
    }


    function confirmDialog(messageHtml) {
        return new Promise(function (resolve) {
            var overlay = document.createElement('div');
            overlay.className = 'wa-confirm-overlay';
            overlay.innerHTML =
                '<div class="wa-confirm-card" role="dialog" aria-modal="true">'
              + '<p>' + messageHtml + '</p>'
              + '<div class="wa-confirm-actions">'
              + '<button type="button" class="btn btn-secondary wa-confirm-no">Cancel</button>'
              + '<button type="button" class="wa-confirm-yes">Delete</button>'
              + '</div></div>';
            document.body.appendChild(overlay);
            function close(val) {
                if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                document.removeEventListener('keydown', onKey);
                resolve(val);
            }
            function onKey(e) { if (e.key === 'Escape') close(false); }
            overlay.querySelector('.wa-confirm-yes').addEventListener('click', function () { close(true); });
            overlay.querySelector('.wa-confirm-no').addEventListener('click', function () { close(false); });
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(false); });
            document.addEventListener('keydown', onKey);
            overlay.querySelector('.wa-confirm-yes').focus();
        });
    }


    function rowMatches(row) {
        if (!allDates && activeDate) {
            var onDate     = row.getAttribute('data-date') === activeDate;
            var onCallback = row.getAttribute('data-callback') === activeDate;
            if (!onDate && !onCallback) return false;
        }

        var status = row.getAttribute('data-status');
        if (activeFilter === 'tocall'     && status !== 'new')        return false;
        if (activeFilter === 'won'        && status !== 'won')        return false;
        if (activeFilter === 'interested' && status !== 'interested') return false;
        if (activeFilter === 'quoted'     && status !== 'quoted')     return false;
        if (activeFilter === 'callbacks') {
            var cbDue = row.getAttribute('data-callback');
            if (!cbDue || status === 'won' || status === 'dead') return false;
            if (allDates) { if (cbDue > todayStr()) return false; }      // due today or overdue
            else if (activeDate && cbDue !== activeDate) return false;   // due on the viewed day
        }

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
        var s = { tocall: 0, interested: 0, quoted: 0, won: 0, callbacks: 0, total: 0 };
        var today = todayStr();
        Array.prototype.forEach.call(body.querySelectorAll('.wa-row'), function (row) {
            var personOk = activePerson === 'everyone' || row.getAttribute('data-assigned') === String(activePerson);
            if (!personOk) return;
            var st = row.getAttribute('data-status');
            var cb = row.getAttribute('data-callback');
            var outstanding = st !== 'won' && st !== 'dead';

            // Callbacks due — scheduled for the viewed day, or all outstanding past/today in "show all"
            if (cb && outstanding) {
                if (allDates) { if (cb <= today) s.callbacks++; }
                else if (cb === activeDate) s.callbacks++;
            }

            var onDate = allDates || !activeDate || row.getAttribute('data-date') === activeDate;
            if (!onDate) return;
            c.all++; s.total++;
            if (st === 'new')        { c.tocall++; s.tocall++; }
            if (st === 'interested') s.interested++;
            if (st === 'quoted')     s.quoted++;
            if (st === 'won')        { c.won++; s.won++; }
        });
        tabs.forEach(function (tab) {
            var span = tab.querySelector('span');
            var key = tab.getAttribute('data-filter');
            if (span && c[key] !== undefined) span.textContent = c[key];
        });
        updateStats(s);
        markOverdue(today);
    }

    function updateStats(s) {
        var statsEl = document.getElementById('waStats');
        if (!statsEl) return;
        var rate = s.total ? Math.round((s.won / s.total) * 100) : 0;
        var map = { tocall: s.tocall, interested: s.interested, quoted: s.quoted, won: s.won, callbacks: s.callbacks, conversion: rate + '%' };
        Object.keys(map).forEach(function (k) {
            var el = statsEl.querySelector('[data-stat="' + k + '"]');
            if (el) el.textContent = map[k];
        });
        var cbCard = statsEl.querySelector('[data-tone="callback"]');
        if (cbCard) cbCard.classList.toggle('has-due', s.callbacks > 0);
    }

    // Flag rows whose callback date has passed and aren't won/dead.
    function markOverdue(today) {
        today = today || todayStr();
        Array.prototype.forEach.call(body.querySelectorAll('.wa-row'), function (row) {
            var cb = row.getAttribute('data-callback');
            var st = row.getAttribute('data-status');
            row.classList.toggle('is-overdue', !!(cb && cb < today && st !== 'won' && st !== 'dead'));
        });
    }

    var statCards = Array.prototype.slice.call(document.querySelectorAll('.wa-stat-card[data-filter]'));


    function setFilter(key) {
        activeFilter = key;
        tabs.forEach(function (t) { t.classList.toggle('is-active', t.getAttribute('data-filter') === key); });
        statCards.forEach(function (c) {
            var on = c.getAttribute('data-filter') === key;
            c.classList.toggle('is-filter-active', on);
            c.setAttribute('aria-pressed', String(on));
        });
        applyFilter();
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () { setFilter(tab.getAttribute('data-filter')); });
    });

    statCards.forEach(function (card) {
        function toggle() {
            var key = card.getAttribute('data-filter');
            setFilter(activeFilter === key ? 'all' : key); // click the active card again to clear
        }
        card.addEventListener('click', toggle);
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (peopleSelect) peopleSelect.addEventListener('change', function () {
        activePerson = peopleSelect.value;
        applyFilter();
        recomputeCounts();
    });


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


    function saveField(control) {
        var row = control.closest('.wa-row') || control.closest('.wa-detail');
        var id = row.getAttribute('data-id');
        var field = control.getAttribute('data-field');
        if (!field) return;

        var value = control.type === 'checkbox' ? (control.checked ? '1' : '0') : control.value;

        post({ action: 'dpowered_update_lead', lead_id: id, field: field, value: value })
            .then(function (res) {
                if (!res || !res.success) {
                    toast((res && res.data && res.data.msg) || 'Could not save — try again.', 'error');
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
            .catch(function () { toast('Network error — change not saved.', 'error'); });
    }

    body.addEventListener('change', function (e) {
        var c = e.target;
        if (c.matches('select[data-field], input[type="checkbox"][data-field]')) saveField(c);
    });
    body.addEventListener('blur', function (e) {
        var c = e.target;
        if (c.matches('input[data-field]:not([type="checkbox"]), textarea[data-field]')) saveField(c);
    }, true);


    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.wa-private-btn');
        if (!btn) return;
        var row        = btn.closest('.wa-row');
        var id         = row.getAttribute('data-id');
        var isPrivate  = row.getAttribute('data-private') === '1';
        var newPrivate = isPrivate ? 0 : 1;

        post({ action: 'dpowered_update_lead', lead_id: id, field: 'private', value: newPrivate })
            .then(function (res) {
                if (!res || !res.success) { toast('Could not update.', 'error'); return; }
                row.setAttribute('data-private', newPrivate);
                row.classList.toggle('is-private-lead', !!newPrivate);
                btn.classList.toggle('is-private', !!newPrivate);
                btn.setAttribute('title', newPrivate ? 'Private — click to share with team' : 'Shared — click to make private');
            });
    });


    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.wa-delete');
        if (!btn) return;
        var row = btn.closest('.wa-row');
        var id = row.getAttribute('data-id');
        var name = (row.querySelector('.wa-business') || {}).value || 'this lead';
        confirmDialog('Delete <strong>' + escHtml(name) + '</strong>? It moves to the trash.').then(function (ok) {
            if (!ok) return;
            post({ action: 'dpowered_delete_lead', lead_id: id }).then(function (res) {
                if (!res || !res.success) { toast('Could not delete.', 'error'); return; }
                var detail = body.querySelector('.wa-detail[data-id="' + id + '"]');
                row.remove();
                if (detail) detail.remove();
                recomputeCounts();
                applyFilter();
                toast('Lead deleted.');
            });
        });
    });


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
                if (!res || !res.success) { toast('Could not save.', 'error'); return; }
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


    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.wa-meeting-done-btn');
        if (!btn) return;
        var card = btn.closest('.wa-meeting-card');
        var id   = card && card.getAttribute('data-id');
        if (!id) return;
        post({ action: 'dpowered_update_meeting', meeting_id: id, field: 'status', value: 'done' })
            .then(function(res) {
                if (!res || !res.success) { toast('Could not update.', 'error'); return; }
                window.location.reload();
            });
    });


    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.wa-meeting-delete-btn');
        if (!btn) return;
        var card = btn.closest('.wa-meeting-card');
        var id   = card && card.getAttribute('data-id');
        var name = card && card.querySelector('.wa-meeting-business') ? card.querySelector('.wa-meeting-business').value : 'this meeting';
        if (!id) return;
        confirmDialog('Delete the meeting with <strong>' + escHtml(name || 'this client') + '</strong>?').then(function (ok) {
            if (!ok) return;
            post({ action: 'dpowered_delete_meeting', meeting_id: id }).then(function(res) {
                if (!res || !res.success) { toast('Could not delete.', 'error'); return; }
                card.remove();
                toast('Meeting deleted.');
            });
        });
    });


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


    var pagesData        = Array.isArray(cfg.pages) ? cfg.pages : [];
    var activePage       = null;
    var pagesLoaded      = false;
    var pageTimer        = null;
    var iconPickerEl     = null;

    var newPageBtn       = document.getElementById('waNewPage');
    var newPageEmptyBtn  = document.getElementById('waNewPageEmpty');
    var pagesList        = document.getElementById('waPagesList');
    var pagesSearch      = document.getElementById('waPagesSearch');
    var pageEmptyState   = document.getElementById('waPageEmptyState');
    var pageEditorWrap   = document.getElementById('waPageEditorWrap');
    var pageIconBtn      = document.getElementById('waPageIconBtn');
    var pageTitleInput   = document.getElementById('waPageTitleInput');
    var pagePrivacyBtn   = document.getElementById('waPagePrivacyBtn');
    var privacyLabel     = document.getElementById('waPrivacyLabel');
    var pageEditedMeta   = document.getElementById('waPageEditedMeta');
    var pageSavedStatus  = document.getElementById('waPageSavedStatus');
    var pageDeleteBtn    = document.getElementById('waPageDeleteBtn');
    var editorContent    = document.getElementById('waEditorContent');
    var editorToolbar    = document.getElementById('waEditorToolbar');
    var pagesSearchTerm  = '';

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
        var term = pagesSearchTerm;
        var shown = pagesData.filter(function (p) {
            return !term || (p.title || 'Untitled').toLowerCase().indexOf(term) !== -1;
        });
        if (!shown.length) {
            pagesList.innerHTML = '<p class="wa-pages-list-empty">No pages match “' + escHtml(term) + '”.</p>';
            return;
        }
        shown.forEach(function (p) {
            var btn = document.createElement('button');
            btn.className = 'wa-page-item' + (activePage && activePage.id === p.id ? ' is-active' : '');
            btn.setAttribute('data-page-id', p.id);
            var sub = '';
            if (p.author_avatar || p.updated_human) {
                sub = '<span class="wa-page-item-sub">'
                    + (p.author_avatar ? avatarHtml(p.author_avatar, 14, 'wa-page-item-av') : '')
                    + escHtml(p.updated_human ? 'Edited ' + p.updated_human : (p.author_name || ''))
                    + '</span>';
            }
            btn.innerHTML = '<span class="wa-page-item-icon">' + escHtml(p.icon || '📄') + '</span>'
                + '<span class="wa-page-item-body">'
                + '<span class="wa-page-item-title">' + escHtml(p.title || 'Untitled')
                + (p.private ? ' <span class="wa-page-item-badge">🔒</span>' : '') + '</span>'
                + sub + '</span>';
            btn.addEventListener('click', function () { openPage(p); });
            pagesList.appendChild(btn);
        });
    }

    function renderEditedMeta(page) {
        if (!pageEditedMeta) return;
        if (!page || (!page.author_name && !page.updated_human)) { pageEditedMeta.hidden = true; pageEditedMeta.innerHTML = ''; return; }
        pageEditedMeta.hidden = false;
        pageEditedMeta.innerHTML = (page.author_avatar ? avatarHtml(page.author_avatar, 16, 'wa-page-edited-av') : '')
            + 'Edited by ' + escHtml(page.author_name || 'someone')
            + (page.updated_human ? ' · ' + escHtml(page.updated_human) : '');
    }

    if (pagesSearch) pagesSearch.addEventListener('input', function () {
        pagesSearchTerm = pagesSearch.value.trim().toLowerCase();
        renderPageList();
    });

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
        renderEditedMeta(page);
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
        renderEditedMeta(null);
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
            renderEditedMeta(page);
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
        var pageId = activePage.id;
        confirmDialog('Delete <strong>' + escHtml(activePage.title || 'Untitled') + '</strong>? This cannot be undone.').then(function (ok) {
        if (!ok) return;
        post({ action: 'dpowered_delete_page', page_id: pageId }).then(function (res) {
            if (!res || !res.success) { toast('Could not delete.', 'error'); return; }
            var idx = -1;
            pagesData.forEach(function (p, i) { if (p.id === pageId) idx = i; });
            if (idx >= 0) pagesData.splice(idx, 1);
            activePage = null;
            renderPageList();
            if (pagesData.length) {
                openPage(pagesData[0]);
            } else {
                if (pageEditorWrap) pageEditorWrap.hidden = true;
                if (pageEmptyState) pageEmptyState.hidden = false;
            }
            toast('Page deleted.');
        });
        });
    });

    // Formatting toolbar
    if (editorToolbar) editorToolbar.addEventListener('mousedown', function (e) {
        var btn = e.target.closest('.wa-fmt');
        if (!btn) return;
        e.preventDefault();
        var cmd = btn.getAttribute('data-cmd');
        var val = btn.getAttribute('data-val') || null;

        if (cmd === 'createLink') {
            var url = window.prompt('Link URL:', 'https://');
            if (!url) return;
            if (!/^https?:\/\//i.test(url) && !/^mailto:/i.test(url)) url = 'https://' + url;
            document.execCommand('createLink', false, url);
        } else if (cmd === 'checklist') {
            // Class-based tasks (survive wp_kses_post — no <input>, no data-*).
            document.execCommand('insertHTML', false,
                '<ul class="wa-checklist"><li class="wa-task">New task</li></ul><p><br></p>');
        } else {
            document.execCommand(cmd, false, val);
        }
        if (editorContent) editorContent.focus();
        scheduleSavePage();
    });

    // Toggle a checklist item done by clicking its checkbox glyph (left ~24px).
    if (editorContent) editorContent.addEventListener('click', function (e) {
        var task = e.target.closest('.wa-task');
        if (!task || !editorContent.contains(task)) return;
        if (e.offsetX > 24) return; // only the box zone toggles; rest edits text
        task.classList.toggle('wa-task-done');
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


    var STATUS_ORDER = { new: 0, called: 1, interested: 2, quoted: 3, won: 4, dead: 5 };
    var sortKey = null, sortDir = 1;
    function sortValue(row, key) {
        if (key === 'business') return ((row.querySelector('.wa-business') || {}).value || '').toLowerCase();
        if (key === 'status') { var st = row.getAttribute('data-status'); return STATUS_ORDER[st] !== undefined ? STATUS_ORDER[st] : 99; }
        if (key === 'assigned') {
            var sel = row.querySelector('select[data-field="assigned"]');
            return sel ? ((sel.options[sel.selectedIndex] || {}).text || '').toLowerCase() : '';
        }
        return '';
    }
    function applySort() {
        if (!sortKey) return;
        var rows = Array.prototype.slice.call(body.querySelectorAll('.wa-row'));
        rows.sort(function (a, b) {
            var va = sortValue(a, sortKey), vb = sortValue(b, sortKey);
            if (va < vb) return -sortDir;
            if (va > vb) return sortDir;
            return 0;
        });
        rows.forEach(function (row) {
            var detail = body.querySelector('.wa-detail[data-id="' + row.getAttribute('data-id') + '"]');
            body.appendChild(row);
            if (detail) body.appendChild(detail);
        });
    }
    Array.prototype.forEach.call(document.querySelectorAll('.wa-th-sort'), function (th) {
        th.addEventListener('click', function () {
            var key = th.getAttribute('data-sort');
            if (sortKey === key) sortDir = -sortDir; else { sortKey = key; sortDir = 1; }
            document.querySelectorAll('.wa-th-sort').forEach(function (o) { o.classList.remove('sort-asc', 'sort-desc'); });
            th.classList.add(sortDir === 1 ? 'sort-asc' : 'sort-desc');
            applySort();
        });
    });


    var exportBtn = document.getElementById('waExportBtn');
    function csvCell(v) { v = String(v == null ? '' : v); return '"' + v.replace(/"/g, '""') + '"'; }
    function exportCsv() {
        var headers = ['Business', 'Contact', 'Phone', 'Status', 'Called', 'Offered', 'Assigned', 'Sheet date', 'Callback', 'Email', 'Notes'];
        var lines = [headers.map(csvCell).join(',')];
        var count = 0;
        Array.prototype.forEach.call(body.querySelectorAll('.wa-row'), function (row) {
            if (row.style.display === 'none') return;
            var id = row.getAttribute('data-id');
            var detail = body.querySelector('.wa-detail[data-id="' + id + '"]');
            function val(sel, ctx) { var el = (ctx || row).querySelector(sel); return el ? el.value : ''; }
            function selText(sel, ctx) { var el = (ctx || row).querySelector(sel); return el ? ((el.options[el.selectedIndex] || {}).text || '') : ''; }
            function chk(sel) { var el = row.querySelector(sel); return el && el.checked ? 'Yes' : 'No'; }
            lines.push([
                val('.wa-business'),
                val('[data-field="contact"]'),
                val('[data-field="phone"]'),
                selText('select[data-field="status"]'),
                chk('[data-field="called"]'),
                chk('[data-field="offered"]'),
                selText('select[data-field="assigned"]'),
                row.getAttribute('data-date') || '',
                row.getAttribute('data-callback') || '',
                detail ? val('[data-field="email"]', detail) : '',
                detail ? val('[data-field="notes"]', detail) : ''
            ].map(csvCell).join(','));
            count++;
        });
        if (!count) { toast('No leads in view to export.', 'error'); return; }
        var blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'dpowered-leads-' + (allDates ? 'all' : (activeDate || todayStr())) + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        toast(count + ' lead' + (count === 1 ? '' : 's') + ' exported.');
    }
    if (exportBtn) exportBtn.addEventListener('click', exportCsv);


    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.wa-call-btn');
        if (!btn) return;
        var cell = btn.closest('.wa-phone-cell');
        var input = cell && cell.querySelector('input[data-field="phone"]');
        var num = input ? input.value.trim() : '';
        if (!num) { toast('No phone number for this lead.', 'error'); return; }
        window.location.href = 'tel:' + num.replace(/[^\d+]/g, '');
    });
    // Dim the call button when the number is empty.
    function refreshCallButtons() {
        Array.prototype.forEach.call(body.querySelectorAll('.wa-phone-cell'), function (cell) {
            var input = cell.querySelector('input[data-field="phone"]');
            var btn = cell.querySelector('.wa-call-btn');
            if (input && btn) btn.classList.toggle('is-empty', !input.value.trim());
        });
    }
    body.addEventListener('input', function (e) {
        if (e.target.matches('input[data-field="phone"]')) {
            var btn = e.target.closest('.wa-phone-cell');
            btn = btn && btn.querySelector('.wa-call-btn');
            if (btn) btn.classList.toggle('is-empty', !e.target.value.trim());
        }
    });


    function typingInField(el) {
        if (!el) return false;
        var tag = el.tagName;
        return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || el.isContentEditable;
    }
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        if (typingInField(e.target)) return;
        if (leadsView && leadsView.hidden) return;       // only on the leads view
        if (modal && !modal.hidden) return;               // not while the add modal is open
        switch (e.key) {
            case 'n': case 'N': e.preventDefault(); openModal(); break;
            case '/':           e.preventDefault(); if (searchInput) searchInput.focus(); break;
            case 'ArrowLeft':   setDate(shiftDate(activeDate, -1)); break;
            case 'ArrowRight':  setDate(shiftDate(activeDate, 1)); break;
            case 't': case 'T': setDate(todayStr()); break;
        }
    });


    function avatarHtml(a, size, cls) {
        cls = cls || '';
        var s = 'width:' + size + 'px;height:' + size + 'px;font-size:' + Math.round(size * 0.4) + 'px;';
        if (a && a.url) {
            return '<span class="wa-avatar ' + cls + '" style="' + s + '" title="' + escHtml(a.name) + '">'
                 + '<img src="' + a.url + '" alt="' + escHtml(a.name) + '"></span>';
        }
        s += 'background:' + ((a && a.color) || '#888') + ';';
        return '<span class="wa-avatar wa-avatar-initials ' + cls + '" style="' + s + '" title="' + escHtml(a ? a.name : '') + '">'
             + escHtml(a ? a.initials : '?') + '</span>';
    }


    var profileBtn  = document.getElementById('waProfileBtn');
    var avatarInput = document.getElementById('waAvatarInput');
    if (profileBtn && avatarInput) {
        profileBtn.addEventListener('click', function () { avatarInput.click(); });
        avatarInput.addEventListener('change', function () {
            var file = avatarInput.files && avatarInput.files[0];
            if (!file) return;
            if (file.size > 4 * 1024 * 1024) { toast('Image must be under 4MB.', 'error'); avatarInput.value = ''; return; }
            var data = new FormData();
            data.append('action', 'dpowered_upload_avatar');
            data.append('nonce', cfg.nonce);
            data.append('avatar', file);
            profileBtn.classList.add('is-uploading');
            fetch(cfg.ajaxurl, { method: 'POST', credentials: 'same-origin', body: data })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    profileBtn.classList.remove('is-uploading');
                    if (!res || !res.success) { toast((res && res.data && res.data.msg) || 'Upload failed.', 'error'); return; }
                    var holder = profileBtn.querySelector('.wa-avatar');
                    if (holder) holder.outerHTML = avatarHtml(res.data.avatar, 40, 'wa-profile-avatar');
                    toast('Profile photo updated.');
                })
                .catch(function () { profileBtn.classList.remove('is-uploading'); toast('Upload failed.', 'error'); });
            avatarInput.value = '';
        });
    }


    (function () {
        var peopleList  = document.getElementById('waPeopleList');
        var onlineCount = document.getElementById('waOnlineCount');

        function ping() { if (!document.hidden) post({ action: 'dpowered_presence_ping' }); }
        ping();
        setInterval(ping, 15000);
        document.addEventListener('visibilitychange', function () { if (!document.hidden) { ping(); pollPresence(); } });

        function renderPeople(team) {
            if (!peopleList) return;
            var online = 0;
            peopleList.innerHTML = team.map(function (p) {
                if (p.online) online++;
                var you = p.uid === cfg.currentUser ? ' <span class="wa-person-you">you</span>' : '';
                var status = p.online ? 'Online now' : (p.last_human ? 'Active ' + escHtml(p.last_human) : 'Offline');
                return '<div class="wa-person' + (p.online ? ' is-online' : '') + '" data-uid="' + p.uid + '">'
                     + '<span class="wa-person-avatar-wrap">'
                     + avatarHtml(p, 36, 'wa-person-avatar')
                     + '<span class="wa-person-dot" style="--pc:' + p.color + '"></span></span>'
                     + '<span class="wa-person-meta">'
                     + '<span class="wa-person-name">' + escHtml(p.name) + you + '</span>'
                     + '<span class="wa-person-status">' + status + '</span></span></div>';
            }).join('');
            if (onlineCount) onlineCount.textContent = online + ' online';
        }

        function pollPresence() {
            if (document.hidden) return;
            post({ action: 'dpowered_presence_poll' }).then(function (res) {
                if (res && res.success) renderPeople(res.data.team || []);
            });
        }
        pollPresence();
        setInterval(pollPresence, 5000);
    })();


    (function () {
        var lastSync = 0; // seeded from server on first poll → avoids clock skew

        function rowFocused(el) {
            var ae = document.activeElement;
            return el && ae && el.contains(ae);
        }
        function flashRemote(row) {
            row.classList.remove('wa-row-remote');
            void row.offsetWidth;
            row.classList.add('wa-row-remote');
        }

        function patchRow(item) {
            var existing       = body.querySelector('.wa-row[data-id="' + item.id + '"]');
            var existingDetail = body.querySelector('.wa-detail[data-id="' + item.id + '"]');
            var isMine = item.editor === cfg.currentUser;

            // Never clobber a field the user is actively editing.
            if (rowFocused(existing) || rowFocused(existingDetail)) return;

            var tmp = document.createElement('tbody');
            tmp.innerHTML = (item.html || '').trim();
            var newRow    = tmp.querySelector('.wa-row');
            var newDetail = tmp.querySelector('.wa-detail');
            if (!newRow) return;

            if (existing) {
                var wasOpen = existingDetail && !existingDetail.hidden;
                if (wasOpen && newDetail) {
                    newDetail.hidden = false;
                    newDetail.style.display = '';
                    var ex = newRow.querySelector('.wa-expand');
                    if (ex) ex.classList.add('is-open');
                }
                existing.replaceWith(newRow);
                if (existingDetail) { existingDetail.replaceWith(newDetail || document.createComment('')); }
                else if (newDetail) { newRow.after(newDetail); }
                if (!isMine) { flashRemote(newRow); toast((item.editor_name || 'A teammate') + ' updated ' + (item.business || 'a lead')); }
            } else {
                body.appendChild(newRow);
                if (newDetail) body.appendChild(newDetail);
                if (!isMine) { flashRemote(newRow); toast((item.editor_name || 'A teammate') + ' added ' + (item.business || 'a lead')); }
            }
        }

        function sync() {
            if (document.hidden) return;
            post({ action: 'dpowered_leads_changes', since: lastSync }).then(function (res) {
                if (!res || !res.success) return;
                var d = res.data;
                var firstRun = lastSync === 0;
                lastSync = d.now;

                var changed = d.rows || [];
                changed.forEach(patchRow);

                // Remove leads that vanished (deleted, or turned private for me).
                var idSet = {};
                (d.ids || []).forEach(function (id) { idSet[id] = true; });
                var removed = 0;
                Array.prototype.forEach.call(body.querySelectorAll('.wa-row'), function (row) {
                    var id = row.getAttribute('data-id');
                    if (!idSet[id]) {
                        var det = body.querySelector('.wa-detail[data-id="' + id + '"]');
                        row.remove();
                        if (det) det.remove();
                        removed++;
                    }
                });

                if (!firstRun && (changed.length || removed)) {
                    recomputeCounts();
                    applyFilter();
                    refreshCallButtons();
                    if (sortKey) applySort();
                }
            });
        }
        setInterval(sync, 3000);
    })();


    (function () {
        var CURSOR_COLORS = [
            '#22c55e', '#f59e0b', '#ef4444', '#a855f7', '#06b6d4',
            '#f97316', '#ec4899', '#14b8a6', '#84cc16', '#fb923c'
        ];
        function cursorColor(uid) { return CURSOR_COLORS[uid % CURSOR_COLORS.length]; }

        // Anchor cursors to the work-area content box so a position maps to the
        // SAME content on every screen (independent of window size + scroll),
        // not a meaningless fraction of each person's viewport.
        function anchor() {
            var el = document.querySelector('.work-area-section .container')
                  || document.querySelector('.wa-workspace-layout')
                  || document.body;
            var r = el.getBoundingClientRect();
            return { left: r.left + window.scrollX, top: r.top + window.scrollY, width: r.width || 1, height: r.height || 1 };
        }

        var activeCursors = {}; // uid → { el, tx, ty, cx, cy, raf }
        var sendX = null, sendY = null, lastSentX = null, lastSentY = null;

        // Pointer tracked as content-relative coords: x = fraction of content
        // width, y = pixels down from content top (document space).
        document.addEventListener('mousemove', function (e) {
            var a = anchor();
            var rx = (e.pageX - a.left) / a.width;
            sendX = rx < 0 ? 0 : rx > 1 ? 1 : rx;
            sendY = e.pageY - a.top;
        });

        function send() {
            post({ action: 'dpowered_cursor_push', x: sendX, y: sendY });
            lastSentX = sendX; lastSentY = sendY;
        }
        // Steady, bounded send — only when the pointer actually moved.
        setInterval(function () {
            if (document.hidden || sendX === null) return;
            if (sendX === lastSentX && sendY === lastSentY) return;
            send();
        }, 80);
        // Heartbeat keeps presence alive while the pointer is still.
        setInterval(function () { if (!document.hidden && sendX !== null) send(); }, 7000);

        function getOrCreate(uid, name) {
            if (activeCursors[uid]) return activeCursors[uid];
            var el = document.createElement('div');
            el.className = 'wa-remote-cursor';
            el.style.setProperty('--wc', cursorColor(uid));
            el.innerHTML = '<span class="wa-rc-label">' + escHtml(name) + '</span>'
                         + '<span class="wa-rc-dot"></span>';
            document.body.appendChild(el);
            var obj = { el: el, tx: 0, ty: 0, cx: null, cy: null, raf: null };
            activeCursors[uid] = obj;
            (function animLoop() {
                if (obj.cx === null) { obj.cx = obj.tx; obj.cy = obj.ty; } // first frame: snap, don't slide from 0,0
                obj.cx += (obj.tx - obj.cx) * 0.3;   // snappier follow
                obj.cy += (obj.ty - obj.cy) * 0.3;
                el.style.left = obj.cx + 'px';
                el.style.top  = obj.cy + 'px';
                obj.raf = requestAnimationFrame(animLoop);
            })();
            return obj;
        }

        function removeCursor(uid) {
            var c = activeCursors[uid];
            if (!c) return;
            cancelAnimationFrame(c.raf);
            if (c.el.parentNode) c.el.parentNode.removeChild(c.el);
            delete activeCursors[uid];
        }

        var onlineBar = null;
        function ensureOnlineBar() {
            if (onlineBar) return onlineBar;
            onlineBar = document.createElement('span');
            onlineBar.className = 'wa-online-bar';
            onlineBar.hidden = true;
            var barUser = document.querySelector('.wa-bar-user');
            if (barUser) barUser.appendChild(onlineBar);
            return onlineBar;
        }

        function updateOnlineBar(cursors) {
            var bar = ensureOnlineBar();
            if (!cursors.length) { bar.hidden = true; return; }
            bar.hidden = false;
            var html = cursors.map(function (c) {
                var col = cursorColor(c.uid);
                var first = (c.name || '').split(' ')[0];
                return '<span class="wa-online-pip" style="background:' + col + '"></span>'
                     + escHtml(first);
            }).join(' · ');
            bar.innerHTML = html;
        }

        function poll() {
            if (document.hidden) {
                Object.keys(activeCursors).forEach(removeCursor);
                return;
            }
            post({ action: 'dpowered_cursor_poll' }).then(function (res) {
                if (!res || !res.success) return;
                var list = res.data.cursors || [];
                var a = anchor();
                var seen = {};
                list.forEach(function (c) {
                    seen[c.uid] = true;
                    var obj = getOrCreate(c.uid, c.name);
                    var lbl = obj.el.querySelector('.wa-rc-label');
                    if (lbl && lbl.textContent !== c.name) lbl.textContent = c.name;
                    // Map content-relative coords back into THIS screen's document px.
                    var cy = c.y < 0 ? 0 : c.y > a.height ? a.height : c.y; // clamp so it can't add phantom scroll
                    obj.tx = a.left + c.x * a.width;
                    obj.ty = a.top + cy;
                });
                Object.keys(activeCursors).forEach(function (uid) {
                    if (!seen[uid]) removeCursor(uid);
                });
                updateOnlineBar(list);
            });
        }

        setInterval(poll, 150);   // faster refresh
    })();


    var hashMatch = (window.location.hash || '').match(/d=(\d{4}-\d{2}-\d{2})/);
    setDate(hashMatch ? hashMatch[1] : (activeDate || todayStr()));
    refreshCallButtons();
})();
