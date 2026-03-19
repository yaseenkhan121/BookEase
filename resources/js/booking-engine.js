document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('provider_id');
    const dateInput = document.getElementById('booking_date');
    const serviceSelect = document.getElementById('service_id');
    const slotContainer = document.getElementById('available-slots');
    const selectedTimeInput = document.getElementById('selected_time');

    /**
     * Fetch available slots from the API
     */
    async function fetchSlots() {
        const providerId = providerSelect?.value;
        const date = dateInput?.value;
        const serviceId = serviceSelect?.value;

        // 1. Basic Validation
        if (!providerId || !date || !serviceId) return;

        // 2. Prevent fetching for past dates
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            slotContainer.innerHTML = '<p class="text-danger font-semibold">Please select a future date.</p>';
            return;
        }

        // 3. UI Loading State
        slotContainer.innerHTML = `
            <div class="flex items-center space-x-2 animate-pulse">
                <div class="w-3 h-3 bg-teal-600 rounded-full"></div>
                <p class="text-gray-500">Finding available times...</p>
            </div>
        `;

        try {
            const response = await fetch(`/api/v1/slots?provider_id=${providerId}&date=${date}&service_id=${serviceId}`);
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();

            slotContainer.innerHTML = '';

            if (!data.available_slots || data.available_slots.length === 0) {
                slotContainer.innerHTML = `
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                        <p class="text-orange-700 text-sm">No slots found. Try another date or provider.</p>
                    </div>
                `;
                return;
            }

            // 4. Render Slots using DocumentFragment (Performance boost)
            const fragment = document.createDocumentFragment();
            
            data.available_slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slot-item'; // Matches our app.css classes
                btn.setAttribute('data-time', slot.time);
                btn.innerHTML = `<span>${slot.formatted_time || slot.time}</span>`;
                
                btn.addEventListener('click', (e) => {
                    handleSlotSelection(e.currentTarget, slot.time);
                });

                fragment.appendChild(btn);
            });

            slotContainer.appendChild(fragment);

        } catch (error) {
            console.error('Error fetching slots:', error);
            slotContainer.innerHTML = '<p class="text-red-500">Failed to load slots. Please refresh.</p>';
        }
    }

    /**
     * Handle Slot Selection UI and Logic
     */
    function handleSlotSelection(element, time) {
        // Clear previous selections
        slotContainer.querySelectorAll('.slot-item').forEach(btn => {
            btn.classList.remove('selected');
        });

        // Set active state
        element.classList.add('selected');
        
        // Update hidden input
        if (selectedTimeInput) {
            selectedTimeInput.value = time;
            // Dispatch event for other listeners
            selectedTimeInput.dispatchEvent(new Event('change'));
        }
    }

    // Event Listeners
    dateInput?.addEventListener('change', fetchSlots);
    providerSelect?.addEventListener('change', fetchSlots);
    serviceSelect?.addEventListener('change', fetchSlots);
});