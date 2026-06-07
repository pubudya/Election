// Voting page logic
let timer;
let timeLeft = 5 * 60; // 5 minutes in seconds
const timerDisplay = document.getElementById('timeRemaining');
const timeProgress = document.getElementById('timeProgress');
// const extendBtn = document.getElementById('extendTimeBtn'); // Button removed from HTML
function updateTimer() {
    if (!timerDisplay) return;
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    if (timeProgress) {
        const pct = Math.max(0, Math.min(100, (timeLeft / (5 * 60)) * 100));
        timeProgress.style.width = `${pct}%`;
        timeProgress.classList.toggle('bg-danger', pct <= 20);
        timeProgress.classList.toggle('bg-warning', pct > 20 && pct <= 60);
        timeProgress.classList.toggle('bg-success', pct > 60);
    }
    if (timeLeft <= 0) {
        clearInterval(timer);
        alert('Time expired! Your voting session has ended.');
        window.location.href = 'login.php?timeout=1';
    }
    timeLeft--;
}
if (timerDisplay) {
    timer = setInterval(updateTimer, 1000);
    updateTimer();
}
// Candidate preference selection logic
const prefs = {1: null, 2: null, 3: null};
const candidateIdToName = (() => {
    const map = {};
    document.querySelectorAll('.candidate-card').forEach(card => {
        const id = card.getAttribute('data-candidate-id');
        const nameEl = card.querySelector('.card-title');
        if (id && nameEl) map[id] = nameEl.textContent.trim();
    });
    return map;
})();
const selectedPrefsDisplay = document.getElementById('selectedPrefsDisplay');
const candidateCards = document.querySelectorAll('.candidate-card');

function getNextAvailablePref(){
    for (let i = 1; i <= 3; i++) if (!prefs[i]) return i;
    return null;
}

function updateHiddenInputs(){
    const firstEl = document.getElementById('firstPref');
    const secondEl = document.getElementById('secondPref');
    const thirdEl = document.getElementById('thirdPref');
    if (firstEl) firstEl.value = prefs[1] || '';
    if (secondEl) secondEl.value = prefs[2] || '';
    if (thirdEl) thirdEl.value = prefs[3] || '';
}

function refreshEnablement(){
    const hasFirst = !!prefs[1];
    const hasSecond = !!prefs[2];
    
    // Disable 2nd preference buttons if no 1st preference is selected
    document.querySelectorAll('.preference-btn[data-pref="2"]').forEach(btn => { 
        btn.disabled = !hasFirst;
        if (!hasFirst) {
            btn.title = 'Please select 1st preference first';
        } else {
            btn.title = 'Select 2nd preference';
        }
    });
    
    // Disable 3rd preference buttons if no 2nd preference is selected
    document.querySelectorAll('.preference-btn[data-pref="3"]').forEach(btn => { 
        btn.disabled = !hasSecond;
        if (!hasSecond) {
            btn.title = 'Please select 2nd preference first';
        } else {
            btn.title = 'Select 3rd preference';
        }
    });
    
    // Enable 1st preference buttons always
    document.querySelectorAll('.preference-btn[data-pref="1"]').forEach(btn => { 
        btn.disabled = false;
        btn.title = 'Select 1st preference';
    });
}

function toggleBtnStyle(btn, active){
    const pref = parseInt(btn.getAttribute('data-pref'), 10);
    
    // Remove all previous button classes
    btn.classList.remove('btn-outline-primary','btn-outline-warning','btn-outline-success','btn-primary','btn-warning','btn-success');
    
    if (active) {
        // Apply active state with enhanced styling
        if (pref === 1) {
            btn.classList.add('btn-primary', 'active');
        } else if (pref === 2) {
            btn.classList.add('btn-warning', 'active');
        } else if (pref === 3) {
            btn.classList.add('btn-success', 'active');
        }
    } else {
        // Apply outline state
        if (pref === 1) {
            btn.classList.add('btn-outline-primary');
        } else if (pref === 2) {
            btn.classList.add('btn-outline-warning');
        } else if (pref === 3) {
            btn.classList.add('btn-outline-success');
        }
    }
    
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
}

function refreshSelectionStyles(){
    console.log('Refreshing selection styles with preferences:', prefs);
    
    // Reset all states first
    candidateCards.forEach(card => {
        card.classList.remove('border-primary','border-warning','border-success','border-3','selected');
        card.querySelectorAll('.preference-btn').forEach(btn => toggleBtnStyle(btn, false));
        const indicator = card.querySelector('.selection-indicator');
        if (indicator) {
            indicator.textContent = '';
            indicator.style.opacity = '0';
        }
    });
    
    // Apply styles for selected prefs
    for (let i = 1; i <= 3; i++){
        const cid = prefs[i];
        if (!cid) continue;
        
        console.log(`Applying style for preference ${i}, candidate ${cid}`);
        
        const card = document.querySelector(`.candidate-card[data-candidate-id="${cid}"]`);
        if (!card) {
            console.log(`Card not found for candidate ${cid}`);
            continue;
        }
        
        if (i === 1) card.classList.add('border-primary','border-3');
        if (i === 2) card.classList.add('border-warning','border-3');
        if (i === 3) card.classList.add('border-success','border-3');
        
        const btn = card.querySelector(`.preference-btn[data-pref="${i}"]`);
        if (btn) {
            console.log(`Button found for preference ${i}, applying active style`);
            toggleBtnStyle(btn, true);
        } else {
            console.log(`Button not found for preference ${i}`);
        }
        
        card.classList.add('selected');
        
        // Update selection indicator
        const indicator = card.querySelector('.selection-indicator');
        if (indicator) {
            indicator.textContent = i;
            indicator.style.opacity = '1';
        }
    }
}

function ordinal(n){return n===1?'1st':n===2?'2nd':n===3?'3rd':`${n}th`;}

function updatePrefsDisplay() {
    if (!selectedPrefsDisplay) return;
    let html = '';
    for (let i = 1; i <= 3; i++) {
        const cid = prefs[i];
        const label = cid ? `#${cid} — ${candidateIdToName[cid] || 'Candidate'}` : '-';
        html += `<div><strong>${ordinal(i)} Preference</strong>: ${label}</div>`;
    }
    selectedPrefsDisplay.innerHTML = html;
}

function clearPrefs() {
    prefs[1] = prefs[2] = prefs[3] = null;
    updateHiddenInputs();
    refreshEnablement();
    refreshSelectionStyles();
    updatePrefsDisplay();
}

const clearBtn = document.getElementById('clearVotesBtn');
if (clearBtn) clearBtn.onclick = clearPrefs;

// Enhanced preference button click with strict sequential enforcement
candidateCards.forEach(card => {
    const cid = card.getAttribute('data-candidate-id');
    card.querySelectorAll('.preference-btn').forEach(btn => {
                    btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Button clicked! Preference:', btn.getAttribute('data-pref'), 'Candidate:', cid);
                const pref = parseInt(btn.getAttribute('data-pref'), 10);
            
            // Strict sequential enforcement
            if (pref === 2 && !prefs[1]) { 
                showSequentialError('Please select your 1st preference first.');
                return; 
            }
            if (pref === 3 && !prefs[2]) { 
                showSequentialError('Please select your 2nd preference first.');
                return; 
            }
            
            // Clear any previous selection of this candidate
            for (let i = 1; i <= 3; i++) { 
                if (prefs[i] == cid) prefs[i] = null; 
            }
            
            // Set the new preference
            prefs[pref] = cid;
            
            // Debug logging
            console.log('Preference selected:', pref, 'for candidate:', cid);
            console.log('Current preferences:', prefs);
            
            updateHiddenInputs();
            refreshEnablement();
            refreshSelectionStyles();
            updatePrefsDisplay();
            
            // Show success message for sequential selection
            if (pref > 1) {
                showSequentialSuccess(`${ordinal(pref)} preference selected successfully!`);
            }
        };
    });
    
    // Enhanced keyboard shortcuts with sequential enforcement
    card.addEventListener('keydown', (e) => {
        if (e.key === '1' || e.key === '2' || e.key === '3') {
            const pref = parseInt(e.key, 10);
            
            // Strict sequential enforcement
            if (pref === 2 && !prefs[1]) { 
                e.preventDefault(); 
                showSequentialError('Please choose your 1st preference first.');
                return; 
            }
            if (pref === 3 && !prefs[2]) { 
                e.preventDefault(); 
                showSequentialError('Please choose your 2nd preference first.');
                return; 
            }
            
            for (let i = 1; i <= 3; i++) { 
                if (prefs[i] == cid) prefs[i] = null; 
            }
            prefs[pref] = cid;
            
            updateHiddenInputs();
            refreshEnablement();
            refreshSelectionStyles();
            updatePrefsDisplay();
            
            if (pref > 1) {
                showSequentialSuccess(`${ordinal(pref)} preference selected successfully!`);
            }
        }
    });
    
    // Enhanced card click with sequential enforcement
    card.addEventListener('click', (e) => {
        if (e.target && e.target.classList && e.target.classList.contains('preference-btn')) return; // buttons handle their own logic
        if (prefs[1] && prefs[2] && prefs[3]) { 
            showSequentialError('You have already selected 3 preferences. Use Clear to change.');
            return; 
        }
        
        // If this candidate is already selected for any pref, ignore
        for (let i = 1; i <= 3; i++) { 
            if (prefs[i] == cid) return; 
        }
        
        const next = getNextAvailablePref();
        
        // Strict sequential enforcement
        if (next === 2 && !prefs[1]) { 
            showSequentialError('Please choose your 1st preference first.');
            return; 
        }
        if (next === 3 && !prefs[2]) { 
            showSequentialError('Please choose your 2nd preference first.');
            return; 
        }
        
        if (!next) return;
        prefs[next] = cid;
        
        updateHiddenInputs();
        refreshEnablement();
        refreshSelectionStyles();
        updatePrefsDisplay();
    });
});

// Helper functions for user feedback
function showSequentialError(message) {
    // Create or update error message
    let errorDiv = document.getElementById('sequentialError');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'sequentialError';
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.style.display = 'none';
        document.querySelector('.voting-alert').after(errorDiv);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 3000);
}

function showSequentialSuccess(message) {
    // Create or update success message
    let successDiv = document.getElementById('sequentialSuccess');
    if (!successDiv) {
        successDiv = document.createElement('div');
        successDiv.id = 'sequentialSuccess';
        successDiv.className = 'alert alert-success mt-3';
        successDiv.style.display = 'none';
        document.querySelector('.voting-alert').after(successDiv);
    }
    
    successDiv.textContent = message;
    successDiv.style.display = 'block';
    
    // Auto-hide after 2 seconds
    setTimeout(() => {
        successDiv.style.display = 'none';
    }, 2000);
}

// Initialize the interface
updatePrefsDisplay();
refreshEnablement();
refreshSelectionStyles();

// Confirmation modal handling
const submitBtn = document.getElementById('submitVoteBtn');
const confirmBtn = document.getElementById('confirmSubmitBtn');
const modalPreview = document.getElementById('modalPrefsPreview');
const votingForm = document.getElementById('votingForm');
if (submitBtn && modalPreview) {
    submitBtn.addEventListener('click', () => {
        modalPreview.innerHTML = '';
        for (let i = 1; i <= 3; i++) {
            const cid = prefs[i];
            const value = cid ? `#${cid} — ${candidateIdToName[cid] || 'Candidate'}` : '-';
            modalPreview.innerHTML += `<div><strong>${ordinal(i)} Preference</strong>: ${value}</div>`;
        }
    });
}
if (confirmBtn && votingForm) {
    confirmBtn.addEventListener('click', (e) => {
        // For safety, ensure first preference exists; let native form submit handle rest
        if (!prefs[1]) {
            e.preventDefault();
            alert('Please select at least your 1st preference.');
            return;
        }
        // No manual .submit() to preserve form attributes and default action
    });
}

// One-time extend time button behavior - removed since button was removed from HTML
// if (extendBtn) {
//     extendBtn.addEventListener('click', () => {
//         timeLeft += 60; // add 1 minute
//         extendBtn.disabled = true;
//         extendBtn.classList.add('disabled');
//         extendBtn.textContent = 'Time Extended';
//     });
// }