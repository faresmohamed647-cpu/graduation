function safestepReplaceArray(target, items) {
    target.splice(0, target.length, ...items);
}

async function safestepApi(url) {
    const token = localStorage.getItem('token') || localStorage.getItem('safestep_token');
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': token ? `Bearer ${token}` : ''
        }
    });

    if (response.status === 401) {
        localStorage.removeItem('safestep_token');
        localStorage.removeItem('token');
        window.location.href = '/login?role=parent';
        return;
    }

    if (response.status === 403) {
        throw new Error('Access Denied');
    }

    if (!response.ok) throw new Error(`API ${response.status}: ${url}`);
    return response.json();
}

async function hydrateParentDashboardFromApi() {
    try {
        const [dashboard, children, userNotifications] = await Promise.all([
            safestepApi('/api/parent/dashboard'),
            safestepApi('/api/parent/children'),
            safestepApi('/api/parent/notifications')
        ]);

        const childRows = children.data || dashboard.data?.children || [];
        childrenData = childRows.map((child, index) => ({
            id: child.id,
            name: child.full_name || child.name || 'Student',
            grade: child.grade || '',
            avatar: `https://source.unsplash.com/300x300/?student,portrait&sig=${index + 50}`,
            status: 'Registered',
            statusClass: 'on-bus',
            pickupLocation: 'Assigned route stop',
            pickupTime: '--',
            dropLocation: child.school_name || '',
            dropTime: '--',
            busNumber: dashboard.data?.latest_trip?.bus?.bus_number || 'Assigned bus',
            driver: dashboard.data?.latest_trip?.driver?.user?.name || 'Assigned driver',
            attendance: [true, true, true, true, true, true, true]
        }));

        const attendanceResponses = await Promise.allSettled(
            childRows.map(child => safestepApi(`/api/parent/children/${child.id}/attendance`))
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

        renderChildrenSections();
        renderNotifications();
        renderAttendanceTable();
        updateAttendanceSummary();
    } catch (error) {
        console.warn('SafeStep parent API hydration skipped:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', hydrateParentDashboardFromApi);
