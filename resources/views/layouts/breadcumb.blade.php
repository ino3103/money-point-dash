<div class="col-lg-12">
    <div class="breadcrumb-main">
        <h4 class="text-capitalize breadcrumb-title">{{ optional($data)['title'] ?? 'Default Title' }}</h4>
        <div class="breadcrumb-action justify-content-center flex-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @if (!empty($data['breadcrumbs']))
                        @foreach ($data['breadcrumbs'] as $breadcrumb)
                            @if (isset($breadcrumb['active']) && $breadcrumb['active'])
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $breadcrumb['name'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] ?? '#' }}">
                                        @if (isset($breadcrumb['icon']))
                                            <i class="{{ $breadcrumb['icon'] }}"></i>
                                        @endif
                                        {{ $breadcrumb['name'] ?? 'Untitled' }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Home</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>
</div>
