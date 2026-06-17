function safestepReplaceArray(target, items) {
    target.splice(0, target.length, ...items);
}

// Using global ApiService (aliased as safestepApi)

async function hydrateParentDashboardFromApi() {
    try {
        const [dashboard, children, userNotifications] = await Promise.all([
            safestepApi('/parent/dashboard'),
            safestepApi('/parent/children'),
            safestepApi('/parent/notifications')
        ]);

        const childRows = children.data || dashboard.data?.children || [];
        childrenData = childRows.map((child, index) => ({
            id: child.id,
            name: child.full_name || child.name || 'Student',
            grade: child.grade || '',
            avatar: `https://source.unsplash.com/300x300/?student,portrait&sig=${index + 50}`,
            status: child.bus_id ? 'Assigned' : 'Pending Assignment',
            statusClass: child.bus_id ? 'on-bus' : 'absent',
            pickupLocation: child.pickup_location || 'Assigned route stop',
            pickupTime: child.pickup_time || '--',
            dropLocation: child.dropoff_location || child.school_name || '',
            dropTime: child.dropoff_time || '--',
            busNumber: child.bus?.bus_number || 'Assigned bus',
            driver: child.bus?.driver?.user?.name || 'Assigned driver',
            attendance: [true, true, true, true, true, true, true]
        }));

        const childNameOverlay = document.getElementById('childNameOverlay');
        if (childNameOverlay && childRows.length) {
            childNameOverlay.textContent = childRows[0].full_name || childRows[0].name || 'Student';
        }

        const attendanceResponses = await Promise.allSettled(
            childRows.map(child => safestepApi(`/parent/children/${child.id}/attendance`))
        );

        const records = [];
        attendanceResponses.forEach((result, index) => {
            if (result.status !== 'fulfilled') return;
            (result.value.data || []).forEach(item => {
                records.push({
                    child: childRows[index]?.full_name || childRows[index]?.name || 'Student',
                    date: item.created_at ? item.created_at.slice(0, 10) : '',
                    pickupTime: item.picked_up_at ? new Date(item.picked_up_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '-',
                    dropTime: item.dropped_off_at ? new Date(item.dropped_off_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '-',
                    status: item.status === 'picked_up' || item.status === 'dropped_off' ? 'Present' : 'Absent'
                });
            });
        });

        safestepReplaceArray(attendanceData, records);

        notifications = (userNotifications.data || []).map((notification, index) => ({
            id: notification.id || index + 1,
            type: notification.read_at ? 'info' : 'success',
            icon: notification.read_at ? 'fa-info-circle' : 'fa-bell',
            title: notification.data?.title || 'Notification',
            message: notification.data?.body || '',
            time: notification.created_at ? new Date(notification.created_at).toLocaleString() : ''
        }));

        const unreadCount = dashboard.data?.unread_notifications
            ?? notifications.filter(n => n.type === 'success').length;
        const badge = document.querySelector('.notification-icon .badge, #notificationBadge');
        if (badge) {
            badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
            badge.style.display = unreadCount > 0 ? '' : 'none';
        }

        renderChildrenSections();
        renderNotifications();
        renderAttendanceTable();
        updateAttendanceSummary();
    } catch (error) {
        console.warn('SafeStep parent API hydration skipped:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', hydrateParentDashboardFromApi);
