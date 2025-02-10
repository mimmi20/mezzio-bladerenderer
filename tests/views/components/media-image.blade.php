@if ($image === false)
<h1>There has been an error loading image "{{ $wordpresstitle }}"</h1>
@else
@if ($preload)
@push('preload')
<link
    rel="preload"
    as="image"
    href="{{ $image->src }}"
    imagesrcset="{{ $image->srcsetWebp ? $image->srcsetWebp : $image->srcset }}"
    imagesizes="{{ $image->sizes }}"
/>
@endpush
@endif
<picture>
    @if ($image->srcsetWebp)
    <source
        srcset="{{ $image->srcsetWebp }}"
        sizes="{{ $image->sizes }}"
        type="image/webp"
    />
    @endif
    <source
        srcset="{{ $image->srcset }}"
        sizes="{{ $image->sizes }}"
        type="{{ $image->metaData['sizes']['medium']['mime-type'] }}"
    />
    <img
        src="{{ $image->src }}"
        width="{{ $image->metaData['width'] }}"
        height="{{ $image->metaData['height'] }}"
        alt="{{ $image->alt }}"
        title="{{ $image->caption }}"
        class="{{ $class }}"
        loading="lazy"
    />
</picture>
@endif
