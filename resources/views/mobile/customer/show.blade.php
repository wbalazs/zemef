@extends('mobile.customer.layout')
@section('title', 'Saját kártyám')
@section('header-actions')
    <form method="post" action="{{ route('mobile.customer.logout') }}">
        @csrf
        <button type="submit" class="icon-button logout" title="Kijelentkezés" aria-label="Kijelentkezés"><i class="fas fa-sign-out-alt" aria-hidden="true"></i></button>
    </form>
@endsection
@section('content')
    @php
        $active = $card->is_currently_active;
        $upcoming = $card->is_upcoming;
        $statusLabel = $active ? 'Érvényes' : ($upcoming ? 'Hamarosan érvényes' : 'Lejárt');
    @endphp
    <section class="customer-heading">
        <p class="eyebrow">Saját kártyám</p>
        <h1>{{ $customer->name }}</h1>
    </section>
    <article class="membership-card" aria-label="{{ $card->name }}">
        <div class="card-top"><h2>{{ $card->name }}</h2><i class="fas fa-gem" aria-hidden="true"></i></div>
        <div class="discount">
            @if($card->type === 'percent')
                <strong>{{ rtrim(rtrim(number_format((float) $card->value, 2, ',', ' '), '0'), ',') }}<span>%</span></strong>
                <span>Kedvezmény</span>
            @elseif($card->type === 'price')
                <strong>{{ number_format((float) $card->value, 0, ',', ' ') }}<span> Ft</span></strong>
                <span>Kedvezmény</span>
            @else
                <strong class="product-discount">Termékkedvezmény</strong>
            @endif
        </div>
        <div class="card-bottom"><span>{{ $customer->name }}</span><span class="card-status"><i class="fas {{ $active ? 'fa-check-circle' : 'fa-clock' }}" aria-hidden="true"></i> {{ $statusLabel }}</span></div>
    </article>
    <section class="validity-band {{ !$active && !$upcoming ? 'expired' : '' }}">
        <i class="far fa-calendar-alt" aria-hidden="true"></i>
        <div><span>Érvényesség</span><strong>{{ $card->start_date?->format('Y. m. d.') ?? 'Nincs megadva' }} - {{ $card->end_date?->format('Y. m. d.') ?? 'Nincs megadva' }}</strong></div>
    </section>
    <div class="details-grid">
        <section class="detail-section">
            <h2>Személyes adatok</h2>
            <dl class="detail-list">
                <div><dt><i class="far fa-user" aria-hidden="true"></i> Név</dt><dd>{{ $customer->name }}</dd></div>
                <div><dt><i class="far fa-envelope" aria-hidden="true"></i> Email cím</dt><dd>{{ $customer->email ?: 'Nincs megadva' }}</dd></div>
                <div><dt><i class="fas fa-phone" aria-hidden="true"></i> Telefonszám</dt><dd>{{ $customer->phone ?: 'Nincs megadva' }}</dd></div>
            </dl>
        </section>
        <section class="detail-section">
            <h2>Elfogadó üzletek</h2>
            @forelse($card->businesses as $business)
                <p class="business-name"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $business->name }}</p>
            @empty
                <p class="business-name"><i class="fas fa-store" aria-hidden="true"></i> Minden üzletben</p>
            @endforelse
            @if($card->type === 'product_item')
                <h2 class="secondary-heading">Kedvezményes termékek</h2>
                <ul class="product-list">
                    @forelse($card->products as $product)
                        <li>{{ $product->name ?: $product->description ?: $product->code }}</li>
                    @empty
                        <li>Nincs kijelölt termék</li>
                    @endforelse
                </ul>
            @endif
        </section>
    </div>
    @if($card->productTypeRules->isNotEmpty() || $card->productGroupRules->isNotEmpty())
        <section class="detail-section rules-section">
            <h2>Egyedi kedvezmények</h2>
            <dl class="rules-list">
                @foreach($card->productTypeRules as $rule)
                    <div><dt>{{ $rule->productType->name ?? 'Terméktípus' }}</dt><dd>{{ number_format((float) $rule->percent, 2, ',', ' ') }}%</dd></div>
                @endforeach
                @foreach($card->productGroupRules as $rule)
                    <div><dt>{{ $rule->productGroup->name ?? 'Termékcsoport' }}</dt><dd>{{ number_format((float) $rule->percent, 2, ',', ' ') }}%</dd></div>
                @endforeach
            </dl>
        </section>
    @endif
@endsection