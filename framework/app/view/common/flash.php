<div id="flash">
    <?php
    if ($this -> flash() -> hasMessages()) {
        print $this -> flash() -> displayErrorMessages();
        print $this -> flash() -> displaySuccessMessages();
    }
    ?>
</div>