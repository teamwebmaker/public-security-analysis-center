const vapidPublicKey = window.APP_VAPID_PUBLIC_KEY;
const csrfToken = window.APP_CSRF_TOKEN;

/**
 * Converts a base64 string to a Uint8Array
 */
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
}

/**
 * Makes a JSON POST request
 */
async function postJSON(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(data),
    });

    if (!res.ok) throw new Error(`Request to ${url} failed`);
    return await res.json();
}

/**
 * Updates the toggle button UI
 */
function updateBtn(btn, isSubscribed, isLoading) {
    btn.disabled = isLoading;

    if (isLoading) {
        btn.textContent = isSubscribed ? 'ვთიშავ...' : 'ვრთავ...';
        return;
    }

    btn.textContent = isSubscribed
        ? 'შეტყობინებების გამორთვა'
        : 'შეტყობინებების ჩართვა';
}

document.addEventListener('DOMContentLoaded', async () => {
    const btn = document.getElementById('push-toggle-btn');
    if (!btn || !('serviceWorker' in navigator)) return;

    // Register and wait for the service worker to be ready
    const reg = await navigator.serviceWorker.register('/sw.js');
    const sw = await navigator.serviceWorker.ready;

    // Check if the browser already has a push subscription
    let subscription = await sw.pushManager.getSubscription();
    let isServerSubscribed = false;

    if (subscription) {
        try {
            // Verify subscription status with the server
            const { exists } = await postJSON('/admin/check-subscription', {
                endpoint: subscription.endpoint,
            });

            isServerSubscribed = exists;

            // Unsubscribe locally if the server doesn’t recognize the subscription
            if (!exists) {
                await subscription.unsubscribe();
                subscription = null;
            }
        } catch (err) {
            console.error('Subscription check failed:', err);
        }
    }

    // Set the button text based on subscription state
    updateBtn(btn, !!subscription, false);

    // Handle click
    btn.onclick = async () => {
        updateBtn(btn, !!subscription, true); // Show loading state

        try {
            if (!subscription) {
                // Ask for permission to show notifications
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    alert('Notification permission denied!');
                    updateBtn(btn, false, false);
                    return;
                }

                // Subscribe to push notifications
                subscription = await sw.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
                });

                // Send subscription data to the server
                await postJSON('/admin/subscribe', { sub: subscription });
            } else {
                // Unsubscribe from push notifications
                await subscription.unsubscribe();

                // Inform the server to remove the subscription
                await postJSON('/admin/unsubscribe', {
                    endpoint: subscription.endpoint,
                });

                subscription = null;
            }

            updateBtn(btn, !!subscription, false);
            window.location.reload(); // Reflect changes in UI
        } catch (err) {
            console.error('Error toggling subscription:', err);
            updateBtn(btn, !!subscription, false);
        }
    };
});
