const vapidPublicKey = window.APP_VAPID_PUBLIC_KEY;

/**
 * Converts a base64 string to a Uint8Array
 */
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

document.addEventListener('DOMContentLoaded', async () => {
    const btn = document.getElementById('push-toggle-btn');
    if (!btn) return;

    // Register Service Worker for push
    const reg = await navigator.serviceWorker.register('/sw.js');
    const sw = await navigator.serviceWorker.ready;

    // Check if already subscribed
    let subscription = await sw.pushManager.getSubscription();
    updateBtn(subscription, false);

    // Toggle button click handler
    btn.onclick = async () => {
        if (!subscription) {
            // Enable notifications
            updateBtn(subscription, true); // Show loading

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                alert('Notification permission denied!');
                updateBtn(subscription, false);
                return;
            }

            subscription = await sw.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            });

            // Send subscription to server
            await fetch('/admin/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.APP_CSRF_TOKEN
                },
                body: JSON.stringify({ sub: subscription })
            });

        } else {
            // Disable notifications
            updateBtn(subscription, true); // Show loading

            await subscription.unsubscribe();

            // Remove subscription from server
            await fetch('/admin/unsubscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.APP_CSRF_TOKEN
                },
                body: JSON.stringify({ endpoint: subscription.endpoint })
            });

            subscription = null;
        }

        updateBtn(subscription, false);

        // Reload page to reflect changes
        window.location.reload();
    };

    /**
     * Updates the button state and text
     * @param {*} subscription - current subscription object
     * @param {boolean} isLoading - whether to show loading text
     */
    function updateBtn(subscription, isLoading) {
        btn.disabled = isLoading;

        if (isLoading) {
            btn.textContent = subscription ? 'ვთიშავ...' : 'ვრთავ...';
        } else {
            btn.textContent = subscription
                ? 'შეტყობინებების გამორთვა'
                : 'შეტყობინებების ჩართვა';
            btn.disabled = false;
        }
    }
});
