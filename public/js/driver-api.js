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

let activeDriverTrip = null;
let driverLocationWatchId = null;
let driverLocationInterval = null;

async function safestepPostApi(url, body) {
    const token = localStorage.getItem('token') || localStorage.getItem('safestep_token');
    const response = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': token ? `Bearer ${token}` : ''
        },
        body: JSON.stringify(body)
    });

    if (!response.ok) {
        const payload = await response.json().catch(() => ({}));
        throw new Error(payload.message || `API ${response.status}: ${url}`);
    }

    return response.json();
}

async function sendDriverLocation(latitude, longitude, speed = null, heading = null) {
    if (!activeDriverTrip || activeDriverTrip.status !== 'active') return;

    try {
        await safestepPostApi('/api/driver/location', {
            latitude,
            longitude,
            speed,
            heading
        });
    } catch (error) {
        console.warn('Driver location update failed:', error.message);
    }
}

function startDriverLocationTracking() {
    if (!activeDriverTrip || activeDriverTrip.status !== 'active') return;
    if (driverLocationInterval) return;

    const pushPosition = (position) => {
        const { latitude, longitude, speed, heading } = position.coords;
        sendDriverLocation(
            latitude,
            longitude,
            typeof speed === 'number' ? Math.round(speed * 3.6) : null,
            typeof heading === 'number' ? heading : null
        );

        if (typeof window.updateDriverMapPosition === 'function') {
            window.updateDriverMapPosition(latitude, longitude);
        }
    };

    if (navigator.geolocation) {
        driverLocationWatchId = navigator.geolocation.watchPosition(
            pushPosition,
            () => console.warn('GPS permission denied, using Alexandria simulation.'),
            { enableHighAccuracy: true, maximumAge: 5000, timeout: 15000 }
        );
    }

    driverLocationInterval = setInterval(() => {
        if (driverLocationWatchId !== null) return;

        if (typeof window.simulateAlexandriaDriverMovement === 'function') {
            const point = window.simulateAlexandriaDriverMovement();
            sendDriverLocation(point.lat, point.lng, point.speed);
            window.updateDriverMapPosition(point.lat, point.lng);
        }
    }, 10000);
}

function stopDriverLocationTracking() {
    if (driverLocationWatchId !== null && navigator.geolocation) {
        navigator.geolocation.clearWatch(driverLocationWatchId);
        driverLocationWatchId = null;
    }
    if (driverLocationInterval) {
        clearInterval(driverLocationInterval);
        driverLocationInterval = null;
    }
}

async function hydrateDriverDashboardFromApi() {
    try {
        const [dashboard, notifications] = await Promise.all([
            safestepApi('/api/driver/dashboard'),
            safestepApi('/api/driver/notifications')
        ]);

        const trip = dashboard.data?.today_trip;
        activeDriverTrip = trip || null;

        if (trip) {
            busInfo.number = trip.bus?.bus_number || busInfo.number;
            busInfo.capacity = trip.bus?.capacity || busInfo.capacity;
            busInfo.route = trip.route?.name || busInfo.route;
            renderBusInfo();

            const badge = document.getElementById('busStatusBadge');
            if (badge) badge.textContent = trip.status === 'active' ? 'On Route' : trip.status || 'Assigned';

            if (trip.status === 'active') {
                startDriverLocationTracking();
            } else {
                stopDriverLocationTracking();
            }
        } else {
            busInfo.number = 'No Bus Assigned';
            busInfo.capacity = 0;
            busInfo.route = 'No route assigned';
            renderBusInfo();
            const badge = document.getElementById('busStatusBadge');
            if (badge) badge.textContent = 'Waiting Assignment';
            stopDriverLocationTracking();
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

async function startDriverTripApi() {
    if (!activeDriverTrip?.id) {
        throw new Error('No trip assigned for today.');
    }
    const response = await safestepPostApi(`/api/driver/trips/${activeDriverTrip.id}/start`, {});
    activeDriverTrip = response.data || activeDriverTrip;
    activeDriverTrip.status = 'active';
    startDriverLocationTracking();
    return activeDriverTrip;
}

async function completeDriverTripApi() {
    if (!activeDriverTrip?.id) {
        throw new Error('No active trip found.');
    }
    stopDriverLocationTracking();
    const response = await safestepPostApi(`/api/driver/trips/${activeDriverTrip.id}/complete`, {});
    activeDriverTrip = response.data || activeDriverTrip;
    activeDriverTrip.status = 'completed';
    return activeDriverTrip;
}

document.addEventListener('DOMContentLoaded', hydrateDriverDashboardFromApi);
