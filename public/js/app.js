/**
 * Attach all event listeners to existing DOM elements.
 * Called once when the page loads.
 */
function addEventListeners() {
  // When an item checkbox is toggled, send an update request
  // Item UI removed: no item event listeners attached.


  const campaignsSearchForm = document.querySelector('#campaigns-search-form');
  if (campaignsSearchForm) {
    campaignsSearchForm.addEventListener('submit', sendCampaignsSearchRequest);
  }

  const clearSearchBtn = document.querySelector('#campaigns-search-clear');
  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', clearCampaignsSearch);
  }

  // Report buttons (campaign / comment)
  const reportButtons = document.querySelectorAll('.report-btn');
  if (reportButtons && reportButtons.length > 0) {
    reportButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        openReportModalFromElement(btn);
      });
    });
  }

  // If the report modal exists, wire form handlers (guard to avoid double-binding)
  const reportModal = document.getElementById('report-modal');
  if (reportModal) {
    const reportForm = reportModal.querySelector('#report-form');
    const reportCancel = reportModal.querySelector('#report-cancel');
    const reportClose = reportModal.querySelector('#report-close');
    
    if (reportCancel) {
      reportCancel.addEventListener('click', () => { reportModal.style.display = 'none'; });
    }
    
    if (reportClose) {
      reportClose.addEventListener('click', () => { reportModal.style.display = 'none'; });
    }

    if (reportForm && !reportForm.dataset.initialized) {
      reportForm.dataset.initialized = '1';

      reportForm.addEventListener('submit', async (evt) => {
        evt.preventDefault();

        const type = reportForm.querySelector('input[name="target_type"]').value;
        const id = reportForm.querySelector('input[name="target_id"]').value;
        const reasonEl = reportForm.querySelector('textarea[name="reason"]');
        const reason = reasonEl ? reasonEl.value.trim() : '';
        const feedback = reportModal.querySelector('#report-feedback');
        const submitBtn = reportForm.querySelector('#report-submit');

        if (!reason) {
          if (feedback) feedback.textContent = 'Please provide a reason.';
          return;
        }

        if (submitBtn) submitBtn.disabled = true;
        if (feedback) feedback.textContent = 'Sending...';

        // Use fetch with AbortController so requests can't hang forever
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 10000);

        try {
          const payload = encodeForAjax({ target_type: type, target_id: id, reason });
          const response = await fetch('/reports', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Content-Type': 'application/x-www-form-urlencoded',
              'Accept': 'application/json',
            },
            body: payload,
            signal: controller.signal,
          });

          clearTimeout(timeout);

          if (!response.ok) {
            // Show server error message if available, otherwise generic
            let text = 'Failed to send report.';
            try { const j = await response.json(); if (j && j.message) text = j.message; } catch (e) {}
            if (feedback) feedback.textContent = text;
            return;
          }

          // Attempt to parse JSON; if fails, show generic success
          try {
            const json = await response.json();
            if (feedback) feedback.textContent = 'Report sent. Thank you.';
          } catch (e) {
            if (feedback) feedback.textContent = 'Report sent.';
          }

          // Close modal after brief pause
          setTimeout(() => { reportModal.style.display = 'none'; if (feedback) feedback.textContent = ''; }, 900);

        } catch (err) {
          if (err.name === 'AbortError') {
            if (feedback) feedback.textContent = 'Request timed out. Please try again.';
          } else {
            if (feedback) feedback.textContent = 'Error sending report.';
          }
        } finally {
          if (submitBtn) submitBtn.disabled = false;
          clearTimeout(timeout);
        }
      });
    }
  }
}

/**
 * Open the report modal using data on a button/link element.
 */
function openReportModalFromElement(el) {
  const type = el.dataset.targetType || el.getAttribute('data-target-type');
  const id = el.dataset.targetId || el.getAttribute('data-target-id');
  const modal = document.getElementById('report-modal');
  if (!modal) {
    // If modal not present, fallback to login or full-page report
    if (el.tagName === 'A' && el.href) window.location = el.href;
    return;
  }

  const form = modal.querySelector('#report-form');
  if (form) {
    const typeInput = form.querySelector('input[name="target_type"]');
    const idInput = form.querySelector('input[name="target_id"]');
    if (typeInput) typeInput.value = type || '';
    if (idInput) idInput.value = id || '';
    const reason = form.querySelector('textarea[name="reason"]');
    if (reason) reason.value = '';
  }

  const feedback = modal.querySelector('#report-feedback');
  if (feedback) feedback.textContent = '';
  modal.style.display = 'block';
  // focus the textarea if possible
  const ta = modal.querySelector('#report-reason');
  if (ta) ta.focus();
}
  
/**
 * Encode a data object into URL-encoded form data.
 * Example: {a: 1, b: 2} → "a=1&b=2"
 */
function encodeForAjax(data) {
  return data ? new URLSearchParams(data).toString() : null;
}
  
/**
 * Send an AJAX request using the Fetch API.
 * Handles CSRF tokens and common headers.
 */
async function sendAjaxRequest(method, url, data, handler) {
  try {
    const response = await fetch(url, {
      method,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: data ? encodeForAjax(data) : null,
    });

    if (!response.ok) {
      // If the server returns an error, refresh the page as fallback
      window.location = '/';
      return;
    }

    // Parse JSON response and pass it to the handler
    const json = await response.json();
    handler(json);
  } catch (err) {
    console.error('Request failed:', err);
    window.location = '/';
  }
}

async function sendGetAjaxRequest(url, handler) {
  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      console.error('Search request failed');
      return;
    }

    const json = await response.json();
    handler(json);
  } catch (err) {
    console.error('Search request failed:', err);
  }
}

// Run setup when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  addEventListeners();
  initializeNotifications();
});

// NOTIFICATIONS

let notificationPollingInterval = null;

function initializeNotifications() {
  const bellBtn = document.getElementById('notification-bell-btn');
  const dropdown = document.getElementById('notification-dropdown');
  
  if (!bellBtn || !dropdown) return;
  
  // Fetch initial unread count
  fetchUnreadCount();
  
  // Toggle dropdown on bell click
  bellBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    const isVisible = dropdown.style.display === 'block';
    
    if (isVisible) {
      dropdown.style.display = 'none';
    } else {
      dropdown.style.display = 'block';
      loadNotifications();
    }
  });
  
  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && e.target !== bellBtn) {
      dropdown.style.display = 'none';
    }
  });
  
  // Poll for new notifications every 30 seconds
  notificationPollingInterval = setInterval(fetchUnreadCount, 30000);
}

async function fetchUnreadCount() {
  try {
    const response = await fetch('/api/notifications/unread-count', {
      headers: {
        'Accept': 'application/json',
      },
    });
    
    if (!response.ok) return;
    
    const data = await response.json();
    updateNotificationBadge(data.count);
  } catch (err) {
    console.error('Failed to fetch unread count:', err);
  }
}

function updateNotificationBadge(count) {
  const badge = document.getElementById('notification-badge');
  if (!badge) return;
  
  if (count > 0) {
    badge.textContent = count > 99 ? '99+' : count;
    badge.style.display = 'block';
  } else {
    badge.style.display = 'none';
  }
}

async function loadNotifications() {
  const listContainer = document.getElementById('notification-list');
  if (!listContainer) return;
  
  listContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: #888;">Loading...</div>';
  
  try {
    const response = await fetch('/notifications', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    
    if (!response.ok) {
      listContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: #888;">Failed to load</div>';
      return;
    }
    
    const html = await response.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const notifications = doc.querySelectorAll('.notification-item');
    
    if (notifications.length === 0) {
      listContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: #888;">No notifications</div>';
      return;
    }
    
    listContainer.innerHTML = '';
    let count = 0;
    
    notifications.forEach(notification => {
      if (count >= 5) return; // Show only 5 most recent
      
      const item = document.createElement('div');
      item.className = 'notification-item-preview';
      
      const isUnread = notification.classList.contains('unread');
      if (isUnread) item.classList.add('unread');
      
      const content = notification.querySelector('p');
      const time = notification.querySelector('small');
      
      // Extract notification ID and campaign link from data attributes
      const notificationId = notification.getAttribute('data-notification-id');
      const campaignLink = notification.getAttribute('data-campaign-link');
      
      if (campaignLink) {
        item.style.cursor = 'pointer';
        item.addEventListener('click', () => {
          markAsReadAndRedirect(notificationId, campaignLink);
        });
      }
      
      item.innerHTML = `
        <p>${content ? content.textContent : ''}</p>
        <small>${time ? time.textContent : ''}</small>
      `;
      
      listContainer.appendChild(item);
      count++;
    });
    
  } catch (err) {
    console.error('Failed to load notifications:', err);
    listContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: #888;">Error loading</div>';
  }
}

async function markAsReadAndRedirect(notificationId, url) {
  try {
    // Mark as read via AJAX
    await fetch(`/notifications/${notificationId}/read`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      }
    });
  } catch (err) {
    console.error('Failed to mark notification as read:', err);
  } finally {
    // Redirect regardless of success/failure
    window.location.href = url;
  }
}

// campaign search form

function sendCampaignsSearchRequest(event) {
  event.preventDefault();
  const searchInput = document.querySelector('#campaigns-search-input');
  const categorySelect = document.querySelector('#campaigns-category-filter');
  const searchTerm = searchInput.value.trim();
  const categoryId = categorySelect ? categorySelect.value : '';
  
  const params = new URLSearchParams();
  if (searchTerm) params.append('search', searchTerm);
  if (categoryId) params.append('category', categoryId);
  
  const url = `/api/campaigns/search${params.toString() ? '?' + params.toString() : ''}`;
  
  sendGetAjaxRequest(url, campaignsSearchHandler);
}

//clear search

function clearCampaignsSearch(event) {
  event.preventDefault();
  const searchInput = document.querySelector('#campaigns-search-input');
  const categorySelect = document.querySelector('#campaigns-category-filter');
  searchInput.value = '';
  if (categorySelect) categorySelect.value = '';
  document.querySelector('#campaigns-search-clear').style.display = 'none';
  sendGetAjaxRequest('/api/campaigns/search', campaignsSearchHandler);
}

function campaignsSearchHandler(data) {
  const grid = document.querySelector('#campaigns-grid');
  const clearBtn = document.querySelector('#campaigns-search-clear');
  const categorySelect = document.querySelector('#campaigns-category-filter');
  
  // show clear button if has search input or category selected
  const hasSearch = data.search && data.search.trim();
  const hasCategory = categorySelect && categorySelect.value;
  if (hasSearch || hasCategory) {
    clearBtn.style.display = 'flex';
  } else {
    clearBtn.style.display = 'none';
  }
  
  grid.innerHTML = '';
  
  if (data.campaigns && data.campaigns.length > 0) {
    data.campaigns.forEach(campaign => {
      const card = createCampaignCard(campaign);
      grid.appendChild(card);
    });
  } else {
    const noResults = document.createElement('div');
    noResults.className = 'no-campaigns-message';
    let message = 'No campaigns found.';
    if (data.search && data.search.trim()) {
      message = `No campaigns found matching "<strong>${escapeHtml(data.search)}</strong>".`;
    }
    noResults.innerHTML = `<p class="muted">${message}</p>`;
    grid.appendChild(noResults);
  }
}


function createCampaignCard(campaign) {
  const article = document.createElement('article');
  article.className = 'campaign-card';
  
  const endDate = campaign.end_date 
    ? new Date(campaign.end_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
    : 'No deadline';
  
  article.innerHTML = `
    <header>
      <span class="campaign-category">${escapeHtml(campaign.category.name)}</span>
      <span class="campaign-status status-${campaign.status}">${capitalizeFirst(campaign.status)}</span>
    </header>
    <a class="card-link" href="/campaigns/${campaign.campaign_id}" aria-label="View campaign ${escapeHtml(campaign.title)}"></a>
    <h3>
      ${escapeHtml(campaign.title)}
    </h3>
    <p class="campaign-card-summary">${escapeHtml(truncateText(campaign.description, 120))}</p>
    <p class="campaign-card-meta">
      by ${campaign.creator && campaign.creator.user_id ? ('<a href="/users/' + campaign.creator.user_id + '"><strong>' + escapeHtml(campaign.creator.name) + '</strong></a>') : ('<strong>' + escapeHtml((campaign.creator && campaign.creator.name) ? campaign.creator.name : 'Anonymous') + '</strong>')}
    </p>
    <div class="campaign-card-progress">
      <div class="progress-track">
        <div class="progress-fill" style="width: ${campaign.progress}%"></div>
      </div>
      <div class="progress-numbers">
        <span>€${formatNumber(campaign.current_amount)} raised</span>
        <span>${Math.round(campaign.progress)}%</span>
      </div>
    </div>
    <ul class="campaign-card-stats">
      <li>
        <span class="stat-label">Goal</span>
        <span class="stat-value">€${formatNumber(campaign.goal_amount)}</span>
      </li>
      <li>
        <span class="stat-label">Ends</span>
        <span class="stat-value">${endDate}</span>
      </li>
      <li>
        <span class="stat-label">Popularity</span>
        <span class="stat-value">${campaign.popularity}</span>
      </li>
    </ul>
  `;
  
  return article;
}


function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}


function capitalizeFirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}


function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
}


function formatNumber(num) {
  return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}
  