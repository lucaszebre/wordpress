<?php
// sidebar.php
// Barre latérale - affiche les widgets si la zone 'sidebar-1' est active
if (is_active_sidebar('sidebar-1')) :
?>
<aside class="sidebar" role="complementary">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside>
<?php
endif;
