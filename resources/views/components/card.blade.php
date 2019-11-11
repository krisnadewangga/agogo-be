<section class="content-header">
    <h1>
      {{ $title }}
    </h1>
    @if(isset($breadcumbs))
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        @foreach($breadcumbs as $key)
            <li><a href="{{ $key['link'] }}">{{ $key['judul'] }}</a></li>
        @endforeach
      </ol>
     @endif
</section>

<section class="content">
    <div class="row">
      <div class="col-md-12">
        {{ $slot }}
      </div>
    </div>
</section>