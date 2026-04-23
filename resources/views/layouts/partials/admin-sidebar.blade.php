<aside class="sidebar">
    <div class="sidebar-panel">
        <div class="sidebar-head">
            <div class="brand-copy">
                <small>{{ $isAr ? 'لوحة التحكم' : 'Admin Suite' }}</small>
                <strong>Dr Halim Dental</strong>
                <span>{{ $isAr ? 'تنظيم أدق للصفحات والتشغيل اليومي' : 'Cleaner control for pages and daily operations' }}</span>
            </div>
            <span class="brand-mark">{!! $icons['grid'] !!}</span>
        </div>

        <div class="workspace-card">
            <div>
                <strong>{{ $isAr ? 'مساحة العمل الرئيسية' : 'Primary Workspace' }}</strong>
                <span>{{ $isAr ? 'تم ترتيب الأقسام لتقليل التزاحم والوصول السريع' : 'Sections are grouped for cleaner, faster navigation' }}</span>
            </div>
            <span class="workspace-badge">{{ $isAr ? 'نشط' : 'Active' }}</span>
        </div>

        <div class="sidebar-groups">
            @foreach($groups as $groupIndex => $group)
                @php
                    $collapseId = $sidebarIdPrefix . '-group-' . $groupIndex;
                    $isOpen = $groupIndex === $activeGroupIndex;
                    $isFinanceGroup = ($group['theme'] ?? null) === 'finance';
                @endphp
                <section class="sidebar-group {{ $isFinanceGroup ? 'is-finance' : '' }}">
                    <button
                        class="group-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $collapseId }}"
                        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                        aria-controls="{{ $collapseId }}"
                    >
                        <span class="group-main">
                            <span class="group-bullet"></span>
                            <span class="group-copy">
                                <strong>{{ $group['title'] }}</strong>
                                <span>{{ $group['subtitle'] }}</span>
                            </span>
                        </span>
                        <span class="group-meta">
                            @if($isFinanceGroup)
                                <span class="finance-badge">{{ $isAr ? 'Finance' : 'Finance' }}</span>
                            @endif
                            <span>{{ $group['items']->count() }}</span>
                            <span class="group-chevron">⌄</span>
                        </span>
                    </button>

                    <div id="{{ $collapseId }}" class="collapse {{ $isOpen ? 'show' : '' }}">
                        <div class="group-body">
                            <div class="nav-list">
                                @foreach($group['items'] as $item)
                                    @php($isActive = $routeName && str_starts_with($routeName, $item['route']))
                                    <a href="{{ route($item['route'], app()->getLocale()) }}" class="nav-link-item {{ $isActive ? 'active' : '' }}">
                                        <span class="nav-main">
                                            <span class="nav-icon">{!! $item['icon'] !!}</span>
                                            <span class="nav-copy">
                                                <span class="nav-label">{{ $item['label'] }}</span>
                                                <span class="nav-desc">{{ $item['desc'] }}</span>
                                            </span>
                                        </span>
                                        <span class="nav-arrow">{{ $isAr ? '‹' : '›' }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>

        <div class="sidebar-footer">
            <a class="btn btn-outline-light" href="{{ url('/' . app()->getLocale()) }}">
                {{ $isAr ? 'عرض الموقع' : 'Open Website' }}
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-light w-100">{{ $isAr ? 'تسجيل الخروج' : 'Logout' }}</button>
            </form>
        </div>
    </div>
</aside>
