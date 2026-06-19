@php
    $teamMembers = [
        [
            'name' => 'Ahmed Hassan',
            'name_ar' => 'أحمد حسن',
            'role' => 'Platform Administrator',
            'role_ar' => 'مدير المنصة',
            'image' => 'team/team-admin.jpg',
            'badge' => 'Admin',
            'badge_class' => 'badge-admin',
            'delay' => '0.3s',
        ],
        [
            'name' => 'Sara Mahmoud',
            'name_ar' => 'سارة محمود',
            'role' => 'Fleet Operations Manager',
            'role_ar' => 'مديرة عمليات الأسطول',
            'image' => 'team/team-operations.jpg',
            'badge' => 'Operations',
            'badge_class' => 'badge-operations',
            'delay' => '0.5s',
        ],
        [
            'name' => 'Mohamed Ali',
            'name_ar' => 'محمد علي',
            'role' => 'Senior Bus Driver',
            'role_ar' => 'سائق حافلة أول',
            'image' => 'team/team-driver.jpg',
            'badge' => 'Driver',
            'badge_class' => 'badge-driver',
            'delay' => '0.7s',
        ],
        [
            'name' => 'Nour El-Din',
            'name_ar' => 'نور الدين',
            'role' => 'School Safety Coordinator',
            'role_ar' => 'منسقة السلامة المدرسية',
            'image' => 'team/team-school.jpg',
            'badge' => 'School',
            'badge_class' => 'badge-school',
            'delay' => '0.9s',
        ],
    ];
@endphp

<!-- Team Start -->
<div class="container-xxl py-5">
    <div class="container py-5">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="text-secondary text-uppercase">Our Team</h6>
            <h1 class="mb-3">Expert Team Members</h1>
            <p class="team-section-lead mx-auto mb-5">
                Meet the SafeStep leadership, operations, drivers, and school coordination team
                keeping every ride safe, tracked, and on time.
            </p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($teamMembers as $member)
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ $member['delay'] }}">
                    <div class="team-item team-item--role p-4 h-100">
                        <div class="team-photo-wrap overflow-hidden mb-4">
                            <img
                                class="img-fluid team-photo"
                                src="{{ asset('img/' . $member['image']) }}"
                                alt="{{ $member['name'] }} — {{ $member['role'] }}"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($member['name']) }}&size=400&background=1d4ed8&color=fff&bold=true';"
                            >
                            <span class="team-role-badge {{ $member['badge_class'] }}">{{ $member['badge'] }}</span>
                        </div>
                        <h5 class="mb-1 team-member-name">{{ $member['name'] }}</h5>
                        <p class="team-member-name-ar mb-1">{{ $member['name_ar'] }}</p>
                        <p class="team-member-role mb-0">{{ $member['role'] }}</p>
                        <small class="team-member-role-ar">{{ $member['role_ar'] }}</small>
                        <div class="btn-slide mt-3">
                            <i class="fa fa-share"></i>
                            <span>
                                <a href="{{ url('/contact') }}" title="Contact {{ $member['name'] }}"><i class="fas fa-envelope"></i></a>
                                <a href="{{ url('/about') }}" title="About SafeStep"><i class="fas fa-info-circle"></i></a>
                                <a href="{{ url('/join') }}" title="Join SafeStep"><i class="fas fa-user-plus"></i></a>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Team End -->
