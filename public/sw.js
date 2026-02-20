self.addEventListener('push', function(event) {
  // console.log('Push received');
  const notification = event.data.json();

  event.waitUntil(
    self.registration.showNotification(notification.title, {
      body: notification.body,
      icon: '/images/themes/message.svg',
      data:{
        url: notification.url
      }
    })
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();

  let targetUrl = '/';
  try {
    const rawUrl = event.notification?.data?.url || '/';
    const parsed = new URL(rawUrl, self.location.origin);

    // If payload URL has a different origin, keep only path/query/hash and open on current origin.
    targetUrl = parsed.origin === self.location.origin
      ? parsed.href
      : `${parsed.pathname}${parsed.search}${parsed.hash}`;
  } catch (e) {
    targetUrl = '/';
  }

  event.waitUntil(clients.openWindow(targetUrl));
});
