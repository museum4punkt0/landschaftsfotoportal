    <section class="page-section" id="{{ $section_id }}">
        <div class="container">
            <div class="text-center">
            @if($section_heading)
                <h2 class="section-heading text-uppercase">{{ $section_heading }}</h2>
            @endif
            @if($section_subheading)
                <h3 class="section-subheading text-muted">{{ $section_subheading }}</h3>
            @endif
                
                @includeWhen(isset($options['image_medium']), 'includes.landschaftsfotoportal.image_medium', [
                    'columns' => ['image_filename' => 13],
                ])
            </div>
