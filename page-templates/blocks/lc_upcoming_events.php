<section class="upcoming_events py-4 has-green-100-background-color" data-aos="fade">
    <div class="container-xl py-4 illustration-bg-2 illustration-right">
        <div class="text-center mb-4">
            <h2><?= get_field('title') ?></h2>
            <?= get_field('intro') ?>
        </div>
        <div class="upcoming_events__list">
            <?php
            $today = date('Ymd'); // Get today's date in ACF-compatible format (YYYYMMDD)

            $args = array(
                'post_type'      => 'event',
                'posts_per_page' => 3, // Limit to 3 events
                'meta_key'       => 'event_start_date',
                'orderby'        => 'meta_value',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'     => 'event_start_date',
                        'value'   => $today,
                        'compare' => '>=', // Include events happening today or in the future
                        'type'    => 'DATE'
                    )
                )
            );

            $events = new WP_Query($args);

            if ($events->have_posts()) {
                while ($events->have_posts()) {
                    $events->the_post();
            ?>
                    <div class="event_row">
                        <div class="event_date">
                            📅
                            <?php
                            $start_date = get_field('event_start_date', get_the_ID());
                            $end_date = get_field('event_end_date', get_the_ID());

                            if ($start_date) {
                                $start = DateTime::createFromFormat('Ymd', $start_date);
                                $start_day = $start->format('jS');
                                $start_month = $start->format('F');
                                $start_year = $start->format('Y');

                                if ($end_date) {
                                    $end = DateTime::createFromFormat('Ymd', $end_date);
                                    $end_day = $end->format('jS');
                                    $end_month = $end->format('F');
                                    $end_year = $end->format('Y');

                                    if ($start_year !== $end_year) {
                                        // If the year changes, show full date for both
                                        echo "{$start_day} {$start_month} {$start_year} - {$end_day} {$end_month} {$end_year}";
                                    } elseif ($start_month !== $end_month) {
                                        // If the month changes but the year stays the same
                                        echo "{$start_day} {$start_month} - {$end_day} {$end_month} {$start_year}";
                                    } else {
                                        // If it's in the same month and year
                                        echo "{$start_day} - {$end_day} {$start_month} {$start_year}";
                                    }
                                } else {
                                    // Only a start date
                                    echo "{$start_day} {$start_month} {$start_year}";
                                }
                            }
                            ?>
                        </div>
                        <div class="event_title"><?= get_the_title(); ?></div>
                        <div class="event_address">
                            <?php
                            $address = get_field('event_address', get_the_ID());
                            $postcode = get_field('event_postcode', get_the_ID());
                            if ($address && $postcode) {
                                $full_address = $address . ', ' . $postcode;
                                $google_maps_url = 'https://www.google.com/maps/search/?q=' . urlencode($full_address);
                                echo '📍 <a href="' . esc_url($google_maps_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($full_address) . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                wp_reset_postdata();
            } else {
                ?>
                <p>No upcoming events.</p>
            <?php
            }

            ?>
        </div>
        <div class="text-center">
            <a href="/events/" class="button button-primary mt-4">View Our Event Calendar</a>
        </div>
    </div>
</section>