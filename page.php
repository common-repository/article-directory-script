<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
			<div class="post" id="post-<?php the_ID(); ?>">
			
				<?php //<h1>the_title();</h1> ?>
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php //edit_post_link('Edit this entry', '<p>', '</p>'); ?>
        <!-- you must leave this intact! -->
        <div class="article_directory_copyright"><a href="http://contentdragon.com">Article Directory</a> - Powered by Content Dragon</div>
        <!-- // -->
			</div>
			
		<?php endwhile; endif; ?>
		
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>