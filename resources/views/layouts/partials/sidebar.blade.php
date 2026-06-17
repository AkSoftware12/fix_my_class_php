@php($user = auth()->user())
<aside class="fmc-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <span class="logo-badge"><i class="bi bi-mortarboard-fill"></i></span>
        <span>{{ setting('app_name', 'Fix My Class') }}</span>
    </a>

    <div class="sidebar-scroll">
        <div class="nav-label">Main</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ active_menu('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </nav>

        @canany(['cities.view', 'coachings.view', 'branches.view', 'banners.view'])
            <div class="nav-label">Platform</div>
            <nav class="nav flex-column">
                @can('cities.view')
                    <a class="nav-link {{ active_menu('admin.cities.*') }}" href="{{ route('admin.cities.index') }}">
                        <i class="bi bi-geo-alt"></i> Cities
                    </a>
                @endcan
                @can('coachings.view')
                    <a class="nav-link {{ active_menu('admin.coachings.*') }}" href="{{ route('admin.coachings.index') }}">
                        <i class="bi bi-building"></i> Coaching Centres
                    </a>
                @endcan
                @can('branches.view')
                    <a class="nav-link {{ active_menu('admin.branches.*') }}" href="{{ route('admin.branches.index') }}">
                        <i class="bi bi-diagram-3"></i> Branches
                    </a>
                @endcan
                @can('subscriptions.view')
                    <a class="nav-link {{ active_menu(['admin.subscriptions.*', 'admin.subscription-plans.*']) }}" href="{{ route('admin.subscriptions.index') }}">
                        <i class="bi bi-credit-card"></i> Subscriptions
                    </a>
                @endcan
                @can('banners.view')
                    <a class="nav-link {{ active_menu('admin.banners.*') }}" href="{{ route('admin.banners.index') }}">
                        <i class="bi bi-image"></i> Banners
                    </a>
                @endcan
            </nav>
        @endcanany

        @canany(['users.view', 'roles.view'])
            <div class="nav-label">Access Control</div>
            <nav class="nav flex-column">
                @can('users.view')
                    <a class="nav-link {{ active_menu('admin.users.*') }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                @endcan
                @can('roles.view')
                    <a class="nav-link {{ active_menu('admin.roles.*') }}" href="{{ route('admin.roles.index') }}">
                        <i class="bi bi-shield-lock"></i> Roles &amp; Permissions
                    </a>
                @endcan
            </nav>
        @endcanany

        @canany(['teachers.view', 'students.view', 'classes.view', 'subjects.view', 'batches.view'])
            <div class="nav-label">Academics</div>
            <nav class="nav flex-column">
                @can('teachers.view')
                    <a class="nav-link {{ active_menu('admin.teachers.*') }}" href="{{ route('admin.teachers.index') }}">
                        <i class="bi bi-person-video3"></i> Teachers
                    </a>
                @endcan
                @can('students.view')
                    <a class="nav-link {{ active_menu('admin.students.*') }}" href="{{ route('admin.students.index') }}">
                        <i class="bi bi-mortarboard"></i> Students
                    </a>
                @endcan
                @can('classes.view')
                    <a class="nav-link {{ active_menu('admin.classes.*') }}" href="{{ route('admin.classes.index') }}">
                        <i class="bi bi-easel"></i> Classes
                    </a>
                @endcan
                @can('batches.view')
                    <a class="nav-link {{ active_menu('admin.batches.*') }}" href="{{ route('admin.batches.index') }}">
                        <i class="bi bi-collection"></i> Batches
                    </a>
                @endcan
                @can('subjects.view')
                    <a class="nav-link {{ active_menu('admin.subjects.*') }}" href="{{ route('admin.subjects.index') }}">
                        <i class="bi bi-book"></i> Subjects
                    </a>
                @endcan
            </nav>
        @endcanany

        @canany(['homework.view', 'notices.view', 'study-materials.view', 'online-classes.view', 'exams.view'])
            <div class="nav-label">Learning</div>
            <nav class="nav flex-column">
                @can('homework.view')
                    <a class="nav-link {{ active_menu('admin.homework.*') }}" href="{{ route('admin.homework.index') }}">
                        <i class="bi bi-journal-check"></i> Homework
                    </a>
                @endcan
                @can('notices.view')
                    <a class="nav-link {{ active_menu('admin.notices.*') }}" href="{{ route('admin.notices.index') }}">
                        <i class="bi bi-megaphone"></i> Notices
                    </a>
                @endcan
                @can('study-materials.view')
                    <a class="nav-link {{ active_menu('admin.study-materials.*') }}" href="{{ route('admin.study-materials.index') }}">
                        <i class="bi bi-folder2-open"></i> Study Materials
                    </a>
                @endcan
                @can('online-classes.view')
                    <a class="nav-link {{ active_menu('admin.online-classes.*') }}" href="{{ route('admin.online-classes.index') }}">
                        <i class="bi bi-camera-video"></i> Online Classes
                    </a>
                @endcan
                @can('exams.view')
                    <a class="nav-link {{ active_menu('admin.exams.*') }}" href="{{ route('admin.exams.index') }}">
                        <i class="bi bi-clipboard-check"></i> Exams &amp; Results
                    </a>
                @endcan
            </nav>
        @endcanany

        @canany(['chat.view', 'leads.view'])
            <div class="nav-label">Engagement</div>
            <nav class="nav flex-column">
                @can('chat.view')
                    <a class="nav-link {{ active_menu('admin.chat-rooms.*') }}" href="{{ route('admin.chat-rooms.index') }}">
                        <i class="bi bi-chat-dots"></i> Chat Monitoring
                    </a>
                @endcan
                @can('leads.view')
                    <a class="nav-link {{ active_menu('admin.leads.*') }}" href="{{ route('admin.leads.index') }}">
                        <i class="bi bi-funnel"></i> Admission Leads
                    </a>
                @endcan
            </nav>
        @endcanany

        @canany(['reports.view', 'settings.view'])
            <div class="nav-label">System</div>
            <nav class="nav flex-column">
                @can('reports.view')
                    <a class="nav-link {{ active_menu('admin.reports.*') }}" href="{{ route('admin.reports.index') }}">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                @endcan
                @can('settings.view')
                    <a class="nav-link {{ active_menu('admin.settings.*') }}" href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                @endcan
            </nav>
        @endcanany
    </div>
</aside>
