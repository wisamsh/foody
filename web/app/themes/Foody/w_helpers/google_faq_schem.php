<?php 

if(get_post_types()=='questions'){ 
    
?>
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "QAPage",
      "mainEntity": {
        "@type": "Question",
        "name": "<?php echo get_the_title();?>",
        "text":  "<?php echo 'פודי שאלות';?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?php echo the_content(); ?>",
          "upvoteCount": 1337,
          "url": "https://example.com/question1#acceptedAnswer"
          }
    }
    </script>
    <?php }?>