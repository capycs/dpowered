<?php get_header(); ?>
<section class="inner-hero">
    <div class="container">
        <h1 class="page-title"><?php the_title(); ?></h1>
    </div>
</section>
<section class="section">
    <div class="container content-area">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="page-content"><?php the_content(); ?></div>
        <?php endwhile; endif; ?>
    </div>
</section>
<?php get_footer(); ?>
