<?php get_header(); ?>
<section class="inner-hero" id="main-content">
    <div class="container inner-hero-content">
        <h1><?php the_title(); ?></h1>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-content reveal">
            <?php if (have_posts()): while (have_posts()): the_post(); the_content(); endwhile; endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
