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
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
});