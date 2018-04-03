<?php
/**
 * @var \App\View\AppView $this
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="acos index large-9 medium-8 columns content">

    <?php
    echo $this->element('design/header');
    ?>

    <?php
    echo $this->element('Acos/links');
    ?>

    <?php
    echo $this->element('design/footer');
    ?>

</div>