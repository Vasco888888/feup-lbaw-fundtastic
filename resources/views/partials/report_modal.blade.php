@auth
<div id="report-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2.5rem; max-width: 540px; width: 90%; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.75rem; font-weight: 700; color: #dc2626; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                Report content
            </h3>
            <button type="button" id="report-close" style="background: #f3f4f6; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e5e7eb'; this.style.color='#374151'" onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280'">&times;</button>
        </div>

        <form id="report-form" method="POST" action="/reports">
            @csrf
            <input type="hidden" name="target_type" value="">
            <input type="hidden" name="target_id" value="">

            <div style="margin-bottom: 1.5rem;">
                <label for="report-reason" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">Reason *</label>
                <textarea id="report-reason" name="reason" rows="6" required maxlength="2000" placeholder="Describe why you are reporting this content (harassment, spam, inappropriate content, etc.)" style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; font-family: inherit; resize: vertical; transition: all 0.2s;" onfocus="this.style.borderColor='#ef4444'; this.style.boxShadow='0 0 0 3px rgba(239, 68, 68, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1.5rem;">
                <button type="button" id="report-cancel" style="padding: 0.875rem 1.75rem; background: white; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                    Cancel
                </button>
                <button type="submit" id="report-submit" style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); font-size: 0.9375rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(239, 68, 68, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 2L11 13" stroke-width="2"/>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke-width="2"/>
                    </svg>
                    Send Report
                </button>
            </div>
            
            <div id="report-feedback" style="margin-top: 1rem; text-align: center; color: #6b7280; font-size: 0.875rem;"></div>
        </form>
    </div>
</div>
@else
    {{-- Guests should log in to report --}}
@endauth
