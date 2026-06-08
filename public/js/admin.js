// Navigation

// Data arrays (Refactored)
let applicationsData = [];
const busRoutesData = [];

const parentsData = [];

const driversData = [];

function mapParentForDashboard(parent) {
    return {
        id: parent.id,
        name: parent.name || parent.user?.name || 'Parent',
        children: parent.children || (parent.students || []).map(student => student.name || student.full_name).filter(Boolean).join(', ') || 'None',
        phone: parent.phone || '',
        email: parent.email || parent.user?.email || '',
        applicationDate: safestepDate(parent.applicationDate || parent.created_at),
        joinDate: safestepDate(parent.joinDate || parent.created_at),
        status: normalizeDashboardStatus(parent.status, parent.active)
    };
}

function mapDriverForDashboard(driver) {
    return {
        id: driver.id,
        name: driver.name || driver.user?.name || driver.full_name || 'Driver',
        license: driver.license || driver.license_number || '',
        phone: driver.phone || '',
        applicationDate: safestepDate(driver.applicationDate || driver.created_at),
        joinDate: safestepDate(driver.joinDate || driver.created_at),
        experience: driver.experience || `${driver.years_experience || driver.experience_years || 0} years`,
        bus: driver.bus || 'Assigned by trips',
        status: normalizeDashboardStatus(driver.status, driver.active)
    };
}

function applyInitialAdminData() {
    const initial = window.__INITIAL_ADMIN_DATA || {};
    if (Array.isArray(initial.parents) && initial.parents.length) {
        safestepReplaceArray(parentsData, initial.parents.map(mapParentForDashboard));
    }
    if (Array.isArray(initial.drivers) && initial.drivers.length) {
        safestepReplaceArray(driversData, initial.drivers.map(mapDriverForDashboard));
    }
}

applyInitialAdminData();

const financialsData = [
    { id: 1, date: '2024-01-15', type: 'income', description: 'Monthly bus fares', amount: 25000, enteredBy: 'Admin User' },
    { id: 2, date: '2024-01-20', type: 'expense', description: 'Fuel costs', amount: -8500, enteredBy: 'Admin User' },
    { id: 3, date: '2024-01-25', type: 'income', description: 'Special route fees', amount: 5200, enteredBy: 'Manager' },
    { id: 4, date: '2024-02-01', type: 'expense', description: 'Maintenance parts', amount: -3200, enteredBy: 'Admin User' },
    { id: 5, date: '2024-02-10', type: 'profit', description: 'January profit', amount: 16500, enteredBy: 'System' },
    { id: 6, date: '2024-02-15', type: 'income', description: 'Monthly bus fares', amount: 26800, enteredBy: 'Admin User' },
    { id: 7, date: '2024-02-20', type: 'expense', description: 'Driver salaries', amount: -15200, enteredBy: 'HR Manager' },
    { id: 8, date: '2024-02-25', type: 'loss', description: 'Equipment damage', amount: -4800, enteredBy: 'Admin User' }
];

const maintenanceData = [
    { id: 1, busNumber: 'Bus #42', plateNumber: 'ABC-123', type: 'repair', description: 'Engine oil change and filter replacement', date: '2024-01-10', cost: 1200, technician: 'Ahmed Tech' },
    { id: 2, busNumber: 'Bus #15', plateNumber: 'DEF-456', type: 'maintenance', description: 'Brake system inspection and adjustment', date: '2024-01-15', cost: 800, technician: 'Mohamed Auto' },
    { id: 3, busNumber: 'Bus #28', plateNumber: 'GHI-789', type: 'repair', description: 'Tire replacement (4 tires)', date: '2024-01-22', cost: 2400, technician: 'Karim Tires' },
    { id: 4, busNumber: 'Bus #33', plateNumber: 'JKL-012', type: 'inspection', description: 'Annual safety inspection', date: '2024-02-01', cost: 500, technician: 'Safety Inspector' },
    { id: 5, busNumber: 'Bus #07', plateNumber: 'MNO-345', type: 'repair', description: 'Air conditioning system repair', date: '2024-02-08', cost: 1800, technician: 'Cool Air Co.' },
    { id: 6, busNumber: 'Bus #19', plateNumber: 'PQR-678', type: 'maintenance', description: 'Battery replacement', date: '2024-02-12', cost: 600, technician: 'Ahmed Tech' },
    { id: 7, busNumber: 'Bus #51', plateNumber: 'STU-901', type: 'repair', description: 'Transmission fluid change', date: '2024-02-18', cost: 950, technician: 'Mohamed Auto' },
    { id: 8, busNumber: 'Bus #12', plateNumber: 'VWX-234', type: 'inspection', description: 'Emergency brake check', date: '2024-02-20', cost: 300, technician: 'Safety Inspector' }
];

const ALEXANDRIA_MAP_CENTER = [31.2001, 29.9187];
let liveTrackingApiData = [];
let liveTrackingPollTimer = null;

const studentsData = [
    { id: 1, studentId: 'STU001', name: 'Ahmed Mohamed', grade: 'Grade 5', school: 'Al-Azhar School', parent: 'Mohamed Hassan', pickupLocation: '12 El-Horreya Rd, Raml Station, Alexandria', dropoffLocation: 'Al-Azhar School', status: 'active' },
    { id: 2, studentId: 'STU002', name: 'Fatima Ali', grade: 'Grade 3', school: 'Alexandria International School', parent: 'Ali Mahmoud', pickupLocation: '25 Fouad St, El-Mansheya, Alexandria', dropoffLocation: 'Alexandria International School', status: 'active' },
    { id: 3, studentId: 'STU003', name: 'Omar Khaled', grade: 'Grade 7', school: 'British School Alexandria', parent: 'Khaled Ahmed', pickupLocation: '33 Abu Qir St, Sidi Gaber, Alexandria', dropoffLocation: 'British School Alexandria', status: 'active' },
    { id: 4, studentId: 'STU004', name: 'Aya Samir', grade: 'Grade 2', school: 'Al-Azhar School', parent: 'Samir Hassan', pickupLocation: '18 Safeya Zaghloul St, Roushdy, Alexandria', dropoffLocation: 'Al-Azhar School', status: 'inactive' },
    { id: 5, studentId: 'STU005', name: 'Mohamed Tamer', grade: 'Grade 9', school: 'Alexandria International School', parent: 'Tamer Mostafa', pickupLocation: '41 Victor Emmanuel Sq, Smouha, Alexandria', dropoffLocation: 'Alexandria International School', status: 'active' },
    { id: 6, studentId: 'STU006', name: 'Sara Ahmed', grade: 'Grade 4', school: 'British School Alexandria', parent: 'Ahmed Youssef', pickupLocation: '60 Port Said St, Cleopatra, Alexandria', dropoffLocation: 'British School Alexandria', status: 'active' },
    { id: 7, studentId: 'STU007', name: 'Karim Hassan', grade: 'Grade 6', school: 'Al-Azhar School', parent: 'Hassan Ali', pickupLocation: '27 El-Gaish Rd, Sporting, Alexandria', dropoffLocation: 'Al-Azhar School', status: 'active' },
    { id: 8, studentId: 'STU008', name: 'Nour Mostafa', grade: 'Grade 1', school: 'Alexandria International School', parent: 'Mostafa Karim', pickupLocation: '9 Khaled Ibn El-Walid St, Sidi Bishr, Alexandria', dropoffLocation: 'Alexandria International School', status: 'active' }
];

const tripsData = [
    { id: 1, tripId: 'TRP001', routeName: 'Morning Route A', bus: 'Bus #42', driver: 'Ahmed Khaled', startTime: '07:00', endTime: '08:30', students: 25, status: 'completed', date: '2024-02-19' },
    { id: 2, tripId: 'TRP002', routeName: 'Morning Route B', bus: 'Bus #15', driver: 'Mohamed Ali', startTime: '07:15', endTime: '08:45', students: 22, status: 'completed', date: '2024-02-19' },
    { id: 3, tripId: 'TRP003', routeName: 'Afternoon Route A', bus: 'Bus #28', driver: 'Youssef Hassan', startTime: '14:00', endTime: '15:30', students: 28, status: 'in-progress', date: '2024-02-19' },
    { id: 4, tripId: 'TRP004', routeName: 'Afternoon Route C', bus: 'Bus #33', driver: 'Omar Samir', startTime: '14:30', endTime: '16:00', students: 20, status: 'scheduled', date: '2024-02-20' },
    { id: 5, tripId: 'TRP005', routeName: 'Evening Route B', bus: 'Bus #07', driver: 'Ramy Mostafa', startTime: '16:00', endTime: '17:30', students: 18, status: 'scheduled', date: '2024-02-20' },
    { id: 6, tripId: 'TRP006', routeName: 'Morning Route D', bus: 'Bus #19', driver: 'Karim Mahmoud', startTime: '07:30', endTime: '09:00', students: 30, status: 'cancelled', date: '2024-02-18' }
];

const routeStopsData = [
    { id: 1, tripId: 'TRP001', order: 1, name: 'Raml Station', location: '31.2001, 29.9187', expectedArrival: '07:05' },
    { id: 2, tripId: 'TRP001', order: 2, name: 'Sidi Gaber', location: '31.2140, 29.9420', expectedArrival: '07:18' },
    { id: 3, tripId: 'TRP001', order: 3, name: 'Al-Azhar School', location: '31.2212, 29.9521', expectedArrival: '08:05' },
    { id: 4, tripId: 'TRP002', order: 1, name: 'El-Mansheya', location: '31.1974, 29.8931', expectedArrival: '07:20' },
    { id: 5, tripId: 'TRP002', order: 2, name: 'Smouha Gate', location: '31.2149, 29.9602', expectedArrival: '07:45' },
    { id: 6, tripId: 'TRP003', order: 1, name: 'School Main Gate', location: '31.2301, 29.9470', expectedArrival: '14:00' },
    { id: 7, tripId: 'TRP003', order: 2, name: 'Sporting Club', location: '31.2222, 29.9342', expectedArrival: '14:25' }
];

const tripHistoryData = [
    { id: 1, tripId: 'TRP001', completedAt: '2024-02-19 08:31', distanceKm: 18.4, averageSpeed: '31 km/h', points: ['31.2001, 29.9187', '31.2140, 29.9420', '31.2212, 29.9521'] },
    { id: 2, tripId: 'TRP002', completedAt: '2024-02-19 08:49', distanceKm: 15.8, averageSpeed: '28 km/h', points: ['31.1974, 29.8931', '31.2149, 29.9602', '31.2280, 29.9690'] },
    { id: 3, tripId: 'TRP006', completedAt: '2024-02-18 09:04', distanceKm: 21.2, averageSpeed: '33 km/h', points: ['31.2322, 29.9510', '31.2250, 29.9682', '31.2190, 29.9820'] }
];

const notificationsData = [
    { id: 1, title: 'Bus Delay Notice', type: 'delay', recipients: 'Parents of Route A', sentDate: '2024-02-19 08:00:00', status: 'sent' },
    { id: 2, title: 'Route Change Alert', type: 'route-change', recipients: 'All Parents', sentDate: '2024-02-18 16:30:00', status: 'sent' },
    { id: 3, title: 'Emergency: Bus Breakdown', type: 'emergency', recipients: 'Parents of Bus #15', sentDate: '2024-02-18 10:15:00', status: 'sent' },
    { id: 4, title: 'Monthly Fee Reminder', type: 'general', recipients: 'All Parents', sentDate: '2024-02-17 09:00:00', status: 'sent' },
    { id: 5, title: 'Weather Delay Notice', type: 'delay', recipients: 'Parents of Route C', sentDate: '2024-02-16 06:45:00', status: 'sent' },
    { id: 6, title: 'New Safety Guidelines', type: 'general', recipients: 'All Users', sentDate: '2024-02-15 14:20:00', status: 'pending' },
    { id: 7, title: 'Parent Message: Pickup Question', type: 'message', recipients: 'Admin Support', sentDate: '2024-02-19 11:05:00', status: 'sent', replyable: true, incomingMessage: 'Can my child be picked up 10 minutes later today?' },
    { id: 8, title: 'Driver Message: Route Note', type: 'message', recipients: 'Admin Support', sentDate: '2024-02-19 11:22:00', status: 'sent', replyable: true, incomingMessage: 'Stop 3 is blocked due to roadwork. Please advise.' }
];

const notificationTemplatesData = [
    { id: 'bus-started', title: 'Bus Started', type: 'general', recipients: 'Parents', message: 'Bus {bus} has started {route}. Expected arrival window is {time}.' },
    { id: 'student-checkin', title: 'Student Checked In', type: 'general', recipients: 'Parents', message: '{student} checked in on {bus} for {trip} at {time}.' },
    { id: 'bus-arrived', title: 'Bus Arrived', type: 'general', recipients: 'Parents', message: 'Bus {bus} arrived at {stop}. Please be ready for pickup/drop-off.' },
    { id: 'delay-alert', title: 'Delay Alert', type: 'delay', recipients: 'Parents', message: '{route} is delayed by {minutes} minutes due to traffic.' }
];

const attendanceData = [
    { id: 1, studentName: 'Ahmed Mohamed', tripId: 'TRP001', busNumber: 'Bus #42', pickupStatus: 'picked', dropoffStatus: 'dropped', time: '07:40 AM' },
    { id: 2, studentName: 'Fatima Ali', tripId: 'TRP002', busNumber: 'Bus #15', pickupStatus: 'pending', dropoffStatus: 'pending', time: '07:48 AM' },
    { id: 3, studentName: 'Omar Khaled', tripId: 'TRP003', busNumber: 'Bus #28', pickupStatus: 'picked', dropoffStatus: 'pending', time: '07:52 AM' },
    { id: 4, studentName: 'Aya Samir', tripId: 'TRP004', busNumber: 'Bus #33', pickupStatus: 'missed', dropoffStatus: 'missed', time: '07:55 AM' },
    { id: 5, studentName: 'Nour Mostafa', tripId: 'TRP005', busNumber: 'Bus #07', pickupStatus: 'picked', dropoffStatus: 'dropped', time: '08:05 AM' }
];

const paymentsData = [
    { id: 1, parentName: 'Mohamed Hassan', student: 'Ahmed Mohamed', amount: 1200, status: 'paid', date: '2024-02-18' },
    { id: 2, parentName: 'Fatima Ali', student: 'Nour Mostafa', amount: 900, status: 'pending', date: '2024-02-19' },
    { id: 3, parentName: 'Sarah Ahmed', student: 'Aya Samir', amount: 1100, status: 'overdue', date: '2024-02-10' },
    { id: 4, parentName: 'Hana Mostafa', student: 'Karim Hassan', amount: 950, status: 'paid', date: '2024-02-17' }
];

const busOccupancyData = [
    { busNumber: 'Bus #42', currentStudents: 38 },
    { busNumber: 'Bus #15', currentStudents: 40 },
    { busNumber: 'Bus #28', currentStudents: 42 },
    { busNumber: 'Bus #33', currentStudents: 48 },
    { busNumber: 'Bus #07', currentStudents: 36 },
    { busNumber: 'Bus #19', currentStudents: 44 },
    { busNumber: 'Bus #51', currentStudents: 28 },
    { busNumber: 'Bus #12', currentStudents: 41 },
    { busNumber: 'Bus #44', currentStudents: 50 },
    { busNumber: 'Bus #23', currentStudents: 39 }
];

const emergencyAlertsData = [
    { id: 1, type: 'breakdown', busNumber: 'Bus #15', driver: 'Mohamed Ali', location: 'Fleming St, Alexandria', time: '08:12 AM' },
    { id: 2, type: 'delay', busNumber: 'Bus #42', driver: 'Ahmed Khaled', location: 'Sidi Gaber, Alexandria', time: '08:20 AM' },
    { id: 3, type: 'medical', busNumber: 'Bus #33', driver: 'Omar Samir', location: 'Roushdy, Alexandria', time: '08:32 AM' },
    { id: 4, type: 'accident', busNumber: 'Bus #28', driver: 'Youssef Hassan', location: 'Stanley Bridge', time: '08:45 AM' }
];

const emergencyLogsData = [
    { id: 1, bus: 'Bus #15', driver: 'Mohamed Ali', location: 'Fleming St, Alexandria', type: 'breakdown', time: '2024-02-19 08:12:00', notes: 'Engine overheating, awaiting support.' },
    { id: 2, bus: 'Bus #42', driver: 'Ahmed Khaled', location: 'Sidi Gaber, Alexandria', type: 'delay', time: '2024-02-19 08:20:00', notes: 'Traffic congestion near bridge.' },
    { id: 3, bus: 'Bus #33', driver: 'Omar Samir', location: 'Roushdy, Alexandria', type: 'medical', time: '2024-02-19 08:32:00', notes: 'Student felt dizzy, requested nurse.' },
    { id: 4, bus: 'Bus #28', driver: 'Youssef Hassan', location: 'Stanley Bridge', type: 'accident', time: '2024-02-19 08:45:00', notes: 'Minor collision, no injuries.' }
];

const smartAlertsData = [
    { id: 1, title: 'Bus stopped too long', bus: 'Bus #15', tripId: 'TRP002', severity: 'high', detectedAt: '2024-02-19 08:24', status: 'open' },
    { id: 2, title: 'Possible route deviation', bus: 'Bus #42', tripId: 'TRP001', severity: 'medium', detectedAt: '2024-02-19 08:12', status: 'open' }
];

const ATTENDANCE_STORAGE_KEY = 'driver_attendance_events';
const attendanceEventsData = [];

const complaintsData = [
    { id: 1, complaintId: 'CMP001', submittedBy: 'Sarah Ahmed', type: 'service', subject: 'Late bus pickup', priority: 'medium', status: 'resolved', date: '2024-02-18' },
    { id: 2, complaintId: 'CMP002', submittedBy: 'Mohamed Hassan', type: 'driver', subject: 'Driver behavior concern', priority: 'high', status: 'in-progress', date: '2024-02-17' },
    { id: 3, complaintId: 'CMP003', submittedBy: 'Mariam Ali', type: 'bus', subject: 'Bus cleanliness issue', priority: 'low', status: 'open', date: '2024-02-16' },
    { id: 4, complaintId: 'CMP004', submittedBy: 'Omar Khaled', type: 'safety', subject: 'Seatbelt not working', priority: 'high', status: 'resolved', date: '2024-02-15' },
    { id: 5, complaintId: 'CMP005', submittedBy: 'Hana Mostafa', type: 'service', subject: 'Incorrect drop-off location', priority: 'medium', status: 'closed', date: '2024-02-14' },
    { id: 6, complaintId: 'CMP006', submittedBy: 'Yassin Samir', type: 'other', subject: 'Lost item on bus', priority: 'low', status: 'open', date: '2024-02-13' }
];

const schoolsData = [
    { id: 1, name: 'Al-Azhar School', type: 'public', district: 'Sidi Gaber', address: '15 El-Horreya Rd, Sidi Gaber, Alexandria', contact: '+20 111 123 4567', students: 450, status: 'active' },
    { id: 2, name: 'Alexandria International School', type: 'international', district: 'Smouha', address: '22 Ahmed Shawky St, Smouha, Alexandria', contact: '+20 111 234 5678', students: 320, status: 'active' },
    { id: 3, name: 'British School Alexandria', type: 'international', district: 'Stanley', address: '789 Learning Blvd, Stanley', contact: '+20 111 345 6789', students: 280, status: 'active' },
    { id: 4, name: 'Modern Egyptian School', type: 'private', district: 'Roushdy', address: '30 Syria St, Roushdy, Alexandria', contact: '+20 111 456 7890', students: 380, status: 'active' },
    { id: 5, name: 'Future Leaders Academy', type: 'private', district: 'Gleem', address: '654 Progress Ave, Gleem', contact: '+20 111 567 8901', students: 250, status: 'active' }
];

const usersData = [
    { id: 1, name: 'Admin User', email: 'admin@safestep.com', role: 'admin', department: 'IT', lastLogin: '2024-02-19 09:00:00', status: 'active' },
    { id: 2, name: 'Sarah Manager', email: 'sarah.manager@safestep.com', role: 'manager', department: 'Operations', lastLogin: '2024-02-19 08:30:00', status: 'active' },
    { id: 3, name: 'Ahmed Khaled', email: 'ahmed.khaled@safestep.com', role: 'driver', department: 'Transportation', lastLogin: '2024-02-19 07:00:00', status: 'active' },
    { id: 4, name: 'Mohamed Hassan', email: 'mohamed.hassan@email.com', role: 'parent', department: 'N/A', lastLogin: '2024-02-18 20:00:00', status: 'active' },
    { id: 5, name: 'Fatima Ali', email: 'fatima.ali@email.com', role: 'parent', department: 'N/A', lastLogin: '2024-02-18 19:30:00', status: 'active' },
    { id: 6, name: 'John Support', email: 'john.support@safestep.com', role: 'staff', department: 'Customer Service', lastLogin: '2024-02-19 08:00:00', status: 'active' },
    { id: 7, name: 'Omar Tech', email: 'omar.tech@safestep.com', role: 'staff', department: 'IT', lastLogin: '2024-02-18 17:00:00', status: 'inactive' }
];

let accountRecoveryData = [
    {
        id: 1,
        requester: 'Mohamed Hassan',
        role: 'parent',
        issue: 'password',
        currentEmail: 'mohamed.hassan@email.com',
        requestedChange: 'Reset password and send temporary login code',
        phone: '+20 111 234 5678',
        verifiedBy: 'Student ID + registered phone',
        status: 'pending',
        requestedAt: '2026-04-29 09:20'
    },
    {
        id: 2,
        requester: 'Ahmed Khaled',
        role: 'driver',
        issue: 'email',
        currentEmail: 'ahmed.khaled@safestep.com',
        requestedChange: 'Change email to ahmed.driver@safestep.com',
        phone: '+20 111 111 2222',
        verifiedBy: 'License number + admin call',
        status: 'reviewing',
        requestedAt: '2026-04-29 13:45'
    },
    {
        id: 3,
        requester: 'Sarah Manager',
        role: 'staff',
        issue: 'both',
        currentEmail: 'sarah.manager@safestep.com',
        requestedChange: 'Update email and generate temporary password',
        phone: '+20 111 765 4321',
        verifiedBy: 'Employee ID + manager approval',
        status: 'completed',
        requestedAt: '2026-04-28 16:10'
    },
    {
        id: 4,
        requester: 'Fatima Ali',
        role: 'parent',
        issue: 'email',
        currentEmail: 'fatima.ali@email.com',
        requestedChange: 'Change email to fatima.parent@email.com',
        phone: '+20 111 876 5432',
        verifiedBy: 'Registered phone pending confirmation',
        status: 'pending',
        requestedAt: '2026-04-30 10:05'
    }
];

const activityLogsData = [
    { id: 1, timestamp: '2024-02-19 09:15:00', user: 'Admin User', action: 'login', module: 'Authentication', description: 'User logged into admin dashboard', ipAddress: '192.168.1.100' },
    { id: 2, timestamp: '2024-02-19 09:10:00', user: 'Sarah Manager', action: 'update', module: 'Trips', description: 'Updated trip TRP003 schedule', ipAddress: '192.168.1.101' },
    { id: 3, timestamp: '2024-02-19 09:05:00', user: 'Ahmed Khaled', action: 'view', module: 'Live Tracking', description: 'Viewed live tracking dashboard', ipAddress: '192.168.1.102' },
    { id: 4, timestamp: '2024-02-19 08:55:00', user: 'Mohamed Hassan', action: 'create', module: 'Complaints', description: 'Submitted new complaint CMP002', ipAddress: '10.0.0.50' },
    { id: 5, timestamp: '2024-02-19 08:50:00', user: 'Admin User', action: 'delete', module: 'Users', description: 'Removed inactive user account', ipAddress: '192.168.1.100' },
    { id: 6, timestamp: '2024-02-19 08:45:00', user: 'John Support', action: 'update', module: 'Notifications', description: 'Sent emergency notification', ipAddress: '192.168.1.103' },
    { id: 7, timestamp: '2024-02-19 08:30:00', user: 'Fatima Ali', action: 'view', module: 'Students', description: 'Viewed student information', ipAddress: '10.0.0.51' },
    { id: 8, timestamp: '2024-02-18 17:45:00', user: 'Omar Tech', action: 'update', module: 'Settings', description: 'Modified system settings', ipAddress: '192.168.1.104' }
];

const navLinks = document.querySelectorAll('.nav-link:not(.logout)');
const pages = document.querySelectorAll('.page');
const pageTitle = document.getElementById('pageTitle');
//Ø³Ù„ÙŠØ¯Ø± Ù„Ù„Ù…ÙˆØ¨ÙŠÙ„
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');
const themeToggleBtn = document.getElementById('themeToggle');
const globalSearchInput = document.querySelector('.search-box input');
const globalSearchResults = document.getElementById('globalSearchResults');
const THEME_STORAGE_KEY = 'safestep-theme';
const BROADCAST_STORAGE_KEY = 'safestep-broadcasts';
const MOBILE_BREAKPOINT = 768;
const sidebarOverlay = document.createElement('div');
sidebarOverlay.className = 'sidebar-overlay';
document.body.appendChild(sidebarOverlay);

function isMobileView() {
    return window.innerWidth <= MOBILE_BREAKPOINT;
}

function setMenuToggleIcon(isOpen) {
    if (!menuToggle) return;
    const icon = menuToggle.querySelector('i');
    if (!icon) return;
    icon.classList.toggle('fa-bars', !isOpen);
    icon.classList.toggle('fa-times', isOpen);
}

function applyTheme(theme) {
    const isDark = theme === 'dark';
    document.body.classList.toggle('dark-mode', isDark);
    if (!themeToggleBtn) return;
    themeToggleBtn.innerHTML = isDark
        ? '<i class="fas fa-sun"></i><span>Light</span>'
        : '<i class="fas fa-moon"></i><span>Dark</span>';
}

function initThemeToggle() {
    const savedTheme = localStorage.getItem(THEME_STORAGE_KEY) || 'light';
    applyTheme(savedTheme);
    if (!themeToggleBtn) return;

    themeToggleBtn.addEventListener('click', () => {
        const nextTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
        applyTheme(nextTheme);
    });
}

initThemeToggle();

function openAddPage(type) {
    const modal = document.getElementById('addResourceModal');
    const title = document.getElementById('addModalTitle');
    const form = document.getElementById('addResourceForm');
    const body = document.getElementById('addModalBody');
    if (!modal || !form || !body) return;

    const configs = {
        'parent': {
            title: 'Add Parent',
            action: '/api/admin/parents',
            fields: `
                <div class="form-group"><label>Full Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" required></div>
                <div class="form-group"><label>Address</label><input type="text" name="address" class="form-control"></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" minlength="6" required></div>
            `
        },
        'driver': {
            title: 'Add Driver',
            action: '/api/admin/drivers',
            fields: `
                <div class="form-group"><label>Full Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" required></div>
                <div class="form-group"><label>License Number</label><input type="text" name="license_number" class="form-control" required></div>
                <div class="form-group"><label>Years Experience</label><input type="number" name="years_experience" class="form-control" value="0"></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" minlength="6" required></div>
            `
        },
        'bus': {
            title: 'Add Bus',
            action: '/api/admin/buses',
            fields: `
                <div class="form-group"><label>Bus Number</label><input type="text" name="bus_number" class="form-control" required></div>
                <div class="form-group"><label>Plate Number</label><input type="text" name="plate_number" class="form-control" required></div>
                <div class="form-group"><label>Capacity</label><input type="number" name="capacity" class="form-control" value="30"></div>
                <div class="form-group"><label>Driver</label><select name="driver_id" class="form-control"><option value="">Select Driver</option>${driversData.map(d => `<option value="${d.id}">${d.name}</option>`).join('')}</select></div>
            `
        },
        'student': {
            title: 'Add Student',
            action: '/api/admin/students',
            fields: `
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                <div class="form-group"><label>School Name</label><input type="text" name="school_name" class="form-control"></div>
                <div class="form-group"><label>Grade</label><input type="text" name="grade" class="form-control"></div>
                <div class="form-group"><label>Parent</label><select name="parent_id" class="form-control" required><option value="">Select Parent</option>${parentsData.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}</select></div>
            `
        },
        'trip': {
            title: 'Add Trip',
            action: '/api/admin/trips',
            fields: `
                <div class="form-group"><label>Trip Date</label><input type="date" name="trip_date" class="form-control" required></div>
                <div class="form-group"><label>Shift</label><select name="shift" class="form-control"><option value="morning">Morning</option><option value="afternoon">Afternoon</option></select></div>
                <div class="form-group"><label>Bus</label><select name="bus_id" class="form-control" required><option value="">Select Bus</option>${busesData.map(b => `<option value="${b.id}">${b.busNumber || b.bus_number || 'Bus ' + b.id}</option>`).join('')}</select></div>
                <div class="form-group"><label>Driver</label><select name="driver_id" class="form-control" required><option value="">Select Driver</option>${driversData.map(d => `<option value="${d.id}">${d.name}</option>`).join('')}</select></div>
                <div class="form-group"><label>Route</label><select name="bus_route_id" class="form-control" required><option value="">Select Route</option>${busRoutesData.map(route => `<option value="${route.id}">${route.name || 'Route ' + route.id}</option>`).join('')}</select></div>
            `
        },
        'user': {
            title: 'Add User',
            action: '/api/admin/users',
            fields: `
                <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>Role</label><select name="role" class="form-control"><option value="admin">Admin</option><option value="parent">Parent</option><option value="driver">Driver</option></select></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" minlength="6" required></div>
            `
        },
        'complaint': {
            title: 'Add Complaint',
            action: '/api/admin/reports',
            fields: `
                <input type="hidden" name="type" value="complaint">
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Description</label><textarea name="body" class="form-control" rows="4" required></textarea></div>
                <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="open">Open</option><option value="resolved">Resolved</option></select></div>
            `
        },
        'school': {
            title: 'Add School',
            action: '/api/admin/schools',
            fields: `
                <div class="form-group"><label>School Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Address</label><input type="text" name="address" class="form-control"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
                <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
            `
        },
        'financial': {
            title: 'Add Financial Entry',
            action: '/api/admin/financial-entries',
            fields: `
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Type</label><select name="type" class="form-control"><option value="income">Income</option><option value="expense">Expense</option></select></div>
                <div class="form-group"><label>Amount</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                <div class="form-group"><label>Date</label><input type="date" name="entry_date" class="form-control"></div>
            `
        },
        'maintenance': {
            title: 'Add Maintenance Record',
            action: '/api/admin/maintenance-records',
            fields: `
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Bus</label><select name="bus_id" class="form-control"><option value="">Select Bus</option>${busesData.map(b => `<option value="${b.id}">${b.bus_number || b.plate_number || 'Bus #' + b.id}</option>`).join('')}</select></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                <div class="form-group"><label>Cost</label><input type="number" name="cost" class="form-control" step="0.01"></div>
                <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="pending">Pending</option><option value="in_progress">In Progress</option><option value="completed">Completed</option></select></div>
                <div class="form-group"><label>Maintenance Date</label><input type="date" name="maintenance_date" class="form-control"></div>
            `
        }
    };

    const config = configs[type];
    if (!config) return alert('Add form not configured for: ' + type);

    title.textContent = config.title;
    form.action = config.action;
    form.dataset.resourceType = type;
    body.innerHTML = config.fields;
    modal.style.display = 'flex';
}

function normalizeSearchText(value) {
    return String(value || '').toLowerCase().trim();
}

function clearSearchHighlights(root) {
    if (!root) return;
    root.querySelectorAll('mark.search-highlight').forEach(mark => {
        const textNode = document.createTextNode(mark.textContent || '');
        mark.replaceWith(textNode);
    });
}

function highlightSearchMatches(root, queryText) {
    if (!root) return;
    clearSearchHighlights(root);
    const query = String(queryText || '').trim();
    if (!query) return;
    const regex = new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');

    root.querySelectorAll('td, p, span').forEach(el => {
        if (el.children.length) return;
        const text = el.textContent;
        if (!text || !regex.test(text)) return;
        el.innerHTML = text.replace(regex, match => `<mark class="search-highlight">${match}</mark>`);
    });
}

function applyGlobalSearch(queryText = '') {
    const activePage = document.querySelector('.page.active');
    if (!activePage) return;

    const query = normalizeSearchText(queryText);
    const tableRows = activePage.querySelectorAll('tbody tr');
    const hasTableRows = tableRows.length > 0;

    if (hasTableRows) {
        tableRows.forEach(row => {
            const rowText = normalizeSearchText(row.textContent);
            const matches = !query || rowText.includes(query);
            row.dataset.searchHidden = matches ? 'false' : 'true';
        });
        highlightSearchMatches(activePage, queryText);
        return;
    }

    const blockSelectors = [
        '.card',
        '.activity-item',
        '.info-field',
        '.permission-item',
        '.setting-item',
        '.status-item',
        '.schedule-item'
    ];

    const blocks = activePage.querySelectorAll(blockSelectors.join(', '));
    blocks.forEach(block => {
        const blockText = normalizeSearchText(block.textContent);
        block.style.display = !query || blockText.includes(query) ? '' : 'none';
    });

    highlightSearchMatches(activePage, queryText);
}

function reapplyGlobalSearch() {
    applyGlobalSearch(globalSearchInput ? globalSearchInput.value : '');
    applyAllTableEnhancements();
}

function getGlobalSearchMatches(query) {
    const normalized = normalizeSearchText(query);
    if (!normalized) return [];

    const matches = [];

    parentsData.forEach(parent => {
        const text = normalizeSearchText(`${parent.name} ${parent.email} ${parent.phone}`);
        if (text.includes(normalized)) {
            matches.push({ label: parent.name, meta: 'Parent', pageId: 'parents' });
        }
    });

    driversData.forEach(driver => {
        const text = normalizeSearchText(`${driver.name} ${driver.phone} ${driver.license}`);
        if (text.includes(normalized)) {
            matches.push({ label: driver.name, meta: 'Driver', pageId: 'drivers' });
        }
    });

    studentsData.forEach(student => {
        const text = normalizeSearchText(`${student.name} ${student.school} ${student.parent}`);
        if (text.includes(normalized)) {
            matches.push({ label: student.name, meta: 'Student', pageId: 'students' });
        }
    });

    busesData.forEach(bus => {
        const text = normalizeSearchText(`${bus.busNumber} ${bus.plate} ${bus.driver} ${bus.route}`);
        if (text.includes(normalized)) {
            matches.push({ label: bus.busNumber, meta: 'Bus', pageId: 'buses' });
        }
    });

    accountRecoveryData.forEach(request => {
        const text = normalizeSearchText(`${request.requester} ${request.currentEmail} ${request.requestedChange} ${request.phone}`);
        if (text.includes(normalized)) {
            matches.push({ label: request.requester, meta: 'Account Recovery', pageId: 'account-recovery' });
        }
    });

    return matches.slice(0, 6);
}

function renderGlobalSearchResults(query) {
    if (!globalSearchResults) return;
    const results = getGlobalSearchMatches(query);
    if (!query || results.length === 0) {
        globalSearchResults.classList.remove('active');
        globalSearchResults.innerHTML = '';
        return;
    }

    globalSearchResults.innerHTML = results
        .map(item => `
            <div class="search-result-item" data-page="${item.pageId}" data-label="${item.label}">
                <span>${item.label}</span>
                <small>${item.meta}</small>
            </div>
        `)
        .join('');
    globalSearchResults.classList.add('active');
}

document.addEventListener('click', (event) => {
    if (!globalSearchResults) return;
    const resultItem = event.target.closest('.search-result-item');
    if (resultItem) {
        const pageId = resultItem.getAttribute('data-page');
        const label = resultItem.getAttribute('data-label');
        if (pageId) {
            navigateTo(pageId);
            if (globalSearchInput) {
                globalSearchInput.value = label;
                applyGlobalSearch(label);
            }
        }
        globalSearchResults.classList.remove('active');
        return;
    }

    if (!event.target.closest('.search-box')) {
        globalSearchResults.classList.remove('active');
    }
});

function matchesPeriod(dateString, period) {
    if (period === 'all') return true;
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) return false;

    const now = new Date();
    const thisMonth = date.getFullYear() === now.getFullYear() && date.getMonth() === now.getMonth();
    const lastMonthDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    const lastMonth = date.getFullYear() === lastMonthDate.getFullYear() && date.getMonth() === lastMonthDate.getMonth();
    const thisYear = date.getFullYear() === now.getFullYear();

    if (period === 'this_month') return thisMonth;
    if (period === 'last_month') return lastMonth;
    if (period === 'this_year') return thisYear;
    return true;
}

function initSelectFilters() {
    const bindings = [
        { id: 'parentStatusFilter', render: renderParents },
        { id: 'driverStatusFilter', render: renderDrivers },
        { id: 'applicationRoleFilter', render: renderApplications },
        { id: 'applicationStatusFilter', render: renderApplications },
        { id: 'recoveryRoleFilter', render: renderAccountRecovery },
        { id: 'recoveryTypeFilter', render: renderAccountRecovery },
        { id: 'recoveryStatusFilter', render: renderAccountRecovery },
        { id: 'requestTypeFilter', render: renderRequests },
        { id: 'requestStatusFilter', render: renderRequests },
        { id: 'financialTypeFilter', render: renderFinancials },
        { id: 'financialPeriodFilter', render: renderFinancials },
        { id: 'maintenanceBusFilter', render: renderMaintenance },
        { id: 'maintenanceTypeFilter', render: renderMaintenance },
        { id: 'studentSchoolFilter', render: renderStudents },
        { id: 'studentGradeFilter', render: renderStudents },
        { id: 'studentStatusFilter', render: renderStudents },
        { id: 'tripStatusFilter', render: renderTrips },
        { id: 'tripDateFilter', render: renderTrips, event: 'input' },
        { id: 'routeStopsTripFilter', render: renderRouteStops },
        { id: 'tripHistoryFilter', render: renderTripPlayback },
        { id: 'notificationTemplateSelect', render: applySelectedNotificationTemplate },
        { id: 'attendancePickupFilter', render: renderAttendance },
        { id: 'attendanceDropoffFilter', render: renderAttendance },
        { id: 'notificationTypeFilter', render: renderNotifications },
        { id: 'notificationStatusFilter', render: renderNotifications },
        { id: 'emergencyLogDateFilter', render: renderEmergencyLogs, event: 'input' },
        { id: 'emergencyLogBusFilter', render: renderEmergencyLogs },
        { id: 'emergencyLogDriverFilter', render: renderEmergencyLogs },
        { id: 'emergencyLogTypeFilter', render: renderEmergencyLogs },
        { id: 'paymentStatusFilter', render: renderPayments },
        { id: 'paymentPeriodFilter', render: renderPayments },
        { id: 'capacityStatusFilter', render: renderBusCapacity },
        { id: 'emergencyTypeFilter', render: renderEmergencyAlerts },
        { id: 'complaintTypeFilter', render: renderComplaints },
        { id: 'complaintStatusFilter', render: renderComplaints },
        { id: 'schoolTypeFilter', render: renderSchools },
        { id: 'schoolDistrictFilter', render: renderSchools },
        { id: 'userRoleFilter', render: renderUsers },
        { id: 'userStatusFilter', render: renderUsers },
        { id: 'activityActionFilter', render: renderActivityLogs },
        { id: 'activityModuleFilter', render: renderActivityLogs },
        { id: 'activityPeriodFilter', render: renderActivityLogs }
    ];

    bindings.forEach(({ id, render, event }) => {
        const element = document.getElementById(id);
        if (!element) return;
        element.addEventListener(event || 'change', () => {
            render();
            reapplyGlobalSearch();
        });
    });
}

function initGlobalSearch() {
    if (!globalSearchInput) return;
    globalSearchInput.addEventListener('input', (event) => {
        const query = event.target.value;
        applyGlobalSearch(query);
        applyAllTableEnhancements();
        renderGlobalSearchResults(query);
    });
}

initGlobalSearch();

function getFieldErrorEl(field) {
    const wrapper = field.closest('.filter-item, .form-group, .form-control') || field.parentElement;
    if (!wrapper) return null;
    let error = wrapper.querySelector('.error-text');
    if (!error) {
        error = document.createElement('div');
        error.className = 'error-text';
        wrapper.appendChild(error);
    }
    return error;
}

function validateField(field) {
    if (!field) return true;
    const value = String(field.value || '').trim();
    const isRequired = field.hasAttribute('required');
    const isEmail = field.type === 'email' || /email/i.test(field.id || '');
    const isPhone = field.type === 'tel' || /phone/i.test(field.id || '');
    let errorMessage = '';

    if (isRequired && !value) {
        errorMessage = 'This field is required.';
    } else if (isEmail && value) {
        const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        if (!emailOk) errorMessage = 'Enter a valid email.';
    } else if (isPhone && value) {
        const phoneOk = /^[+()0-9\s-]{7,}$/.test(value);
        if (!phoneOk) errorMessage = 'Enter a valid phone number.';
    }

    const errorEl = getFieldErrorEl(field);
    if (errorEl) {
        errorEl.textContent = errorMessage;
    }
    field.classList.toggle('field-error', Boolean(errorMessage));
    return !errorMessage;
}

function validateForm(form) {
    if (!form) return true;
    const fields = Array.from(form.querySelectorAll('input, textarea, select'));
    const results = fields.map(field => validateField(field));
    return results.every(Boolean);
}

function initFormValidation() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (event) => {
            if (!validateForm(form)) {
                event.preventDefault();
                showToast('Please fix form errors before submitting.', 'warning');
            }
        });
        form.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', () => validateField(field));
            field.addEventListener('input', () => validateField(field));
        });
    });
}

initFormValidation();

function updateStatValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

const DASHBOARD_STAT_MAP = [
    ['totalParentsStat', 'total_parents'],
    ['totalDriversStat', 'total_drivers'],
    ['totalStudentsStat', 'total_students'],
    ['totalBusesStat', 'total_buses'],
    ['activeBusesStat', 'active_buses'],
    ['activeTripsStat', 'active_trips'],
    ['todayTripsStat', 'today_trips'],
    ['pendingRequestsStat', 'pending_requests'],
    ['complaintsTodayStat', 'complaints_today']
];

const lastDashboardStats = {};
let dashboardStatsInitialized = false;

function applyDashboardStat(id, next, options = {}) {
    const el = document.getElementById(id);
    if (!el) return;

    const nextNum = Number(next) || 0;
    const prev = lastDashboardStats[id];

    if (!dashboardStatsInitialized || options.force) {
        el.textContent = String(nextNum);
        lastDashboardStats[id] = nextNum;
        return;
    }

    if (nextNum > prev) {
        el.textContent = String(nextNum);
        lastDashboardStats[id] = nextNum;
        const card = el.closest('.stat-card');
        if (card) {
            card.classList.add('stat-increment');
            setTimeout(() => card.classList.remove('stat-increment'), 1200);
        }
        if (typeof options.onIncrease === 'function') {
            options.onIncrease(id, nextNum, prev);
        }
    }
}

function applyDashboardStats(data, options = {}) {
    if (!data) return;
    DASHBOARD_STAT_MAP.forEach(([elementId, key]) => {
        applyDashboardStat(elementId, data[key] ?? 0, options);
    });
    dashboardStatsInitialized = true;
}

function renderAdminRecentActivity(items) {
    const list = document.getElementById('adminRecentActivityList');
    if (!list || !Array.isArray(items)) return;

    if (!items.length) {
        list.innerHTML = '<div class="activity-item"><div class="activity-content"><p style="color:#94a3b8;">No recent activity.</p></div></div>';
        return;
    }

    list.innerHTML = items.map(item => {
        const role = String(item.role || 'other').toLowerCase();
        const iconClass = role === 'parent' ? 'blue' : (role === 'driver' ? 'green' : 'purple');
        const icon = role === 'parent' ? 'user-plus' : (role === 'driver' ? 'id-card' : 'inbox');
        const when = item.created_at ? new Date(item.created_at).toLocaleString() : '';
        const action = item.kind === 'service_request' ? 'requests' : 'applications';
        return `
            <div class="activity-item" data-activity-action="${action}">
                <div class="activity-icon ${iconClass}">
                    <i class="fas fa-${icon}"></i>
                </div>
                <div class="activity-content">
                    <p><strong>${escapeSafeStepHtml(item.title || 'New submission')}</strong> — <span class="status-badge">${escapeSafeStepHtml(item.status || 'pending')}</span></p>
                    <span>${escapeSafeStepHtml(item.subtitle || '')}${when ? ' • ' + when : ''}</span>
                </div>
            </div>
        `;
    }).join('');
}

function updateDashboardStats() {
    updateStatValue('totalParentsStat', parentsData.length);
    updateStatValue('totalDriversStat', driversData.length);
    updateStatValue('totalStudentsStat', studentsData.length);
    updateStatValue('totalBusesStat', busesData.length);

    const activeBuses = busesData.filter(bus => bus.status === 'active').length;
    updateStatValue('activeBusesStat', activeBuses);

    const activeTrips = tripsData.filter(trip => trip.status === 'in-progress').length;
    updateStatValue('activeTripsStat', activeTrips);

    const pendingRequests = getNormalizedRequests().filter(req => req.status === 'new').length;
    updateStatValue('pendingRequestsStat', pendingRequests);

    const complaintsToday = complaintsData.filter(c => c.date === new Date().toISOString().split('T')[0]).length;
    updateStatValue('complaintsTodayStat', complaintsToday);

    const todayTrips = tripsData.filter(trip => trip.status === 'completed' || trip.status === 'in-progress').length;
    updateStatValue('todayTripsStat', todayTrips);
}

function updateNotificationBadge() {
    const badge = document.querySelector('.notification-icon .badge');
    if (!badge) return;
    const count = notificationsData.length;
    badge.textContent = count > 99 ? '99+' : String(count);
}

function playNotificationSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = ctx.createOscillator();
        const gain = ctx.createGain();
        oscillator.type = 'sine';
        oscillator.frequency.value = 880;
        gain.gain.value = 0.05;
        oscillator.connect(gain);
        gain.connect(ctx.destination);
        oscillator.start();
        oscillator.stop(ctx.currentTime + 0.15);
    } catch {
        // Audio not supported
    }
}

function simulateRealtimeUpdates() {
    const activePage = document.querySelector('.page.active');
    if (activePage?.id === 'live-tracking' && typeof loadLiveTrackingFromApi === 'function') {
        loadLiveTrackingFromApi().then(() => renderLiveTracking({ skipReload: true }));
    }

    if (Math.random() > 0.6) {
        notificationsData.unshift({
            id: Date.now(),
            title: 'System Update',
            type: 'general',
            recipients: 'All Users',
            sentDate: new Date().toLocaleString(),
            status: 'sent'
        });
        updateNotificationBadge();
        showToast('New system notification received.', 'info');
        playNotificationSound();
    }

    if (Math.random() > 0.75) {
        const newAlert = {
            id: Date.now(),
            type: 'delay',
            busNumber: 'Bus #42',
            driver: 'Ahmed Khaled',
            location: 'Sporting, Alexandria',
            time: new Date().toLocaleTimeString()
        };
        emergencyAlertsData.unshift(newAlert);
        emergencyLogsData.unshift({
            id: newAlert.id,
            bus: newAlert.busNumber,
            driver: newAlert.driver,
            location: newAlert.location,
            type: newAlert.type,
            time: new Date().toISOString().replace('T', ' ').slice(0, 19),
            notes: 'Auto-generated alert from tracking system.'
        });
    }

    const activePageId = document.querySelector('.page.active')?.id;
    if (activePageId === 'live-tracking') renderLiveTracking();
    if (activePageId === 'notifications') {
        renderNotifications();
        renderNotificationTemplates();
        renderEmergencyAlerts();
    }
    if (activePageId === 'emergency-logs') {
        renderEmergencyLogs();
        renderSmartAlerts();
    }
    if (activePageId === 'students') {
        renderStudents();
        renderAttendanceRealtime();
    }
}

setInterval(simulateRealtimeUpdates, 15000);

const tableStates = new Map();
let tableEnhancementsInitialized = false;

function getTableState(table) {
    if (!tableStates.has(table)) {
        tableStates.set(table, { page: 1, pageSize: 20, sortIndex: -1, sortDir: 'asc' });
    }
    return tableStates.get(table);
}

function ensurePaginationControls(table) {
    const wrapper = table.closest('.table-wrapper');
    if (!wrapper) return null;
    let controls = wrapper.nextElementSibling;
    if (!controls || !controls.classList.contains('table-pagination')) {
        controls = document.createElement('div');
        controls.className = 'table-pagination';
        controls.innerHTML = `
            <button class="btn-secondary btn-compact" type="button" data-page="prev">
                <i class="fas fa-chevron-left"></i> Prev
            </button>
            <span class="page-info">Page 1 of 1</span>
            <button class="btn-secondary btn-compact" type="button" data-page="next">
                Next <i class="fas fa-chevron-right"></i>
            </button>
            <select class="form-control page-size">
                <option value="10">10 / page</option>
                <option value="20">20 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
            </select>
        `;
        wrapper.insertAdjacentElement('afterend', controls);
    }
    controls.setAttribute('data-table-id', table.id || '');
    return controls;
}

function applyTableEnhancements(table) {
    if (!table || !table.tBodies || !table.tBodies[0]) return;
    if (!table.id) {
        table.id = `table-${Math.random().toString(36).slice(2, 8)}`;
    }
    const state = getTableState(table);
    const tbody = table.tBodies[0];
    const headers = Array.from(table.querySelectorAll('thead th'));

    headers.forEach((th, index) => {
        if (th.dataset.noSort === 'true') return;
        if (/actions|reply/i.test(th.textContent || '')) {
            th.dataset.noSort = 'true';
            return;
        }
        th.classList.add('sortable');
        th.classList.toggle('sorted-asc', state.sortIndex === index && state.sortDir === 'asc');
        th.classList.toggle('sorted-desc', state.sortIndex === index && state.sortDir === 'desc');
    });

    const rows = Array.from(tbody.rows);
    if (state.sortIndex >= 0) {
        rows.sort((a, b) => {
            const aText = (a.cells[state.sortIndex]?.textContent || '').trim().toLowerCase();
            const bText = (b.cells[state.sortIndex]?.textContent || '').trim().toLowerCase();
            if (aText === bText) return 0;
            return state.sortDir === 'asc' ? aText.localeCompare(bText) : bText.localeCompare(aText);
        });
        rows.forEach(row => tbody.appendChild(row));
    }

    const visibleRows = rows.filter(row => row.dataset.searchHidden !== 'true');
    const totalPages = Math.max(1, Math.ceil(visibleRows.length / state.pageSize));
    if (state.page > totalPages) state.page = totalPages;

    rows.forEach(row => {
        row.style.display = 'none';
    });

    visibleRows.forEach((row, idx) => {
        const start = (state.page - 1) * state.pageSize;
        const end = state.page * state.pageSize;
        row.style.display = idx >= start && idx < end ? '' : 'none';
    });

    const controls = ensurePaginationControls(table);
    if (controls) {
        const info = controls.querySelector('.page-info');
        const prevBtn = controls.querySelector('[data-page="prev"]');
        const nextBtn = controls.querySelector('[data-page="next"]');
        const pageSizeSelect = controls.querySelector('.page-size');
        if (info) info.textContent = `Page ${state.page} of ${totalPages}`;
        if (prevBtn) prevBtn.disabled = state.page <= 1;
        if (nextBtn) nextBtn.disabled = state.page >= totalPages;
        if (pageSizeSelect) pageSizeSelect.value = String(state.pageSize);
    }
}

function applyAllTableEnhancements() {
    const activePage = document.querySelector('.page.active');
    if (!activePage) return;
    activePage.querySelectorAll('.data-table').forEach(applyTableEnhancements);
}

function initTableEnhancements() {
    if (tableEnhancementsInitialized) return;

    document.addEventListener('click', (event) => {
        const th = event.target.closest('th');
        if (th && th.closest('.data-table') && th.dataset.noSort !== 'true') {
            const table = th.closest('.data-table');
            const headers = Array.from(table.querySelectorAll('thead th'));
            const index = headers.indexOf(th);
            if (index >= 0) {
                const state = getTableState(table);
                state.sortDir = state.sortIndex === index && state.sortDir === 'asc' ? 'desc' : 'asc';
                state.sortIndex = index;
                state.page = 1;
                applyTableEnhancements(table);
            }
        }

        const pageBtn = event.target.closest('.table-pagination button');
        if (pageBtn) {
            const controls = pageBtn.closest('.table-pagination');
            const tableId = controls?.getAttribute('data-table-id');
            const table = tableId ? document.getElementById(tableId) : null;
            if (!table) return;
            const state = getTableState(table);
            const direction = pageBtn.getAttribute('data-page');
            if (direction === 'prev') state.page = Math.max(1, state.page - 1);
            if (direction === 'next') state.page = state.page + 1;
            applyTableEnhancements(table);
        }
    });

    document.addEventListener('change', (event) => {
        const sizeSelect = event.target.closest('.table-pagination .page-size');
        if (!sizeSelect) return;
        const controls = sizeSelect.closest('.table-pagination');
        const tableId = controls?.getAttribute('data-table-id');
        const table = tableId ? document.getElementById(tableId) : null;
        if (!table) return;
        const state = getTableState(table);
        state.pageSize = Number(sizeSelect.value) || 5;
        state.page = 1;
        applyTableEnhancements(table);
    });

    tableEnhancementsInitialized = true;
}

initTableEnhancements();

// Sidebar toggle functionality
const mainContent = document.querySelector('.main-content');

function updateSidebarMargin() {
    if (sidebar.classList.contains('hidden')) {
        mainContent.classList.add('sidebar-hidden');
    } else {
        mainContent.classList.remove('sidebar-hidden');
    }
}

function setSidebarState(isOpen) {
    if (!sidebar) return;

    if (isMobileView()) {
        sidebar.classList.toggle('hidden', !isOpen);
        sidebarOverlay.classList.toggle('active', isOpen);
        document.body.classList.toggle('sidebar-open', isOpen);
    } else {
        sidebar.classList.remove('hidden');
        sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
        isOpen = true;
    }

    updateSidebarMargin();
    if (menuToggle) menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    setMenuToggleIcon(isOpen);
}

navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        
        pages.forEach(page => page.classList.remove('active'));
        
        const pageId = link.getAttribute('data-page');
        const targetPage = document.getElementById(pageId);
        
        if (targetPage) {
            targetPage.classList.add('active');
        } else {
            console.error('Page not found:', pageId);
            const dashboardPage = document.getElementById('dashboard');
            if (dashboardPage) dashboardPage.classList.add('active');
            return;
        }
        
        pageTitle.textContent = link.querySelector('span').textContent;
        
        // Render appropriate table when page becomes active
        if (pageId === 'applications') {
            renderApplications();
            loadApplicationsFromApi();
        } else if (pageId === 'parents') {
            renderParents();
        } else if (pageId === 'drivers') {
            renderDrivers();
        } else if (pageId === 'buses') {
            renderBuses();
        } else if (pageId === 'requests') {
            renderRequests();
            loadRequestsFromApi();
        } else if (pageId === 'school-requests') {
            renderSchoolRequests();
            loadSchoolRequestsFromApi();
        } else if (pageId === 'account-recovery') {
            renderAccountRecovery();
        } else if (pageId === 'financials') {
            renderFinancials();
        } else if (pageId === 'maintenance') {
            renderMaintenance();
        } else if (pageId === 'live-tracking') {
            renderLiveTracking();
            renderTripPlayback();
        } else if (pageId === 'students') {
            renderStudents();
            renderAttendance();
            renderAttendanceRealtime();
        } else if (pageId === 'trips') {
            renderTrips();
            renderAssignmentOverview();
            renderRouteStops();
        } else if (pageId === 'notifications') {
            renderNotifications();
            renderNotificationTemplates();
            renderEmergencyAlerts();
        } else if (pageId === 'complaints') {
            renderComplaints();
        } else if (pageId === 'schools') {
            renderSchools();
        } else if (pageId === 'users') {
            renderUsers();
        } else if (pageId === 'settings') {
            // Settings page doesn't need initial rendering
        } else if (pageId === 'activity-logs') {
            renderActivityLogs();
            initStudentQrTools();
        } else if (pageId === 'emergency-logs') {
            renderEmergencyLogs();
            renderSmartAlerts();
        }
        
        if (isMobileView()) setSidebarState(false);

        applyGlobalSearch(globalSearchInput ? globalSearchInput.value : '');
    });
});

// Logout
document.querySelector('.nav-link.logout').addEventListener('click', (e) => {
    e.preventDefault();
    if (confirm('Ø®Ø´ Ø¬ÙˆØ© ØªØ§Ù†ÙŠ ')) {
        window.location.href = '/logout';
    }
});

// Initialize page state
document.addEventListener('DOMContentLoaded', () => {
    
    // If Add Student was submitted, Admin consumes it and can generate the QR instantly.
    const newlyAddedStudent = consumePendingStudentForAdmin();
    
    // Set dashboard as active by default
    const dashboardLink = document.querySelector('.nav-link[data-page="dashboard"]');
    const dashboardPage = document.getElementById('dashboard');
    
    if (dashboardLink && dashboardPage) {
        dashboardLink.classList.add('active');
        dashboardPage.classList.add('active');
        pageTitle.textContent = 'Dashboard Overview';
    }
    
    // Render all tables initially
    renderParents();
    renderApplications();
    renderDrivers();
    renderAccountRecovery();
    renderRequests();
    renderFinancials();
    renderMaintenance();
    renderLiveTracking();
    renderTripPlayback();
    renderStudents();
    renderAttendance();
    renderAttendanceRealtime();
    renderTrips();
    renderAssignmentOverview();
    renderRouteStops();
    renderNotifications();
    renderNotificationTemplates();
    renderEmergencyAlerts();
    renderEmergencyLogs();
    renderSmartAlerts();
    renderComplaints();
    renderSchools();
    renderUsers();
    renderActivityLogs();
    renderPayments();
    renderBusCapacity();
    updateDashboardStats();
    updateNotificationBadge();
    initSelectFilters();
    initStudentQrTools();

    const initialPage = window.__INITIAL_ADMIN_PAGE || 'dashboard';
    if (initialPage !== 'dashboard') {
        navigateTo(initialPage);
    }

    if (newlyAddedStudent) {
        autoGenerateStudentQrForStudent(newlyAddedStudent);
    }

    reapplyGlobalSearch();
    setSidebarState(!isMobileView());
});

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.contains('hidden');
        setSidebarState(isOpen);
    });
}

sidebarOverlay.addEventListener('click', () => setSidebarState(false));

window.addEventListener('resize', () => {
    setSidebarState(!isMobileView());
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') setSidebarState(false);
});

// Ø§Ù„Ø±Ø³Ø§Ù„Ø© Ø§Ù„ÙŠ Ø¨ØªØ·Ù„Ø¹ Ù…Ù† ÙÙˆÙ‚
function renderParents() {
    const tbody = document.querySelector('#parentsTable tbody');
    if (!tbody) return;

    const statusFilter = document.getElementById('parentStatusFilter')?.value || 'all';
    tbody.innerHTML = '';

    parentsData
        .filter(parent => statusFilter === 'all' || parent.status === statusFilter)
        .forEach(parent => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${parent.name}</strong></td>
            <td>${parent.children}</td>
            <td>${parent.phone}</td>
            <td>${parent.email}</td>
            <td>${new Date(parent.applicationDate).toLocaleDateString()}</td>
            <td>${new Date(parent.joinDate).toLocaleDateString()}</td>
            <td><span class="status-badge ${parent.status}">${parent.status.charAt(0).toUpperCase() + parent.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    ${parent.status === 'pending' || parent.status === 'inactive' ? `
                        <button class="btn btn-success" style="padding: 6px 12px; font-size: 12px;" onclick="approveParent(${parent.id})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="rejectParent(${parent.id})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    ` : ''}
                    <div class="action-icon view" onclick="viewParent(${parent.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editParent(${parent.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteParent(${parent.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function renderPendingDriverApplicantsForParents() {
    const tbody = document.querySelector('#parentsDriversApplicantsTable tbody');
    if (!tbody) return;

    const pendingDrivers = driversData.filter(driver => driver.status === 'pending');
    const pendingCount = document.getElementById('pendingDriversCount');
    if (pendingCount) {
        pendingCount.textContent = `${pendingDrivers.length} Pending`;
    }

    tbody.innerHTML = '';

    pendingDrivers.forEach(driver => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${driver.name}</strong></td>
            <td>${driver.license}</td>
            <td>${driver.phone}</td>
            <td>${new Date(driver.applicationDate).toLocaleDateString()}</td>
            <td>
                <div class="table-actions">
                    <button class="btn btn-success" style="padding: 6px 12px; font-size: 12px;" onclick="approveDriver(${driver.id})">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="rejectDriver(${driver.id})">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <div class="action-icon view" onclick="viewDriver(${driver.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function applicationStatusClass(status) {
    return status || 'pending';
}

function escapeSafeStepHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));
}

function renderApplications() {
    const tbody = document.querySelector('#applicationsTable tbody');
    if (!tbody) return;

    const roleFilter = document.getElementById('applicationRoleFilter')?.value || 'all';
    const statusFilter = document.getElementById('applicationStatusFilter')?.value || 'active';
    const filtered = applicationsData
        .filter(app => roleFilter === 'all' || String(app.role || '').toLowerCase() === roleFilter)
        .filter(app => {
            if (statusFilter === 'all') return true;
            if (statusFilter === 'active') return app.status === 'pending' || app.status === 'reviewed' || !app.status;
            return app.status === statusFilter;
        });

    tbody.innerHTML = '';

    if (!filtered.length) {
        renderEmptyRow(tbody, 6, 'No applications found.');
        return;
    }

    filtered.forEach(app => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${escapeSafeStepHtml(app.full_name || app.name || 'Applicant')}</strong></td>
            <td>${escapeSafeStepHtml(app.email)}</td>
            <td>${escapeSafeStepHtml(String(app.role || '').toLowerCase())}</td>
            <td><span id="admin-app-status-${app.id}" class="status-badge ${applicationStatusClass(app.status)}">${escapeSafeStepHtml(app.status || 'pending')}</span></td>
            <td>${safestepDate(app.created_at)}</td>
            <td>
                <div class="table-actions">
                    <button class="btn btn-info" style="padding:6px 12px;font-size:12px;background:#6366f1;color:#fff;border:none;" onclick="viewApplicationDetails(${app.id})">
                        <i class="fas fa-eye"></i> Details
                    </button>
                    <button class="btn btn-success" style="padding:6px 12px;font-size:12px;" onclick="updateApplicationStatus(${app.id}, 'accepted')">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-danger" style="padding:6px 12px;font-size:12px;" onclick="updateApplicationStatus(${app.id}, 'rejected')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function viewApplicationDetails(id) {
    const app = applicationsData.find(a => Number(a.id) === Number(id));
    if (!app) return;

    document.getElementById('modalAppName').textContent = app.full_name || app.name || 'N/A';
    document.getElementById('modalAppEmail').textContent = app.email || 'N/A';
    document.getElementById('modalAppPhone').textContent = app.phone || 'N/A';
    
    const roleSpan = document.getElementById('modalAppRole');
    roleSpan.textContent = String(app.role || 'N/A');
    roleSpan.className = `status-badge ${app.role ? String(app.role).toLowerCase() : 'pending'}`;

    // Extract clean notes and metadata
    let cleanNotes = app.notes || '';
    let metadata = {};

    if (cleanNotes.includes('meta:')) {
        const parts = cleanNotes.split('meta:');
        cleanNotes = parts[0].trim();
        try {
            metadata = JSON.parse(parts[1]) || {};
        } catch (e) {
            console.error('Failed to parse app metadata JSON:', e);
        }
    }

    document.getElementById('modalAppNotes').textContent = cleanNotes || 'No notes available.';

    // Generate metadata grid
    const grid = document.getElementById('modalAppMetadataGrid');
    grid.innerHTML = '';

    const keys = Object.keys(metadata);
    if (keys.length === 0) {
        grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:var(--text-muted); font-size:13px; padding:12px;">No extra metadata recorded.</div>';
    } else {
        keys.forEach(key => {
            const val = metadata[key];
            const div = document.createElement('div');
            div.style.cssText = 'background:rgba(0,0,0,0.02); border:1px solid var(--input-border, rgba(226,232,240,0.8)); border-radius:10px; padding:10px 12px; display:flex; flex-direction:column; gap:4px;';
            
            const formattedLabel = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            
            div.innerHTML = `
                <span style="font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">${escapeSafeStepHtml(formattedLabel)}</span>
                <span style="font-size:13px; font-weight:700; color:var(--text-secondary); word-break:break-word;">${escapeSafeStepHtml(typeof val === 'object' ? JSON.stringify(val) : val)}</span>
            `;
            grid.appendChild(div);
        });
    }

    const modal = document.getElementById('applicationDetailsModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.offsetHeight; // force reflow
        modal.style.opacity = '1';
        modal.querySelector('.safestep-modal-content').style.transform = 'scale(1)';
    }
}

function closeApplicationDetailsModal() {
    const modal = document.getElementById('applicationDetailsModal');
    if (modal) {
        modal.style.opacity = '0';
        modal.querySelector('.safestep-modal-content').style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}

async function loadApplicationsFromApi() {
    try {
        const response = await safestepApi('/api/admin/applications');
        safestepReplaceArray(applicationsData, response.data || []);
        renderApplications();
        console.log('ADMIN APPLICATIONS API LOADED', applicationsData.length);
    } catch (error) {
        console.warn('Failed to load admin applications:', error.message);
        renderApplications();
    }
}

async function loadParentsFromApi() {
    try {
        const response = await safestepApi('/api/admin/parents?per_page=all');
        safestepReplaceArray(parentsData, (response.data || []).map(mapParentForDashboard));
        renderParents();
        console.log('ADMIN PARENTS API LOADED', parentsData.length);
    } catch (error) {
        console.warn('Failed to load admin parents:', error.message);
        renderParents();
    }
}

async function loadDriversFromApi() {
    try {
        const response = await safestepApi('/api/admin/drivers?per_page=all');
        safestepReplaceArray(driversData, (response.data || []).map(mapDriverForDashboard));
        renderDrivers();
        console.log('ADMIN DRIVERS API LOADED', driversData.length);
    } catch (error) {
        console.warn('Failed to load admin drivers:', error.message);
        renderDrivers();
    }
}

async function updateApplicationStatus(id, status) {
    const buttons = document.querySelectorAll(`button[onclick*="updateApplicationStatus(${id},"]`);
    buttons.forEach(button => {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    });

    try {
        const response = await safestepApi(`/api/admin/applications/${id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
        });

        const updated = response.data;
        const index = applicationsData.findIndex(app => Number(app.id) === Number(id));
        if (index >= 0) applicationsData[index] = updated;

        const badge = document.getElementById(`admin-app-status-${id}`);
        if (badge) {
            badge.textContent = status;
            badge.className = `status-badge ${applicationStatusClass(status)}`;
        }

        renderApplications();
        const role = String(updated?.role || '').toLowerCase();
        const targetPage = role === 'driver' ? 'drivers' : (role === 'parent' ? 'parents' : 'applications');
        if (status === 'accepted') {
            showToast(`تمت الموافقة — راجع قسم ${role === 'driver' ? 'السائقين' : (role === 'parent' ? 'الأهالي' : 'التقديمات')}`, 'success');
            if (typeof hydrateAdminDashboardFromApi === 'function') {
                await hydrateAdminDashboardFromApi();
            }
            if (typeof navigateTo === 'function' && (role === 'driver' || role === 'parent')) {
                navigateTo(targetPage);
            }
        } else {
            showToast('Application updated successfully', 'success');
            if (typeof hydrateAdminDashboardFromApi === 'function') {
                await hydrateAdminDashboardFromApi();
            }
        }
        console.log('ADMIN APPLICATION STATUS UPDATED', id, status);
    } catch (error) {
        showToast('Unable to update application', 'error');
        console.error('Application status update failed:', error);
    } finally {
        buttons.forEach(button => {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Save';
        });
    }
}

function addParent() {
    openAddPage('parent');
}

function viewParent(id) {
    const parent = parentsData.find(p => p.id === id);
    if (parent) {
        alert(`Viewing details for: ${parent.name}\nChildren: ${parent.children}\nPhone: ${parent.phone}\nEmail: ${parent.email}`);
    }
}

function editParent(id) {
    const parent = parentsData.find(p => p.id === id);
    if (parent) {
        alert(`Edit form for ${parent.name} would open here.`);
    }
}

async function deleteParent(id) {
    const parent = parentsData.find(p => p.id === id);
    if (!parent || !confirm(`Are you sure you want to delete ${parent.name}?`)) return;
    try {
        await safestepApi(`/api/admin/parents/${id}`, { method: 'DELETE' });
        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        } else {
            const index = parentsData.findIndex(p => p.id === id);
            if (index >= 0) parentsData.splice(index, 1);
            renderParents();
        }
        showToast('Parent deleted successfully!', 'success');
    } catch (err) {
        showToast('Failed to delete parent.', 'error');
    }
}

// Drivers Data
function renderDrivers() {
    const tbody = document.querySelector('#driversTable tbody');
    if (!tbody) return;

    const statusFilter = document.getElementById('driverStatusFilter')?.value || 'all';
    tbody.innerHTML = '';

    driversData
        .filter(driver => statusFilter === 'all' || driver.status === statusFilter)
        .forEach(driver => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${driver.name}</strong></td>
            <td>${driver.license}</td>
            <td>${driver.phone}</td>
            <td>${new Date(driver.applicationDate).toLocaleDateString()}</td>
            <td>${new Date(driver.joinDate).toLocaleDateString()}</td>
            <td>${driver.bus}</td>
            <td>${driver.experience}</td>
            <td><span class="status-badge ${driver.status}">${driver.status.charAt(0).toUpperCase() + driver.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    ${driver.status === 'pending' ? `
                        <button class="btn btn-success" style="padding: 6px 12px; font-size: 12px;" onclick="approveDriver(${driver.id})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="rejectDriver(${driver.id})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    ` : `
                        <div class="action-icon view" onclick="viewDriver(${driver.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="action-icon edit" onclick="editDriver(${driver.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="action-icon delete" onclick="deleteDriver(${driver.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </div>
                    `}
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    renderPendingDriverApplicantsForParents();
}

function renderFinancials() {
    const tbody = document.querySelector('#financialsTable tbody');
    if (!tbody) {
        console.error('Financials table tbody not found');
        return;
    }
    const typeFilter = document.getElementById('financialTypeFilter')?.value || 'all';
    const periodFilter = document.getElementById('financialPeriodFilter')?.value || 'all';
    const filteredData = financialsData
        .filter(entry => typeFilter === 'all' || entry.type === typeFilter)
        .filter(entry => matchesPeriod(entry.date, periodFilter));

    tbody.innerHTML = '';
    filteredData.forEach(entry => {
        const tr = document.createElement('tr');
        const amountClass = entry.amount >= 0 ? 'positive' : 'negative';
        const amountPrefix = entry.amount >= 0 ? '+' : '';
        tr.innerHTML = `
            <td>${new Date(entry.date).toLocaleDateString()}</td>
            <td><span class="status-badge ${entry.type}">${entry.type.charAt(0).toUpperCase() + entry.type.slice(1)}</span></td>
            <td>${entry.description}</td>
            <td class="${amountClass}">${amountPrefix}EGP ${Math.abs(entry.amount).toLocaleString()}</td>
            <td>${entry.enteredBy}</td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewFinancialEntry(${entry.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editFinancialEntry(${entry.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteFinancialEntry(${entry.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function renderMaintenance() {
    const tbody = document.querySelector('#maintenanceTable tbody');
    if (!tbody) {
        console.error('Maintenance table tbody not found');
        return;
    }
    const busFilterEl = document.getElementById('maintenanceBusFilter');
    const typeFilter = document.getElementById('maintenanceTypeFilter')?.value || 'all';
    const currentBusFilter = busFilterEl?.value || 'all';
    const buses = [...new Set(maintenanceData.map(item => item.busNumber))];
    if (busFilterEl) {
        busFilterEl.innerHTML = ['<option value="all">All Buses</option>']
            .concat(buses.map(bus => `<option value="${bus}">${bus}</option>`))
            .join('');
        busFilterEl.value = buses.includes(currentBusFilter) ? currentBusFilter : 'all';
    }
    const busFilter = busFilterEl?.value || 'all';

    const filteredData = maintenanceData
        .filter(record => busFilter === 'all' || record.busNumber === busFilter)
        .filter(record => typeFilter === 'all' || record.type === typeFilter);

    tbody.innerHTML = '';
    filteredData.forEach(record => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${record.busNumber}</td>
            <td>${record.plateNumber}</td>
            <td><span class="status-badge ${record.type}">${record.type.charAt(0).toUpperCase() + record.type.slice(1)}</span></td>
            <td>${record.description}</td>
            <td>${new Date(record.date).toLocaleDateString()}</td>
            <td>EGP ${record.cost.toLocaleString()}</td>
            <td>${record.technician}</td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewMaintenanceRecord(${record.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editMaintenanceRecord(${record.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteMaintenanceRecord(${record.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

let selectedTrackingBusId = null;
let trackingListenersBound = false;
let trackingMapInstance = null;
let trackingMapMarkers = new Map();
let trackingRouteLine = null;
let trackingStopsLayer = null;

function parseCoordinates(locationText) {
    if (!locationText || typeof locationText !== 'string' || !locationText.includes(',')) return null;
    const [latText, lngText] = locationText.split(',').map(v => v.trim());
    const lat = Number(latText);
    const lng = Number(lngText);
    if (Number.isNaN(lat) || Number.isNaN(lng)) return null;
    return { lat, lng };
}

function setLoadingState(targetId, isLoading) {
    const element = document.getElementById(targetId);
    const wrapper = element?.closest('.table-wrapper') || element?.closest('.tracking-map');
    if (!wrapper) return;
    wrapper.classList.toggle('is-loading', isLoading);
}

function renderEmptyRow(tbody, colspan, message) {
    if (!tbody) return;
    tbody.innerHTML = `
        <tr class="empty-row">
            <td colspan="${colspan}">${message || 'No data available.'}</td>
        </tr>
    `;
}

function initTrackingMap(center) {
    if (!window.L || trackingMapInstance) return;
    const mapContainer = document.getElementById('liveTrackingMap');
    if (!mapContainer) return;
    const mapCenter = center || ALEXANDRIA_MAP_CENTER;
    trackingMapInstance = L.map(mapContainer, { zoomControl: false }).setView(mapCenter, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(trackingMapInstance);
    L.control.zoom({ position: 'bottomright' }).addTo(trackingMapInstance);
}

function buildRouteStops(selectedBus) {
    if (!selectedBus) return [];

    if (Array.isArray(selectedBus.routeStops) && selectedBus.routeStops.length) {
        return selectedBus.routeStops
            .map(stop => ({
                name: stop.name || 'Stop',
                lat: Number(stop.lat),
                lng: Number(stop.lng),
                order: stop.order ?? 0,
            }))
            .filter(stop => !Number.isNaN(stop.lat) && !Number.isNaN(stop.lng))
            .sort((a, b) => a.order - b.order);
    }

    const trip = tripsData.find(item => item.bus === selectedBus.busNumber || item.routeName === selectedBus.route);
    const stops = routeStopsData
        .filter(stop => stop.tripId === trip?.tripId)
        .sort((a, b) => a.order - b.order)
        .map(stop => {
            const coords = parseCoordinates(stop.location);
            return { ...stop, lat: coords?.lat, lng: coords?.lng };
        })
        .filter(stop => typeof stop.lat === 'number' && typeof stop.lng === 'number');

    if (stops.length) return stops;
    if (!selectedBus.lat || !selectedBus.lng) return [];
    return [{ name: 'Current GPS point', lat: selectedBus.lat, lng: selectedBus.lng }];
}

function createBusMapIcon(isSelected) {
    return L.divIcon({
        className: 'bus-map-marker',
        html: `<div class="bus-map-marker-pin ${isSelected ? 'selected' : ''}"><i class="fas fa-bus"></i></div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18],
    });
}

function updateTrackingMap(selectedBus, filteredBuses) {
    if (!selectedBus) return;

    const gpsBuses = filteredBuses.filter(bus => bus.hasGps);
    const mapCenter = selectedBus.hasGps
        ? [selectedBus.lat, selectedBus.lng]
        : (gpsBuses[0] ? [gpsBuses[0].lat, gpsBuses[0].lng] : ALEXANDRIA_MAP_CENTER);

    initTrackingMap(mapCenter);
    if (!trackingMapInstance) return;

    const activeMarkerIds = new Set();

    gpsBuses.forEach(bus => {
        activeMarkerIds.add(bus.id);
        const existing = trackingMapMarkers.get(bus.id);
        const markerLabel = `<strong>${bus.busNumber}</strong><br>${bus.route}<br>${bus.driver}<br>${bus.speed}`;
        const icon = createBusMapIcon(bus.id === selectedBus.id);

        if (existing) {
            existing.setLatLng([bus.lat, bus.lng]).setIcon(icon).bindPopup(markerLabel);
        } else {
            const marker = L.marker([bus.lat, bus.lng], { icon }).addTo(trackingMapInstance).bindPopup(markerLabel);
            trackingMapMarkers.set(bus.id, marker);
        }
    });

    trackingMapMarkers.forEach((marker, busId) => {
        if (!activeMarkerIds.has(busId)) {
            marker.remove();
            trackingMapMarkers.delete(busId);
        }
    });

    if (selectedBus.hasGps) {
        trackingMapInstance.setView([selectedBus.lat, selectedBus.lng], 14, { animate: true });
    } else {
        trackingMapInstance.setView(mapCenter, 12, { animate: true });
    }

    if (trackingRouteLine) {
        trackingRouteLine.remove();
        trackingRouteLine = null;
    }
    if (trackingStopsLayer) {
        trackingStopsLayer.remove();
        trackingStopsLayer = null;
    }

    const stops = buildRouteStops(selectedBus);
    const routeCoords = selectedBus.hasGps
        ? [[selectedBus.lat, selectedBus.lng], ...stops.map(s => [s.lat, s.lng])]
        : stops.map(s => [s.lat, s.lng]);

    if (routeCoords.length >= 2) {
        trackingRouteLine = L.polyline(routeCoords, { color: '#2563eb', weight: 4, dashArray: '6 6' }).addTo(trackingMapInstance);
    }
    trackingStopsLayer = L.layerGroup(stops.map(stop =>
        L.circleMarker([stop.lat, stop.lng], { radius: 6, color: '#10b981', fillColor: '#10b981', fillOpacity: 0.9 })
            .bindPopup(`<strong>${stop.name}</strong>`)
    )).addTo(trackingMapInstance);
}

function formatTrackingTimestamp(value) {
    if (!value) return 'No recent update';
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? String(value) : date.toLocaleString();
}

function buildRegisteredTrackingData() {
    const gpsByBusId = new Map(liveTrackingApiData.map(item => [item.bus_id, item]));

    const source = busesData.length
        ? busesData
        : liveTrackingApiData.map(item => ({
            id: item.bus_id,
            busNumber: item.bus_number,
            driver: item.driver || 'Unassigned',
            route: item.route || 'No Route Assigned',
        }));

    return source.map(bus => {
        const gps = gpsByBusId.get(bus.id);
        const lat = gps?.latitude ?? null;
        const lng = gps?.longitude ?? null;
        const hasGps = typeof lat === 'number' && typeof lng === 'number';
        const routeName = gps?.route || (bus.route && bus.route !== 'Unassigned' ? bus.route : 'No Route Assigned');

        return {
            id: bus.id,
            busNumber: bus.busNumber || gps?.bus_number || `Bus #${bus.id}`,
            route: routeName,
            currentLocation: hasGps ? `${lat.toFixed(5)}, ${lng.toFixed(5)}` : 'No GPS signal',
            speed: typeof gps?.speed === 'number' ? `${gps.speed} km/h` : 'N/A',
            status: gps?.status || 'inactive',
            lastUpdate: formatTrackingTimestamp(gps?.last_update),
            driver: gps?.driver || bus.driver || 'Unassigned',
            lat,
            lng,
            hasGps,
            routeStops: gps?.route_stops || [],
            tripId: gps?.trip_id || null,
        };
    });
}

async function loadLiveTrackingFromApi() {
    try {
        const response = await safestepApi('/api/admin/tracking/live');
        liveTrackingApiData = response.data || [];
        return liveTrackingApiData;
    } catch (error) {
        console.warn('Live tracking API skipped:', error.message);
        return liveTrackingApiData;
    }
}

function startLiveTrackingPoll() {
    if (liveTrackingPollTimer) return;
    liveTrackingPollTimer = setInterval(async () => {
        const activePage = document.querySelector('.page.active');
        if (!activePage || activePage.id !== 'live-tracking') {
            stopLiveTrackingPoll();
            return;
        }
        await loadLiveTrackingFromApi();
        renderLiveTracking({ skipReload: true });
    }, 8000);
}

function stopLiveTrackingPoll() {
    if (!liveTrackingPollTimer) return;
    clearInterval(liveTrackingPollTimer);
    liveTrackingPollTimer = null;
}

function getTrackingFilters() {
    const busFilter = document.getElementById('trackingBusFilter');
    const routeFilter = document.getElementById('trackingRouteFilter');
    return {
        bus: busFilter ? busFilter.value : 'all',
        route: routeFilter ? routeFilter.value : 'all'
    };
}

function populateTrackingFilters(data) {
    const busFilter = document.getElementById('trackingBusFilter');
    const routeFilter = document.getElementById('trackingRouteFilter');
    if (!busFilter || !routeFilter) return;

    const currentBus = busFilter.value || 'all';
    const currentRoute = routeFilter.value || 'all';

    const busOptions = ['<option value="all">All Buses</option>']
        .concat(data.map(bus => `<option value="${bus.busNumber}">${bus.busNumber}</option>`));
    busFilter.innerHTML = busOptions.join('');

    const routes = [...new Set(data.map(bus => bus.route).filter(Boolean))];
    const routeOptions = ['<option value="all">All Routes</option>']
        .concat(routes.map(route => `<option value="${route}">${route}</option>`));
    routeFilter.innerHTML = routeOptions.join('');

    busFilter.value = [...busFilter.options].some(opt => opt.value === currentBus) ? currentBus : 'all';
    routeFilter.value = [...routeFilter.options].some(opt => opt.value === currentRoute) ? currentRoute : 'all';

    if (!trackingListenersBound) {
        busFilter.addEventListener('change', renderLiveTracking);
        routeFilter.addEventListener('change', renderLiveTracking);
        trackingListenersBound = true;
    }
}

function renderTrackingMapPanel(filteredBuses) {
    const mapContent = document.getElementById('trackingMapContent');
    if (!mapContent) return;

    if (filteredBuses.length === 0) {
        if (trackingMapInstance) {
            trackingMapInstance.remove();
            trackingMapInstance = null;
            trackingMapMarkers.clear();
        }
        mapContent.innerHTML = '<div class="tracking-empty"><p>No buses match current filters.</p></div>';
        return;
    }

    if (trackingMapInstance) {
        trackingMapInstance.remove();
        trackingMapInstance = null;
        trackingMapMarkers.clear();
    }

    const selectedBus = filteredBuses.find(bus => bus.id === selectedTrackingBusId) || filteredBuses[0];
    selectedTrackingBusId = selectedBus.id;

    const busButtons = filteredBuses.map(bus => `
        <button class="tracking-bus-chip ${bus.id === selectedBus.id ? 'active' : ''}" type="button" onclick="viewBusLocation(${bus.id})">
            <strong>${bus.busNumber}</strong>
            <small>${bus.route}</small>
        </button>
    `).join('');

    const statusText = selectedBus.status.charAt(0).toUpperCase() + selectedBus.status.slice(1);
    const mapLink = selectedBus.hasGps
        ? `https://www.google.com/maps?q=${selectedBus.lat},${selectedBus.lng}`
        : '#';
    const stops = buildRouteStops(selectedBus);
    const stopsMarkup = stops.length
        ? stops.map(stop => `<span class="tracking-stop">${stop.name}</span>`).join('')
        : '<span class="tracking-stop">No stops available</span>';

    mapContent.innerHTML = `
        <div class="tracking-bus-list">
            ${busButtons}
        </div>
        <div class="tracking-details">
            <div class="tracking-map-canvas" id="liveTrackingMap"></div>
            <div class="tracking-stops">${stopsMarkup}</div>
            <div class="tracking-route-hint">
                Alexandria live fleet map — ${selectedBus.route}
            </div>
            <div class="tracking-gps-card">
                <div class="tracking-gps-header">
                    <h3>${selectedBus.busNumber} GPS</h3>
                    <span class="status-badge ${selectedBus.status}">${statusText}</span>
                </div>
                <div class="tracking-gps-meta">
                    <div class="meta-item"><label>Route</label><strong>${selectedBus.route}</strong></div>
                    <div class="meta-item"><label>Driver</label><strong>${selectedBus.driver}</strong></div>
                    <div class="meta-item"><label>Current Coordinates</label><strong>${selectedBus.currentLocation}</strong></div>
                    <div class="meta-item"><label>Speed</label><strong>${selectedBus.speed}</strong></div>
                    <div class="meta-item"><label>Last Update</label><strong>${selectedBus.lastUpdate}</strong></div>
                    <div class="meta-item"><label>GPS Signal</label><strong>${selectedBus.hasGps ? 'Available' : 'No Signal'}</strong></div>
                </div>
                <div class="tracking-gps-actions">
                    <button class="btn-secondary" type="button" onclick="trackBus(${selectedBus.id})">
                        <i class="fas fa-route"></i> Track This Bus
                    </button>
                    <a class="btn-primary ${selectedBus.hasGps ? '' : 'disabled'}" href="${mapLink}" ${selectedBus.hasGps ? 'target=\"_blank\" rel=\"noopener noreferrer\"' : 'onclick=\"return false;\"'}>
                        <i class="fas fa-map-marked-alt"></i> Open GPS Map
                    </a>
                </div>
            </div>
        </div>
    `;

    setTimeout(() => updateTrackingMap(selectedBus, filteredBuses), 0);
}

async function renderLiveTracking(options = {}) {
    const tbody = document.querySelector('#trackingTable tbody');
    if (!tbody) {
        console.error('Tracking table tbody not found');
        return;
    }
    setLoadingState('trackingTable', true);
    setLoadingState('trackingMapContent', true);

    if (!options.skipReload) {
        await loadLiveTrackingFromApi();
        startLiveTrackingPoll();
    }

    const allBuses = buildRegisteredTrackingData();
    populateTrackingFilters(allBuses);

    const { bus, route } = getTrackingFilters();
    const filteredBuses = allBuses.filter(item => {
        const busMatch = bus === 'all' || item.busNumber === bus;
        const routeMatch = route === 'all' || item.route === route;
        return busMatch && routeMatch;
    });

    if (!selectedTrackingBusId && filteredBuses.length > 0) {
        selectedTrackingBusId = filteredBuses[0].id;
    }
    if (selectedTrackingBusId && !filteredBuses.some(busItem => busItem.id === selectedTrackingBusId)) {
        selectedTrackingBusId = filteredBuses.length > 0 ? filteredBuses[0].id : null;
    }

    tbody.innerHTML = '';
    if (filteredBuses.length === 0) {
        renderEmptyRow(tbody, 7, 'No buses match current filters.');
    }
    filteredBuses.forEach(busItem => {
        const statusText = busItem.status.charAt(0).toUpperCase() + busItem.status.slice(1);
        const tr = document.createElement('tr');
        if (busItem.id === selectedTrackingBusId) {
            tr.classList.add('selected');
        }
        tr.style.cursor = 'pointer';
        tr.onclick = () => viewBusLocation(busItem.id);
        tr.innerHTML = `
            <td><strong>${busItem.busNumber}</strong></td>
            <td>${busItem.route}</td>
            <td>${busItem.currentLocation}</td>
            <td>${busItem.speed}</td>
            <td><span class="status-badge ${busItem.status}">${statusText}</span></td>
            <td>${busItem.lastUpdate}</td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="event.stopPropagation(); viewBusLocation(${busItem.id})" title="View GPS">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="action-icon edit" onclick="event.stopPropagation(); trackBus(${busItem.id})" title="Track">
                        <i class="fas fa-route"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    renderTrackingMapPanel(filteredBuses);
    reapplyGlobalSearch();
    setTimeout(() => {
        setLoadingState('trackingTable', false);
        setLoadingState('trackingMapContent', false);
    }, 300);
}

function populateTripHistoryFilter() {
    const filter = document.getElementById('tripHistoryFilter');
    if (!filter) return;
    const current = filter.value || '';
    const options = ['<option value="">Select trip</option>'].concat(
        tripHistoryData.map(history => {
            const trip = tripsData.find(item => item.tripId === history.tripId);
            return `<option value="${history.id}">${history.tripId} - ${trip?.routeName || 'Route history'}</option>`;
        })
    );
    filter.innerHTML = options.join('');
    filter.value = [...filter.options].some(option => option.value === current) ? current : String(tripHistoryData[0]?.id || '');
}

function renderTripPlayback(activePointIndex = -1) {
    const map = document.getElementById('tripPlaybackMap');
    const details = document.getElementById('tripPlaybackDetails');
    if (!map || !details) return;

    populateTripHistoryFilter();
    const selectedId = Number(document.getElementById('tripHistoryFilter')?.value || tripHistoryData[0]?.id);
    const history = tripHistoryData.find(item => item.id === selectedId);
    if (!history) {
        map.innerHTML = '<div class="tracking-empty"><p>No trip history available.</p></div>';
        details.innerHTML = '';
        return;
    }

    const trip = tripsData.find(item => item.tripId === history.tripId);
    const stops = routeStopsData.filter(stop => stop.tripId === history.tripId).sort((a, b) => a.order - b.order);
    const points = history.points.map((point, index) => {
        const coords = parseCoordinates(point);
        return { index, label: stops[index]?.name || `Point ${index + 1}`, coords };
    });

    map.innerHTML = `
        <div class="playback-route">
            ${points.map(point => `
                <div class="playback-point ${point.index === activePointIndex ? 'active' : ''}">
                    <span>${point.index + 1}</span>
                    <strong>${point.label}</strong>
                    <small>${point.coords ? `${point.coords.lat.toFixed(4)}, ${point.coords.lng.toFixed(4)}` : 'No coordinates'}</small>
                </div>
            `).join('')}
        </div>
    `;

    details.innerHTML = `
        <div class="tracking-gps-card">
            <div class="tracking-gps-header">
                <h3>${history.tripId}</h3>
                <span class="status-badge completed">Completed</span>
            </div>
            <div class="tracking-gps-meta">
                <div class="meta-item"><label>Route</label><strong>${trip?.routeName || 'Unknown route'}</strong></div>
                <div class="meta-item"><label>Bus</label><strong>${trip?.bus || 'N/A'}</strong></div>
                <div class="meta-item"><label>Driver</label><strong>${trip?.driver || 'N/A'}</strong></div>
                <div class="meta-item"><label>Completed At</label><strong>${history.completedAt}</strong></div>
                <div class="meta-item"><label>Distance</label><strong>${history.distanceKm} km</strong></div>
                <div class="meta-item"><label>Average Speed</label><strong>${history.averageSpeed}</strong></div>
            </div>
        </div>
    `;
}

function replaySelectedTrip() {
    const selectedId = Number(document.getElementById('tripHistoryFilter')?.value || tripHistoryData[0]?.id);
    const history = tripHistoryData.find(item => item.id === selectedId);
    if (!history) return;

    let index = 0;
    renderTripPlayback(index);
    const interval = setInterval(() => {
        index += 1;
        renderTripPlayback(index);
        if (index >= history.points.length - 1) {
            clearInterval(interval);
            showToast(`Replay completed for ${history.tripId}.`, 'success');
        }
    }, 800);
}

function renderStudents() {
    const tbody = document.querySelector('#studentsTable tbody');
    if (!tbody) {
        console.error('Students table tbody not found');
        return;
    }
    syncAttendanceEvents();
    const schoolFilterEl = document.getElementById('studentSchoolFilter');
    const gradeFilter = document.getElementById('studentGradeFilter')?.value || 'all';
    const statusFilter = document.getElementById('studentStatusFilter')?.value || 'all';
    const currentSchoolFilter = schoolFilterEl?.value || 'all';
    const schools = [...new Set(studentsData.map(item => item.school))];
    if (schoolFilterEl) {
        schoolFilterEl.innerHTML = ['<option value="all">All Schools</option>']
            .concat(schools.map(school => `<option value="${school}">${school}</option>`))
            .join('');
        schoolFilterEl.value = schools.includes(currentSchoolFilter) ? currentSchoolFilter : 'all';
    }
    const schoolFilter = schoolFilterEl?.value || 'all';

    const filteredData = studentsData
        .filter(student => schoolFilter === 'all' || student.school === schoolFilter)
        .filter(student => {
            if (gradeFilter === 'all') return true;
            if (gradeFilter === 'kindergarten') return student.grade.toLowerCase().includes('kindergarten');
            const gradeMatch = student.grade.match(/\d+/);
            return gradeMatch ? gradeMatch[0] === gradeFilter : false;
        })
        .filter(student => statusFilter === 'all' || student.status === statusFilter);

    tbody.innerHTML = '';
    filteredData.forEach((student, index) => {
        const trip = getStudentTripAssignment(student, index);
        const attendance = getStudentAttendance(student.name);
        const attendanceLabel = attendance
            ? `${attendance.pickupStatus === 'picked' ? 'Checked in' : attendance.pickupStatus === 'missed' ? 'Missed' : 'Pending'} / ${attendance.dropoffStatus === 'dropped' ? 'Checked out' : attendance.dropoffStatus === 'missed' ? 'Missed' : 'Pending'}`
            : 'No record';
        const attendanceClass = attendance?.pickupStatus === 'missed' || attendance?.dropoffStatus === 'missed'
            ? 'cancelled'
            : attendance?.dropoffStatus === 'dropped'
                ? 'completed'
                : 'pending';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${student.studentId}</td>
            <td><strong>${student.name}</strong></td>
            <td>${student.grade}</td>
            <td>${student.school}</td>
            <td>${student.parent}</td>
            <td>${student.pickupLocation}</td>
            <td>${student.dropoffLocation}</td>
            <td>${trip ? `${trip.tripId}<br><small>${trip.routeName}</small>` : 'Unassigned'}</td>
            <td><span class="status-badge ${attendanceClass}">${attendanceLabel}</span></td>
            <td><span class="status-badge ${student.status}">${student.status.charAt(0).toUpperCase() + student.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewStudent(${student.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editStudent(${student.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteStudent(${student.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function getStudentTripAssignment(student, index = 0) {
    const attendance = getStudentAttendance(student.name);
    if (attendance?.tripId) return tripsData.find(trip => trip.tripId === attendance.tripId);
    const activeTrips = tripsData.filter(trip => trip.status !== 'cancelled');
    return activeTrips[index % activeTrips.length] || null;
}

function getStudentAttendance(studentName) {
    const realtime = attendanceEventsData.find(item => item.student === studentName);
    if (realtime) {
        return {
            tripId: realtime.tripId || getTripByBus(realtime.bus)?.tripId,
            pickupStatus: realtime.pickupStatus,
            dropoffStatus: realtime.dropoffStatus
        };
    }
    return attendanceData.find(item => item.studentName === studentName);
}

function getTripByBus(busNumber) {
    return tripsData.find(trip => trip.bus === busNumber);
}

function renderTrips() {
    const tbody = document.querySelector('#tripsTable tbody');
    if (!tbody) {
        console.error('Trips table tbody not found');
        return;
    }
    const statusFilter = document.getElementById('tripStatusFilter')?.value || 'all';
    const dateFilter = document.getElementById('tripDateFilter')?.value || '';
    const filteredData = tripsData
        .filter(trip => statusFilter === 'all' || trip.status === statusFilter)
        .filter(trip => !dateFilter || !trip.date || trip.date === dateFilter);

    tbody.innerHTML = '';
    filteredData.forEach(trip => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${trip.tripId}</td>
            <td>${trip.routeName}</td>
            <td>${trip.bus}</td>
            <td>${trip.driver}</td>
            <td>${trip.startTime}</td>
            <td>${trip.endTime}</td>
            <td><span class="badge">${trip.students} students</span></td>
            <td><span class="status-badge ${trip.status}">${trip.status.charAt(0).toUpperCase() + trip.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewTrip(${trip.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editTrip(${trip.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteTrip(${trip.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    renderAssignmentOverview();
    renderRouteStops();
}

function renderAssignmentOverview() {
    const container = document.getElementById('assignmentOverview');
    populateAssignmentControls();
    if (!container) return;

    container.innerHTML = tripsData.map(trip => {
        const bus = busesData.find(item => item.busNumber === trip.bus);
        const driver = driversData.find(item => item.name === trip.driver);
        const assignmentOk = Boolean(bus && driver && bus.driver === trip.driver);
        return `
            <div class="assignment-card ${assignmentOk ? 'is-complete' : 'needs-review'}">
                <div>
                    <strong>${trip.tripId}</strong>
                    <span>${trip.routeName}</span>
                </div>
                <div class="assignment-chain">
                    <span><i class="fas fa-user-tie"></i> ${driver?.name || trip.driver || 'No driver'}</span>
                    <span><i class="fas fa-bus"></i> ${bus?.busNumber || trip.bus || 'No bus'}</span>
                    <span><i class="fas fa-route"></i> ${trip.startTime} - ${trip.endTime}</span>
                </div>
                <span class="status-badge ${assignmentOk ? 'completed' : 'pending'}">${assignmentOk ? 'Assigned' : 'Review'}</span>
            </div>
        `;
    }).join('');
}

function populateAssignmentControls() {
    const tripSelect = document.getElementById('assignmentTripSelect');
    const busSelect = document.getElementById('assignmentBusSelect');
    const driverSelect = document.getElementById('assignmentDriverSelect');
    if (!tripSelect || !busSelect || !driverSelect) return;

    const currentTrip = tripSelect.value || '';
    const currentBus = busSelect.value || '';
    const currentDriver = driverSelect.value || '';

    tripSelect.innerHTML = tripsData.map(trip => `<option value="${trip.tripId}">${trip.tripId} - ${trip.routeName}</option>`).join('');
    busSelect.innerHTML = busesData.map(bus => `<option value="${bus.busNumber}">${bus.busNumber} - ${bus.plate}</option>`).join('');
    driverSelect.innerHTML = driversData
        .filter(driver => driver.status === 'active')
        .map(driver => `<option value="${driver.name}">${driver.name}</option>`)
        .join('');

    tripSelect.value = [...tripSelect.options].some(option => option.value === currentTrip) ? currentTrip : tripsData[0]?.tripId || '';
    busSelect.value = [...busSelect.options].some(option => option.value === currentBus) ? currentBus : busesData[0]?.busNumber || '';
    driverSelect.value = [...driverSelect.options].some(option => option.value === currentDriver) ? currentDriver : driversData.find(driver => driver.status === 'active')?.name || '';
}

function applyTripAssignment() {
    const tripId = document.getElementById('assignmentTripSelect')?.value;
    const busNumber = document.getElementById('assignmentBusSelect')?.value;
    const driverName = document.getElementById('assignmentDriverSelect')?.value;
    const trip = tripsData.find(item => item.tripId === tripId);
    const bus = busesData.find(item => item.busNumber === busNumber);
    const driver = driversData.find(item => item.name === driverName);

    if (!trip || !bus || !driver) {
        showToast('Select a valid trip, bus, and driver.', 'warning');
        return;
    }

    trip.bus = bus.busNumber;
    trip.driver = driver.name;
    bus.driver = driver.name;
    bus.route = trip.routeName;
    driver.bus = bus.busNumber;

    renderTrips();
    renderBuses();
    renderDrivers();
    renderLiveTracking();
    showToast(`${driver.name} assigned to ${bus.busNumber} for ${trip.tripId}.`, 'success');
}

function populateRouteStopsTripFilter() {
    const filter = document.getElementById('routeStopsTripFilter');
    if (!filter) return;
    const current = filter.value || '';
    filter.innerHTML = ['<option value="">Select trip</option>']
        .concat(tripsData.map(trip => `<option value="${trip.tripId}">${trip.tripId} - ${trip.routeName}</option>`))
        .join('');
    filter.value = [...filter.options].some(option => option.value === current) ? current : String(tripsData[0]?.tripId || '');
}

function renderRouteStops() {
    const tbody = document.getElementById('routeStopsBody');
    if (!tbody) return;
    populateRouteStopsTripFilter();

    const tripId = document.getElementById('routeStopsTripFilter')?.value || tripsData[0]?.tripId;
    const stops = routeStopsData.filter(stop => stop.tripId === tripId).sort((a, b) => a.order - b.order);

    tbody.innerHTML = '';
    if (stops.length === 0) {
        renderEmptyRow(tbody, 5, 'No stops configured for this trip.');
        return;
    }

    stops.forEach(stop => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${stop.order}</td>
            <td><strong>${stop.name}</strong></td>
            <td>${stop.location}</td>
            <td>${stop.expectedArrival}</td>
            <td>
                <div class="table-actions">
                    <button class="btn-secondary btn-compact" type="button" onclick="moveRouteStop(${stop.id}, -1)" title="Move up">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button class="btn-secondary btn-compact" type="button" onclick="moveRouteStop(${stop.id}, 1)" title="Move down">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function addStopToSelectedTrip() {
    const tripId = document.getElementById('routeStopsTripFilter')?.value;
    const nameInput = document.getElementById('stopNameInput');
    const locationInput = document.getElementById('stopLocationInput');
    const arrivalInput = document.getElementById('stopArrivalInput');
    const name = nameInput?.value.trim();
    const location = locationInput?.value.trim();
    const expectedArrival = arrivalInput?.value;

    if (!tripId || !name || !location || !expectedArrival) {
        showToast('Select a trip and complete stop details.', 'warning');
        return;
    }

    const sameTripStops = routeStopsData.filter(stop => stop.tripId === tripId);
    routeStopsData.push({
        id: Date.now(),
        tripId,
        order: sameTripStops.length + 1,
        name,
        location,
        expectedArrival
    });

    if (nameInput) nameInput.value = '';
    if (locationInput) locationInput.value = '';
    if (arrivalInput) arrivalInput.value = '';
    renderRouteStops();
    renderLiveTracking();
    showToast('Route stop added.', 'success');
}

function moveRouteStop(stopId, direction) {
    const stop = routeStopsData.find(item => item.id === stopId);
    if (!stop) return;
    const stops = routeStopsData.filter(item => item.tripId === stop.tripId).sort((a, b) => a.order - b.order);
    const currentIndex = stops.findIndex(item => item.id === stopId);
    const next = stops[currentIndex + direction];
    if (!next) return;
    const oldOrder = stop.order;
    stop.order = next.order;
    next.order = oldOrder;
    renderRouteStops();
}

function renderNotifications() {
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) {
        console.error('Notifications table tbody not found');
        return;
    }
    setLoadingState('notificationsTable', true);
    const typeFilter = document.getElementById('notificationTypeFilter')?.value || 'all';
    const statusFilter = document.getElementById('notificationStatusFilter')?.value || 'all';
    const filteredData = notificationsData
        .filter(notification => typeFilter === 'all' || notification.type === typeFilter)
        .filter(notification => statusFilter === 'all' || notification.status === statusFilter);

    tbody.innerHTML = '';
    if (filteredData.length === 0) {
        renderEmptyRow(tbody, 7, 'No notifications found.');
    }
    filteredData.forEach(notification => {
        const tr = document.createElement('tr');
        const replyMessage = notification.incomingMessage || notification.message || notification.title || 'Message';
        const replyCell = `
            <div class="reply-cell">
                <div class="reply-message">${replyMessage}</div>
                <textarea class="form-control reply-input" id="reply-${notification.id}" rows="2" placeholder="Write reply..."></textarea>
                <div class="reply-actions">
                    <button class="btn-secondary btn-reply-clear" type="button" data-id="${notification.id}">Clear</button>
                    <button class="btn-primary btn-reply-send" type="button" data-id="${notification.id}">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                    <span class="reply-status" id="reply-status-${notification.id}" aria-live="polite"></span>
                </div>
            </div>
        `;
        tr.innerHTML = `
            <td><strong>${notification.title}</strong></td>
            <td><span class="status-badge ${notification.type}">${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}</span></td>
            <td>${notification.recipients}</td>
            <td>${notification.sentDate}</td>
            <td><span class="status-badge ${notification.status}">${notification.status.charAt(0).toUpperCase() + notification.status.slice(1)}</span></td>
            <td>${replyCell}</td>
            <td>
                <div class="table-actions">
                    <button class="btn-secondary btn-compact" type="button" onclick="viewNotification(${notification.id})" title="View Details">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn-primary btn-compact" type="button" onclick="resendNotification(${notification.id})" title="Resend">
                        <i class="fas fa-paper-plane"></i> Resend
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    initNotificationReplyActions();
    setTimeout(() => setLoadingState('notificationsTable', false), 300);
}

function renderNotificationTemplates() {
    const grid = document.getElementById('notificationTemplatesGrid');
    const select = document.getElementById('notificationTemplateSelect');

    if (select) {
        const current = select.value || '';
        select.innerHTML = ['<option value="">Custom message</option>']
            .concat(notificationTemplatesData.map(template => `<option value="${template.id}">${template.title}</option>`))
            .join('');
        select.value = notificationTemplatesData.some(template => template.id === current) ? current : '';
    }

    if (!grid) return;
    grid.innerHTML = notificationTemplatesData.map(template => `
        <div class="template-card">
            <div class="template-card-header">
                <strong>${template.title}</strong>
                <span class="status-badge ${template.type}">${template.type}</span>
            </div>
            <p>${template.message}</p>
            <button class="btn-secondary btn-compact" type="button" onclick="useNotificationTemplate('${template.id}')">
                <i class="fas fa-wand-magic-sparkles"></i> Use Template
            </button>
        </div>
    `).join('');
}

function applySelectedNotificationTemplate() {
    const selected = document.getElementById('notificationTemplateSelect')?.value;
    if (selected) useNotificationTemplate(selected);
}

function useNotificationTemplate(templateId) {
    const template = notificationTemplatesData.find(item => item.id === templateId);
    if (!template) return;
    const title = document.getElementById('broadcastTitle');
    const type = document.getElementById('broadcastType');
    const message = document.getElementById('broadcastMessage');
    const toParents = document.getElementById('broadcastToParents');
    const toDrivers = document.getElementById('broadcastToDrivers');
    const select = document.getElementById('notificationTemplateSelect');

    if (title) title.value = template.title;
    if (type) type.value = template.type;
    if (message) message.value = template.message;
    if (toParents) toParents.checked = /parent/i.test(template.recipients);
    if (toDrivers) toDrivers.checked = /driver|all/i.test(template.recipients);
    if (select) select.value = template.id;
    focusBroadcastForm();
}

function formatAttendanceBadge(status, type) {
    if (status === 'picked' || status === 'dropped') {
        return `<span class="status-badge completed">${type}</span>`;
    }
    if (status === 'missed') {
        return `<span class="status-badge cancelled">${type}</span>`;
    }
    return `<span class="status-badge pending">${type}</span>`;
}

function renderAttendance() {
    const tbody = document.querySelector('#attendanceTable tbody');
    if (!tbody) return;
    setLoadingState('attendanceTable', true);
    const pickupFilter = document.getElementById('attendancePickupFilter')?.value || 'all';
    const dropoffFilter = document.getElementById('attendanceDropoffFilter')?.value || 'all';

    const filtered = attendanceData
        .filter(row => pickupFilter === 'all' || row.pickupStatus === pickupFilter)
        .filter(row => dropoffFilter === 'all' || row.dropoffStatus === dropoffFilter);

    tbody.innerHTML = '';
    if (filtered.length === 0) {
        renderEmptyRow(tbody, 6, 'No attendance records available.');
    }
    filtered.forEach(row => {
        const pickupLabel = row.pickupStatus === 'picked' ? 'Picked Up' : row.pickupStatus === 'missed' ? 'Missed' : 'Pending';
        const dropoffLabel = row.dropoffStatus === 'dropped' ? 'Dropped' : row.dropoffStatus === 'missed' ? 'Missed' : 'Pending';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${row.studentName}</strong></td>
            <td>${row.tripId || getTripByBus(row.busNumber)?.tripId || 'N/A'}</td>
            <td>${row.busNumber}</td>
            <td>${formatAttendanceBadge(row.pickupStatus, pickupLabel)}</td>
            <td>${formatAttendanceBadge(row.dropoffStatus, dropoffLabel)}</td>
            <td>${row.time}</td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    setTimeout(() => setLoadingState('attendanceTable', false), 300);
}

function renderPayments() {
    const tbody = document.querySelector('#paymentsTable tbody');
    if (!tbody) return;
    setLoadingState('paymentsTable', true);
    const statusFilter = document.getElementById('paymentStatusFilter')?.value || 'all';
    const periodFilter = document.getElementById('paymentPeriodFilter')?.value || 'all';

    const filtered = paymentsData
        .filter(payment => statusFilter === 'all' || payment.status === statusFilter)
        .filter(payment => matchesPeriod(payment.date, periodFilter));

    tbody.innerHTML = '';
    if (filtered.length === 0) {
        renderEmptyRow(tbody, 5, 'No payments found.');
    }
    filtered.forEach(payment => {
        const statusLabel = payment.status.charAt(0).toUpperCase() + payment.status.slice(1);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${payment.parentName}</strong></td>
            <td>${payment.student}</td>
            <td>EGP ${payment.amount.toLocaleString()}</td>
            <td><span class="status-badge ${payment.status}">${statusLabel}</span></td>
            <td>${payment.date}</td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    setTimeout(() => setLoadingState('paymentsTable', false), 300);
}

function renderBusCapacity() {
    const tbody = document.querySelector('#busCapacityTable tbody');
    if (!tbody) return;
    setLoadingState('busCapacityTable', true);
    const statusFilter = document.getElementById('capacityStatusFilter')?.value || 'all';
    const capacityMap = new Map(busOccupancyData.map(item => [item.busNumber, item.currentStudents]));

    const rows = busesData.map(bus => {
        const currentStudents = capacityMap.get(bus.busNumber) || Math.floor(bus.capacity * 0.7);
        const availableSeats = Math.max(bus.capacity - currentStudents, 0);
        let status = 'available';
        if (availableSeats === 0) status = 'full';
        else if (availableSeats <= 5) status = 'limited';
        return {
            busNumber: bus.busNumber,
            maxCapacity: bus.capacity,
            currentStudents,
            availableSeats,
            status
        };
    }).filter(row => statusFilter === 'all' || row.status === statusFilter);

    tbody.innerHTML = '';
    if (rows.length === 0) {
        renderEmptyRow(tbody, 5, 'No capacity data available.');
    }
    rows.forEach(row => {
        const statusLabel = row.status.charAt(0).toUpperCase() + row.status.slice(1);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${row.busNumber}</strong></td>
            <td>${row.maxCapacity}</td>
            <td>${row.currentStudents}</td>
            <td>${row.availableSeats}</td>
            <td><span class="status-badge ${row.status}">${statusLabel}</span></td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    setTimeout(() => setLoadingState('busCapacityTable', false), 300);
}

function renderEmergencyAlerts() {
    const tbody = document.querySelector('#emergencyAlertsTable tbody');
    if (!tbody) return;
    setLoadingState('emergencyAlertsTable', true);
    const typeFilter = document.getElementById('emergencyTypeFilter')?.value || 'all';
    const filtered = emergencyAlertsData.filter(alert => typeFilter === 'all' || alert.type === typeFilter);

    tbody.innerHTML = '';
    if (filtered.length === 0) {
        renderEmptyRow(tbody, 5, 'No emergency alerts available.');
    }
    filtered.forEach(alert => {
        const typeLabel = alert.type.charAt(0).toUpperCase() + alert.type.slice(1);
        const badgeClass = alert.type === 'delay' ? 'delay' : 'emergency';
        const tr = document.createElement('tr');
        if (alert.type === 'accident' || alert.type === 'medical') {
            tr.classList.add('emergency-critical');
        }
        tr.innerHTML = `
            <td><span class="status-badge ${badgeClass}">${typeLabel}</span></td>
            <td><strong>${alert.busNumber}</strong></td>
            <td>${alert.driver}</td>
            <td>${alert.location}</td>
            <td>${alert.time}</td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    setTimeout(() => setLoadingState('emergencyAlertsTable', false), 300);
}

function populateEmergencyLogFilters() {
    const busFilter = document.getElementById('emergencyLogBusFilter');
    const driverFilter = document.getElementById('emergencyLogDriverFilter');
    if (!busFilter || !driverFilter) return;

    const currentBus = busFilter.value || 'all';
    const currentDriver = driverFilter.value || 'all';

    const busOptions = ['<option value="all">All Buses</option>']
        .concat([...new Set(emergencyLogsData.map(item => item.bus))].map(bus => `<option value="${bus}">${bus}</option>`));
    const driverOptions = ['<option value="all">All Drivers</option>']
        .concat([...new Set(emergencyLogsData.map(item => item.driver))].map(driver => `<option value="${driver}">${driver}</option>`));

    busFilter.innerHTML = busOptions.join('');
    driverFilter.innerHTML = driverOptions.join('');

    busFilter.value = [...busFilter.options].some(opt => opt.value === currentBus) ? currentBus : 'all';
    driverFilter.value = [...driverFilter.options].some(opt => opt.value === currentDriver) ? currentDriver : 'all';
}

function renderEmergencyLogs() {
    const tbody = document.getElementById('emergencyLogsBody');
    if (!tbody) return;
    setLoadingState('emergencyLogsTable', true);

    const dateFilter = document.getElementById('emergencyLogDateFilter')?.value || '';
    const busFilter = document.getElementById('emergencyLogBusFilter')?.value || 'all';
    const driverFilter = document.getElementById('emergencyLogDriverFilter')?.value || 'all';
    const typeFilter = document.getElementById('emergencyLogTypeFilter')?.value || 'all';

    populateEmergencyLogFilters();

    const filtered = emergencyLogsData.filter(item => {
        const dateMatch = !dateFilter || item.time.startsWith(dateFilter);
        const busMatch = busFilter === 'all' || item.bus === busFilter;
        const driverMatch = driverFilter === 'all' || item.driver === driverFilter;
        const typeMatch = typeFilter === 'all' || item.type === typeFilter;
        return dateMatch && busMatch && driverMatch && typeMatch;
    });

    tbody.innerHTML = '';
    if (filtered.length === 0) {
        renderEmptyRow(tbody, 6, 'No emergency logs available.');
    }

    filtered.forEach(item => {
        const typeLabel = item.type.charAt(0).toUpperCase() + item.type.slice(1);
        const badgeClass = item.type === 'delay' ? 'delay' : 'emergency';
        const tr = document.createElement('tr');
        if (item.type === 'accident' || item.type === 'medical') tr.classList.add('emergency-critical');
        tr.innerHTML = `
            <td><strong>${item.bus}</strong></td>
            <td>${item.driver}</td>
            <td>${item.location}</td>
            <td><span class="status-badge ${badgeClass}">${typeLabel}</span></td>
            <td>${item.time}</td>
            <td>${item.notes}</td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    setTimeout(() => setLoadingState('emergencyLogsTable', false), 300);
}

function renderSmartAlerts() {
    const tbody = document.getElementById('smartAlertsBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    if (smartAlertsData.length === 0) {
        renderEmptyRow(tbody, 6, 'No smart alerts detected.');
        return;
    }

    smartAlertsData.forEach(alert => {
        const tr = document.createElement('tr');
        const severityClass = alert.severity === 'high' ? 'high' : alert.severity === 'medium' ? 'medium' : 'low';
        tr.innerHTML = `
            <td><strong>${alert.title}</strong></td>
            <td>${alert.bus}</td>
            <td>${alert.tripId}</td>
            <td><span class="status-badge ${severityClass}">${alert.severity}</span></td>
            <td>${alert.detectedAt}</td>
            <td>
                <button class="btn-primary btn-compact" type="button" onclick="notifySmartAlert(${alert.id})">
                    <i class="fas fa-bell"></i> Notify
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function runSmartAlertScan() {
    buildRegisteredTrackingData().forEach(bus => {
        const speed = Number(String(bus.speed).replace(/\D/g, '')) || 0;
        const trip = getTripByBus(bus.busNumber);
        const stoppedAlertExists = smartAlertsData.some(alert => alert.bus === bus.busNumber && alert.title === 'Bus stopped too long');
        if (bus.status === 'stopped' && speed === 0 && !stoppedAlertExists) {
            smartAlertsData.unshift({
                id: Date.now() + bus.id,
                title: 'Bus stopped too long',
                bus: bus.busNumber,
                tripId: trip?.tripId || 'Unassigned',
                severity: 'high',
                detectedAt: new Date().toLocaleString(),
                status: 'open'
            });
        }
    });

    renderSmartAlerts();
    showToast('Smart alert scan completed.', 'success');
}

function notifySmartAlert(id) {
    const alert = smartAlertsData.find(item => item.id === id);
    if (!alert) return;

    const notification = {
        id: Date.now(),
        title: alert.title,
        type: alert.severity === 'high' ? 'emergency' : 'delay',
        recipients: `Parents and driver of ${alert.bus}`,
        sentDate: new Date().toLocaleString(),
        status: 'sent',
        message: `${alert.title} detected on ${alert.bus} during ${alert.tripId}. Operations team has been notified.`
    };
    notificationsData.unshift(notification);
    emergencyLogsData.unshift({
        id: notification.id,
        bus: alert.bus,
        driver: getTripByBus(alert.bus)?.driver || 'Unassigned',
        location: buildRegisteredTrackingData().find(item => item.busNumber === alert.bus)?.currentLocation || 'Unknown',
        type: notification.type === 'emergency' ? 'general' : 'delay',
        time: new Date().toISOString().replace('T', ' ').slice(0, 19),
        notes: notification.message
    });
    alert.status = 'notified';
    renderSmartAlerts();
    renderNotifications();
    renderEmergencyLogs();
    updateNotificationBadge();
    showToast('Smart alert notification sent.', 'success');
}

function syncAttendanceEvents() {
    try {
        const raw = localStorage.getItem(ATTENDANCE_STORAGE_KEY);
        if (!raw) return;
        const events = JSON.parse(raw);
        if (!Array.isArray(events)) return;

        const existingIds = new Set(attendanceEventsData.map(e => e.id));
        events.forEach(event => {
            if (existingIds.has(event.id)) return;
            event.tripId = event.tripId || getTripByBus(event.bus)?.tripId || 'Unassigned';
            attendanceEventsData.unshift(event);
            queueParentAttendanceNotification(event);
        });
    } catch {
        // ignore
    }
}

function renderAttendanceRealtime() {
    const tbody = document.getElementById('attendanceRealtimeBody');
    if (!tbody) return;
    setLoadingState('attendanceRealtimeTable', true);

    syncAttendanceEvents();

    tbody.innerHTML = '';
    if (attendanceEventsData.length === 0) {
        renderEmptyRow(tbody, 6, 'No attendance updates yet.');
        setTimeout(() => setLoadingState('attendanceRealtimeTable', false), 300);
        return;
    }

    attendanceEventsData.slice(0, 12).forEach(item => {
        const pickupBadge = item.pickupStatus === 'missed' ? 'cancelled' : item.pickupStatus === 'picked' ? 'completed' : 'pending';
        const dropoffBadge = item.dropoffStatus === 'missed' ? 'cancelled' : item.dropoffStatus === 'dropped' ? 'completed' : 'pending';
        const tr = document.createElement('tr');
        if (item.pickupStatus === 'missed' || item.dropoffStatus === 'missed') {
            tr.classList.add('emergency-critical');
        }
        tr.innerHTML = `
            <td><strong>${item.student}</strong></td>
            <td>${item.tripId || getTripByBus(item.bus)?.tripId || 'N/A'}</td>
            <td>${item.bus}</td>
            <td><span class="status-badge ${pickupBadge}">${item.pickupLabel}</span></td>
            <td><span class="status-badge ${dropoffBadge}">${item.dropoffLabel}</span></td>
            <td>${item.time}</td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
    setTimeout(() => setLoadingState('attendanceRealtimeTable', false), 300);
}

function queueParentAttendanceNotification(event) {
    const eventLabel = event.dropoffStatus === 'dropped' ? 'checked out' : event.pickupStatus === 'picked' ? 'checked in' : event.pickupStatus === 'missed' ? 'missed pickup' : '';
    if (!eventLabel) return;
    const title = `Student ${eventLabel}`;
    const alreadyQueued = notificationsData.some(notification => notification.sourceEventId === event.id && notification.title === title);
    if (alreadyQueued) return;

    notificationsData.unshift({
        id: Date.now() + Math.floor(Math.random() * 1000),
        sourceEventId: event.id,
        title,
        type: event.pickupStatus === 'missed' || event.dropoffStatus === 'missed' ? 'delay' : 'general',
        recipients: `Parent of ${event.student}`,
        sentDate: event.time || new Date().toLocaleString(),
        status: 'sent',
        message: `${event.student} ${eventLabel} on ${event.bus} for ${event.tripId || 'assigned trip'}.`
    });
    updateNotificationBadge();
}

let notificationReplyBound = false;
function initNotificationReplyActions() {
    if (notificationReplyBound) return;
    const table = document.getElementById('notificationsTable');
    if (!table) return;

    table.addEventListener('click', (event) => {
        const sendBtn = event.target.closest('.btn-reply-send');
        const clearBtn = event.target.closest('.btn-reply-clear');
        if (!sendBtn && !clearBtn) return;

        const id = (sendBtn || clearBtn).getAttribute('data-id');
        const input = document.getElementById(`reply-${id}`);
        const status = document.getElementById(`reply-status-${id}`);
        if (!input) return;

        if (clearBtn) {
            input.value = '';
            if (status) status.textContent = '';
            return;
        }

        const message = input.value.trim();
        if (!message) {
            if (status) status.textContent = 'Please write a reply first.';
            return;
        }

        input.value = '';
        if (status) status.textContent = 'Reply sent.';
        setTimeout(() => { if (status) status.textContent = ''; }, 2000);
    });

    notificationReplyBound = true;
}

function renderComplaints() {
    const tbody = document.querySelector('#complaintsTable tbody');
    if (!tbody) {
        console.error('Complaints table tbody not found');
        return;
    }
    const typeFilter = document.getElementById('complaintTypeFilter')?.value || 'all';
    const statusFilter = document.getElementById('complaintStatusFilter')?.value || 'all';
    const filteredData = complaintsData
        .filter(complaint => typeFilter === 'all' || complaint.type === typeFilter)
        .filter(complaint => statusFilter === 'all' || complaint.status === statusFilter);

    tbody.innerHTML = '';
    filteredData.forEach(complaint => {
        const tr = document.createElement('tr');
        const priorityClass = complaint.priority === 'high' ? 'danger' : complaint.priority === 'medium' ? 'warning' : 'success';
        tr.innerHTML = `
            <td>${complaint.complaintId}</td>
            <td>${complaint.submittedBy}</td>
            <td><span class="status-badge ${complaint.type}">${complaint.type.charAt(0).toUpperCase() + complaint.type.slice(1)}</span></td>
            <td>${complaint.subject}</td>
            <td><span class="status-badge ${priorityClass}">${complaint.priority.charAt(0).toUpperCase() + complaint.priority.slice(1)}</span></td>
            <td><span class="status-badge ${complaint.status}">${complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1)}</span></td>
            <td>${complaint.date}</td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewComplaint(${complaint.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="updateComplaintStatus(${complaint.id})" title="Update Status">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function renderSchools() {
    const tbody = document.querySelector('#schoolsTable tbody');
    if (!tbody) {
        console.error('Schools table tbody not found');
        return;
    }
    const typeFilter = document.getElementById('schoolTypeFilter')?.value || 'all';
    const districtFilterEl = document.getElementById('schoolDistrictFilter');
    const currentDistrictFilter = districtFilterEl?.value || 'all';
    const districts = [...new Set(schoolsData.map(item => item.district))];
    if (districtFilterEl) {
        districtFilterEl.innerHTML = ['<option value="all">All Districts</option>']
            .concat(districts.map(district => `<option value="${district}">${district}</option>`))
            .join('');
        districtFilterEl.value = districts.includes(currentDistrictFilter) ? currentDistrictFilter : 'all';
    }
    const districtFilter = districtFilterEl?.value || 'all';

    const filteredData = schoolsData
        .filter(school => typeFilter === 'all' || school.type === typeFilter)
        .filter(school => districtFilter === 'all' || school.district === districtFilter);

    tbody.innerHTML = '';
    filteredData.forEach(school => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${school.name}</strong></td>
            <td><span class="status-badge ${school.type}">${school.type.charAt(0).toUpperCase() + school.type.slice(1)}</span></td>
            <td>${school.district}</td>
            <td>${school.address}</td>
            <td>${school.contact}</td>
            <td><span class="badge">${school.students} students</span></td>
            <td><span class="status-badge ${school.status}">${school.status.charAt(0).toUpperCase() + school.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewSchool(${school.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editSchool(${school.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteSchool(${school.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function renderUsers() {
    const tbody = document.querySelector('#usersTable tbody');
    if (!tbody) {
        console.error('Users table tbody not found');
        return;
    }
    const roleFilter = document.getElementById('userRoleFilter')?.value || 'all';
    const statusFilter = document.getElementById('userStatusFilter')?.value || 'all';
    const filteredData = usersData
        .filter(user => roleFilter === 'all' || user.role === roleFilter)
        .filter(user => statusFilter === 'all' || user.status === statusFilter);

    tbody.innerHTML = '';
    filteredData.forEach(user => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${user.name}</strong></td>
            <td>${user.email}</td>
            <td><span class="status-badge ${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span></td>
            <td>${user.department}</td>
            <td>${user.lastLogin}</td>
            <td><span class="status-badge ${user.status}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewUser(${user.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editUser(${user.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteUser(${user.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

function formatRecoveryIssue(issue) {
    if (issue === 'email') return 'Forgot Email';
    if (issue === 'password') return 'Forgot Password';
    return 'Email & Password';
}

function updateRecoveryStats() {
    const pendingEl = document.getElementById('recoveryPendingStat');
    const processingEl = document.getElementById('recoveryProcessingStat');
    const completedEl = document.getElementById('recoveryCompletedStat');

    if (pendingEl) pendingEl.textContent = accountRecoveryData.filter(item => item.status === 'pending').length;
    if (processingEl) processingEl.textContent = accountRecoveryData.filter(item => item.status === 'reviewing').length;
    if (completedEl) completedEl.textContent = accountRecoveryData.filter(item => item.status === 'completed').length;
}

function renderAccountRecovery() {
    const tbody = document.querySelector('#accountRecoveryTable tbody');
    if (!tbody) return;

    const roleFilter = document.getElementById('recoveryRoleFilter')?.value || 'all';
    const typeFilter = document.getElementById('recoveryTypeFilter')?.value || 'all';
    const statusFilter = document.getElementById('recoveryStatusFilter')?.value || 'all';

    const filteredData = accountRecoveryData
        .filter(item => roleFilter === 'all' || item.role === roleFilter)
        .filter(item => typeFilter === 'all' || item.issue === typeFilter)
        .filter(item => statusFilter === 'all' || item.status === statusFilter);

    tbody.innerHTML = '';

    if (filteredData.length === 0) {
        renderEmptyRow(tbody, 9, 'No account recovery requests match the selected filters.');
        updateRecoveryStats();
        return;
    }

    filteredData.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <strong>${item.requester}</strong>
                <small class="table-subtext">${item.phone}</small>
            </td>
            <td><span class="status-badge ${item.role}">${item.role === 'staff' ? 'Administration' : item.role.charAt(0).toUpperCase() + item.role.slice(1)}</span></td>
            <td><span class="status-badge ${item.issue}">${formatRecoveryIssue(item.issue)}</span></td>
            <td>${item.currentEmail}</td>
            <td>${item.requestedChange}</td>
            <td>${item.verifiedBy}</td>
            <td><span class="status-badge ${item.status}">${item.status === 'reviewing' ? 'Reviewing' : item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></td>
            <td>${item.requestedAt}</td>
            <td>
                <div class="table-actions recovery-actions">
                    <div class="action-icon view" onclick="viewRecoveryRequest(${item.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    ${item.status === 'pending' ? `
                        <div class="action-icon edit" onclick="markRecoveryReviewing(${item.id})" title="Start Review">
                            <i class="fas fa-play"></i>
                        </div>
                    ` : ''}
                    ${item.status !== 'completed' && item.status !== 'rejected' ? `
                        <div class="action-icon edit" onclick="completeRecoveryRequest(${item.id})" title="Complete Request">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="action-icon delete" onclick="rejectRecoveryRequest(${item.id})" title="Reject Request">
                            <i class="fas fa-times"></i>
                        </div>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    updateRecoveryStats();
    reapplyGlobalSearch();
}

function findRecoveryRequest(id) {
    return accountRecoveryData.find(item => item.id === id);
}

function viewRecoveryRequest(id) {
    const item = findRecoveryRequest(id);
    if (!item) return;

    alert(
        `Requester: ${item.requester}\n` +
        `Role: ${item.role}\n` +
        `Issue: ${formatRecoveryIssue(item.issue)}\n` +
        `Current email: ${item.currentEmail}\n` +
        `Requested change: ${item.requestedChange}\n` +
        `Verification: ${item.verifiedBy}\n` +
        `Phone: ${item.phone}\n` +
        `Status: ${item.status}`
    );
}

function markRecoveryReviewing(id) {
    const item = findRecoveryRequest(id);
    if (!item) return;
    item.status = 'reviewing';
    renderAccountRecovery();
    showToast('Recovery request moved to review.', 'info');
}

function completeRecoveryRequest(id) {
    const item = findRecoveryRequest(id);
    if (!item) return;

    const newEmail = prompt('Confirm email for this account:', item.currentEmail);
    if (!newEmail || !newEmail.trim()) return;

    const temporaryPassword = prompt('Enter temporary password or reset note:', 'Temporary password sent securely');
    if (!temporaryPassword || !temporaryPassword.trim()) return;

    item.currentEmail = newEmail.trim();
    item.requestedChange = temporaryPassword.trim();
    item.status = 'completed';
    item.verifiedBy = `${item.verifiedBy} - completed by Admin`;

    renderAccountRecovery();
    showToast('Account recovery request completed.', 'success');
}

function rejectRecoveryRequest(id) {
    const item = findRecoveryRequest(id);
    if (!item) return;
    if (!confirm('Reject this account recovery request?')) return;

    item.status = 'rejected';
    renderAccountRecovery();
    showToast('Account recovery request rejected.', 'warning');
}

function createRecoveryRequest() {
    const requester = prompt('Requester name:');
    if (!requester || !requester.trim()) return;

    const role = prompt('Role (parent/driver/staff):', 'parent');
    const normalizedRole = ['parent', 'driver', 'staff'].includes((role || '').toLowerCase()) ? role.toLowerCase() : 'parent';

    const issue = prompt('Issue (email/password/both):', 'password');
    const normalizedIssue = ['email', 'password', 'both'].includes((issue || '').toLowerCase()) ? issue.toLowerCase() : 'password';

    const currentEmail = prompt('Current email:', '');
    const requestedChange = prompt('Requested change:', normalizedIssue === 'email' ? 'Change email to ' : 'Reset password');
    const phone = prompt('Phone number:', '');

    accountRecoveryData.unshift({
        id: Date.now(),
        requester: requester.trim(),
        role: normalizedRole,
        issue: normalizedIssue,
        currentEmail: (currentEmail || 'Unknown email').trim(),
        requestedChange: (requestedChange || 'Account recovery requested').trim(),
        phone: (phone || 'No phone provided').trim(),
        verifiedBy: 'Manual admin entry',
        status: 'pending',
        requestedAt: new Date().toLocaleString()
    });

    renderAccountRecovery();
    showToast('Account recovery request added.', 'success');
}

function renderActivityLogs() {
    const tbody = document.querySelector('#activityLogsTable tbody');
    if (!tbody) {
        console.error('Activity logs table tbody not found');
        return;
    }

    const actionFilter = document.getElementById('activityActionFilter')?.value || 'all';
    const moduleFilterEl = document.getElementById('activityModuleFilter');
    const periodFilter = document.getElementById('activityPeriodFilter')?.value || 'all';

    if (moduleFilterEl) {
        const currentModule = moduleFilterEl.value || 'all';
        const modules = [...new Set(activityLogsData.map(log => log.module))].sort();
        moduleFilterEl.innerHTML = ['<option value="all">All Modules</option>']
            .concat(modules.map(moduleName => `<option value="${moduleName}">${moduleName}</option>`))
            .join('');
        moduleFilterEl.value = modules.includes(currentModule) ? currentModule : 'all';
    }

    const moduleFilter = moduleFilterEl?.value || 'all';
    const filteredLogs = activityLogsData
        .filter(log => actionFilter === 'all' || log.action === actionFilter)
        .filter(log => moduleFilter === 'all' || log.module === moduleFilter)
        .filter(log => matchesPeriod(log.timestamp, periodFilter));

    tbody.innerHTML = '';

    filteredLogs.forEach(log => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${log.timestamp}</td>
            <td>${log.user}</td>
            <td><span class="status-badge ${log.action}">${log.action.charAt(0).toUpperCase() + log.action.slice(1)}</span></td>
            <td>${log.module}</td>
            <td>${log.description}</td>
            <td>${log.ipAddress}</td>
        `;
        tbody.appendChild(tr);
    });

    reapplyGlobalSearch();
}

let currentStudentQrPayload = '';
let currentStudentQrImageUrl = '';
let currentStudentQrFilename = '';

function appendActivityLog(action, module, description) {
    const now = new Date();
    const pad = value => String(value).padStart(2, '0');
    const timestamp = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

    activityLogsData.unshift({
        id: activityLogsData.length ? Math.max(...activityLogsData.map(log => log.id)) + 1 : 1,
        timestamp,
        user: 'Admin User',
        action,
        module,
        description,
        ipAddress: '127.0.0.1'
    });
}

function initStudentQrTools() {
    const studentSelect = document.getElementById('qrStudentSelect');
    if (!studentSelect) return;

    if (!studentSelect.dataset.bound) {
        studentSelect.addEventListener('change', () => {
            const selectedStudent = studentsData.find(student => String(student.id) === studentSelect.value);
            if (!selectedStudent) return;
            document.getElementById('qrNoteInput').value = selectedStudent.pickupLocation;
        });
        studentSelect.dataset.bound = 'true';
    }

    const currentValue = studentSelect.value;
    const options = ['<option value="">Select student...</option>']
        .concat(studentsData.map(student => `<option value="${student.id}">${student.name} (${student.studentId})</option>`));
    studentSelect.innerHTML = options.join('');
    if ([...studentSelect.options].some(option => option.value === currentValue)) {
        studentSelect.value = currentValue;
    }
}

function consumePendingStudentForAdmin() {
    let pendingStr = null;
    try {
        pendingStr = localStorage.getItem('pending_student');
    } catch {
        return null;
    }
    if (!pendingStr) return null;

    let pending = null;
    try {
        pending = JSON.parse(pendingStr);
    } catch {
        try { localStorage.removeItem('pending_student'); } catch { /* ignore */ }
        return null;
    }

    // Basic validation: form builder uses studentId/fullName/grade/school/parent/pickupLocation/dropoffLocation/status
    if (!pending?.studentId || !pending?.fullName) {
        try { localStorage.removeItem('pending_student'); } catch { /* ignore */ }
        return null;
    }

    const nextId = studentsData.length ? Math.max(...studentsData.map(s => s.id)) + 1 : 1;
    const newStudent = {
        id: nextId,
        studentId: String(pending.studentId),
        name: String(pending.fullName),
        grade: String(pending.grade || ''),
        school: String(pending.school || ''),
        parent: String(pending.parent || ''),
        pickupLocation: String(pending.pickupLocation || ''),
        dropoffLocation: String(pending.dropoffLocation || ''),
        status: String(pending.status || 'active')
    };

    studentsData.unshift(newStudent); // show newest first
    
    // Track the creation event in Activity Logs
    try {
        appendActivityLog('create', 'Students', `Added student ${newStudent.name} (${newStudent.studentId})`);
    } catch { /* ignore */ }

    try { localStorage.removeItem('pending_student'); } catch { /* ignore */ }
    return newStudent;
}

function autoGenerateStudentQrForStudent(newStudent) {
    if (!newStudent) return;

    const studentSelect = document.getElementById('qrStudentSelect');
    const zoneSelect = document.getElementById('qrZoneSelect');
    const tripTypeSelect = document.getElementById('qrTripType');
    const noteInput = document.getElementById('qrNoteInput');

    if (!studentSelect || !zoneSelect || !tripTypeSelect) return;

    // Select newly added student (value matches studentsData.id)
    studentSelect.value = String(newStudent.id);
    studentSelect.dispatchEvent(new Event('change', { bubbles: true }));

    // Default zone/trip if user hasn't chosen anything yet
    if (!zoneSelect.value) zoneSelect.value = 'zone_a';
    if (!tripTypeSelect.value) tripTypeSelect.value = 'pickup';

    if (noteInput && !noteInput.value) {
        noteInput.value = newStudent.pickupLocation || '';
    }

    generateStudentQr();

    // Ensure user lands on Activity Logs page where the QR builder is located.
    if (typeof navigateTo === 'function') {
        navigateTo('activity-logs');
    }

    const qrPreview = document.getElementById('studentQrContainer');
    qrPreview?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function generateStudentQr() {
    const studentId = document.getElementById('qrStudentSelect')?.value;
    const zone = document.getElementById('qrZoneSelect')?.value;
    const tripType = document.getElementById('qrTripType')?.value || 'pickup';
    const note = (document.getElementById('qrNoteInput')?.value || '').trim();
    const qrContainer = document.getElementById('studentQrContainer');
    const qrImage = document.getElementById('studentQrImage');
    const payloadPreview = document.getElementById('qrPayloadPreview');

    if (!studentId) {
        showToast('Please select a student first.', 'warning');
        return;
    }

    if (!zone) {
        showToast('Please select a zone/region first.', 'warning');
        return;
    }

    const student = studentsData.find(item => String(item.id) === studentId);
    if (!student || !qrContainer || !qrImage || !payloadPreview) return;

    const payloadObject = {
        studentId: student.studentId,
        name: student.name,
        grade: student.grade,
        parent: student.parent,
        school: student.school,
        zone: zone,
        tripType,
        note,
        generatedAt: new Date().toISOString()
    };

    currentStudentQrPayload = JSON.stringify(payloadObject);
    currentStudentQrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(currentStudentQrPayload)}`;
    currentStudentQrFilename = `student-qr-${student.studentId}-${zone}-${tripType}.png`;

    qrImage.src = currentStudentQrImageUrl;
    qrImage.alt = `QR code for ${student.name} - Zone: ${zone}`;
    qrContainer.classList.add('has-qr');
    payloadPreview.textContent = currentStudentQrPayload;

    // Persist for QR.html student card
    try {
        localStorage.setItem('student_qr_last_payload', currentStudentQrPayload);
        localStorage.setItem('student_qr_last_image_url', currentStudentQrImageUrl);
    } catch { /* ignore */ }

    appendActivityLog('create', 'Student QR', `Generated QR for ${student.name} (Zone: ${zone}, ${tripType})`);
    renderActivityLogs();
    showToast(`Student QR generated successfully for ${zone}.`, 'success');
}

function downloadStudentQr() {
    if (!currentStudentQrImageUrl) {
        showToast('Generate a QR code first.', 'warning');
        return;
    }

    const link = document.createElement('a');
    link.href = currentStudentQrImageUrl;
    link.download = currentStudentQrFilename || 'student-qr.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    appendActivityLog('download', 'Student QR', 'Downloaded generated student QR');
    renderActivityLogs();
    showToast('QR download started.', 'success');
}

function copyStudentQrPayload() {
    if (!currentStudentQrPayload) {
        showToast('Generate a QR code first.', 'warning');
        return;
    }

    const copySuccess = () => {
        appendActivityLog('copy', 'Student QR', 'Copied student QR payload');
        renderActivityLogs();
        showToast('QR payload copied.', 'success');
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(currentStudentQrPayload).then(copySuccess).catch(() => {
            showToast('Could not copy payload.', 'error');
        });
        return;
    }

    const textArea = document.createElement('textarea');
    textArea.value = currentStudentQrPayload;
    document.body.appendChild(textArea);
    textArea.select();
    const isCopied = document.execCommand('copy');
    document.body.removeChild(textArea);
    if (isCopied) {
        copySuccess();
    } else {
        showToast('Could not copy payload.', 'error');
    }
}

async function approveDriver(id) {
    const driver = driversData.find(d => d.id === id);
    if (!driver || !confirm(`Approve driver ${driver.name}?`)) return;
    try {
        await safestepApi(`/api/admin/drivers/${id}/approve`, { method: 'POST' });
        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        } else {
            driver.status = 'active';
            driver.active = true;
            renderDrivers();
            renderPendingDriverApplicantsForParents();
        }
        if (typeof navigateTo === 'function') navigateTo('drivers');
        showToast('Driver approved — ظهر في قائمة السائقين', 'success');
    } catch (err) {
        showToast('Failed to approve driver.', 'error');
    }
}

async function rejectDriver(id) {
    const driver = driversData.find(d => d.id === id);
    if (!driver || !confirm(`Reject driver ${driver.name}?`)) return;
    try {
        await safestepApi(`/api/admin/drivers/${id}/reject`, { method: 'POST' });
        driver.status = 'rejected';
        driver.active = false;
        renderDrivers();
        renderPendingDriverApplicantsForParents();
        showToast('Driver rejected.', 'success');
    } catch (err) {
        showToast('Failed to reject driver.', 'error');
    }
}

async function approveParent(id) {
    const parent = parentsData.find(p => p.id === id);
    if (!parent || !confirm(`Approve parent ${parent.name}?`)) return;
    try {
        await safestepApi(`/api/admin/parents/${id}/approve`, { method: 'POST' });
        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        } else {
            parent.status = 'active';
            parent.active = true;
            renderParents();
        }
        if (typeof navigateTo === 'function') navigateTo('parents');
        showToast('Parent approved — ظهر في قائمة الأهالي', 'success');
    } catch (err) {
        showToast('Failed to approve parent.', 'error');
    }
}

async function rejectParent(id) {
    const parent = parentsData.find(p => p.id === id);
    if (!parent || !confirm(`Reject parent ${parent.name}?`)) return;
    try {
        await safestepApi(`/api/admin/parents/${id}/reject`, { method: 'POST' });
        parent.status = 'rejected';
        parent.active = false;
        renderParents();
        showToast('Parent rejected.', 'success');
    } catch (err) {
        showToast('Failed to reject parent.', 'error');
    }
}

function viewDriver(id) {
    const driver = driversData.find(d => d.id === id);
    if (driver) {
        alert(`Driver Details:\nName: ${driver.name}\nLicense: ${driver.license}\nPhone: ${driver.phone}\nBus: ${driver.bus}`);
    }
}

function editDriver(id) {
    const driver = driversData.find(d => d.id === id);
    if (driver) {
        alert(`Edit form for ${driver.name} would open here.`);
    }
}

async function deleteDriver(id) {
    const driver = driversData.find(d => d.id === id);
    if (!driver || !confirm(`Delete driver ${driver.name}?`)) return;
    try {
        await safestepApi(`/api/admin/drivers/${id}`, { method: 'DELETE' });
        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        } else {
            const index = driversData.findIndex(d => d.id === id);
            if (index >= 0) driversData.splice(index, 1);
            renderDrivers();
            renderPendingDriverApplicantsForParents();
        }
        showToast('Driver deleted successfully!', 'success');
    } catch (err) {
        showToast('Failed to delete driver.', 'error');
    }
}

// Buses Data
const busesData = [
     { id: 1, busNumber: 'Bus #42', plate: 'ALX-1234', driver: 'Ahmed Khaled', route: 'Route A', capacity: 45, status: 'active' },
    { id: 2, busNumber: 'Bus #15', plate: 'ALX-5678', driver: 'Mohamed Ali', route: 'Route B', capacity: 40, status: 'maintenance' },
    { id: 3, busNumber: 'Bus #28', plate: 'ALX-9012', driver: 'Youssef Hassan', route: 'Route C', capacity: 45, status: 'active' },
    { id: 4, busNumber: 'Bus #33', plate: 'ALX-3456', driver: 'Omar Samir', route: 'Route D', capacity: 50, status: 'active' },
    { id: 5, busNumber: 'Bus #07', plate: 'ALX-7890', driver: 'Ramy Mostafa', route: 'Route E', capacity: 40, status: 'active' },
    { id: 6, busNumber: 'Bus #19', plate: 'ALX-2345', driver: 'Karim Mahmoud', route: 'Route F', capacity: 45, status: 'active' },
    { id: 7, busNumber: 'Bus #51', plate: 'ALX-6789', driver: 'Hassan Ahmed', route: 'Route G', capacity: 40, status: 'inactive' },
    { id: 8, busNumber: 'Bus #12', plate: 'ALX-0123', driver: 'Mahmoud Fathy', route: 'Route H', capacity: 45, status: 'active' },
    { id: 9, busNumber: 'Bus #44', plate: 'ALX-4567', driver: 'Amr Abdelrahman', route: 'Route I', capacity: 50, status: 'active' },
    { id: 10, busNumber: 'Bus #23', plate: 'ALX-8901', driver: 'Unassigned', route: 'Unassigned', capacity: 45, status: 'maintenance' }
];

function renderBuses() {
    const tbody = document.querySelector('#busesTable tbody');
    tbody.innerHTML = '';
    
    busesData.forEach(bus => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${bus.busNumber}</strong></td>
            <td>${bus.plate}</td>
            <td>${bus.driver}</td>
            <td>${bus.route}</td>
            <td>${bus.capacity} seats</td>
            <td><span class="status-badge ${bus.status}">${bus.status.charAt(0).toUpperCase() + bus.status.slice(1)}</span></td>
            <td>
                <div class="table-actions">
                    <div class="action-icon view" onclick="viewBus(${bus.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="action-icon edit" onclick="editBus(${bus.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-icon delete" onclick="deleteBus(${bus.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function addBus() {
    openAddPage('bus');
}

function viewBus(id) {
    const bus = busesData.find(b => b.id === id);
    if (bus) {
        alert(`Bus Details:\n${bus.busNumber}\nPlate: ${bus.plate}\nDriver: ${bus.driver}\nRoute: ${bus.route}\nCapacity: ${bus.capacity} seats`);
    }
}

function editBus(id) {
    const bus = busesData.find(b => b.id === id);
    if (bus) {
        alert(`Edit form for ${bus.busNumber} would open here.`);
    }
}

function deleteBus(id) {
    const bus = busesData.find(b => b.id === id);
    if (bus && confirm(`Delete ${bus.busNumber}?`)) {
        const index = busesData.findIndex(b => b.id === id);
        busesData.splice(index, 1);
        renderBuses();
        alert('Bus deleted successfully!');
    }
}

renderBuses();

// Requests Data (from parents and drivers)
let requestsData = [
    { id: 1, from: 'Sarah Ahmed', role: 'parent', subject: 'Bus delay on Route A', priority: 'high', status: 'new', createdAt: '2024-06-01 08:15' },
    { id: 2, from: 'Mohamed Ali', role: 'driver', subject: 'Request for leave on June 10th', priority: 'medium', status: 'in_progress', createdAt: '2024-06-02 14:30' },
    { id: 3, from: 'Youssef Hassan', role: 'driver', subject: 'Bus #28 maintenance issue', priority: 'high', status: 'new', createdAt: '2024-06-03 09:45' },
    { id: 4, from: 'Omar Samir', role: 'driver', subject: 'Route change request for Route D', priority: 'low', status: 'resolved', createdAt: '2024-06-04 11:20' },
    { id: 5, from: 'Ramy Mostafa', role: 'driver', subject: 'Request for additional training', priority: 'medium', status: 'in_progress', createdAt: '2024-06-05 16:00' },
    { id: 6, from: 'Karim Mahmoud', role: 'driver', subject: 'Issue with bus assignment', priority: 'high', status: 'new', createdAt: '2024-06-06 10:30' },
    { id: 7, from: 'Hassan Ahmed', role: 'driver', subject: 'Request for schedule change', priority: 'low', status: 'resolved', createdAt: '2024-06-07 13:45' },
    { id: 8, from: 'Tamer Said', role: 'driver', subject: 'Report of traffic congestion on Route F', priority: 'medium', status: 'new', createdAt: '2024-06-08 09:00' },
    { id: 9, from: 'Mahmoud Fathy', role: 'driver', subject: 'Request for bus replacement', priority: 'high', status: 'in_progress', createdAt: '2024-06-09 15:30' },
    { id: 10, from: 'Amr Abdelrahman', role: 'driver', subject: 'Feedback on new route assignment', priority: 'low', status: 'resolved', createdAt: '2024-06-10 12:00' }
];

function normalizeRequest(request, fallbackId) {
    const rawRole = request.role || request.user_role || (request.user?.role) || 'parent';
    const normalizedStatus = request.status || 'new';
    const userName = request.user?.name || request.from || request.full_name || (rawRole === 'guest' ? 'Website Guest' : 'Unknown User');

    let normalizedRole;
    if (rawRole === 'guest') normalizedRole = 'guest';
    else if (rawRole === 'driver') normalizedRole = 'driver';
    else if (rawRole === 'school_admin' || rawRole === 'school-admin') normalizedRole = 'school_admin';
    else normalizedRole = 'parent';

    return {
        id: request.id || fallbackId,
        from: userName,
        role: normalizedRole,
        subject: request.subject || request.request_type || 'General request',
        description: request.description || '',
        priority: request.priority || 'medium',
        status: normalizedStatus,
        createdAt: request.createdAt || request.created_at || new Date().toISOString(),
        raw: request
    };
}

function getNormalizedRequests() {
    return requestsData.map((request, index) => normalizeRequest(request, index + 1));
}

function renderRequests() {
    const tbody = document.querySelector('#requestsTable tbody');
    if (!tbody) return;

    const typeFilter = document.getElementById('requestTypeFilter')?.value || 'all';
    const statusFilter = document.getElementById('requestStatusFilter')?.value || 'all';

    tbody.innerHTML = '';

    getNormalizedRequests()
        .filter(req => (typeFilter === 'all' || req.role === typeFilter))
        .filter(req => (statusFilter === 'all' || req.status === statusFilter))
        .forEach(req => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${req.from}</strong></td>
                <td>${req.role === 'guest' ? 'Guest' : req.role === 'parent' ? 'Parent' : 'Driver'}</td>
                <td>${req.subject}</td>
                <td>
                    <span class="status-badge ${req.priority === 'high' ? 'danger' : req.priority === 'medium' ? 'warning' : 'active'}">
                        ${req.priority.charAt(0).toUpperCase() + req.priority.slice(1)}
                    </span>
                </td>
                <td>
                    <span class="status-badge ${req.status}">
                        ${req.status === 'pending' ? 'New' : req.status === 'in-progress' || req.status === 'in_progress' ? 'In Progress' : req.status === 'resolved' ? 'Resolved' : req.status === 'rejected' ? 'Rejected' : req.status}
                    </span>
                </td>
                <td>${req.createdAt}</td>
                <td>
                    <div class="table-actions">
                        ${req.status !== 'resolved' && req.status !== 'rejected' ? `
                            <button class="btn btn-success" style="padding: 6px 10px; font-size: 12px;" onclick="markRequestResolved(${req.id})">
                                <i class="fas fa-check"></i> Resolve
                            </button>
                            ${req.status === 'pending' ? `
                                <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="markRequestInProgress(${req.id})">
                                    <i class="fas fa-play"></i> Start
                                </button>
                            ` : ''}
                        ` : `
                            <div class="action-icon view" onclick="viewRequest(${req.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </div>
                        `}
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

    reapplyGlobalSearch();
}

function updateRequestStatusById(id, nextStatus) {
    const requestIndex = requestsData.findIndex((request, index) => normalizeRequest(request, index + 1).id === id);
    if (requestIndex < 0) return false;

    requestsData[requestIndex].status = nextStatus;
    return true;
}

async function markRequestInProgress(id) {
    try {
        await safestepApi(`/api/admin/service-requests/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'in-progress' })
        });
        if (!updateRequestStatusById(id, 'in-progress')) return;
        renderRequests();
        showToast('Request marked as in progress.', 'success');
    } catch (err) {
        console.warn('Failed to update request status:', err.message);
        alert('Failed to update request. Please try again.');
    }
}

async function markRequestResolved(id) {
    const req = getNormalizedRequests().find(r => r.id === id);
    if (!req) return;
    if (confirm('Mark this request as resolved?')) {
        try {
            await safestepApi(`/api/admin/service-requests/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: 'resolved' })
            });
            if (!updateRequestStatusById(id, 'resolved')) return;
            renderRequests();
            showToast('Request marked as resolved.', 'success');
            if (typeof hydrateAdminDashboardFromApi === 'function') {
                await hydrateAdminDashboardFromApi();
            }
        } catch (err) {
            console.warn('Failed to resolve request:', err.message);
            alert('Failed to update request. Please try again.');
        }
    }
}

function viewRequest(id) {
    const req = getNormalizedRequests().find(r => r.id === id);
    if (!req) return;
    const raw = req.raw || {};
    const notes = raw.notes || '';
    const description = raw.description || '';
    const metadata = raw.metadata ? JSON.stringify(raw.metadata, null, 2) : '';
    alert(
        `Request from: ${req.from} (${req.role})\n` +
        `Subject: ${req.subject}\n` +
        `Priority: ${req.priority}\n` +
        `Status: ${req.status}\n` +
        `Created: ${req.createdAt}\n\n` +
        `Description:\n${description}\n\n` +
        (notes ? `Notes:\n${notes}\n\n` : '') +
        (metadata ? `Metadata:\n${metadata}` : '')
    );
}

// Initialize Charts when page loads
window.addEventListener('load', () => {
    initDashboardChart();
    initReportsCharts();
    loadRequestsFromStorage();
    loadRequestsFromApi();
});

// Dashboard Trips Chart
function initDashboardChart() {
    const ctx = document.getElementById('tripsChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Morning Trips',
                data: [32, 35, 34, 36, 35, 28, 24],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Afternoon Trips',
                data: [30, 33, 32, 34, 33, 26, 22],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                }
            }
        }
    });
}

// Reports Page Charts
function initReportsCharts() {
    // Daily Trips Chart
    const dailyCtx = document.getElementById('dailyTripsChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Completed Trips',
                    data: [245, 268, 252, 280],
                    backgroundColor: '#3B82F6'
                }, {
                    label: 'Cancelled Trips',
                    data: [5, 8, 6, 4],
                    backgroundColor: '#EF4444'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    data: [92, 5, 3],
                    backgroundColor: ['#10B981', '#EF4444', '#F59E0B'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Students per School Chart
    const studentsPerSchoolCtx = document.getElementById('studentsPerSchoolChart');
    if (studentsPerSchoolCtx) {
        new Chart(studentsPerSchoolCtx, {
            type: 'bar',
            data: {
                labels: schoolsData.map(s => s.name),
                datasets: [{
                    label: 'Students',
                    data: schoolsData.map(s => s.students),
                    backgroundColor: '#6366F1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Monthly Complaints Chart
    const monthlyComplaintsCtx = document.getElementById('monthlyComplaintsChart');
    if (monthlyComplaintsCtx) {
        new Chart(monthlyComplaintsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Complaints',
                    data: [6, 4, 5, 3, 2, 4],
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Route Performance Chart
    const routeCtx = document.getElementById('routePerformanceChart');
    if (routeCtx) {
        new Chart(routeCtx, {
            type: 'bar',
            data: {
                labels: routePerformanceData.map(r => r.route),
                datasets: [{
                    label: 'Total Trips',
                    data: routePerformanceData.map(r => r.trips),
                    backgroundColor: '#3B82F6'
                }, {
                    label: 'On-Time Rate %',
                    data: routePerformanceData.map(r => r.onTimeRate),
                    backgroundColor: '#10B981',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Trips' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'On-Time %' },
                        min: 0,
                        max: 100
                    }
                }
            }
        });
    }

    // Incident Report Chart
    const incidentCtx = document.getElementById('incidentChart');
    if (incidentCtx) {
        new Chart(incidentCtx, {
            type: 'radar',
            data: {
                labels: ['Delays', 'Minor Accidents', 'Vehicle Issues', 'Conductor Issues'],
                datasets: [{
                    label: 'This Month',
                    data: [5, 2, 3, 1],
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2
                }, {
                    label: 'Last Month',
                    data: [4, 3, 2, 2],
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }

    // Fleet Utilization Chart
    const fleetCtx = document.getElementById('fleetChart');
    if (fleetCtx) {
        new Chart(fleetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Maintenance', 'Idle'],
                datasets: [{
                    data: [8, 2, 0],
                    backgroundColor: ['#10B981', '#F59E0B', '#6B7280'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Initialize performance lists and tables
    renderDriverPerformance();
    renderStatisticsTable();
    updateMonthlySummary();
}

// Search functionality
const searchInput = document.querySelector('.search-box input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        console.log('Searching for:', searchTerm);
    });
}

// Period selector for charts
const periodSelector = document.querySelector('.period-selector');
if (periodSelector) {
    periodSelector.addEventListener('change', (e) => {
        console.log('Period changed to:', e.target.value);
        // Update chart data based on selected period
    });
}

// ===== REPORTS DATA =====
const routePerformanceData = [
    { route: 'Route A', trips: 145, onTimeRate: 96, avgPassengers: 38, incidents: 0, driver: 'Ahmed Khaled' },
    { route: 'Route B', trips: 132, onTimeRate: 92, avgPassengers: 35, incidents: 1, driver: 'Mohamed Ali' },
    { route: 'Route C', trips: 138, onTimeRate: 94, avgPassengers: 39, incidents: 0, driver: 'Youssef Hassan' },
    { route: 'Route D', trips: 125, onTimeRate: 88, avgPassengers: 42, incidents: 2, driver: 'Omar Samir' },
    { route: 'Route E', trips: 140, onTimeRate: 95, avgPassengers: 36, incidents: 0, driver: 'Ramy Mostafa' },
    { route: 'Route F', trips: 128, onTimeRate: 91, avgPassengers: 40, incidents: 1, driver: 'Karim Mahmoud' },
    { route: 'Route G', trips: 135, onTimeRate: 93, avgPassengers: 37, incidents: 0, driver: 'Hassan Ahmed' },
    { route: 'Route H', trips: 142, onTimeRate: 97, avgPassengers: 43, incidents: 0, driver: 'Mahmoud Fathy' },
    { route: 'Route I', trips: 148, onTimeRate: 98, avgPassengers: 44, incidents: 0, driver: 'Amr Abdelrahman' }
];

const driverPerformanceData = [
    { name: 'Amr Abdelrahman', score: 98, trips: 148, onTime: 98, safety: 100 },
    { name: 'Mahmoud Fathy', score: 97, trips: 142, onTime: 97, safety: 100 },
    { name: 'Ahmed Khaled', score: 96, trips: 145, onTime: 96, safety: 98 },
    { name: 'Ramy Mostafa', score: 95, trips: 140, onTime: 95, safety: 99 },
    { name: 'Route C Driver', score: 94, trips: 138, onTime: 94, safety: 98 },
    { name: 'Hassan Ahmed', score: 93, trips: 135, onTime: 93, safety: 97 },
    { name: 'Mohamed Ali', score: 92, trips: 132, onTime: 92, safety: 96 },
    { name: 'Karim Mahmoud', score: 91, trips: 128, onTime: 91, safety: 95 },
    { name: 'Omar Samir', score: 88, trips: 125, onTime: 88, safety: 92 }
];

const incidentData = {
    minor: 8,
    moderate: 3,
    severe: 0,
    categories: {
        delay: 5,
        minorAccident: 2,
        vehicleIssue: 3,
        conductorIssue: 1
    }
};

// Render Driver Performance List
function renderDriverPerformance() {
    const container = document.getElementById('driverPerformanceList');
    if (!container) return;
    
    container.innerHTML = driverPerformanceData.map(driver => `
        <div style="padding: 12px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="font-weight: 600; margin: 0 0 4px 0;">${driver.name}</p>
                <small style="color: #6b7280;">Trips: ${driver.trips} | On-Time: ${driver.onTime}%</small>
            </div>
            <div style="text-align: right;">
                <div style="background: ${driver.score >= 95 ? '#10b981' : driver.score >= 90 ? '#f59e0b' : '#ef4444'}; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 14px;">
                    ${driver.score}/100
                </div>
            </div>
        </div>
    `).join('');
}

// Render Statistics Table
function renderStatisticsTable() {
    const tbody = document.getElementById('statisticsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = routePerformanceData.map(route => `
        <tr>
            <td><strong>${route.route}</strong></td>
            <td>${route.trips}</td>
            <td><span class="progress-bar" style="width: ${route.onTimeRate}%;"></span> ${route.onTimeRate}%</td>
            <td>${route.avgPassengers}/45</td>
            <td><span class="status-badge ${route.incidents === 0 ? 'active' : 'warning'}">${route.incidents}</span></td>
            <td>${route.driver}</td>
            <td><span class="status-badge active">Active</span></td>
        </tr>
    `).join('');
}

// Update Monthly Summary Stats
function updateMonthlySummary() {
    const totalTrips = routePerformanceData.reduce((sum, route) => sum + route.trips, 0);
    const totalIncidents = routePerformanceData.reduce((sum, route) => sum + route.incidents, 0);
    const avgOnTimeRate = Math.round(routePerformanceData.reduce((sum, route) => sum + route.onTimeRate, 0) / routePerformanceData.length);
    
    document.getElementById('totalTrips').textContent = totalTrips.toLocaleString();
    document.getElementById('onTimeRate').textContent = avgOnTimeRate + '%';
    document.getElementById('avgAttendance').textContent = '92%';
    document.getElementById('incidentsCount').textContent = totalIncidents;
    document.getElementById('costPerTrip').textContent = '$' + (totalTrips > 0 ? (totalTrips / 10).toFixed(2) : '0.00');
    document.getElementById('fuelCost').textContent = '$' + Math.round(totalTrips * 3);
}

// Load requests from API
async function loadRequestsFromApi() {
    try {
        const response = await safestepApi('/api/admin/service-requests');
        const payload = response.data?.data ?? response.data ?? [];
        const items = Array.isArray(payload) ? payload : (payload.data || []);
        requestsData = items;
        renderRequests();
    } catch (err) {
        console.warn('Failed to load service requests:', err.message);
        renderRequests();
    }
}

function loadRequestsFromStorage() {
    // Disabled: API is the single source of truth for admin requests.
}

function focusBroadcastForm() {
    const form = document.getElementById('broadcastForm');
    if (!form) return;
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    const title = document.getElementById('broadcastTitle');
    if (title) title.focus();
}

document.getElementById('broadcastForm')?.addEventListener('submit', function(event) {
    event.preventDefault();
    sendBroadcastNotification();
});

function storeBroadcast(payload) {
    try {
        const raw = localStorage.getItem(BROADCAST_STORAGE_KEY);
        const list = raw ? JSON.parse(raw) : [];
        list.push(payload);
        localStorage.setItem(BROADCAST_STORAGE_KEY, JSON.stringify(list));
    } catch {
        // Ignore storage errors
    }
}

async function sendBroadcastNotification() {
    const form = document.getElementById('broadcastForm');
    if (form && !validateForm(form)) {
        showToast('Please complete required fields.', 'warning');
        return;
    }
    const titleInput = document.getElementById('broadcastTitle');
    const messageInput = document.getElementById('broadcastMessage');
    const typeSelect = document.getElementById('broadcastType');
    const toParents = document.getElementById('broadcastToParents');
    const toDrivers = document.getElementById('broadcastToDrivers');

    if (!titleInput || !messageInput || !typeSelect || !toParents || !toDrivers) return;

    const title = titleInput.value.trim();
    const message = messageInput.value.trim();
    const type = typeSelect.value;
    const targets = [];
    if (toParents.checked) targets.push('parent');
    if (toDrivers.checked) targets.push('driver');

    if (!title || !message) {
        showToast('Please enter a title and message.', 'warning');
        return;
    }

    if (!targets.length) {
        showToast('Select at least one recipient group.', 'warning');
        return;
    }

    try {
        for (const role of targets) {
            await safestepApi('/api/admin/notifications/send-bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ role, title, body: message })
            });
        }

        titleInput.value = '';
        messageInput.value = '';
        typeSelect.value = 'general';
        const templateSelect = document.getElementById('notificationTemplateSelect');
        if (templateSelect) templateSelect.value = '';
        toParents.checked = true;
        toDrivers.checked = true;

        showToast('Notification sent to database.', 'success');
        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        }
    } catch (err) {
        showToast(err.message || 'Failed to send notification.', 'error');
    }
}

// ===== PROFESSIONAL REPORT FUNCTIONS =====

// Export Report as PDF
function exportReportPDFDetailed() {
    const reportTitle = 'School Bus Tracking System - Monthly Report';
    const period = document.getElementById('reportPeriod')?.value || 'current';
    const timestamp = new Date().toLocaleString();
    
    const reportData = {
        title: reportTitle,
        period: period,
        generatedAt: timestamp,
        totalTrips: routePerformanceData.reduce((sum, r) => sum + r.trips, 0),
        avgOnTime: Math.round(routePerformanceData.reduce((sum, r) => sum + r.onTimeRate, 0) / routePerformanceData.length),
        attendance: 92,
        incidents: routePerformanceData.reduce((sum, r) => sum + r.incidents, 0)
    };
    
    let pdfContent = `
${reportData.title}
Generated: ${reportData.generatedAt}
Period: ${reportData.period}

MONTHLY SUMMARY
================
Total Trips: ${reportData.totalTrips}
On-Time Rate: ${reportData.avgOnTime}%
Attendance Rate: ${reportData.attendance}%
Incidents: ${reportData.incidents}

ROUTE PERFORMANCE
=================
`;
    
    routePerformanceData.forEach(route => {
        pdfContent += `\n${route.route}: ${route.trips} trips, ${route.onTimeRate}% on-time, Driver: ${route.driver}`;
    });
    
    pdfContent += `\n\nDRIVER RANKINGS\n===============\n`;
    driverPerformanceData.forEach((driver, idx) => {
        pdfContent += `${idx + 1}. ${driver.name}: ${driver.score}/100\n`;
    });
    
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(pdfContent));
    element.setAttribute('download', `Bus_Tracking_Report_${new Date().toISOString().split('T')[0]}.txt`);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
    
    alert('Report exported successfully!');
}

function exportTableToCsv(tableId, filename) {
    const element = document.getElementById(tableId);
    const table = element?.tagName === 'TABLE' ? element : element?.closest('table');
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tr'));
    const csv = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.map(cell => {
            const text = cell.textContent.replace(/\s+/g, ' ').trim();
            return `"${text.replace(/"/g, '""')}"`;
        }).join(',');
    }).join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${filename || 'export'}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportTableToPdf(tableId, filename) {
    const element = document.getElementById(tableId);
    const table = element?.tagName === 'TABLE' ? element : element?.closest('table');
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tr'));
    const text = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.map(cell => cell.textContent.replace(/\s+/g, ' ').trim()).join(' | ');
    }).join('\n');

    const blob = new Blob([text], { type: 'text/plain;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${filename || 'export'}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Update Report Period
function updateReportPeriod() {
    const period = document.getElementById('reportPeriod').value;
    console.log('Report period changed to:', period);
    
    // Here you would typically reload data based on the selected period
    let periodLabel = '';
    switch(period) {
        case 'current': periodLabel = 'Current Month'; break;
        case 'previous': periodLabel = 'Previous Month'; break;
        case 'quarter': periodLabel = 'This Quarter'; break;
        case 'year': periodLabel = 'This Year'; break;
    }
    
    alert(`Loaded report for: ${periodLabel}`);
}

// Update Report Timestamp
function updateReportTimestamp() {
    const now = new Date();
    const timeString = now.toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true
    });
    const element = document.getElementById('reportUpdateTime');
    if (element) {
        element.textContent = `Today at ${timeString}`;
    }
}

// Generate Report Summary
function generateReportSummary() {
    const totalTrips = routePerformanceData.reduce((sum, route) => sum + route.trips, 0);
    const avgOnTime = Math.round(routePerformanceData.reduce((sum, route) => sum + route.onTimeRate, 0) / routePerformanceData.length);
    const totalIncidents = routePerformanceData.reduce((sum, route) => sum + route.incidents, 0);
    
    return {
        totalTrips,
        avgOnTime,
        attendance: 92,
        incidents: totalIncidents,
        performance: avgOnTime >= 95 ? 'Excellent' : avgOnTime >= 90 ? 'Good' : 'Needs Improvement'
    };
}

// Initialize report features on page load
window.addEventListener('load', () => {
    updateReportTimestamp();
    generateReportSummary();
    
    // Update timestamp every minute
    setInterval(updateReportTimestamp, 60000);
});

// Financials functions
function addFinancialEntry() {
    openAddPage('financial');
}

function viewFinancialEntry(id) {
    alert('View Financial Entry details would open here.');
}

function editFinancialEntry(id) {
    alert('Edit Financial Entry form would open here.');
}

function deleteFinancialEntry(id) {
    if (confirm('Are you sure you want to delete this financial entry?')) {
        alert('Financial entry deleted.');
    }
}

// Maintenance functions
function addMaintenanceRecord() {
    openAddPage('maintenance');
}

function viewMaintenanceRecord(id) {
    alert('View Maintenance Record details would open here.');
}

function editMaintenanceRecord(id) {
    alert('Edit Maintenance Record form would open here.');
}

function deleteMaintenanceRecord(id) {
    if (confirm('Are you sure you want to delete this maintenance record?')) {
        alert('Maintenance record deleted.');
    }
}

// Live Tracking Functions
function viewBusLocation(id) {
    selectedTrackingBusId = id;
    navigateTo('live-tracking');
    renderLiveTracking();
    const panel = document.querySelector('.tracking-map');
    if (panel) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function trackBus(id) {
    viewBusLocation(id);
    const selectedBus = buildRegisteredTrackingData().find(bus => bus.id === id);
    if (selectedBus) {
        showToast(`Tracking ${selectedBus.busNumber} GPS`, 'info');
    }
}

// Students Functions
function viewStudent(id) {
    const student = studentsData.find(s => s.id === id);
    if (student) {
        alert(`Student Details:
ID: ${student.studentId}
Name: ${student.name}
Grade: ${student.grade}
School: ${student.school}
Parent: ${student.parent}
Pickup Location: ${student.pickupLocation}
Drop-off Location: ${student.dropoffLocation}
Status: ${student.status}`);
    }
}

function editStudent(id) {
    alert('Edit Student form would open here.');
}

function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        alert('Student deleted.');
    }
}

function addStudent() {
    openAddPage('student');
}

// Trips Functions
function viewTrip(id) {
    const trip = tripsData.find(t => t.id === id);
    if (trip) {
        const stops = routeStopsData
            .filter(stop => stop.tripId === trip.tripId)
            .sort((a, b) => a.order - b.order)
            .map(stop => `${stop.order}. ${stop.name} (${stop.expectedArrival}) - ${stop.location}`)
            .join('\n') || 'No stops configured';
        alert(`Trip Details:
Trip ID: ${trip.tripId}
Route: ${trip.routeName}
Bus: ${trip.bus}
Driver: ${trip.driver}
Start Time: ${trip.startTime}
End Time: ${trip.endTime}
Students: ${trip.students}
Status: ${trip.status}

Stops:
${stops}`);
    }
}

function editTrip(id) {
    alert('Edit Trip form would open here.');
}

function deleteTrip(id) {
    if (confirm('Are you sure you want to delete this trip?')) {
        alert('Trip deleted.');
    }
}

function addTrip() {
    openAddPage('trip');
}

// Notifications Functions
function viewNotification(id) {
    const notification = notificationsData.find(n => n.id === id);
    if (notification) {
        alert(`Notification Details:
Title: ${notification.title}
Type: ${notification.type}
Recipients: ${notification.recipients}
Sent Date: ${notification.sentDate}
Status: ${notification.status}`);
    }
}

function resendNotification(id) {
    if (confirm('Are you sure you want to resend this notification?')) {
        alert('Notification resent successfully.');
    }
}

function sendNotification() {
    alert('Send Notification form would open here.');
}

// Complaints Functions
function viewComplaint(id) {
    const complaint = complaintsData.find(c => c.id === id);
    if (complaint) {
        alert(`Complaint Details:
Complaint ID: ${complaint.complaintId}
Submitted By: ${complaint.submittedBy}
Type: ${complaint.type}
Subject: ${complaint.subject}
Priority: ${complaint.priority}
Status: ${complaint.status}
Date: ${complaint.date}`);
    }
}

async function updateComplaintStatus(id) {
    const complaint = complaintsData.find(c => c.id === id);
    if (!complaint) return;
    const newStatus = prompt('Enter new status (open/in-progress/resolved/closed):', complaint.status);
    if (!newStatus || !['open', 'in-progress', 'resolved', 'closed'].includes(newStatus)) return;
    try {
        await safestepApi(`/api/admin/reports/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        complaint.status = newStatus;
        renderComplaints();
        showToast('Complaint status updated.', 'success');
        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        }
    } catch (err) {
        showToast('Failed to update complaint.', 'error');
    }
}

function addComplaint() {
    openAddPage('complaint');
}

// Schools Functions
function viewSchool(id) {
    const school = schoolsData.find(s => s.id === id);
    if (school) {
        alert(`School Details:
Name: ${school.name}
Type: ${school.type}
District: ${school.district}
Address: ${school.address}
Contact: ${school.contact}
Students: ${school.students}
Status: ${school.status}`);
    }
}

function editSchool(id) {
    alert('Edit School form would open here.');
}

function deleteSchool(id) {
    if (confirm('Are you sure you want to delete this school?')) {
        alert('School deleted.');
    }
}

function addSchool() {
    openAddPage('school');
}

// Users Functions
function viewUser(id) {
    const user = usersData.find(u => u.id === id);
    if (user) {
        alert(`User Details:
Name: ${user.name}
Email: ${user.email}
Role: ${user.role}
Department: ${user.department}
Last Login: ${user.lastLogin}
Status: ${user.status}`);
    }
}

function editUser(id) {
    alert('Edit User form would open here.');
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        alert('User deleted.');
    }
}

function addUser() {
    openAddPage('user');
}

// Settings Functions
function saveSettings() {
    // Collect all settings values
    const settings = {
        systemName: document.getElementById('systemName').value,
        systemDescription: document.getElementById('systemDescription').value,
        defaultLanguage: document.getElementById('defaultLanguage').value,
        timezone: document.getElementById('timezone').value,
        dateFormat: document.getElementById('dateFormat').value,
        currency: document.getElementById('currency').value,
        emailNotifications: document.getElementById('emailNotifications').checked,
        smsNotifications: document.getElementById('smsNotifications').checked,
        pushNotifications: document.getElementById('pushNotifications').checked,
        emergencyAlerts: document.getElementById('emergencyAlerts').checked,
        sessionTimeout: document.getElementById('sessionTimeout').value,
        passwordPolicy: document.getElementById('passwordPolicy').value,
        maxLoginAttempts: document.getElementById('maxLoginAttempts').value,
        twoFactorAuth: document.getElementById('twoFactorAuth').checked,
        maxFileSize: document.getElementById('maxFileSize').value,
        backupFrequency: document.getElementById('backupFrequency').value,
        logRetention: document.getElementById('logRetention').value,
        maintenanceMode: document.getElementById('maintenanceMode').checked,
        defaultBusCapacity: document.getElementById('defaultBusCapacity').value,
        speedLimit: document.getElementById('speedLimit').value,
        routeDeviation: document.getElementById('routeDeviation').value,
        autoTracking: document.getElementById('autoTracking').checked
    };

    // Save to localStorage (in a real app, this would be sent to server)
    localStorage.setItem('adminSettings', JSON.stringify(settings));

    // Update last saved time
    const now = new Date();
    document.querySelector('.settings-last-saved').textContent =
        `Last saved: ${now.toLocaleDateString()} at ${now.toLocaleTimeString()}`;

    showToast('Settings saved successfully!', 'success');
}

function exportSettings() {
    const settings = localStorage.getItem('adminSettings');
    if (!settings) {
        showToast('No settings found to export.', 'warning');
        return;
    }

    const dataStr = JSON.stringify(JSON.parse(settings), null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});

    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = 'admin-settings.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast('Settings exported successfully!', 'success');
}

function importSettings() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';

    input.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const settings = JSON.parse(e.target.result);

                // Apply settings to form
                Object.keys(settings).forEach(key => {
                    const element = document.getElementById(key);
                    if (element) {
                        if (element.type === 'checkbox') {
                            element.checked = settings[key];
                        } else {
                            element.value = settings[key];
                        }
                    }
                });

                showToast('Settings imported successfully!', 'success');
            } catch (error) {
                showToast('Invalid settings file.', 'error');
            }
        };
        reader.readAsText(file);
    };

    input.click();
}

function resetSettings() {
    if (confirm('Are you sure you want to reset all settings to default? This action cannot be undone.')) {
        // Reset all form fields to default values
        document.getElementById('systemName').value = 'SAFESTEP BUS';
        document.getElementById('systemDescription').value = 'School Bus Tracking & Management System';
        document.getElementById('defaultLanguage').value = 'en';
        document.getElementById('timezone').value = 'Africa/Cairo';
        document.getElementById('dateFormat').value = 'DD/MM/YYYY';
        document.getElementById('currency').value = 'EGP';

        document.getElementById('emailNotifications').checked = true;
        document.getElementById('smsNotifications').checked = true;
        document.getElementById('pushNotifications').checked = false;
        document.getElementById('emergencyAlerts').checked = true;

        document.getElementById('sessionTimeout').value = '30';
        document.getElementById('passwordPolicy').value = 'strong';
        document.getElementById('maxLoginAttempts').value = '5';
        document.getElementById('twoFactorAuth').checked = false;

        document.getElementById('maxFileSize').value = '10';
        document.getElementById('backupFrequency').value = 'weekly';
        document.getElementById('logRetention').value = '90';
        document.getElementById('maintenanceMode').checked = false;

        document.getElementById('defaultBusCapacity').value = '45';
        document.getElementById('speedLimit').value = '80';
        document.getElementById('routeDeviation').value = '100';
        document.getElementById('autoTracking').checked = true;

        // Remove from localStorage
        localStorage.removeItem('adminSettings');

        showToast('Settings reset to default values.', 'info');
    }
}

// Toast notification system
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;

    // Set icon based on type
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };

    toast.innerHTML = `
        <i class="${icons[type] || icons.info}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add to page
    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// ===== ADMIN PROFILE FUNCTIONS =====

// Navigate between pages
function navigateTo(pageId) {
    // Hide all pages
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => {
        page.classList.remove('active');
    });

    // Show the selected page
    const selectedPage = document.getElementById(pageId);
        if (selectedPage) {
            selectedPage.classList.add('active');
        
        // Reset sidebar visibility when navigating to admin-profile
        if (pageId === 'admin-profile') {
            const grid = document.getElementById('profileContentGrid');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            if (grid) {
                grid.classList.remove('sidebar-visible');
            }
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                icon.className = 'fas fa-chevron-right';
                toggleBtn.querySelector('span').textContent = 'Info';
            }
        }
        
        // Update page title
        const pageTitle = document.getElementById('pageTitle');
        if (pageTitle) {
            const titles = {
                'dashboard': 'Dashboard Overview',
                'parents': 'Parents Management',
                'drivers': 'Drivers Management',
                'buses': 'Bus Fleet Management',
                'reports': 'Reports & Analytics',
                'requests': 'Requests Management',
                'account-recovery': 'Account Recovery',
                'financials': 'Financial Management',
                'maintenance': 'Bus Maintenance',
                'live-tracking': 'Live Bus Tracking',
                'students': 'Students Management',
                'trips': 'Trips & Routes',
                'notifications': 'Notifications',
                'emergency-logs': 'Emergency Logs',
                'complaints': 'Complaints Management',
                'schools': 'Schools Management',
                'users': 'Users & Roles',
                'settings': 'System Settings',
                'activity-logs': 'Activity Logs',
                'admin-profile': 'Admin Profile'
            };
            pageTitle.textContent = titles[pageId] || 'Page';
        }

        // Update navigation active state
        const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-page') === pageId) {
                    link.classList.add('active');
                }
            });

            if (pageId === 'applications') {
                renderApplications();
                loadApplicationsFromApi();
            } else if (pageId === 'parents') {
                renderParents();
            } else if (pageId === 'drivers') {
                renderDrivers();
            } else if (pageId === 'buses') {
                renderBuses();
            } else if (pageId === 'reports') {
                renderStatisticsTable();
                renderDriverPerformance();
            } else if (pageId === 'requests') {
                renderRequests();
            } else if (pageId === 'financials') {
                renderFinancials();
                renderPayments();
            } else if (pageId === 'live-tracking') {
                renderLiveTracking();
                renderTripPlayback();
            } else if (pageId === 'account-recovery') {
                renderAccountRecovery();
            } else if (pageId === 'students') {
                renderStudents();
                renderAttendance();
                renderAttendanceRealtime();
            } else if (pageId === 'trips') {
                renderTrips();
                renderAssignmentOverview();
                renderRouteStops();
            } else if (pageId === 'notifications') {
                renderNotifications();
                renderNotificationTemplates();
                renderEmergencyAlerts();
            } else if (pageId === 'emergency-logs') {
                renderEmergencyLogs();
                renderSmartAlerts();
            } else if (pageId === 'activity-logs') {
                renderActivityLogs();
                initStudentQrTools();
            }

            reapplyGlobalSearch();
        }
}

// Edit Admin Profile
function editAdminProfile() {
    const newName = prompt('Enter your full name:', 'Admin User');
    if (newName && newName.trim()) {
        showToast('Profile updated successfully!', 'success');
    }
}

// Change Admin Password
function changeAdminPassword() {
    const currentPassword = prompt('Enter your current password:');
    if (!currentPassword) return;

    const newPassword = prompt('Enter your new password:');
    if (!newPassword) return;

    const confirmPassword = prompt('Confirm your new password:');
    if (!confirmPassword) return;

    if (newPassword !== confirmPassword) {
        showToast('Passwords do not match!', 'error');
        return;
    }

    if (newPassword.length < 8) {
        showToast('Password must be at least 8 characters long!', 'error');
        return;
    }

    showToast('Password changed successfully!', 'success');
}

// Admin Security Settings
function adminSecuritySettings() {
    const twoFactorEnabled = confirm('Enable Two-Factor Authentication (2FA)?');
    if (twoFactorEnabled) {
        showToast('Two-Factor Authentication has been enabled!', 'success');
    } else {
        showToast('Two-Factor Authentication is disabled.', 'info');
    }
}

// Toggle Admin Sidebar
function toggleAdminSidebar() {
    const grid = document.getElementById('profileContentGrid');
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    
    if (grid && toggleBtn) {
        grid.classList.toggle('sidebar-visible');
        
        // Change button icon
        const icon = toggleBtn.querySelector('i');
        if (grid.classList.contains('sidebar-visible')) {
            icon.className = 'fas fa-chevron-left';
            toggleBtn.querySelector('span').textContent = 'Hide';
        } else {
            icon.className = 'fas fa-chevron-right';
            toggleBtn.querySelector('span').textContent = 'Info';
        }
    }
}
// Missing button functions
function addDriver() {
    openAddPage('driver');
}

function exportReportPDF() {
    showToast('Exporting report to PDF...', 'info');
    exportReportPDFDetailed();
    setTimeout(() => {
        showToast('Report exported successfully!', 'success');
    }, 1500);
}

async function refreshTracking() {
    showToast('Refreshing live tracking...', 'info');
    await loadLiveTrackingFromApi();
    await renderLiveTracking({ skipReload: true });
    showToast('Tracking data updated!', 'success');
}

// Show notifications popup
function showNotifications() {
    const notifications = [
        { msg: 'New driver registered: Ahmed Hassan', time: '5 min ago', icon: 'fas fa-user-plus', color: '#3b82f6' },
        { msg: 'Bus maintenance due for Bus #5', time: '15 min ago', icon: 'fas fa-tools', color: '#f59e0b' },
        { msg: 'Parent request pending approval', time: '2 hours ago', icon: 'fas fa-inbox', color: '#8b5cf6' },
        { msg: 'System backup completed successfully', time: '4 hours ago', icon: 'fas fa-check-circle', color: '#10b981' },
        { msg: 'New trip scheduled for tomorrow', time: 'Yesterday', icon: 'fas fa-route', color: '#06b6d4' }
    ];

    let notifHtml = '<div style="max-height: 400px; overflow-y: auto;">';
    notifications.forEach((notif, idx) => {
        notifHtml += `
            <div style="padding: 12px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 10px; align-items: flex-start;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: ${notif.color}20; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="${notif.icon}" style="color: ${notif.color}; font-size: 14px;"></i>
                </div>
                <div style="flex: 1; font-size: 13px;">
                    <p style="margin: 0; color: #1f2937; font-weight: 500;">${notif.msg}</p>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-size: 12px;">${notif.time}</p>
                </div>
            </div>
        `;
    });
    notifHtml += '</div>';

    // Create modal-like popup
    const popup = document.createElement('div');
    popup.style.cssText = `
        position: fixed;
        top: 70px;
        right: 20px;
        width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        z-index: 2000;
        animation: slideInRight 0.3s ease;
    `;
    popup.innerHTML = `
        <div style="padding: 16px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1f2937;">Notifications</h3>
            <button onclick="this.closest('div').remove()" style="background: none; border: none; cursor: pointer; font-size: 18px; color: #9ca3af;">âœ•</button>
        </div>
        ${notifHtml}
    `;
    
    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 8000);
}

function safestepReplaceArray(target, items) {
    target.splice(0, target.length, ...items);
}

function safestepValidationMessage(data, fallback) {
    const errors = data && data.errors ? data.errors : null;
    if (errors && typeof errors === 'object') {
        const first = Object.values(errors)[0];
        if (Array.isArray(first) && first.length) return first[0];
        if (typeof first === 'string' && first) return first;
    }
    return (data && data.message && data.message !== 'Validation failed.') ? data.message : fallback;
}

async function safestepApi(url, options = {}) {
    const token = localStorage.getItem('token') || localStorage.getItem('safestep_token');
    const { headers: customHeaders, ...restOptions } = options;
    const response = await fetch(url, {
        credentials: 'same-origin',
        ...restOptions,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': token ? `Bearer ${token}` : '',
            ...(customHeaders || {})
        }
    });

    if (response.status === 401) {
        localStorage.removeItem('safestep_token');
        localStorage.removeItem('token');
        window.location.href = '/login?role=admin';
        return;
    }

    if (response.status === 403) {
        throw new Error('Access Denied');
    }

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
        throw new Error(safestepValidationMessage(data, `Request failed (${response.status})`));
    }
    return data;
}

function safestepDate(value) {
    return value ? String(value).slice(0, 10) : new Date().toISOString().slice(0, 10);
}

function normalizeDashboardStatus(value, active) {
    const status = String(value || '').toLowerCase();
    if (status === 'approved') return 'active';
    if (status === 'rejected') return 'inactive';
    if (status) return status;
    return active ? 'active' : 'inactive';
}

function pageForResourceType(type) {
    return {
        parent: 'parents',
        driver: 'drivers',
        student: 'students',
        bus: 'buses',
        trip: 'trips',
        user: 'users',
        complaint: 'complaints',
        school: 'schools',
        financial: 'financials',
        maintenance: 'maintenance'
    }[type] || null;
}

async function hydrateAdminDashboardFromApi() {
    try {
        const settled = await Promise.allSettled([
            safestepApi('/api/admin/dashboard/stats'),
            safestepApi('/api/admin/applications'),
            safestepApi('/api/admin/parents?per_page=all'),
            safestepApi('/api/admin/drivers?per_page=all'),
            safestepApi('/api/admin/students?per_page=all'),
            safestepApi('/api/admin/trips'),
            safestepApi('/api/admin/buses?per_page=all'),
            safestepApi('/api/admin/notifications'),
            safestepApi('/api/admin/service-requests'),
            safestepApi('/api/admin/reports'),
            safestepApi('/api/admin/schools'),
            safestepApi('/api/admin/financial-entries'),
            safestepApi('/api/admin/maintenance-records'),
            safestepApi('/api/admin/routes'),
            safestepApi('/api/admin/users')
        ]);
        const safeResult = (index, fallback = { data: [] }) =>
            settled[index].status === 'fulfilled' ? settled[index].value : fallback;

        const stats = safeResult(0, { data: {} });
        const applications = safeResult(1);
        const parents = safeResult(2);
        const drivers = safeResult(3);
        const students = safeResult(4);
        const trips = safeResult(5);
        const buses = safeResult(6);
        const notifications = safeResult(7);
        const serviceRequests = safeResult(8);
        const reports = safeResult(9);
        const schools = safeResult(10);
        const financials = safeResult(11);
        const maintenance = safeResult(12);
        const routes = safeResult(13);
        const users = safeResult(14);

        // Load service requests into requestsData for the Requests Center
        const servicePayload = serviceRequests.data?.data ?? serviceRequests.data ?? [];
        const serviceReqItems = Array.isArray(servicePayload) ? servicePayload : (servicePayload.data || []);
        requestsData = serviceReqItems;
        renderRequests();

        if (stats.data) {
            const pendingApps = (applications.data || []).filter(a => a.status === 'pending').length;
            const pendingServiceReqs = (serviceReqItems || []).filter(r => r.status === 'pending').length;
            const statsPayload = {
                ...stats.data,
                pending_requests: stats.data.pending_requests ?? (pendingApps + pendingServiceReqs)
            };
            applyDashboardStats(statsPayload, { force: true });
            renderAdminRecentActivity(stats.data.recent_activity);
        }

        safestepReplaceArray(applicationsData, applications.data || []);
        safestepReplaceArray(busRoutesData, routes.data || []);

        safestepReplaceArray(parentsData, (parents.data || []).map(mapParentForDashboard));

        safestepReplaceArray(driversData, (drivers.data || []).map(mapDriverForDashboard));

        safestepReplaceArray(usersData, (users.data || []).map(user => ({
            id: user.id,
            name: user.name || 'User',
            email: user.email || '',
            role: user.role || (Array.isArray(user.roles) ? user.roles[0] : 'parent'),
            department: user.role === 'admin' ? 'Administration' : (user.role === 'driver' ? 'Transportation' : 'Parent Portal'),
            lastLogin: user.updated_at ? new Date(user.updated_at).toLocaleString() : 'Never',
            status: user.status || 'active'
        })));

        safestepReplaceArray(studentsData, (students.data || []).map(student => ({
            id: student.id,
            studentId: `STU${String(student.id).padStart(3, '0')}`,
            name: student.name || student.full_name,
            grade: student.grade || '',
            school: student.school_name || '',
            parent: student.parent?.user?.name || '',
            pickupLocation: 'Assigned route stop',
            dropoffLocation: student.school_name || '',
            status: student.active ? 'active' : 'inactive'
        })));

        safestepReplaceArray(tripsData, (trips.data || []).map(trip => ({
            id: trip.id,
            tripId: `TRP${String(trip.id).padStart(3, '0')}`,
            routeName: trip.route?.name || 'Route',
            bus: trip.bus?.bus_number || '',
            driver: trip.driver?.name || trip.driver?.user?.name || '',
            startTime: trip.started_at ? new Date(trip.started_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '--',
            endTime: trip.ended_at ? new Date(trip.ended_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '--',
            students: trip.attendance_count || 0,
            status: trip.status === 'active' ? 'in-progress' : trip.status,
            date: trip.trip_date
        })));

        safestepReplaceArray(busesData, (buses.data || []).map(bus => ({
            id: bus.id,
            busNumber: bus.bus_number,
            plate: bus.plate_number || '',
            driver: 'Assigned by trips',
            route: 'Assigned by trips',
            capacity: bus.capacity,
            status: bus.status || (bus.active ? 'active' : 'inactive')
        })));

        safestepReplaceArray(notificationsData, (notifications.data || []).map((notification, index) => ({
            id: notification.id || index + 1,
            title: notification.data?.title || notification.data?.body || 'Notification',
            type: notification.data?.submission_type || notification.data?.type || 'general',
            recipients: 'Admin',
            sentDate: notification.created_at || new Date().toISOString(),
            status: notification.read_at ? 'read' : 'sent',
            message: notification.data?.body || ''
        })));

        safestepReplaceArray(complaintsData, (reports.data || []).map(report => ({
            id: report.id,
            complaintId: `CMP${String(report.id).padStart(3, '0')}`,
            submittedBy: report.user?.name || 'System',
            type: report.type || 'complaint',
            subject: report.title || 'Complaint',
            priority: 'medium',
            status: report.status || 'open',
            date: safestepDate(report.created_at)
        })));

        safestepReplaceArray(schoolsData, (schools.data || []).map(school => ({
            id: school.id,
            name: school.name,
            type: 'school',
            district: school.address || '—',
            address: school.address || '',
            contact: school.phone || school.email || '',
            students: 0,
            status: 'active'
        })));

        safestepReplaceArray(financialsData, (financials.data || []).map(entry => ({
            id: entry.id,
            date: safestepDate(entry.entry_date || entry.created_at),
            type: entry.type,
            description: entry.description || entry.title,
            amount: entry.type === 'expense' ? -Math.abs(Number(entry.amount) || 0) : Math.abs(Number(entry.amount) || 0),
            enteredBy: 'Admin'
        })));

        safestepReplaceArray(maintenanceData, (maintenance.data || []).map(record => ({
            id: record.id,
            busNumber: record.bus?.bus_number ? `Bus ${record.bus.bus_number}` : '—',
            plateNumber: record.bus?.plate_number || '',
            type: record.status || 'maintenance',
            description: record.description || record.title,
            date: safestepDate(record.maintenance_date || record.created_at),
            cost: Number(record.cost) || 0,
            technician: 'Fleet'
        })));

        renderParents();
        renderApplications();
        renderDrivers();
        renderStudents();
        renderTrips();
        renderBuses();
        renderNotifications();
        renderComplaints();
        renderFinancials();
        renderMaintenance();
        renderSchools();
        renderUsers();
        updateNotificationBadge();
        console.log('ADMIN DASHBOARD API HYDRATED', {
            applications: applicationsData.length,
            parents: parentsData.length,
            drivers: driversData.length,
            buses: busesData.length
        });
    } catch (error) {
        console.warn('SafeStep API hydration skipped:', error.message);
    }
}

// Handle add resource form success - immediately refresh the relevant section
document.addEventListener('ajaxform:success', async function(e) {
    var form = e.detail.form;
    if (!form || form.id !== 'addResourceForm') return;
    e.preventDefault();
    const resourceType = form.dataset.resourceType || '';
    const targetPage = pageForResourceType(resourceType);
    const modal = document.getElementById('addResourceModal');
    if (modal) modal.style.display = 'none';
    showToast('Resource created successfully!', 'success');

    if (typeof hydrateAdminDashboardFromApi === 'function') {
        await hydrateAdminDashboardFromApi();
    }
    if (targetPage && typeof navigateTo === 'function') {
        navigateTo(targetPage);
    }
});

async function pollAdminDashboardStats() {
    try {
        const stats = await safestepApi('/api/admin/dashboard/stats');
        if (!stats?.data) return;

        const prevPending = lastDashboardStats.pendingRequestsStat ?? 0;

        applyDashboardStats(stats.data, {
            onIncrease(id) {
                if (id === 'pendingRequestsStat') {
                    playNotificationSound();
                    showToast('طلب أو تقديم جديد وصل!', 'info');
                    loadRequestsFromApi();
                    loadApplicationsFromApi();
                    renderAdminRecentActivity(stats.data.recent_activity);
                } else if (id === 'complaintsTodayStat') {
                    showToast('بلاغ أو شكوى جديدة', 'warning');
                }
            }
        });

        const notifications = await safestepApi('/api/admin/notifications');
        const items = notifications.data || [];
        const unread = items.filter(n => !n.read_at).length;
        const badge = document.querySelector('.notification-icon .badge');
        if (badge) {
            badge.textContent = unread > 99 ? '99+' : String(unread);
        }
    } catch (error) {
        console.warn('Dashboard poll skipped:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    hydrateAdminDashboardFromApi();
    setInterval(pollAdminDashboardStats, 10000);
});

document.addEventListener('spa:pageChanged', (e) => {
    if (e.detail?.pageId === 'requests') {
        loadRequestsFromApi();
    }
    if (e.detail?.pageId === 'school-requests') {
        loadSchoolRequestsFromApi();
    }
    if (e.detail?.pageId !== 'live-tracking' && typeof stopLiveTrackingPoll === 'function') {
        stopLiveTrackingPoll();
    }
});

updateApplicationStatus = async function(id, status) {
    const buttons = document.querySelectorAll(`button[onclick*="updateApplicationStatus(${id},"]`);
    buttons.forEach(button => {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    });

    try {
        const response = await safestepApi(`/api/admin/applications/${id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
        });

        const updated = response.data;
        const index = applicationsData.findIndex(app => Number(app.id) === Number(id));
        if (index >= 0) applicationsData[index] = updated;

        const role = String(updated?.role || '').toLowerCase();
        const targetPage = role === 'driver' ? 'drivers' : (role === 'parent' ? 'parents' : 'applications');

        if (typeof hydrateAdminDashboardFromApi === 'function') {
            await hydrateAdminDashboardFromApi();
        } else {
            renderApplications();
        }

        if (status === 'accepted') {
            showToast(`Application approved and moved to ${role === 'driver' ? 'Drivers' : (role === 'parent' ? 'Parents' : 'Applications')}.`, 'success');
            if (typeof navigateTo === 'function' && (role === 'driver' || role === 'parent')) {
                navigateTo(targetPage);
            }
        } else {
            showToast('Application updated successfully', 'success');
        }
    } catch (error) {
        showToast(error.message || 'Unable to update application', 'error');
        console.error('Application status update failed:', error);
    } finally {
        buttons.forEach(button => {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Save';
        });
    }
};

// --- School Requests Section ---
let schoolRequestsData = [];

async function loadSchoolRequestsFromApi() {
    try {
        const response = await safestepApi('/api/admin/service-requests?role=school_admin');
        const payload = response.data?.data ?? response.data ?? [];
        const items = Array.isArray(payload) ? payload : (payload.data || []);
        schoolRequestsData = items;
        renderSchoolRequests();
    } catch (err) {
        console.warn('Failed to load school requests:', err.message);
        renderSchoolRequests();
    }
}

function renderSchoolRequests() {
    const tbody = document.querySelector('#schoolRequestsTable tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (schoolRequestsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 20px;">No pending school requests.</td></tr>';
        return;
    }

    schoolRequestsData.forEach(req => {
        const tr = document.createElement('tr');
        const metadata = req.metadata || {};
        let details = '';
        if (req.request_type === 'add_student') {
            details = `<b>Student:</b> ${metadata.full_name || metadata.name || ''} (Grade: ${metadata.grade || ''})`;
        } else if (req.request_type === 'add_bus') {
            details = `<b>Bus:</b> ${metadata.bus_number || ''} (Plate: ${metadata.plate_number || ''}, Capacity: ${metadata.capacity || ''})`;
        } else if (req.request_type === 'add_route') {
            details = `<b>Route:</b> ${metadata.name || ''} (${metadata.type || ''}, ${metadata.estimated_minutes || ''} min)`;
        } else if (req.request_type === 'add_trip') {
            details = `<b>Trip:</b> ${metadata.trip_date || ''} (${metadata.shift || ''})`;
        } else {
            details = req.description;
        }

        tr.innerHTML = `
            <td><strong>${req.user?.name || req.from || 'School Admin'}</strong></td>
            <td><span class="status-badge active">${req.request_type.replace('add_', 'Add ').toUpperCase()}</span></td>
            <td>${details}</td>
            <td>
                <span class="status-badge ${req.priority === 'high' ? 'danger' : req.priority === 'medium' ? 'warning' : 'active'}">
                    ${req.priority.charAt(0).toUpperCase() + req.priority.slice(1)}
                </span>
            </td>
            <td>
                <span class="status-badge ${req.status}">
                    ${req.status === 'pending' ? 'New' : req.status === 'resolved' ? 'Approved' : req.status === 'rejected' ? 'Rejected' : req.status}
                </span>
            </td>
            <td>${req.created_at ? new Date(req.created_at).toLocaleString() : ''}</td>
            <td>
                <div class="table-actions">
                    ${req.status === 'pending' ? `
                        <button class="btn btn-success" style="padding: 6px 10px; font-size: 12px;" onclick="approveSchoolRequest(${req.id})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-danger" style="padding: 6px 10px; font-size: 12px; margin-left: 5px;" onclick="rejectSchoolRequest(${req.id})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    ` : `
                        <span style="color: var(--text-muted); font-size: 12px;">No Actions</span>
                    `}
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

async function approveSchoolRequest(id) {
    if (!confirm('Are you sure you want to approve this request? This will create the entity in the system.')) return;
    try {
        await safestepApi(`/api/admin/service-requests/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'resolved' })
        });
        showToast('Request approved and created successfully.', 'success');
        await loadSchoolRequestsFromApi();
    } catch (err) {
        console.error(err);
        showToast('Failed to approve request.', 'error');
    }
}

async function rejectSchoolRequest(id) {
    if (!confirm('Are you sure you want to reject this request?')) return;
    try {
        await safestepApi(`/api/admin/service-requests/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'rejected' })
        });
        showToast('Request rejected.', 'info');
        await loadSchoolRequestsFromApi();
    } catch (err) {
        console.error(err);
        showToast('Failed to reject request.', 'error');
    }
}

window.loadSchoolRequestsFromApi = loadSchoolRequestsFromApi;
window.renderSchoolRequests = renderSchoolRequests;
window.approveSchoolRequest = approveSchoolRequest;
window.rejectSchoolRequest = rejectSchoolRequest;
