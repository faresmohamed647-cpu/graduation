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
        window.location.href = '/login?role=driver';
        return;
    }

    if (response.status === 403) {
        throw new Error('Access Denied');
    }

    if (!response.ok) throw new Error(`API ${response.status}: ${url}`);
    return response.json();
}

async function hydrateDriverDashboardFromApi() {
    try {
        const [dashboard, notifications] = await Promise.all([
            safestepApi('/api/driver/dashboard'),
            safestepApi('/api/driver/notifications')
        ]);

        const trip = dashboard.data?.today_trip;
        if (trip) {
            busInfo.number = trip.bus?.bus_number || busInfo.number;
            busInfo.capacity = trip.bus?.capacity || busInfo.capacity;
            busInfo.route = trip.route?.name || busInfo.route;
            renderBusInfo();

            const badge = document.getElementById('busStatusBadge');
            if (badge) badge.textContent = trip.status === 'active' ? 'On Route' : trip.status || 'Assigned';
        }

        const students = dashboard.data?.students || [];
        safestepReplaceArray(studentsData, students.map(student => ({
            id: student.student_id,
            name: student.name || 'Student',
            grade: '',
            pickup: 'Assigned stop',
            status: student.status === 'picked_up' || student.status === 'dropped_off' ? true : null
        })));

        safestepReplaceArray(attendanceData, students.map(student => ({
            student_id: student.student_id,
            name: student.name || 'Student',
            pickup: student.picked_up_at ? 'picked' : 'pending',
            dropoff: student.dropped_off_at ? 'dropped' : 'pending'
        })));

        safestepReplaceArray(driverNotifications, (notifications.data || []).map((notification, index) => ({
            id: notification.id || index + 1,
            type: notification.read_at ? 'info' : 'success',
            icon: notification.read_at ? 'fa-info-circle' : 'fa-bell',
            title: notification.data?.title || 'Notification',
            message: notification.data?.body || '',
            time: notification.created_at ? new Date(notification.created_at).toLocaleString() : '',
            read: Boolean(notification.read_at)
        })));

        renderStudents();
        renderAttendanceTable();
        renderDriverNotifications();
        updateAttendanceSummary();
        updateNotificationBadge();
    } catch (error) {
        console.warn('SafeStep driver API hydration skipped:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', hydrateDriverDashboardFromApi);
