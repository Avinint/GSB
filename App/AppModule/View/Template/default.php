<!DOCTYPE html>
<html lang="en">
<?php $this->header(); ?>
<body>
<header>
    <a class="site-title" href="">
        <h1>Bruno AVININT</h1>
    </a>
</header>


    <!-- Fixed navbar -->
    <?php if ($this->template == 'default') {
        $this->display('Template:navbar');
    } ?>
    <?=$content;?>
    <?php $this->footer(); ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo $this->asset('js/bootstrap.min.js')?>"></script>
</body>
</html>