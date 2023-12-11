<?php
$fields = get_field('hero');
$meetings = get_field('meetings', 'option');

//content box vars
$content_box     = $fields['content_box'] ?? [];
$title           = $content_box['title'] ?? '';
$description     = $content_box['description'] ?? '';

//meeting link
$link            = $meetings['15_minutes_meeting'] ?? [];
$link_url        = esc_url($link['url']) ?? '';
$link_text       = esc_html($link['title']) ?? '';
$link_target     = esc_attr($link['target']) ?? '';

//media box vars
$media_box        = $fields['media_box'] ?? [];
$hero_image_array = $media_box['image'] ?? [];
$hero_image_id    = $hero_image_array['ID'] ?? '';
$hero_image_alt   = $hero_image_array['alt'] ?? '';

//advant boxes vars
$box_one_text      = $fields['box_one_text'] ?? '';
$box_second_number = $fields['second_box_number'] ?? '';
$box_second_text   = $fields['second_box_text'] ?? '';
$box_third_number  = $fields['third_box_number'] ?? '';
$box_third_text    = $fields['third_box_text'] ?? '';

?>

<section class="home-hero">
    <div class="home-hero__blur">
        <svg class="home-hero__blur-image" width="1212" height="1293" viewBox="0 0 1212 1293" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_f_83_6)">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M737.695 84.2639C810.459 62.776 858.446 -19.1092 934.32 -18.8771C1009.81 -18.6462 1060.07 54.8768 1129.1 85.4611C1223.98 127.498 1362.5 102.583 1413.05 193.225C1462.17 281.317 1358.69 381.074 1349.98 481.549C1337.47 625.837 1441.14 791.822 1351.32 905.411C1263.99 1015.85 1088.81 988.735 948.004 989.898C807.915 991.056 649.257 1006.17 545.442 912.07C443.655 819.807 474.912 656.954 435.503 525.352C398.183 400.725 247.4 269.571 320.913 162.269C402.798 42.7469 598.745 125.298 737.695 84.2639Z" fill="#5CE1E6" fill-opacity="0.3"/>
            </g>
            <defs>
                <filter id="filter0_f_83_6" x="0.983887" y="-318.878" width="1725.23" height="1610.91" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                    <feGaussianBlur stdDeviation="150" result="effect1_foregroundBlur_83_6"/>
                </filter>
            </defs>
        </svg>
    </div>
    <!-- /.home-hero__blur -->

    <div class="home-hero__container container">
       <div class="home-hero__row">
           <div class="home-hero__content-wrapper wow fadeInLeft" data-wow-duration="1s">
               <?php if(!empty($title)) : ?>
                   <?php echo sprintf('<h1 class="home-hero__title">%s</h1>', $title); ?>
                   <!-- /.home-hero__title -->
               <?php endif; ?>

               <?php if(!empty($description)) : ?>
                   <?php echo sprintf('<p class="home-hero__description">%s</p>', $description); ?>
                   <!-- /.home-hero__description -->
               <?php endif; ?>

               <?php if(!empty($link)) : ?>
                   <div class="home-hero__cta-wrapper">
                       <?php echo sprintf('<a href="%s" class="home-hero__cta-btn cta-btn cta-btn--blue" target="%s">%s</a>',
                           $link_url, $link_target, $link_text);
                       ?>
                       <!-- /.home-hero__cta-btn -->
                   </div>
                   <!-- /.home-hero__cta-wrapper -->
               <?php endif; ?>
           </div>
           <!-- /.home-hero__content-box -->

           <?php if(!empty($hero_image_id)) : ?>
               <div class="home-hero__media-wrapper media-wrapper wow fadeInRight" data-wow-duration="1s">
                   <div class="media-wrapper__image-wrapper">
                       <!-- /.media-box__logo -->

                       <?php echo wp_get_attachment_image($hero_image_id, 'full', '',array('alt'=> $hero_image_alt, 'class' => 'media-wrapper__image')); ?>

                       <svg class="media-wrapper__circle media-wrapper__circle--desktop" width="770" height="808" viewBox="0 0 770 808" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M177 396C133.282 458.576 125.997 511.826 154.765 555.931C201.782 628.013 343.05 618.495 470.297 534.672C597.544 450.85 662.584 324.465 615.567 252.383C608.859 242.098 601.232 234.483 591 227.5" stroke="#D9D9D9" stroke-linecap="round"/>
                           <path d="M589.721 354.21C604.817 360.05 621.812 352.491 627.689 337.323C621.812 352.491 629.283 369.514 644.379 375.354C629.283 369.514 612.288 377.073 606.412 392.241C612.288 377.073 604.817 360.05 589.721 354.21Z" fill="#D9D9D9"/>
                           <path d="M135.102 414.884C148.337 421.653 164.565 416.355 171.356 403.049C164.565 416.355 169.788 432.623 183.023 439.392C169.788 432.623 153.559 437.921 146.769 451.226C153.559 437.921 148.337 421.653 135.102 414.884Z" fill="#D9D9D9"/>
                       </svg>
                       <svg class="media-wrapper__circle media-wrapper__circle--mobile" width="355" height="420" viewBox="0 0 355 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M46.5295 205.913C23.7883 238.364 19.9985 265.978 34.9633 288.85C59.4207 326.229 132.907 321.294 199.099 277.826C265.291 234.357 299.124 168.817 274.666 131.437C271.177 126.104 267.209 122.155 261.887 118.533" stroke="#D9D9D9" stroke-linecap="round"/>
                           <path d="M261.222 184.242C269.075 187.27 277.915 183.35 280.972 175.485C277.915 183.35 281.802 192.178 289.654 195.207C281.802 192.178 272.961 196.098 269.904 203.964C272.961 196.098 269.075 187.27 261.222 184.242Z" fill="#D9D9D9"/>
                           <path d="M24.7347 215.706C31.6194 219.216 40.061 216.469 43.5934 209.569C40.061 216.469 42.7776 224.905 49.6623 228.415C42.7776 224.905 34.336 227.652 30.8036 234.552C34.336 227.652 31.6194 219.216 24.7347 215.706Z" fill="#D9D9D9"/>
                       </svg>
                   </div>
                   <!-- /.media-box__image -->
               </div>
               <!-- /.home-hero__media-box -->
           <?php endif; ?>
       </div>
       <!-- /.home-hero__row -->

        <div class="home-hero__advant-boxes-wrapper">
            <div class="home-hero__advant-boxes-row">
                <?php if(!empty($box_one_text)) : ?>
                <div class="home-hero__advant-box advant-box advant-box--accent wow fadeInUp" data-wow-duration="1s" data-wow-delay="1s">
                    <div class="advant-box__top-element advant-box__top-element--accent"></div>

                    <svg class="advant-box__star" width="114" height="113" viewBox="0 0 114 113" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M113.995 56.4788L63.1959 57.0185C60.0872 57.0515 57.5752 59.5406 57.5419 62.621L56.9973 112.958L56.4527 62.621C56.4194 59.5406 53.9074 57.0515 50.7987 57.0185L-2.28882e-05 56.4788L50.7987 55.9392C53.9074 55.9062 56.4194 53.4171 56.4527 50.3366L56.9973 0L57.5419 50.3366C57.5752 53.4171 60.0872 55.9062 63.1959 55.9392L113.995 56.4788Z" fill="#F05D23"/>
                    </svg>
                    <?php echo sprintf('<p class="advant-box__accent-text">%s</p>', $box_one_text); ?>
                </div>
                <!-- /.home-hero__advant-box -->
                <?php endif; ?>

                <?php if(!empty($box_second_number) && !empty($box_second_text)) : ?>
                <div class="home-hero__advant-box advant-box advant-box--orange wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.4s">
                    <div class="advant-box__top-element advant-box__top-element--orange"></div>

                    <?php echo sprintf('<p class="advant-box__number advant-box__number--gray">%s</p>', $box_second_number); ?>
                    <?php echo sprintf('<p class="advant-box__text advant-box__text--gray">%s</p>', $box_second_text); ?>
                </div>
                <!-- /.home-hero__advant-box -->
                <?php endif; ?>

                <?php if(!empty($box_third_number) || !empty($box_third_text)) : ?>
                <div class="home-hero__advant-box advant-box advant-box--green wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.6s">
                    <div class="advant-box__top-element advant-box__top-element--green"></div>

                    <?php echo sprintf('<p class="advant-box__number advant-box__number--dark">%s</p>', $box_third_number); ?>
                    <?php echo sprintf('<p class="advant-box__text advant-box__text--dark">%s</p>', $box_third_text); ?>
                </div>
                <!-- /.home-hero__advant-box -->
                <?php endif; ?>
            </div>
            <!-- /.home-hero__advant-boxes-row -->
        </div>
        <!-- /.home-hero__advant-boxes-wrapper -->
    </div>
    <!-- /.container -->

    <?php get_template_part('template-parts/modal', 'modal'); ?>
</section>
<!-- /.home-hero -->